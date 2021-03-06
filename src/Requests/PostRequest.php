<?php
namespace Echosign\Requests;

use Echosign\Abstracts\HttpRequest;

/**
 * Class PostRequest
 * @package Echosign\Requests
 */
class PostRequest extends HttpRequest
{
    const REQUEST_METHOD = 'POST';

    /**
     * @return string
     */
    public function getRequestMethod()
    {
        return self::REQUEST_METHOD;
    }

}