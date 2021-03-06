<?php
namespace Echosign\Creators;

use Echosign\Agreements;
use Echosign\Exceptions\JsonApiResponseException;
use Echosign\RequestBuilders\Agreement\DocumentCreationInfo;
use Echosign\RequestBuilders\Agreement\FileInfo;
use Echosign\RequestBuilders\Agreement\InteractiveOptions;
use Echosign\RequestBuilders\AgreementCreationInfo;

class Agreement extends CreatorBase
{
    /**
     * @var Agreements
     */
    protected $agreement;

    /**
     * @var DocumentCreationInfo
     */
    protected $documentCreationInfo;

    /**
     * @var string
     */
    protected $signatureType = 'ESIGN';

    /**
     * @var string
     */
    protected $signatureFlow = 'SENDER_SIGNATURE_NOT_REQUIRED';

    /**
     * @var array
     */
    protected $mergefieldInfos = [];


    /**
     * @param $token
     * @param HttpTransport $transport
     * @param array $transportOptions
     */
    public function __construct( $token, HttpTransport $transport = null, array $transportOptions = [] )
    {
        parent::__construct($token, $transport, $transportOptions);

        $this->agreement = new Agreements( $this->getToken(), $this->getTransport() );
    }

    /**
     * Create an agreement from a libraryDocumentId for specified signerEmail with message. You can use
     * $this->getResponse() to get created agreement. If successful it returns the agreementId.
     * @param $signerEmail
     * @param $message
     * @param $libraryDocumentId
     * @param $agreementName
     * @return bool|string
     * @internal param $signatureType
     * @internal param $signatureFlow
     */
    public function createFromLibraryDocumentId( $signerEmail, $message, $libraryDocumentId, $agreementName )
    {
        $fileInfo                    = new FileInfo();
        $fileInfo->libraryDocumentId = $libraryDocumentId;

        return $this->send($fileInfo, $agreementName, $message, $signerEmail);
    }

    /**
     * Create an agreement from a libraryDocumentName for specified signerEmail with message. You can use
     * $this->getResponse() to get created agreement. If successful it returns the agreementId.
     * @param $signerEmail
     * @param $message
     * @param $libraryDocumentName
     * @param $agreementName
     * @return bool|string
     * @internal param $signatureType
     * @internal param $signatureFlow
     */
    public function createFromLibraryDocumentName( $signerEmail, $message, $libraryDocumentName, $agreementName )
    {
        $fileInfo                      = new FileInfo();
        $fileInfo->libraryDocumentName = $libraryDocumentName;

        return $this->send($fileInfo, $agreementName, $message, $signerEmail);
    }

    /**
     * Create an agreement from a file hosted on a remote server, for specified signerEmail with message. You can use
     * $this->getResponse() to get created agreement. If successful it returns the agreementId.
     * @param $signerEmail
     * @param $message
     * @param $fileName
     * @param $url
     * @param $agreementName
     * @return bool|string
     * @internal param $signatureType
     * @internal param $signatureFlow
     */
    public function createFromUrl( $signerEmail, $message, $fileName, $url, $agreementName )
    {
        $fileInfo = new FileInfo();
        $fileInfo->setDocumentURL( $fileName, $url );

        return $this->send($fileInfo, $agreementName, $message, $signerEmail);
    }

    /**
     * Create an agreement from a transientDocumentId, for specified signerEmail with message. You can use
     * $this->getResponse() to get created agreement. If successful it returns the agreementId.
     * @param $signerEmail
     * @param $message
     * @param $transientDocumentId
     * @param $agreementName
     * @return bool|string
     * @internal param $signatureType
     * @internal param $signatureFlow
     */
    public function createFromTransientDocumentId( $signerEmail, $message, $transientDocumentId, $agreementName )
    {
        $fileInfo                      = new FileInfo();
        $fileInfo->transientDocumentId = $transientDocumentId;

        return $this->send($fileInfo, $agreementName, $message, $signerEmail);
    }

    /**
     * Create an agreement from a local file, for specified signerEmail with message. It first creates a transient
     * document, then creates the agreement. You can use $this->getResponse() to get created agreement. If successful
     * it returns the agreementId.
     * @param $signerEmail
     * @param $message
     * @param $filename
     * @param $agreementName
     * @return bool|string
     * @internal param $signatureType
     * @internal param $signatureFlow
     */
    public function createTransientAgreement( $signerEmail, $message, $filename, $agreementName )
    {
        $transientDocument = new TransientDocument( $this->getToken(), $this->getTransport() );

        $transientDocumentId = $transientDocument->create( $filename );

        if ($transientDocumentId === false) {
            $this->response      = $transientDocument->getResponse();
            $this->errorMessages = $transientDocument->getErrorMessages();
            return false;
        }

        return $this->createFromTransientDocumentId( $signerEmail, $message, $transientDocumentId, $agreementName );
    }

    /**
     * Create an agreement from a local file, for specified signerEmail with message. It first creates a library
     * document, then creates the agreement. You can use $this->getResponse() to get created agreement. If successful
     * it returns the agreementId.
     * @param $signerEmail
     * @param $message
     * @param $filename
     * @param $agreementName
     * @return bool|string
     */
    public function createLibraryDocumentAgreement( $signerEmail, $message, $filename, $agreementName )
    {
        $libraryDocument   = new LibraryDocument( $this->getToken(), $this->getTransport() );
        $libraryDocumentId = $libraryDocument->createFromLocalFile( $filename, basename( $filename ), 'DOCUMENT' );

        if ($libraryDocumentId === false) {
            $this->response      = $libraryDocument->getResponse();
            $this->errorMessages = $libraryDocument->getErrorMessages();
            return false;
        }

        return $this->createFromLibraryDocumentId( $signerEmail, $message, $libraryDocumentId, $agreementName );
    }

    /**
     * @return Agreements
     */
    public function getAgreement()
    {
        return $this->agreement;
    }

    /**
     * @return string
     */
    public function getSignatureType()
    {
        return $this->signatureType;
    }

    /**
     * @param string $signatureType
     * @return $this
     */
    public function setSignatureType( $signatureType )
    {
        $this->signatureType = $signatureType;
        return $this;
    }

    /**
     * @return string
     */
    public function getSignatureFlow()
    {
        return $this->signatureFlow;
    }

    /**
     * @param string $signatureFlow
     * @return $this
     */
    public function setSignatureFlow( $signatureFlow )
    {
        $this->signatureFlow = $signatureFlow;
        return $this;
    }

    /**
     * @return DocumentCreationInfo
     */
    public function getDocumentCreationInfo($fileInfo, $agreementName, $message, $signerEmail)
    {
        if (is_null($this->documentCreationInfo)) {
            $this->documentCreationInfo = new DocumentCreationInfo($fileInfo, $agreementName, $this->getSignatureType(), $this->getSignatureFlow());
        }

        $this->documentCreationInfo->setMessage( $message )
                             ->addRecipient( 'SIGNER', $signerEmail );

        foreach ($this->mergefieldInfos as $mergefieldInfo) {
            $this->documentCreationInfo->addMergeFieldInfo($mergefieldInfo);
        }

        return $this->documentCreationInfo;
    }

    /**
     * @param DocumentCreationInfo $documentCreationInfo
     * @return $this
     */
    public function setDocumentCreationInfo(DocumentCreationInfo $documentCreationInfo)
    {
        $this->documentCreationInfo = $documentCreationInfo;

        return $this;
    }

    public function setMergeFieldInfos($mergefieldInfos)
    {
        foreach ((array) $mergefieldInfos as $mergefieldInfo) {
            $this->mergefieldInfos[] = $mergefieldInfo;
        }

        return $this;
    }

    public function send($fileInfo, $agreementName, $message, $signerEmail)
    {
        $docCreationInfo = $this->getDocumentCreationInfo($fileInfo, $agreementName, $message, $signerEmail);

        $agreementCreationInfo = new AgreementCreationInfo( $docCreationInfo, new InteractiveOptions() );

        try {
            $this->response = $this->agreement->create( $agreementCreationInfo );
        } catch ( JsonApiResponseException $e ) {
            $this->errorMessages[ $e->getCode() ] = sprintf( '%s - %s', $e->getApiCode(), $e->getMessage() );
            return false;
        } catch ( \Exception $e ) {
            $this->errorMessages[ $e->getCode() ] = $e->getMessage();
            return false;
        }

        return $this->response->getAgreementId();
    }

}