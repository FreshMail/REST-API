# FreshMail

A php library which implements connection to FreshMail REST API.

This API client covers all functions of API V2 such as:
 - subscribers management
 - list management
 - campaign management
 - sending transactional SMS messages

If You want to send transactional messages in rich format please use new [API V3 client](https://github.com/FreshMail/php-api-client).

## Installation via composer (compatible with PHP >=7.0)

Add via composer:
    
    composer require freshmail/rest-api:^3.0

## Installation of old version of library (compatible with PHP >=5.3)

Add via composer:
    
    composer require freshmail/rest-api:^2.0

## Usage

Below some simple examples, for whole API function see [full API V2 doc](https://freshmail.pl/developer-api/jak-zaczac/)

#### Test connection
    
    use \FreshMail\ApiV2\Client;
    
    $token = 'MY_APP_TOKEN';
    $apiClient = new Client($token);
    
    $apiClient->doRequest('ping');
    
#### Create subscribers list

    use \FreshMail\ApiV2\Client;
    
    $token = 'MY_APP_TOKEN';
    $apiClient = new Client($token);
    
    $data = [
        'name' => 'List with subscribers from my website'
    ];
    
    $apiClient->doRequest('subscribers_list/create', $data);
    
#### Add subscriber to list

    use \FreshMail\ApiV2\Client;
        
    $token = 'MY_APP_TOKEN';
    $apiClient = new Client($token);
    
    $data = [
        'email' => 'example@email.address',
        'list' => 'list_hash'
    ];
    
    $apiClient->doRequest('subscriber/add', $data);

#### Get file from async api

    use \FreshMail\ApiV2\Client;

    $token = 'MY_APP_TOKEN';
    $apiClient = new Client($token);

    $data = [
        'id_job' => 'XXX'
    ];

    $zipContent = $apiClient->doFileRequest('async_result/getFile', $data);

    file_put_contents('/testLocation/testfile.zip', $zipContent);

## Proxy setup

To use proxy You can pass Your own GuzzleHttp Client:

    use \FreshMail\ApiV2\Client;
    
    $guzzleClient = new \GuzzleHttp\Client(
        [
            'proxy' => 'my proxy url'
        ]
    );
        
    $token = 'MY_APP_TOKEN';
    $apiClient = new Client($token);
    $apiClient->setGuzzleHttpClient($guzzleClient);

## Debugging

#### PSR-3 Logger Interface

You can use any library that implements [PSR-3](https://www.php-fig.org/psr/psr-3/) `Psr\Log\LoggerInterface`, example with Monolog below:

    use \FreshMail\ApiV2\Client;  
    
    $logger = new \Monolog\Logger('myCustomLogger');
    $logger->pushHandler(new \Monolog\Handler\StreamHandler('php://stderr', \Monolog\Logger::DEBUG));
        
    $token = 'MY_APP_TOKEN';
    $apiClient = new Client($token);
    $apiClient->setLogger($monolog);

#### Using Guzzle

You can also pass Your own GuzzleHttp Client with proper configuration:


    use \FreshMail\ApiV2\Client;
    
    $stack = \GuzzleHttp\HandlerStack::create();
    $stack->push(
        \GuzzleHttp\Middleware::log(
            new \Monolog\Logger('Logger'),
            new \GuzzleHttp\MessageFormatter(\GuzzleHttp\MessageFormatter::DEBUG)
        )
    );
    
    $guzzleClient = new \GuzzleHttp\Client(
        [
            'handler' => $stack,
        ]
    );
        
    $token = 'MY_APP_TOKEN';
    $apiClient = new Client($token);
    $apiClient->setGuzzleHttpClient($guzzleClient);