<?php

namespace Flagrow\HtmlErrors\Listeners;

use Exception;
use Flarum\Event\ConfigureMiddleware;
use Flarum\Settings\SettingsRepositoryInterface;
use Illuminate\Contracts\Events\Dispatcher;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Zend\Diactoros\Response\HtmlResponse;

class Middlewares
{
    public function subscribe(Dispatcher $events)
    {
        $events->listen(ConfigureMiddleware::class, [$this, 'configureMiddleware']);
    }

    public function configureMiddleware(ConfigureMiddleware $event)
    {
        if (!$event->isForum()) {
            return;
        }

        $event->pipe(function(Request $request, Response $response, callable $out = null)  {
            try {
                $response = $out($request, $response);
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
        });
    }
}
