<?php

require '../../src/FreshMail/RestApi.php';
require '../../src/FreshMail/RestException.php';
require '../../config.php';

$rest = new \FreshMail\RestApi();

$rest->setApiKey(FM_API_KEY);
$rest->setApiSecret(FM_API_SECRET);

$data = [
    'email' => 'john@doe.tld',
    'list'  => '',

    'custom_fields' => [
        'first_name' => 'John',
        'last_name'  => 'Doe',
    ],

    //'state'   => 2
    //'confirm' => 1
];

try {
    $response = $rest->doRequest('subscriber/add', $data);

    echo 'Subscriber added, received data: ';
    print_r($response);
    echo PHP_EOL;
} catch (Exception $e) {
    echo 'Error message: ' . $e->getMessage() . ', Error code: ' . $e->getCode() . ', HTTP code: ' . $rest->getHttpCode() . PHP_EOL;
}
