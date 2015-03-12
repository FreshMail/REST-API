<?php

/**
 *  Klasa do uwierzytelniania i wysyłania danych za pomocą REST API FreshMail
 *
 *  @author Tadeusz Kania, Piotr Suszalski
 *  @since  2012-06-14
 *
 */

class FmRestApi
{

    private $strApiSecret   = null;
    private $strApiKey      = null;
    private $response    = null;
    private $rawResponse = null;
    private $httpCode    = null;
    private $contentType = 'application/json';

    const host   = 'https://api.freshmail.com/';
    const prefix = 'rest/';
    //--------------------------------------------------------------------------

    /**
     * Metoda pobiera kody błędów
     *
     * @return array
     */
    public function getErrors()
    {
        if ( isset( $this->errors['errors'] ) ) {
            return $this->errors['errors'];
        }

        return false;
    }

     /**
     * @return array
     */
    public function getResponse()
    {
        return $this->response;
    }

     /**
     * @return array
     */
    public function getRawResponse()
    {
        return $this->rawResponse;
    }

     /**
     * @return array
     */
    public function getHttpCode()
    {
        return $this->httpCode;
    }

    /**
     * Metoda ustawia secret do API
     *
     * @param type $strSectret
     * @return rest_api
     */
    public function setApiSecret( $strSectret = '' )
    {
        $this->strApiSecret = $strSectret;
        return $this;
    } // setApiSecret

    public function setContentType( $contentType = '' )
    {
        $this->contentType = $contentType;
        return $this;
    }

    /**
     * Metoda ustawia klucz do API
     *
     * @param string $strKey
     * @return rest_api
     */
    public function setApiKey ( $strKey = '' )
    {
        $this->strApiKey = $strKey;
        return $this;
    } // setApiKey

    public function doRequest( $strUrl, $arrParams = array(), $boolRawResponse = false )
    {
        if ( empty($arrParams) ) {
            $strPostData = '';
        } elseif ( $this->contentType == 'application/json' ) {
            $strPostData = json_encode( $arrParams );
        } elseif ( !empty($arrParams) ) {
            $strPostData = http_build_query( $arrParams );
        }

        $strSign = sha1( $this->strApiKey . '/' . self::prefix . $strUrl . $strPostData . $this->strApiSecret );

        $arrHeaders = array();
        $arrHeaders[] = 'X-Rest-ApiKey: ' . $this->strApiKey;
        $arrHeaders[] = 'X-Rest-ApiSign: ' . $strSign;

        if ($this->contentType) {
            $arrHeaders[] = 'Content-Type: '.$this->contentType;
        }

        $resCurl = curl_init( self::host . self::prefix . $strUrl );
        curl_setopt( $resCurl, CURLOPT_HTTPHEADER, $arrHeaders );
        curl_setopt( $resCurl, CURLOPT_HEADER, false );
        curl_setopt( $resCurl, CURLOPT_RETURNTRANSFER, true);

        if ($strPostData) {
            curl_setopt( $resCurl, CURLOPT_POST, true);
            curl_setopt( $resCurl, CURLOPT_POSTFIELDS, $strPostData );
        } // endif

        $this->rawResponse = curl_exec( $resCurl );
        $this->httpCode = curl_getinfo( $resCurl, CURLINFO_HTTP_CODE );

        if ($boolRawResponse) {
            return $this->rawResponse;
        } // endif

        $this->response = json_decode( $this->rawResponse, true );
        if ($this->httpCode != 200) {
            $this->errors = $this->response['errors'];
            if (is_array($this->errors)) {
                foreach ($this->errors as $arrError) {
                    throw new RestException($arrError['message'], $arrError['code']);
                } // endforeach
            } // endif
        } // endif

        if (is_array($this->response) == false) {
            throw new Exception('Connection error - curl error message: '.curl_error($resCurl).' ('.curl_errno($resCurl).')');
        } // endif

        return $this->response;
    } // doRequest

}

class RestException extends Exception
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
