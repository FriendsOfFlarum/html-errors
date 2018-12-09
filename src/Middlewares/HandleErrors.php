<?php

namespace Flagrow\HtmlErrors\Middlewares;

use Exception;
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
            $errorCode = $exception->getCode();

            // Based on the same logic as Flarum\Http\Middleware\HandleErrors
            // If the code of the error looks like an HTML status, we try to handle it
            if (is_int($errorCode) && $errorCode >= 400 && $errorCode < 600) {
                /**
                 * @var $settings SettingsRepositoryInterface
                 */
                $settings = app(SettingsRepositoryInterface::class);

                // Get the custom html for that error if it exists
                // This supports more codes than what is exposed in the extension settings
                $html = $settings->get('flagrow-html-errors.custom' . $errorCode . 'ErrorHtml');

                if ($html) {
                    return new HtmlResponse($html, $errorCode);
                }
            }

            throw $exception;
        }

        return $response;
    }
}
