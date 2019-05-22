<?php

use Illuminate\Database\Seeder;
use CodeShopping\Models\Product;
use CodeShopping\Models\ProductPhoto;
use Illuminate\Http\UploadedFile;
use CodeShopping\Models\ChatGroup;

class ChatGroupsTableSeeder extends Seeder
{

    private $allFakerPhotos;
    private $fakerPhotosPath = 'app/faker/chat_groups';

    public function run()
    {
        $this->allFakerPhotos = $this->getFakerPhotos();
        $this->deleteAllPhotosInChatGroupPath();
        $self = $this;

        factory(ChatGroup::class, 10)->make()->each(function ($group) use ($self) {
            ChatGroup::createWithPhoto([
                'name' => $group->name,
                'photo' => $self->getUploadedFile()
            ]);
        });
    }

    //pega todas as fotos do diretório
    public function getFakerPhotos()
    {
        $path = storage_path($this->fakerPhotosPath);
        return collect(\File::allFiles($path));
    }

    // delete todas as photos que foram enviadas no 'upload'
    private function deleteAllPhotosInChatGroupPath()
    {
        $path = ChatGroup::CHAT_GROUP_PHOTO_PATH;
        \File::deleteDirectory(storage_path($path), true);
    }

    private function getUploadedFile()
    {
        $photoFile = $this->allFakerPhotos->random();
        $uploadFile = new UploadedFile(
            $photoFile->getRealPath(),
            str_random(16) . '.' . $photoFile->getExtension()
        );
        return $uploadFile;
    }
}
