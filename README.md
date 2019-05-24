New Relic APM for Swoole
========================

This library enables monitoring of PHP applications running on [Swoole](https://www.swoole.co.uk/) web-server via [New Relic APM](https://newrelic.com/).

**Features:**
- Transparent request monitoring
- Request transaction identification
- Request transaction boundaries
- Coroutine requests aggregation

## Demo

![New Relic APM dashboard](docs/img/newrelic_apm_swoole.png)

## Installation

The library is to be installed via [Composer](https://getcomposer.org/) as a dependency:
```bash
composer require upscale/swoole-newrelic
```

## Usage

The easiest way to start monitoring is to activate the profiler globally for all requests from start to finish.
This approach is by design completely transparent to an application running on the server.
No code changes are needed beyond editing a few lines of code in the server entry point.

Install the monitoring instrumentation for all requests:
```php
$server->on('request', function ($request, $response) use ($server) {
    // PHP processing within request boundary...
    usleep(1000 * rand(100, 300));
    
    // Send response
    $response->header('Content-Type', 'text/plain');
    $response->end("Served by worker {$server->worker_id}\n");
    
    // PHP processing outside of request boundary...
    usleep(1000 * rand(50, 150));
});

$apm = new \Upscale\Swoole\Newrelic\Apm(
    new \Upscale\Swoole\Newrelic\Apm\TransactionFactory()
);
$apm->instrument($server);
unset($apm);

$server->start();
```

## Limitations

Transactions are classified as "Non-web transactions" due to the [CLI SAPI](https://www.php.net/manual/en/features.commandline.introduction.php) environment.
  
Nested requests invoked via the [coroutine](https://www.swoole.co.uk/coroutine) mechanism are reported as part of the topmost transaction.

## Contributing

Pull Requests with fixes and improvements are welcome!

## License

Copyright © Upscale Software. All rights reserved.

Licensed under the [Apache License, Version 2.0](http://www.apache.org/licenses/LICENSE-2.0).