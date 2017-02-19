<?php

require '../../src/FreshMail/RestApi.php';
require '../../src/FreshMail/RestException.php';
require '../../config.php';

$rest = new \FreshMail\RestApi();

$rest->setApiKey(FM_API_KEY);
$rest->setApiSecret(FM_API_SECRET);

try {
    $response = $rest->doRequest('ping');

    echo 'Ping ok, received data: ';
    print_r($response);
    echo PHP_EOL;
} catch (Exception $e) {
    echo 'Error message: ' . $e->getMessage() . ', Error code: ' . $e->getCode() . ', HTTP code: ' . $rest->getHttpCode() . PHP_EOL;
}

try {
    $response = $rest->doRequest('ping', ['testKey' => 'testValue']);

    echo 'Ping ok, received data: ';
    print_r($response);
    echo PHP_EOL;
} catch (Exception $e) {
    echo 'Error message: ' . $e->getMessage() . ', Error code: ' . $e->getCode() . ', HTTP code: ' . $rest->getHttpCode() . PHP_EOL;
}
