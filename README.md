New Relic Monitoring of Swoole
==============================

This library enables monitoring of PHP applications powered by [Swoole](https://www.swoole.co.uk/) web-server via [New Relic](https://newrelic.com/) products.

**Features:**
- New Relic APM integration
- New Relic Browser integration

## Demo

![New Relic APM dashboard](docs/img/newrelic_apm_swoole.png)

## Installation

The library is to be installed via [Composer](https://getcomposer.org/) as a dependency:
```bash
composer require upscale/swoole-newrelic
```

## Usage

### Production

Monitoring of all incoming requests from start to finish can be activated via a few lines of code in the server entry point.
The monitoring instrumentation is by design completely transparent to an application running on the server.

Install the monitoring instrumentation for all requests:
```php
use Upscale\Swoole\Newrelic;

$page = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Example page</title>
</head>
<body>
    Served by Swoole server
</body>
</html>

HTML;

$server = new \Swoole\Http\Server('127.0.0.1', 8080);
$server->on('request', function ($request, $response) use ($page) {
    // PHP processing within request boundary...
    usleep(1000 * rand(100, 300));
    
    // Send response
    $response->end($page);
    
    // PHP processing outside of request boundary...
    usleep(1000 * rand(50, 150));
});

// Real user monitoring (RUM)
$rum = new Newrelic\Browser(new Newrelic\Browser\TransactionFactory());
$rum->instrument($server);

// Application performnce monitoring (APM)
$apm = new Newrelic\Apm(new Newrelic\Apm\TransactionFactory());
$apm->instrument($server);

unset($rum, $apm);

$server->start();
```

APM instrumentation can be used standalone or in conjunction with the Browser.
Browser must be instrumented first.

Browser instrumentation is applied to non-AJAX requests having `text/html` response MIME type (Swoole default).

### Development

Having to install the New Relic PHP extension locally may be inconvenient and outright undesirable for developers.
The workaround is to replace the New Relic reporting functionality with the "stub" implementation doing nothing:
```json
{
    "require": {
        "upscale/swoole-newrelic": "^1.0",
        "killmails/polyfill-newrelic": "^1.0"
    },
    "replace": {
        "ext-newrelic": "*"
    }
}
```

The PHP extension is used when installed and substituted with the [polyfill](https://github.com/killmails/polyfill-newrelic) otherwise.  

## Limitations

Concurrent requests subject to [coroutine](https://www.swoole.co.uk/coroutine) multi-tasking are reported as part of the first in-flight transaction.

## Contributing

Pull Requests with fixes and improvements are welcome!

## License

Copyright © Upscale Software. All rights reserved.

Licensed under the [Apache License, Version 2.0](http://www.apache.org/licenses/LICENSE-2.0).