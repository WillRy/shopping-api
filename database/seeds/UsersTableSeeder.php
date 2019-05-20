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
        });
        factory(User::class, 1)->create([
            'email' => 'customer@user.com',
            'role' => User::ROLE_CUSTOMER
        ])->each(function ($user) {
            Model::reguard();
            $user->updateWithProfile([
                'phone_number' => "+16505541234",
                'photo' => $this->getAdminPhoto()
            ]);
            Model::unguard();
        });

        factory(User::class, 50)->create([
            'role' => User::ROLE_CUSTOMER
        ]);
    }

    public function getAdminPhoto()
    {
        return new \Illuminate\Http\UploadedFile(
            storage_path('app/faker/users/user.png'),
            str_random(16) . 'png'
        );
    }
}
