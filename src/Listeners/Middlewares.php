<?php

namespace FoF\HtmlErrors\Listeners;

use Flarum\Event\ConfigureMiddleware;
use FoF\HtmlErrors\Middlewares\HandleErrors;
use Illuminate\Contracts\Events\Dispatcher;

class Middlewares
{
    public function subscribe(Dispatcher $events)
    {
        // It is crucial this listener is registered before any middleware that relies on exceptions
        // Because otherwise it would prevent any of those from catching exceptions themselves
        // This way the error handling only happens if really no other extension catches it
        $events->listen(ConfigureMiddleware::class, [$this, 'configureMiddleware'], 10);
    }

    public function configureMiddleware(ConfigureMiddleware $event)
    {
        // Only add to forum and only when debug mode is turned off
        if (!$event->isForum() || app()->inDebugMode()) {
            return;
        }

        $event->pipe(app(HandleErrors::class));
    }
}
