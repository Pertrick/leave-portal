<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Account Request Settings
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration settings for account requests.
    |
    */

    'require_pending_request' => env('REQUIRE_PENDING_REQUEST', true),

    'request_status' => [
        'pending' => 'pending',
        'approved' => 'approved',
        'rejected' => 'rejected',
    ],
]; 