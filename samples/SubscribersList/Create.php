<?php

require '../../src/FreshMail/RestApi.php';
require '../../src/FreshMail/RestException.php';
require '../../config.php';

$rest = new \FreshMail\RestApi();

$rest->setApiKey(FM_API_KEY);
$rest->setApiSecret(FM_API_SECRET);

$data = [
    'name'        => 'VIP Subscribers',
    'description' => 'VIP Subscribers', // Not required

    'custom_fields' => [
        [
            'name' => 'First Name'
        ],

        [
            'name' => 'Last Name'
        ],
    ]
];

try {
    $response = $rest->doRequest('subscribers_list/create', $data);

    echo 'Subscribers list created, received data: ';
    var_dump($response);
    echo PHP_EOL;
} catch (Exception $e) {
    echo 'Error message: ' . $e->getMessage() . ', Error code: ' . $e->getCode() . ', HTTP code: ' . $rest->getHttpCode() . PHP_EOL;
}
