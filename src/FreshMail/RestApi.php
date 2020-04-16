<?php

namespace FreshMail;

use Exception;

/**
 *  Klasa do uwierzytelniania i wysyłania danych za pomocą REST API FreshMail
 *
 *  @author Tadeusz Kania, Piotr Suszalski, Grzegorz Gorczyca, Piotr Leżoń
 *  @since  2012-06-14
 */
class RestApi
{
    const HOST   = 'https://api.freshmail.com/';
    const PREFIX = 'rest/';

    const DEFAULT_FILE_PATH = '/tmp/';

    /**
     * @var string
     */
    private $apiSecret;

    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var string
     */
    private $token;

    /**
     * @var array
     */
    private $response     = null;

    /**
     * @var string
     */
    private $rawResponse  = null;

    /**
     * @var int
     */
    private $httpCode;

    /**
     * @var string
     */
    private $contentType = 'application/json';

    /**
     * @var array
     */
    private $errors = array();

    /**
     * Get errors.
     *
     * @return array|null
     */
    public function getErrors()
    {
        if (isset($this->errors['errors'])) {
            return $this->errors['errors'];
        }

        return null;
    }

    /**
     * Get response.
     *
     * @return array
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Get raw response.
     *
     * @return string
     */
    public function getRawResponse()
    {
        return $this->rawResponse;
    }

    /**
     * Get HTTP code.
     *
     * @return int
     */
    public function getHttpCode()
    {
        return $this->httpCode;
    }

    /**
     * Set API key.
     *
     * @param string $apiKey
     *
     * @return self
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
        $this->token = '';
        return $this;
    }

    /**
     * Set API secret key.
     *
     * @param string $apiSecret
     *
     * @return self
     */
    public function setApiSecret($apiSecret)
    {
        $this->apiSecret = $apiSecret;
        $this->token = '';
        return $this;
    }

    /**
     * @param $token
     * @return $this
     */
    public function setToken($token)
    {
        $this->token = $token;
        $this->apiKey = '';
        $this->apiSecret = '';
        return $this;
    }

    /**
     * Set content type.
     *
     * @param string $contentType
     *
     * @return self
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;

        return $this;
    }

    /**
     * Fire request
     *
     * @param string $strUrl - URL path
     * @param array $arrParams - Data
     * @param bool $returnRawResponse - Return raw response
     *
     * @return array|string - Response
     *
     * @throws \FreshMail\RestException
     * @throws \Exception
     */
    public function doRequest($strUrl, $arrParams = array(), $returnRawResponse = false)
    {
        if (empty($arrParams)) {
            $strPostData = '';
        } elseif ($this->contentType == 'application/json') {
            $strPostData = json_encode($arrParams);
        } elseif (!empty($arrParams)) {
            $strPostData = http_build_query($arrParams);
        }

        $headers = array();
        if ($this->token) {
            $headers[] = sprintf('Authorization: Bearer %s', $this->token);
        } elseif ($this->apiKey) {
            $apiSignature = sha1($this->apiKey . '/' . self::PREFIX . $strUrl . $strPostData . $this->apiSecret);
            $headers[] = sprintf('X-Rest-ApiKey: %s', $this->apiKey);
            $headers[] = sprintf('X-Rest-ApiSign: %s', $apiSignature);
        }

        if (!empty($this->contentType)) {
            $headers[] = 'Content-Type: ' . $this->contentType;
        }

        $cUrl = curl_init(self::HOST . self::PREFIX . $strUrl);

        curl_setopt($cUrl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($cUrl, CURLOPT_HEADER, true);
        curl_setopt($cUrl, CURLOPT_RETURNTRANSFER, true);

        if ($strPostData) {
            curl_setopt($cUrl, CURLOPT_POST, true);
            curl_setopt($cUrl, CURLOPT_POSTFIELDS, $strPostData);
        }

        $this->rawResponse = curl_exec($cUrl);
        $this->httpCode    = curl_getinfo($cUrl, CURLINFO_HTTP_CODE);

        if ($returnRawResponse) {
            return $this->rawResponse;
        }

        $this->getResponseFromHeaders($cUrl);

        if ($this->httpCode != 200) {
            $this->errors = $this->response['errors'];

            if (is_array($this->errors)) {
                foreach ($this->errors as $error) {
                    throw new RestException($error['message'], $error['code']);
                }
            }
        }

        if (is_array($this->response) == false) {
            throw new Exception('Invalid json response');
        }

        return $this->response;
    }

    /**
     * Parse Curl response
     *
     * @param resource $cUrl
     *
     * @return void
     */
    private function getResponseFromHeaders($cUrl)
    {
        $headerSize = curl_getinfo($cUrl, CURLINFO_HEADER_SIZE);
        $headers    = substr($this->rawResponse, 0, $headerSize);

        preg_match('/Content-Type:\s*([a-z-Z\/]*)\s/', $headers, $responseType);

        if (isset($responseType[1]) && strtolower($responseType[1]) == 'application/zip') {
            preg_match('/filename\=\"([a-zA-Z0-9\.]+)\"/', $headers, $fileName);
            file_put_contents(self::DEFAULT_FILE_PATH . $fileName[1], substr($this->rawResponse, $headerSize));

            $this->response = array(
                'path' => self::DEFAULT_FILE_PATH . $fileName[1]
            );
        } else {
            $this->response = json_decode(substr($this->rawResponse, $headerSize), true);
        }
    }
}
