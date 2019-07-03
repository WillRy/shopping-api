<?php

namespace CodeShopping\Exceptions;

class ChatInvitationUserException extends \Exception
{
    const ERROR_NOT_INVITATION = 1;
    const ERROR_HAS_SELLER = 2;
    const ERROR_IS_MEMBER = 3;
    const ERROR_HAS_STORED = 4;

}
