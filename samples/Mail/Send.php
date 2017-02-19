<?php

require '../../src/FreshMail/RestApi.php';
require '../../src/FreshMail/RestException.php';
require '../../config.php';

$rest = new \FreshMail\RestApi();

$rest->setApiKey(FM_API_KEY);
$rest->setApiSecret(FM_API_SECRET);

$data = [
    'subscriber' => 'john@doe.tld',
    'subject'    => 'Message subject',
    'html'       => '<h1>HTML message content</h1>',
    'text'       => 'Text message content',
    'from'       => 'jane@doe.tld',
    'from_name'  => 'Jane Doe'
];

try {
    $response = $rest->doRequest('mail', $data);

    echo 'Mail sent, received data: ';
    print_r($response);
    echo PHP_EOL;
} catch (Exception $e) {
    echo 'Error message: ' . $e->getMessage() . ', Error code: ' . $e->getCode() . ', HTTP code: ' . $rest->getHttpCode() . PHP_EOL;
}
