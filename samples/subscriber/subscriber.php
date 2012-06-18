<?php

require '../../class.rest.php';
require '../../config.php';

$rest = new FmRestAPI();
$rest->setApiKey( FM_API_KEY );
$rest->setApiSecret( FM_API_SECRET );

$data = array(
    'email' => 'put email here',
    'list'  => 'put subscriber list hash',
    'custom_fields' => array(
        'personalization_tag_1' => 'value 1',
        'personalization_tag_2' => 'value 2',
    ),
    //'state'   => 2
    //'confirm' => 1
);


//testing transactional mail request
try {
    $response = $rest->doRequest('subscriber/add', $data);

    echo 'Subscriber added correctly, received data: ';
    print_r($response);
    echo PHP_EOL;
} catch (Exception $e) {
    echo 'Error message: '.$e->getMessage().', Error code: '.$e->getCode().', HTTP code: '.$rest->getHttpCode().PHP_EOL;
}
