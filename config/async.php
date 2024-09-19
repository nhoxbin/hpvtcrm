<?php

return [
    /*
     * The PHP binary will be use in async processes.
     */
    'withBinary' => 'C:\laragon\bin\php\php-8.3.6-Win32-vs16-x64\php', // PHP_BINARY

    /*
     * Maximum concurrency async processes.
     */
    'concurrency' => 30,

    /*
     * Async process timeout.
     */
    'timeout' => 15,

    /*
     * Sleep (micro-second) time when waiting async processes.
     */
    'sleepTime' => 50000,

    /*
     * Default output length of async processes.
     */
    'defaultOutputLength' => 1024 * 10,

    /*
     * An autoload script to boot composer autoload and Laravel application.
     * Default null meaning using an autoload of this package.
     */
    'autoload' => null,
];
