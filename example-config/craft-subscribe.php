<?php

use born05\craftsubscribe\CraftSubscribe;

/**
 * BE AWARE: This is an example, change the values according your needs.
 * Rename this file to 'craft-subscribe.php'
 */
return [
    'type' => CraftSubscribe::MAILCHIMP_CRM_HANDLE,
    'apiBasePath' => 'https://us7.api.mailchimp.com/3.0',
    'apiKey' => getenv('MAILCHIMP_API_KEY'),
    'listId' => '',
    'merge_fields' => [
        'FNAME' => [
            'type' => 'string',
            'required' => false,
        ],
        'LANG' => [
            'type' => 'string',
            'required' => false,
            'default' => 'NL',
        ],
    ],
    'honeypot' => true,
    'honeypotKey' => 'name',
];
