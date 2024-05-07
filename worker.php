<?php

declare(strict_types=1);

use Spiral\RoadRunner\Http\HttpWorker;
use Spiral\RoadRunner\Http\PSR7Worker;
use Spiral\RoadRunner\Worker;
use Swoole\Coroutine;

require_once __DIR__ . '/vendor/autoload.php';

Coroutine::set([
    'hook_flags' => SWOOLE_HOOK_ALL,
]);

function main(): void {
    $worker = Worker::create();
    $http = new HttpWorker($worker);

    while ($request = $http->waitRequest()) {
        Coroutine::create(static function () use ($worker, $http): void {
            file_get_contents('http://httpbin.org/delay/2');
            $http->respond(200, 'Hello, World!');
            $worker->getLogger()->info('Request processed');
        });
        $worker->getLogger()->debug('Non-blocking');
    }
}

Coroutine\run(main(...));
// main();
