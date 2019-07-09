<?php

namespace CodeShopping\Providers;

use Kreait\Firebase;
use Kreait\Firebase\Factory;
use CodeShopping\Models\Order;
use CodeShopping\Models\Product;
use Kreait\Firebase\ServiceAccount;
use CodeShopping\Models\ProductInput;
use CodeShopping\Models\ProductOutput;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use CodeShopping\Observers\OrderObserver;
use CodeShopping\Firebase\NotificationType;
use CodeShopping\Models\ChatInvitationUser;
use CodeShopping\Models\ChatGroupInvitation;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        ProductInput::created(function ($input) {
            $product = $input->product;
            $product->increaseStock($input->amount);
        });
        ProductOutput::created(function ($input) {
            $product = $input->product;
            $product->decreaseStock($input->amount);
        });

        ChatGroupInvitation::creating(function ($invitation) {
            $invitation->slug = str_random(7);
            $invitation->remaining = $invitation->total;
        });
        ChatGroupInvitation::updating(function (ChatGroupInvitation $invitation) {
            $oldRemaining = $invitation->getOriginal('remaining');
            $newRemaining = $invitation->remaining;
            if ($oldRemaining === $newRemaining) {
                $invitation->remaining = $invitation->total;
            }
        });
        ChatInvitationUser::created(function ($userInvitation) {
            $linkInvitation = $userInvitation->invitation;
            $linkInvitation->remaining -= 1;
            $linkInvitation->save();
        });

        ChatInvitationUser::updated(function ($userInvitation) {
            if ($userInvitation->status == ChatInvitationUser::STATUS_PENDING) {
                return;
            }
            if ($userInvitation->status == ChatInvitationUser::STATUS_REPROVED) {
                $linkInvitation = $userInvitation->invitation;
                $linkInvitation->remaining += 1;
                $linkInvitation->save();
            }
            $group = $userInvitation->invitation->group;
            $user_id = $userInvitation->user->id;
            $group->users()->attach($user_id);

            $token = $userInvitation->user->profile->device_token;
            if (!$token) {
                return;
            }
            $messaging = app(CloudMessagingFb::class);
            $messaging->setTitle("Sua inscrição foi aprovada")
                ->setBody('Voce está inscrito em um novo grupo')
                ->setTokens([$token])
                ->setData([
                    'type' => NotificationType::CHAT_GROUP_SUBSCRIBE,
                    'chat_group_name' => $group->name
                ])
                ->send();
        });

        Order::observe(OrderObserver::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(Firebase::class, function () {
            $serviceAccount = Firebase\ServiceAccount::fromJsonFile(base_path('firebase-admin.json'));
            return (new Firebase\Factory())->withServiceAccount($serviceAccount)->create();
        });
    }
}
