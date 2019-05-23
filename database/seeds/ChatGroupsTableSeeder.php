<?php

use CodeShopping\Models\User;
use Illuminate\Database\Seeder;
use CodeShopping\Models\Product;
use Illuminate\Http\UploadedFile;
use CodeShopping\Models\ChatGroup;
use CodeShopping\Models\ProductPhoto;

class ChatGroupsTableSeeder extends Seeder
{

    private $allFakerPhotos;
    private $fakerPhotosPath = 'app/faker/chat_groups';

    public function run()
    {
        $this->allFakerPhotos = $this->getFakerPhotos();
        $this->deleteAllPhotosInChatGroupPath();
        $self = $this;
        $customerDefault = User::whereEmail('customer@user.com')->first();

        $otherCustomers = User::whereRole(User::ROLE_CUSTOMER)
            ->whereNotIn('id', [$customerDefault->id])
            ->get();

        factory(ChatGroup::class, 10)->make()->each(function ($group) use ($self, $otherCustomers ) {
            $group = ChatGroup::createWithPhoto([
                'name' => $group->name,
                'photo' => $self->getUploadedFile()
            ]);
            $customersIds = $otherCustomers->random(10)->pluck('id')->toArray();
            $group->users()->attach($customersIds);
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
