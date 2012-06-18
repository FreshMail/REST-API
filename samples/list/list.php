<?php

require '../../class.rest.php';
require '../../config.php';

$rest = new FmRestAPI();
$rest->setApiKey( FM_API_KEY );
$rest->setApiSecret( FM_API_SECRET );

$data = array(
    'name'          => 'Example list name',
    'description'   => 'Not required',
    'custom_fields' => array(
        array(
            'name' => 'custom_field_1'
        )
    )
);


//testing transactional mail request
try {
    $response = $rest->doRequest('subscribers_list/create', $data);

    echo 'List created, received data: ';
    var_dump($response);
    echo PHP_EOL;
} catch (Exception $e) {
    echo 'Error message: '.$e->getMessage().', Error code: '.$e->getCode().', HTTP code: '.$rest->getHttpCode().PHP_EOL;
}
