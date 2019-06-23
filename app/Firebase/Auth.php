<?php
namespace CodeShopping\Firebase;
use Kreait\Firebase;
use Kreait\Firebase\Auth\UserRecord;
class Auth
{
    /**
     * @var Firebase
     */
    private $firebase;
    /**
     * Auth constructor.
     */
    public function __construct(Firebase $firebase)
    {
        $this->firebase = $firebase;
    }
    public function user($token): UserRecord
    {
        $verifiedIdToken = $this->firebase->getAuth()->verifyIdToken($token);
        $uid = $verifiedIdToken->getClaim('sub');
        return $this->firebase->getAuth()->getUser($uid);
    }
    public function phoneNumber($token): string
    {
        $user = $this->user($token);
        return $user->phoneNumber;
    }
}
