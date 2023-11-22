# Content negotiation middleware

[![Total Downloads]](https://packagist.org/packages/lcobucci/content-negotiation-middleware)
[![Latest Stable Version]](https://packagist.org/packages/lcobucci/content-negotiation-middleware)
[![Unstable Version]](https://packagist.org/packages/lcobucci/content-negotiation-middleware)

[![Build Status]](https://github.com/lcobucci/content-negotiation-middleware/actions?query=workflow%3A%22PHPUnit%20Tests%22+branch%3A3.1)
[![Code Coverage]](https://codecov.io/gh/lcobucci/content-negotiation-middleware)

## Motivation

Packages like `middlewares/negotiation` do a very good job to detect the correct
content type based on the `Accept` header (or extension in the URI), however they
delegate to the `RequestHandler` to format the content according to the detected
mime type.

That works fine for most cases but it usually creates a lot of duplication in
complex software, where every single `RequestHandler` should do that formatting
(or depend on some component to do that). That logic should also be added to the
middleware that handles exceptions and converts them to the appropriated HTTP
response.

The goal of this middleware is to provide full content negotiation (detection
and formatting).

## Installation

This package is available on [Packagist], and we recommend you to install it using [Composer]:

```shell
composer require lcobucci/content-negotiation-middleware middlewares/negotiation laminas/laminas-diactoros
```

### Adventure mode

If you're ready for an adventure and don't want to use `middlewares/negotiation`
to handle the detection or `laminas/diactoros` to create the response
body (`StreamInterface` implementation), don't despair! You'll only have to use
the normal `ContentTypeMiddleware::__construct()` instead of
`ContentTypeMiddleware::fromRecommendedSettings()`.

We do have a small preference for the mentioned packages and didn't want to reinvent
the wheel... but you know, it's a free world.

## PHP Configuration

In order to make sure that other components are returning the expected objects we decided
to use `assert()`, which is a very interesting feature in PHP but not often used.
The nice thing about `assert()` is that we can (and should) disable it in production mode
so that we don't have useless statements.

So, for production mode, we recommend you to set `zend.assertions` to `-1` in your `php.ini`.
For development, you should leave `zend.assertions` as `1` and set `assert.exception` to `1`, which
will make PHP throw an [`AssertionError`](https://secure.php.net/manual/en/class.assertionerror.php)
when things go wrong.

Check the documentation for more information: https://secure.php.net/manual/en/function.assert.php

## Usage

Your very first step is to create the middleware using the correct configuration:

```php
<?php
declare(strict_types=1);

use Lcobucci\ContentNegotiation\ContentTypeMiddleware;
use Lcobucci\ContentNegotiation\Formatter\Json;
use Lcobucci\ContentNegotiation\Formatter\StringCast;
use Laminas\Diactoros\StreamFactory;

$middleware = ContentTypeMiddleware::fromRecommendedSettings(
    // First argument is the list of formats you want to support:
    [
        'json',
        // You may also specify the full configuration of the format.
        // That's handy if you need to add extensions or mime-types:
        'html' => [
            'extension' => ['html', 'htm', 'php'],
            'mime-type' => ['text/html', 'application/xhtml+xml'],
            'charset' => true,
        ],
    ],
    // It's very important to mention that:
    //
    // * the first format will be used as fallback (no acceptable mime type
    // found)
    // * the order of elements does matter
    // * the first element of `mime-type` list will be used as negotiated type


    // The second argument is the list of formatters that will be used for
    // each mime type:
    [
        'application/json' => new Json(),
        'text/html'        => new StringCast(),
    ],

     // The last argument is any implementation for the StreamFactoryInterface (PSR-17)  
    new StreamFactory()
);
```

Then you must add the middleware to very beginning of your pipeline, which will depend on the library/framework you're using, but it will be something similar to this:

```php
<?php

// ...

$application->pipe($middleware);
```

Finally, you just need to use `UnformattedResponse` as return of the request handlers you create to trigger to formatting when needed:

```php
<?php
declare(strict_types=1);

namespace Me\MyApp;

use Fig\Http\Message\StatusCodeInterface;
use Lcobucci\ContentNegotiation\UnformattedResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response;

final class MyHandler implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        // Does the necessary process and creates `$result` with the unformatted
        // content.

        return new UnformattedResponse(
            (new Response())->withStatus(StatusCodeInterface::STATUS_CREATED),
            $result
        );
    }
}
```

### Formatters

We provide some basic formatters by default:

* `Json`
* `StringCast`
* `JmsSerializer` (requires you to also install and configure [`jms/serializer`](https://jmsyst.com/libs/serializer))
* `Plates` (requires you to also install and configure [`league/plates`](http://platesphp.com))
* `Twig` (requires you to also install and configure [`twig/twig`](https://twig.symfony.com))

If you want to create a customised formatter the only thing needed is to
implement the `Formatter` interface:

```php
<?php
declare(strict_types=1);

namespace Me\MyApp;

use Lcobucci\ContentNegotiation\Formatter;
use Lcobucci\ContentNegotiation\UnformattedResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;

final class MyFancyFormatter implements Formatter
{
    public function format(UnformattedResponse $response, StreamFactoryInterface $streamFactory): ResponseInterface
    {
        $content = ''; // Do some fancy formatting of $response->getUnformattedContent() and put into $content

        return $response->withBody($streamFactory->createStream($content));
    }
}
```

## License

MIT, see [LICENSE].

[Total Downloads]: https://img.shields.io/packagist/dt/lcobucci/content-negotiation-middleware.svg?style=flat-square
[Latest Stable Version]: https://img.shields.io/packagist/v/lcobucci/content-negotiation-middleware.svg?style=flat-square
[Unstable Version]: https://img.shields.io/packagist/vpre/lcobucci/content-negotiation-middleware.svg?style=flat-square
[Build Status]: https://img.shields.io/github/actions/workflow/status/lcobucci/content-negotiation-middleware/phpunit.yml?style=flat-square
[Code Coverage]: https://codecov.io/gh/lcobucci/content-negotiation-middleware/branch/3.1/graph/badge.svg
[Packagist]: http://packagist.org/packages/lcobucci/content-negotiation-middleware
[Composer]: http://getcomposer.org
[LICENSE]: LICENSE
