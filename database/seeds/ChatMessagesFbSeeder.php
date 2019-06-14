<?php
declare(strict_types=1);
use Illuminate\Database\Seeder;
use CodeShopping\Models\User;
use CodeShopping\Models\ChatGroup;
use Faker\Factory as FakerFactory;
use CodeShopping\Firebase\ChatMessageFb;
use Illuminate\Http\UploadedFile;

class ChatMessagesFbSeeder extends Seeder
{

    protected $numMessages = 10;
    private $allFakerFiles;
    private $fakerFilesPath = 'app/faker/chat_message_files';

    public function run()
    {
        $this->allFakerFiles = $this->getFakerFiles();

        $chatGroups = $this->getChatGroups();
        $users = User::all();
        $chatMessage = new ChatMessageFb();

        $self = $this;
        $chatGroups->each(function ($group) use ($users, $chatMessage, $self) {
            $chatMessage->deleteMessages($group);
            foreach (range(1, $self->numMessages) as $value) {
                $textOrFile = rand(1,10) % 2 === 0 ? 'text' : 'file';

                if($textOrFile === 'text'){
                    $content = FakerFactory::create()->sentence(10);
                    $type = 'text';
                }else{
                    $content = $self->getUploadedFile();
                    $type = $content->getExtension() === 'wav' ? 'audio' : 'image';
                }

                $chatMessage->create([
                    'chat_group' => $group,
                    'content' => $content,
                    'type' => $type,
                    'firebase_uid' => $users->random()->profile->firebase_uid
                ]);
            }
        });
    }

    protected function getChatGroups()
    {
        return ChatGroup::all();
    }
    public function getFakerFiles()
    {
        $path = storage_path($this->fakerFilesPath);
        return collect(\File::allFiles($path));
    }

    public function getUploadedFile():UploadedFile
    {
        $photoFile = $this->allFakerFiles->random();
        $uploadFile = new UploadedFile(
            $photoFile->getRealPath(),
            str_random(16).'.'.$photoFile->getExtension()
        );
        return $uploadFile;
    }
}
