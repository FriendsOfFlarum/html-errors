<?php

namespace FoF\HtmlErrors\Middlewares;

use Exception;
use Flarum\Foundation\ErrorHandling\Registry;
use Flarum\Foundation\ErrorHandling\Reporter;
use Flarum\Settings\SettingsRepositoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\HtmlResponse;

class HandleErrors implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            $response = $handler->handle($request);
        } catch (Exception $exception) {
            /**
             * @var Registry $registry
             */
            $registry = app(Registry::class);

            $error = $registry->handle($exception);

            /**
             * @var $settings SettingsRepositoryInterface
             */
            $settings = app(SettingsRepositoryInterface::class);

            // Get the custom html for that error if it exists
            // This supports more codes than what is exposed in the extension settings
            $html = $settings->get('flagrow-html-errors.custom' . $error->getStatusCode() . 'ErrorHtml');

            if ($html) {
                // Because returning our own HTML will bypass Flarum's reporting feature,
                // we call the reporters ourselves if necessary
                // This way 500 errors can be customized but will still be sent to the log files / Sentry / other
                if ($error->shouldBeReported()) {
                    /**
                     * @var Reporter[] $reporters
                     */
                    $reporters = app()->tagged(Reporter::class);

                    foreach ($reporters as $reporter) {
                        $reporter->report($error->getException());
                    }
                }

                return new HtmlResponse($html, $error->getStatusCode());
            }

            throw $exception;
        }

        return $response;
    }
}
