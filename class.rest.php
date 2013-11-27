<?php

require_once 'src/FreshMail/RestApi.php';
require_once 'src/FreshMail/RestException.php';

class FmRestApi extends \FreshMail\RestApi
{
}

/* USAGE: *****

$rest = new FmRestApi();
$rest->setApiSecret(API_SECRET);
$rest->setApiKey(API_KEY);

//ping GET (do testowania autoryzacji)
try {
    $response = $rest->doRequest('ping');
    print_r($response);
} catch (Exception $e) {
    //echo 'Code: '.$e->getCode().' Message: '.$e->getMessage()."\n";
    print_r($rest->getResponse());
}

//ping POST (do testowania autoryzacji)
try {
    $postdata = array('any required data');
    $response = $rest->doRequest('ping', $postdata);
    print_r($response);
} catch (Exception $e) {
    //echo 'Code: '.$e->getCode().' Message: '.$e->getMessage()."\n";
    print_r($rest->getResponse());
}

//mail POST
try {
    $data = array('subscriber' => 'put email address here',
                  'subject' => 'put subject',
                  'text' => 'put text message',
                  'html' => '<strong>put HTML message here</strong>');
    $response = $rest->doRequest('mail', $data);
    print_r($response);
} catch (Exception $e) {
    //echo 'Code: '.$e->getCode().' Message: '.$e->getMessage()."\n";
    print_r($rest->getResponse());
}

*/
