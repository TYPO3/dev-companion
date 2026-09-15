<?php

declare(strict_types=1);

/**
 * Locates and loads the Composer autoloader for the stdio entrypoint.
 *
 * The file lives in a different place according to how this package is in use.
 * In a standalone checkout it is ./vendor/autoload.php, while as an installed
 * dependency the package sits in vendor/typo3/dev-companion/ and the autoloader
 * is three levels up. Composer's bin proxy tells us the exact path via
 * $_composer_autoload_path, so that wins when present.
 */
(static function (): void {
    $candidates = [];

    // Composer's bin proxy sets it, and it is authoritative when the entrypoint
    // ran through vendor/bin/.
    if (isset($GLOBALS['_composer_autoload_path'])) {
        $candidates[] = $GLOBALS['_composer_autoload_path'];
    }

    $candidates[] = dirname(__DIR__) . '/vendor/autoload.php'; // standalone checkout
    $candidates[] = dirname(__DIR__, 3) . '/autoload.php';     // vendor/typo3/dev-companion/

    foreach ($candidates as $candidate) {
        if (is_file($candidate)) {
            require $candidate;

            return;
        }
    }

    fwrite(STDERR, 'typo3-dev-companion: Composer autoloader not found.'
        . " Run 'composer install' in the package root.\n");
    exit(1);
})();
