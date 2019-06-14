<?php

use Illuminate\Database\Seeder;
use CodeShopping\Models\ChatGroup;

class ChatMessagesLargeFbSeeder extends ChatMessagesFbSeeder
{
    protected $numMessages = 100;

    protected function getChatGroups()
    {
        return ChatGroup::whereId(1)->get();
    }
}
