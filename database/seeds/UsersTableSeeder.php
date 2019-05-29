<?php

use CodeShopping\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use CodeShopping\Models\UserProfile;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \File::deleteDirectory(UserProfile::photoPath(), true);
        factory(User::class, 1)->create([
            'email' => 'admin@user.com'
        ])->each(function ($user) {
            Model::reguard();
            $user->updateWithProfile([
                'phone_number' => "+16505551234",
                'photo' => $this->getAdminPhoto()
            ]);
            Model::unguard();
            $user->profile->firebase_uid = 'zr75WVgxutZu4owosIL2tq9zLx53';
            $user->profile->save();
        });
        factory(User::class, 1)->create([
            'email' => 'customer@user.com',
            'role' => User::ROLE_CUSTOMER
        ])->each(function ($user) {
            Model::reguard();
            $user->updateWithProfile([
                'phone_number' => "+16505551235",
                'photo' => $this->getAdminPhoto()
            ]);
            Model::unguard();
            $user->profile->firebase_uid = '9AFsx3Jc4lflW5t9zy1ebWNkeA63';
            $user->profile->save();
        });

        factory(User::class, 20)->create([
            'role' => User::ROLE_CUSTOMER
        ])->each(function($user,$key) {
            $user->profile->phone_number = "+165055510{$key}";
            $user->profile->firebase_uid = "user-{$key}";
            $user->profile->save();
        });
    }

    public function getAdminPhoto()
    {
        return new \Illuminate\Http\UploadedFile(
            storage_path('app/faker/users/user.png'),
            str_random(16) . 'png'
        );
    }
}
