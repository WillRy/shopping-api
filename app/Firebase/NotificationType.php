<?php

namespace CodeShopping\Firebase;

interface NotificationType
{

    const CHAT_GROUP_SUBSCRIBE = "1";
    const NEW_MESSAGE = "2";
    const ORDER_DO_PAYMENT = "3";
    const ORDER_APPROVED = "4";
    const ORDER_SENT = "5";
}
