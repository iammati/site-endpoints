<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Site-Endpoints',
    'description' => 'Ridiculous fast endpoint declarations to make any TYPO3 application use fast responsible endpoint pathnames and necessary middlewares only.',
    'version' => '0.1.1',
    'category' => 'plugin',
    'author' => 'Mati',
    'author_email' => 'mati_01@icloud.com',
    'state' => 'stable',
    'constraints' => [
        'conflicts' => [],
        'suggests' => [],
        'depends' => [
            'typo3' => '11.5.4-11.5.99',
        ],
    ],
];
