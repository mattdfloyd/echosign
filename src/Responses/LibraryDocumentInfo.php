<?php
namespace Echosign\Responses;

use Echosign\Interfaces\ApiResponse;
use Echosign\Util;

/**
 * Class LibraryDocumentInfo
 * @package Echosign\Responses
 */
class LibraryDocumentInfo implements ApiResponse
{

    protected $message;
    protected $securityOptions = [ ];
    protected $status;
    protected $events = [ ];
    protected $name;
    protected $locale;
    protected $libraryDocumentId;
    protected $participants = [ ];
    protected $latestVersionId;

    /**
     * @var array
     */
    protected $response;

    public function __construct( array $response )
    {
        $this->response = $response;

        foreach ([
                     'message',
                     'securityOptions',
                     'status',
                     'expiration',
                     'events',
                     'name',
                     'locale',
                     'nextParticipantInfos',
                     'agreementId',
                     'participants',
                     'latestVersionId',
                     'libraryDocumentId'
                 ] as $k) {
            $this->$k = Util::array_get( $response, $k );
        }
    }

    /**
     * @return mixed
     */
    public function getLibraryDocumentId()
    {
        return $this->libraryDocumentId;
    }

    /**
     * @return array
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * @return mixed
     */
    public function getLatestVersionId()
    {
        return $this->latestVersionId;
    }

    /**
     * @return mixed
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getParticipants()
    {
        return $this->participants;
    }

    /**
     * @return array
     */
    public function getSecurityOptions()
    {
        return $this->securityOptions;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return array
     */
    public function getResponse()
    {
        return $this->response;
    }

}