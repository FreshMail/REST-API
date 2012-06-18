<?php

require '../class.rest.php';
require '../config.php';

$rest = new FmRestAPI();
$rest->setApiKey( FM_API_KEY );
$rest->setApiSecret( FM_API_SECRET );

//testing GET request
try {
    $response = $rest->doRequest('ping');

    echo 'PING OK, received data: ';
    print_r($response);
    echo PHP_EOL;
} catch (Exception $e) {
    echo 'Error message: '.$e->getMessage().', Error code: '.$e->getCode().', HTTP code: '.$rest->getHttpCode().PHP_EOL;
}

//testing POST request
try {
    $postData = array( 'someData' => 'someValue' );
    $response = $rest->doRequest('ping', $postData);

    echo 'PING OK, received data: ';
    print_r($response);
    echo PHP_EOL;
} catch (Exception $e) {
    echo 'Error message: '.$e->getMessage().', Error code: '.$e->getCode().', HTTP code: '.$rest->getHttpCode().PHP_EOL;
}
