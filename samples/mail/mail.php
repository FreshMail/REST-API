<?php

require '../../class.rest.php';
require '../../config.php';

$rest = new FmRestAPI();
$rest->setApiKey( FM_API_KEY );
$rest->setApiSecret( FM_API_SECRET );

$data = array(
    'subscriber' => 'put email here',
    'subject'    => 'It\'s only example',
    'html'       => 'Some sample <strong>HTML</strong> message',
    'text'       => 'Some sample text message',
    'from'       => 'example@example.com',
    'from_name'  => 'I\'m only example',
    //'tracking'   => 1,
);


//testing transactional mail request
try {
    $response = $rest->doRequest('mail', $data);

    echo 'Mail sended correctly, received data: ';
    print_r($response);
    echo PHP_EOL;
} catch (Exception $e) {
    echo 'Error while sending mail: ';
    print_r($rest->getResponse());
}
