<?php
namespace Echosign\RequestBuilders\Agreement;

use Echosign\Interfaces\RequestBuilder;

/**
 * Class InteractiveOptions
 * @package Echosign\RequestBuilders\Agreement
 */
class InteractiveOptions implements RequestBuilder
{
    /**
     * @var bool
     */
    public $noChrome;

    /**
     * @var bool
     */
    public $authoringRequested;

    /**
     * @var bool
     */
    public $autoLoginUser;

    /**
     * @var bool
     */
    public $locale;

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'noChrome'           => (bool) $this->noChrome,
            'authoringRequested' => (bool) $this->authoringRequested,
            'autoLoginUser'      => (bool) $this->autoLoginUser,
            'locale'             => (bool) $this->locale,
        ];
    }

}