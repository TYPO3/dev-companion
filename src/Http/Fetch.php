<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Http;

use TYPO3\DevCompanion\Server\Factory;

/**
 * The one way this server reads a host outside itself.
 *
 * The timeouts, the redirect limit and the agent are a policy. A policy in two
 * places is two policies as soon as somebody edits one of them. A lookup runs
 * inside a tool call somebody waits on, so a slow host is a host that did not
 * answer. The agent is this server's own because bot protection challenges the
 * browser-shaped ones. Every read asks for compression, the TYPO3 Explained
 * root is 169 kB plain against 19.9 kB compressed. One handle serves every read
 * of one instance, `D-ANS-066`.
 */
final class Fetch
{
    /** Seconds to reach the host at all. */
    public const CONNECT_TIMEOUT = 3;

    /** Seconds for the whole request, connection included. */
    public const TIMEOUT = 8;

    /**
     * How many redirects a read follows. Documentation hosts move pages and
     * answer with one. A chain longer than this is a portal rather than a
     * source.
     */
    private const MAX_REDIRECTS = 3;

    /**
     * The transport, where a caller has one. Every test in this repository
     * passes one rather than reaches the network, which is what keeps the suite
     * the same offline and on a plane.
     *
     * @var (\Closure(string): ?string)|null
     */
    private readonly ?\Closure $transport;

    /**
     * The handle every read of this instance goes through, once there has been
     * one. It is what holds the connection open between them; a caller that
     * wants a fresh connection takes a fresh `Fetch`.
     */
    private ?\CurlHandle $handle = null;

    /** @param (\Closure(string): ?string)|null $transport */
    public function __construct(?\Closure $transport = null)
    {
        $this->transport = $transport;
    }

    /**
     * The body of a successful read, or null for everything else.
     *
     * Null is the whole error vocabulary here on purpose. A 404, a timeout and
     * a DNS failure are one answer to the caller of a lookup, the source did
     * not answer this question. What to say about it belongs to the source that
     * knows the question.
     *
     * @param array<int, string> $headers extra request headers, in `Name: value` form
     */
    public function get(string $url, array $headers = [], ?string $agent = null): ?string
    {
        return $this->read($url, $headers, $agent)['body'];
    }

    /**
     * What a host that challenges browsers answers instead.
     *
     * The protection in front of one of the sources here inverts the usual
     * repair. A browser-shaped agent gets a 200 and a challenge page, and a
     * plain client agent gets the answer. So where a body did not parse as the
     * expected shape, the retry is not "look more like a browser" but the
     * opposite. It is worth one extra round trip, because the alternative tells
     * the caller that the source is down when it is not.
     */
    public const PLAIN_AGENT = 'curl/8.5.0';

    /**
     * What an API prefixes its JSON with so a browser cannot execute the
     * response as a script. It is not JSON and has to come off before the body
     * parses. A body that does not start with it passes through untouched,
     * because anything else at the head is a portal rather than a guard.
     */
    private const XSSI_PREFIX = ")]}'";

    /**
     * The JSON a body carries, or null for everything that is not JSON.
     *
     * The sources that reach outside this package read JSON and nothing else
     * (`D-ANS-034`). A page with a 200 in front of it is what bot protection
     * and captive portals answer with. The only safe read of one is that the
     * question has no answer. So this returns null rather than lets a caller
     * decide how much of an HTML document looks like an answer.
     *
     * @return array<mixed>|null
     */
    public static function decode(?string $body): ?array
    {
        if ($body === null) {
            return null;
        }

        $body = ltrim($body);
        if (str_starts_with($body, self::XSSI_PREFIX)) {
            $body = ltrim(substr($body, strlen(self::XSSI_PREFIX)));
        }

        $decoded = json_decode($body, true);

        return is_array($decoded) ? $decoded : null;
    }

    /**
     * The same read, with the status the host answered.
     *
     * One source needs it. An issue tracker answers 404 for an issue that does
     * not exist. "There is no such issue" is a different answer to the caller
     * than "the tracker did not answer". Everything else collapses both into a
     * body it did not get, which is why `get()` is the shorter one.
     *
     * A transport is a body without a status, so an injected one reports 200
     * for what it returns and 0 for what it does not. What that leaves
     * untestable is the mapping of one status onto one answer, and the source
     * that does the mapping says so.
     *
     * The entity tag comes back beside them, because it is the whole of what a
     * caller needs to ask the same question again for nothing. docs.typo3.org
     * serves every artefact under `Cache-Control: public, no-cache` with an
     * `ETag`, and answers `If-None-Match` with a 304 and no body at all.
     *
     * @param array<int, string> $headers extra request headers, in `Name: value` form
     * @return array{status: int, body: ?string, etag: ?string}
     */
    public function read(string $url, array $headers = [], ?string $agent = null): array
    {
        // A read of nowhere is a read that did not happen, and it answers as
        // one rather than goes to curl. What a caller has is a URL it composed,
        // and an empty one means the composition went wrong.
        if ($url === '') {
            return ['status' => 0, 'body' => null, 'etag' => null];
        }

        if ($this->transport !== null) {
            $body = ($this->transport)($url);

            return ['status' => $body === null ? 0 : 200, 'body' => $body, 'etag' => null];
        }

        if ($this->handle === null) {
            // Refused with the extension named, before PHP's own error names a
            // function instead — `D-ANS-155`.
            if (!function_exists('curl_init')) {
                throw new \RuntimeException(sprintf(
                    'The PHP that runs this server has no curl extension, and every read of a host outside it goes through curl. '
                    . 'That PHP is %s (%s). Install ext-curl for it, or start the server with a PHP that has it.',
                    PHP_BINARY,
                    PHP_VERSION,
                ));
            }
            $handle = curl_init();
            if ($handle === false) {
                return ['status' => 0, 'body' => null, 'etag' => null];
            }
            $this->handle = $handle;
        }
        $handle = $this->handle;

        $etag = null;
        // Every option a read varies stands fresh on every read, so nothing a
        // previous one asked for survives into the next. `If-None-Match` is the
        // one that would be silent about it. Left behind, it turns the next
        // read of the same URL into a 304 with no body.
        curl_setopt_array($handle, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => self::MAX_REDIRECTS,
            CURLOPT_CONNECTTIMEOUT => self::CONNECT_TIMEOUT,
            CURLOPT_TIMEOUT => self::TIMEOUT,
            CURLOPT_USERAGENT => $agent ?: 'typo3-dev-companion/' . Factory::SERVER_VERSION,
            CURLOPT_HTTPHEADER => $headers,
            // The empty string is every encoding this build of curl can undo,
            // and curl undoes it before the body comes back.
            CURLOPT_ENCODING => '',
            CURLOPT_HEADERFUNCTION => static function ($handle, string $line) use (&$etag): int {
                if (stripos($line, 'etag:') === 0) {
                    $etag = trim(substr($line, strlen('etag:')));
                }

                return strlen($line);
            },
        ]);
        $body = curl_exec($handle);
        $status = (int) curl_getinfo($handle, CURLINFO_RESPONSE_CODE);

        return [
            'status' => $status,
            'body' => is_string($body) && $status >= 200 && $status < 300 ? $body : null,
            'etag' => $etag,
        ];
    }
}
