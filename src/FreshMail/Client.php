<?php declare(strict_types=1);

namespace FreshMail\ApiV2;

use Exception;
use FreshMail\ApiV2\Factory\MonologFactory;
use GuzzleHttp\RequestOptions;
use Psr\Log\LoggerInterface;

/**
 *  Class to make proper request (with authorization) to FreshMail Rest API V2
 *
 *  @author Tadeusz Kania, Piotr Suszalski, Grzegorz Gorczyca, Piotr Leżoń
 *  @since  2012-06-14
 */
class Client
{
    const HOST   = 'api.freshmail.com';
    const SCHEME = 'https';
    const PREFIX = 'rest';
    const VERSION = 'v2';

    const CLIENT_VERSION = '3.0';

    /**
     * Bearer Token for authorization
     * @var string
     */
    private $bearerToken;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var \GuzzleHttp\Client
     */
    private $guzzle;

    /**
     * Client constructor.
     * @param string $bearerToken
     */
    public function __construct($bearerToken = '')
    {
        $this->bearerToken = $bearerToken;
        $this->logger = MonologFactory::createInstance();
        $this->guzzle = new \GuzzleHttp\Client();
    }

    /**
     * @param $uri
     * @param array $params
     * @return array
     * @throws Exception
     */
    public function doRequest(string $uri, array $params = [])
    {
        try {
            $method = ($params) ? 'POST' : 'GET';

            $response = $this->guzzle->request($method, $uri, $this->getRequestOptions($params));
            $rawResponse = $response->getBody()->getContents();
            $jsonResponse = json_decode($rawResponse, true);

            if (!$jsonResponse) {
                throw new ServerException(sprintf('Unable to parse response from server, raw response: %s', $rawResponse));
            }

            return $jsonResponse;
        } catch (\GuzzleHttp\Exception\ClientException $exception) {
            if ($exception->getCode() == 401) {
                throw new UnauthorizedException('Request unauthorized');
            }

            throw new \FreshMail\ApiV2\ClientException(sprintf(
                'Connection error, error message: %s',
                $exception->getResponse()->getBody()->getContents()
            ));
        } catch (\GuzzleHttp\Exception\ConnectException $exception) {
            throw new ConnectionException(sprintf('Connection error, error message: '.$exception->getMessage()));
        }
    }

    /**
     * @param $uri
     * @param array $params
     * @return string
     * @throws Exception
     */
    public function doFileRequest(string $uri, array $params = [])
    {
        try {
            $method = ($params) ? 'POST' : 'GET';

            $response = $this->guzzle->request($method, $uri, $this->getRequestOptions($params));
            if ($response->getHeaderLine('Content-Type') !== 'application/zip') {
                throw new ServerException(sprintf('Response content type is not supported: %s', $response->getHeaderLine('Content-Type')));
            }
            return  $response->getBody()->getContents();
        } catch (\GuzzleHttp\Exception\ClientException $exception) {
            if ($exception->getCode() == 401) {
                throw new UnauthorizedException('Request unauthorized', 401);
            }

            throw new \FreshMail\ApiV2\ClientException($exception->getMessage(), $exception->getResponse()->getStatusCode());
        } catch (\GuzzleHttp\Exception\ConnectException $exception) {
            throw new ConnectionException($exception->getMessage());
        }
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param \GuzzleHttp\Client $guzzle
     */
    public function setGuzzleHttpClient(\GuzzleHttp\Client $guzzle)
    {
        $this->guzzle = $guzzle;
    }

    /**
     * @return array
     */
    private function getRequestOptions(array $requestData): array
    {
        return [
            'base_uri' => sprintf('%s://%s/%s/', self::SCHEME, self::HOST, self::PREFIX),
            RequestOptions::BODY => json_encode($requestData),
            RequestOptions::HEADERS => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->bearerToken,
                'User-Agent' => $this->createUserAgent()
            ]
        ];
    }

    /**
     * @return string
     */
    private function createUserAgent(): string
    {
        return
            sprintf(
                'freshmail/php-api-v2-client:%s;guzzle:%s;php:%s;interface:%s',
                self::VERSION,
                self::CLIENT_VERSION,
                PHP_VERSION,
                php_sapi_name()
            );
    }
}
