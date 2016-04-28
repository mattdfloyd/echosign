<?php
namespace Echosign;

use Echosign\Abstracts\Resource;
use Echosign\RequestBuilders\AgreementCreationInfo;
use Echosign\RequestBuilders\AgreementStatusUpdateInfo;
use Echosign\Requests\PutRequest;
use Echosign\Responses\AgreementCreationResponse;
use Echosign\Responses\AgreementDocuments;
use Echosign\Responses\AgreementInfo;
use Echosign\Responses\AgreementStatusUpdateResponse;
use Echosign\Responses\CombinedDocumentPagesInfo;
use Echosign\Responses\SigningUrls;
use Echosign\Responses\UserAgreements;

/**
 * Class Agreements
 * @package Echosign
 */
class Agreements extends Resource
{
    protected $baseApiPath = 'agreements';

    /**
     * Creates an agreement. Sends it out for signatures, and returns the agreementID in the response to the client
     * @param AgreementCreationInfo $agreementCreationInfo
     * @param string $userId
     * @param string $userEmail
     * @return AgreementCreationResponse
     */
    public function create( AgreementCreationInfo $agreementCreationInfo, $userId = null, $userEmail = null )
    {
        $response = $this->simplePostRequest( $agreementCreationInfo->toArray(), $userId, $userEmail );

        return new AgreementCreationResponse( $response );
    }

    /**
     * Retrieves agreements for the user
     * @param string $userId
     * @param string $userEmail
     * @param string $query
     * @return UserAgreements
     */
    public function listAll( $userId = null, $userEmail = null, $query = null )
    {
        $response = $this->simpleGetRequest( [ 'query' => $query ], $userId, $userEmail );

        return new UserAgreements( $response );
    }

    /**
     * Retrieves the latest status of an agreement
     * @param $agreementId
     * @return AgreementInfo
     */
    public function status( $agreementId )
    {
        $this->setApiRequestUrl( $agreementId );

        $response = $this->simpleGetRequest();

        return new AgreementInfo( $response );
    }

    /**
     * Retrieves the IDs of all the main and supporting documents of an agreement identified by agreementid
     * @param $agreementId
     * @return AgreementDocuments
     */
    public function documents( $agreementId )
    {
        $this->setApiRequestUrl( $agreementId . '/documents' );

        $response = $this->simpleGetRequest();

        return new AgreementDocuments( $response );
    }

    /**
     * Retrieves the stream of a document of an agreement, and saves to a local file
     * @param $agreementId
     * @param $documentId
     * @param $saveToPath
     * @return bool
     */
    public function downloadDocument( $agreementId, $documentId, $saveToPath )
    {
        $this->setApiRequestUrl( $agreementId . '/documents/' . $documentId );

        return $this->saveFileRequest( $saveToPath );
    }

    /**
     * Retrieves the audit trail of an agreement identified by agreementId, and saves to local file
     * @param $agreementId
     * @param $saveToPath
     * @return bool
     */
    public function auditTrail( $agreementId, $saveToPath )
    {
        $this->setApiRequestUrl( $agreementId . '/auditTrail' );

        return $this->saveFileRequest( $saveToPath );
    }

    /**
     * @param $agreementId
     * @return SigningUrls
     */
    public function signingUrls( $agreementId )
    {
        $this->setApiRequestUrl( $agreementId . '/signingUrls' );

        $response = $this->simpleGetRequest();

        return new SigningUrls( $response );
    }

    /**
     * Gets a single combined PDF document for the documents associated with an agreement, saved to a local file
     * @param $agreementId
     * @param $saveToPath
     * @return bool
     */
    public function combinedDocument( $agreementId )
    {
        $this->setApiRequestUrl( $agreementId . '/combinedDocument' );

        return $this->simpleGetRequest();
    }

    /**
     * Retrieves info of all pages of a combined PDF document for the documents associated with an agreement
     * @param $agreementId
     * @param $allPages
     * @return CombinedDocumentPagesInfo
     */
    public function pagesInfo( $agreementId, $allPages = false )
    {
        $this->setApiRequestUrl( $agreementId . '/combinedDocument/pagesInfo' );

        $response = $this->simpleGetRequest( [ 'includeSupportingDocumentsPagesInfo' => $allPages ] );

        return new CombinedDocumentPagesInfo( $response );
    }

    /**
     * Retrieves data entered by the user into interactive form fields at the time they signed the agreement, saves
     * to a local file
     * @param $agreementId
     * @param $saveToPath
     * @return bool
     */
    public function formData( $agreementId, $saveToPath )
    {
        $this->setApiRequestUrl( $agreementId . '/formData' );

        return $this->saveFileRequest( $saveToPath );
    }

    /**
     * Cancels an agreement
     * @param $agreementId
     * @param AgreementStatusUpdateInfo $agreementStatusUpdateInfo
     * @return AgreementStatusUpdateResponse
     */
    public function cancel( $agreementId, AgreementStatusUpdateInfo $agreementStatusUpdateInfo )
    {
        $this->setApiRequestUrl( $agreementId . '/status' );

        $request = new PutRequest( $this->getOAuthToken(), $this->getRequestUrl() );
        $request->setBody( $agreementStatusUpdateInfo->toArray() );

        $this->setRequest( $request );
        $this->logDebug( "Creating PUT request to " . $this->getRequestUrl() );

        $transport = $this->getTransport();
        $response  = $transport->handleRequest( $request );

        $this->logDebug( "response", $response );

        return new AgreementStatusUpdateResponse( $response );
    }
}