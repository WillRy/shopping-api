<?php
declare (strict_types = 1);

namespace CodeShopping\Firebase;

use Kreait\Firebase;
use Illuminate\Http\UploadedFile;
use CodeShopping\Models\ChatGroup;
use CodeShopping\Events\ChatMessageSent;

class ChatMessageFb
{

    use FirebaseSync;

    private $chatGroup;

    public function create(array $data)
    {
        $this->chatGroup = $data['chat_group'];
        $type = $data['type'];

        switch ($type) {
            case 'audio':
                $this->upload($data['content']);
                $uploadedFile = $data['content'];
                $fileUrl = $this->groupFilesDir() . '/' . $this->buildFileName($uploadedFile);
                $data['content'] = $fileUrl;
                break;
            case 'image':
                $this->upload($data['content']);
                $uploadedFile = $data['content'];
                $fileUrl = $this->groupFilesDir() . '/' . $uploadedFile->hashName();
                $data['content'] = $fileUrl;
                break;
        }
        $reference = $this->getMessagesReference();
        $newReference = $reference->push([
            'type' => $data['type'],
            'content' => $data['content'],
            'created_at' => ['.sv' => 'timestamp'],
            'user_id' => $data['user']->profile->firebase_uid
        ]);
        $this->setLastMessage($newReference->getKey());
        $this->chatGroup->updateInFb();

        if(!app()->runningInConsole() && !app()->runningUnitTests()){
            event(new ChatMessageSent($this->chatGroup, $data['type'], $data['content'], $data['user']));
        }
    }

    private function buildFileName(UploadedFile $file)
    {
        switch ($file->getMimeType()) {
            case 'audio/x-hx-aac-adts':
                return "{$file->hashName()}acc";
            default:
                return $file->hashName();
        }
    }

    private function upload(UploadedFile $file)
    {
        $file->storeAs($this->groupFilesDir(), $this->buildFileName($file), ['disk' => 'public']);
    }

    private function groupFilesDir()
    {
        return ChatGroup::DIR_CHAT_GROUPS . '/' . $this->chatGroup->id . '/messages_files';
    }

    public function deleteMessages(ChatGroup $chatGroup)
    {
        $this->chatGroup = $chatGroup;
        $this->getMessagesReference()->remove();
    }

    public function setLastMessage($messageUid)
    {
        $path = "{$this->getChatGroupsMessagesReference()}/last_message_id";
        $reference = $this->getFirebaseDatabase()->getReference($path);
        $reference->set($messageUid);
    }

    private function getMessagesReference()
    {
        $path = "{$this->getChatGroupsMessagesReference()}/messages";
        return $this->getFirebaseDatabase()->getReference($path);
    }

    public function getChatGroupsMessagesReference()
    {
        return "/chat_groups_messages/{$this->chatGroup->id}";
    }
}
