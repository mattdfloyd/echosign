<?php
namespace Echosign\RequestBuilders\Agreement;

use Echosign\Interfaces\RequestBuilder;

/**
 * Class RecipientSetInfo
 * @package Echosign\RequestBuilders\Agreement
 */
class RecipientSetInfo implements RequestBuilder
{

    /**
     * @var array
     */
    public $recipientSetMemberInfos;

    /**
     * @var array
     */
    public $recipientSetRole;

    /**
     * You must specify email OR fax, but not both.
     * @param string $role
     * @param string $email
     * @param string $fax
     * @throws \RuntimeException
     */
    public function __construct( $role, $email = null, $fax = null )
    {
        if (!in_array( $role, [ 'SIGNER', 'APPROVER' ] )) {
            throw new \InvalidArgumentException('Invalid role given');
        }

        $this->recipientSetRole[] = $role;

        if( $email && $fax ) {
            throw new \RuntimeException("You must specify email OR fax, but NOT BOTH");
        }

        $this->recipientSetMemberInfos[] = ['email' => filter_var( $email, FILTER_SANITIZE_EMAIL )];
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return array_filter( [
            'recipientSetMemberInfos'   => $this->recipientSetMemberInfos,
            'recipientSetRole' => $this->recipientSetRole
        ] );
    }

}