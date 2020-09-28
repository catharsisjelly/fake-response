<?php

use App\Middleware\RequestConfigMiddleware;
use GuzzleHttp\Psr7\Request;
use Slim\Factory\AppFactory;
use Symfony\Component\Yaml\Yaml;
use GuzzleHttp\Psr7\Response;

require __DIR__ . '/../../vendor/autoload.php';

/**
 * Instantiate App
 *
 * In order for the factory to work you need to ensure you have installed
 * a supported PSR-7 implementation of your choice e.g.: Slim PSR-7 and a supported
 * ServerRequest creator (included with Slim PSR-7)
 */
$app = AppFactory::create();

// Add Routing Middleware
$app->addRoutingMiddleware();

/**
 * Add Error Handling Middleware
 *
 * @param bool $displayErrorDetails -> Should be set to false in production
 * @param bool $logErrors -> Parameter is passed to the default ErrorHandler
 * @param bool $logErrorDetails -> Display error details in error log
 * which can be replaced by a callable of your choice.

 * Note: This middleware should be added last. It will not handle any exceptions/errors
 * for middleware added after it.
 */
$errorMiddleware = $app->addErrorMiddleware(true, true, true);

$config = Yaml::parseFile(__DIR__ . '/../../config/rest.yml');

$app->add(new RequestConfigMiddleware($config['request'] ?? null));

foreach ($config['routes'] as $route) {
    $path = $route['path'];
    if (!array_key_exists('methods', $route)) {
        continue;
    }
    foreach ($route['methods'] as $method => $params) {
        $app->$method($path, function (Request $request, Response $response, $args) use ($params) {
            $responseStatus = (int) $request->getHeaderLine('X-Response-Requested') ?? 200; // Default to OK

            $response->withStatus($responseStatus);
            $response->withHeader('Content-Type', 'application/json');
            $response->getBody()->write(json_encode($params[$responseStatus]));
            return $response;
        });
    }
}

// Run app
$app->run();
