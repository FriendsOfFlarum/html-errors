<?php

namespace Flagrow\HtmlErrors\Listeners;

use Flagrow\HtmlErrors\Middlewares\HandleErrors;
use Flarum\Event\ConfigureMiddleware;
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
        if (!$event->isForum()) {
            return;
        }

        $event->pipe(app(HandleErrors::class));
    }
}
