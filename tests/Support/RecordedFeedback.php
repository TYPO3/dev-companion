<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Support;

use PHPUnit\Framework\Attributes\After;
use Symfony\Component\Finder\Finder;
use TYPO3\DevCompanion\Feedback\Channel;
use TYPO3\DevCompanion\Paths;

/**
 * A feedback store of the test's own, for the cases that record one.
 *
 * The same arrangement as `QueuedTodo` and for the same reason. A unit test
 * writes into no directory this repository keeps. A fixture left in `feedback/`
 * by a run that died is a report somebody left (`R-COD-003`).
 *
 * The redirect happens on the first write. So the cases that are *about* the
 * corpus, the mangled names and the tools every feedback in the archive names,
 * still read the real one.
 */
trait RecordedFeedback
{
    /** What a fixture's text carries, so a case can pick its own out of what it wrote. */
    private const MARKER = 'phpunit-feedback-fixture';

    /** The store this case writes into, made on the first record. */
    private ?string $ownFeedback = null;

    #[After]
    public function removeRecordedFeedback(): void
    {
        Paths::useFeedback(null);

        $store = $this->ownFeedback;
        $this->ownFeedback = null;
        if ($store === null || !is_dir($store)) {
            return;
        }

        foreach (Finder::create()->files()->in($store) as $file) {
            unlink($file->getPathname());
        }
        // The queue with it. A feedback arrives with the card that asks for its
        // judgement, so a store of this case's own has one of those too.
        @rmdir($store . '/todo/open');
        @rmdir($store . '/todo');
        @rmdir($store . '/feedback/archive');
        @rmdir($store . '/feedback');
        @rmdir($store);
    }

    /**
     * One recorded feedback, in a store of this case's own.
     *
     * `Channel::record()` decides the file name and the front matter, which is
     * what most of these cases are about. So it is still what writes, and only
     * where it writes is this case's own.
     *
     * @param array<string, mixed> $payload
     * @param array<int, string> $redacted what `record()` took out, for the cases that hold it
     *
     * @param-out array<int, string> $redacted
     */
    private function recordFeedback(array $payload, array &$redacted = []): string
    {
        $this->ownFeedbackStore();

        return Channel::record($payload + ['observation' => self::MARKER], $redacted);
    }

    /** Where a path this store handed back actually is. */
    private function inStore(string $relative): string
    {
        return $this->ownFeedbackStore() . '/' . $relative;
    }

    /**
     * The store this case writes into, made once and pointed at.
     *
     * The archive comes with it. A closed feedback moves there, and a store
     * without one would fail on the move rather than on what the case holds.
     */
    private function ownFeedbackStore(): string
    {
        if ($this->ownFeedback !== null) {
            return $this->ownFeedback;
        }

        // A root of its own with a `feedback` in it. What `record()` and
        // `archive()` hand back is a path relative to that root, and the cases
        // read the file at it.
        $root = sys_get_temp_dir() . '/' . self::MARKER . '-' . getmypid() . '-' . bin2hex(random_bytes(6));
        mkdir($root . '/feedback/archive', 0o777, true);
        Paths::useFeedback($root . '/feedback');

        return $this->ownFeedback = $root;
    }
}
