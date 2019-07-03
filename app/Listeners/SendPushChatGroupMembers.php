<?php
declare(strict_types=1);

namespace CodeShopping\Listeners;

use CodeShopping\Models\User;
use CodeShopping\Models\UserProfile;
use CodeShopping\Events\ChatMessageSent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use CodeShopping\Models\ChatGroup;
use CodeShopping\Firebase\CloudMessagingFb;
use CodeShopping\Firebase\NotificationType;

class SendPushChatGroupMembers
{

    private $event;

    public function __construct()
    {
        //
    }


    public function handle(ChatMessageSent $event)
    // public function handle()
    {

        $this->event = $event;

        $tokens = $this->getTokens();
        if(!count($tokens)){
            return;
        }

        $from = $this->event->getFrom();
        $chatGroup = $this->event->getChatGroup();
        $messaging = app(CloudMessagingFb::class);
        $messaging->setTitle("{$from->name} enviou uma mensagem em {$chatGroup->name}")
                  ->setBody($this->getBody())
                  ->setTokens($tokens)
                  ->setData([
                      'type' => NotificationType::NEW_MESSAGE,
                      'chat_group_id' => $chatGroup->id
                  ])
                  ->send();

    }

    private function getTokens() : array
    {
        $membersTokens = $this->getMembersTokens();
        // dd($membersTokens);
        $sellersTokens = $this->getSellersTokens();


        return array_merge($membersTokens,$sellersTokens);
    }

    private function getMembersTokens() : array
    {
        $chatGroups = $this->event->getChatGroup();
        $from = $this->event->getFrom();

        $users = $chatGroups->users()->whereHas('profile', function($query) use($from){
            $query->whereNotNull('device_token')->whereNotIn('id',[$from->profile->id]);
        })->get();

        $membersTokensCollection = $users->map(function($user){
            return $user->profile->device_token;
        });

        return $membersTokensCollection->toArray();
    }

    private function getSellersTokens() : array
    {
        $from = $this->event->getFrom();
        $sellersTokensCollection = UserProfile::whereNotNull('device_token')
        ->whereNotIn('id',[$from->profile->id])
        ->whereHas('user', function($query){
            $query->where('role',User::ROLE_SELLER);
        })
        ->get()
        ->pluck('device_token');

        return $sellersTokensCollection->toArray();

    }

    private function getBody()
    {
        switch($this->event->getMessageType()){
            case 'text':
                return substr($this->event->getContent(),0,20);
            case 'image':
                return 'Novo audio';
            case 'audio':
                return 'Novo audio';
        }
    }

}
