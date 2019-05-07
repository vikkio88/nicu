<?php

use Nicu\Actions\Misc\Ping;

return [
    'routes' => [
        [
            'route' => '/ping',
            'method' => 'get',
            'action' => Ping::class
        ]
    ]
];
