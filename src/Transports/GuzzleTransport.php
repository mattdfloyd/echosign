<?php
namespace Echosign\Transports;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Message\Response;
use Echosign\Abstracts\HttpRequest;
use Echosign\Interfaces\HttpTransport;
use GuzzleHttp\Exception\ClientException;
use Echosign\Exceptions\JsonApiResponseException;

/**
 * Class GuzzleTransport
 * @package Echosign\Transports
 */
class GuzzleTransport implements HttpTransport
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var ClientException
     */
    protected $httpException;

    /**
     * @param array $config
     */
    public function __construct( array $config = [ ] )
    {
        $this->client = new Client( $config );
    }

    /**
     * @param HttpRequest $httpRequest
     * @return array|mixed
     * @throws JsonApiResponseException
     * @throws \RuntimeException
     */
    public function handleRequest( HttpRequest $httpRequest )
    {
        if ($httpRequest->isJsonRequest()) {
            $requestBody = json_encode($httpRequest->getBody());
        } else {
            $requestBody = $httpRequest->getBody();
        }

        if ($httpRequest->saveResponseToFile()) {
            $requestBody['save_to'] = $httpRequest->getFileSavePath();
        }

        $url = $httpRequest->getRequestUrl();

        if (empty( $url )) {
            throw new \RuntimeException( 'request url is empty.' );
        }

        $request = new Request(
            $httpRequest->getRequestMethod(),
            $url,
            $httpRequest->getHeaders(),
            $requestBody
        );

        try {
            $response = $this->client->send( $request );
        } catch( ClientException $e ) {
            $this->httpException = $e;
            $response            = $e->getResponse();
        }
        return $this->handleResponse( $response );
    }

    /**
     * @param Response $response
     * @return array|mixed
     * @throws JsonApiResponseException
     */
    public function handleResponse( $response )
    {
        $contentType = $response->getHeader('content-type');
        // if its not json, then just return the response and handle it in your own object.
        if (stripos( $contentType[0], 'application/json' ) === false) {
            return $response;
        }

        $json = json_decode($response->getBody(), true);

        // adobe says hey this didn't work!
        if ($response->getStatusCode() >= 400) {
            // oops an error with the response, from Adobe complaining about something in your code.
            throw new JsonApiResponseException( $response->getStatusCode(), $json['message'], $json['code'] );
        }

        return $json;
    }

    /**
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @return ClientException
     */
    public function getHttpException()
    {
        return $this->httpException;
    }

}