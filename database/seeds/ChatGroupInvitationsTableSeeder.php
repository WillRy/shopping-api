<?php

use Illuminate\Database\Seeder;
use CodeShopping\Models\Category;
use CodeShopping\Models\ChatGroup;
use CodeShopping\Models\ChatGroupInvitation;

class ChatGroupInvitationsTableSeeder extends Seeder
{
    public function run()
    {
        $chatGroups = ChatGroup::all();

        factory(ChatGroupInvitation::class, 1)
            ->make()
            ->each(function ($invitation) use ($chatGroups) {
                $invitation->group_id = $chatGroups->first()->id;
                $invitation->save();
            });

        factory(ChatGroupInvitation::class, 20)
            ->make()
            ->each(function ($invitation) use ($chatGroups) {
                $invitation->group_id = $chatGroups->random()->id;
                $invitation->save();
            });
    }
}
