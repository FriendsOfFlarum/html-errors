<?php

namespace Flagrow\HtmlErrors;

use Flarum\Extend;
use Illuminate\Contracts\Events\Dispatcher;

return [
    (new Extend\Frontend('admin'))
        ->js(__DIR__ . '/js/dist/admin.js'),
    new Extend\Locales(__DIR__ . '/locale'),
    function (Dispatcher $events) {
        $events->subscribe(Listeners\Middlewares::class);
    },
];
