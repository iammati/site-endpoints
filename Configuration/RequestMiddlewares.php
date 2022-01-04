<?php

declare(strict_types=1);

use Site\SiteEndpoints\Middleware;

return [
    'frontend' => [
        'site/site-endpoints' => [
            'target' => Middleware\EndpointsInitiator::class,

            'after' => [
                'typo3/cms-frontend/site',
            ],

            'before' => [
                'typo3/cms-frontend/base-redirect-resolver',
            ],
        ],
    ],
];
