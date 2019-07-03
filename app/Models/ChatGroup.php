<?php

namespace CodeShopping\Models;

use CodeShopping\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use CodeShopping\Firebase\FirebaseSync;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Fico7489\Laravel\Pivot\Traits\PivotEventTrait;

class ChatGroup extends Model
{

    use SoftDeletes, FirebaseSync, PivotEventTrait;

    const BASE_PATH = 'app/public';
    const DIR_CHAT_GROUPS = 'chat_groups';


    const CHAT_GROUP_PHOTO_PATH = self::BASE_PATH . '/' . self::DIR_CHAT_GROUPS;

    protected $fillable = ['name', 'photo'];

    protected $dates = ['deleted_at'];


    public static function createWithPhoto (array $data): ChatGroup
    {
        try {
            $photo = $data['photo'];
            self::uploadPhoto($data['photo']);
            $data['photo'] = $data['photo']->hashName();
            \DB::beginTransaction();
            $chatGroup = self::create($data);
            \DB::commit();
        } catch (\Exception $e) {
            self::deleteFile($photo);
            \DB::rollBack();
            throw $e;
        }
        return $chatGroup;
    }

    public function updateWithPhoto (array $data): ChatGroup
    {
        try {
            $photo = $data['photo'];
            if (isset($data['photo'])){
                self::uploadPhoto($data['photo']);
                $this->deletePhoto();
                $data['photo'] = $data['photo']->hashName();
            }
            \DB::beginTransaction();
            $this->fill($data)->save();
            \DB::commit();
        } catch (\Exception $e) {
            if (isset($photo)){
                self::deleteFile($photo);
            }
            \DB::rollBack();
            throw $e;
        }
        return $this;
    }

    public static function uploadPhoto(UploadedFile $photo)
    {
        $dir = self::photoDir();
        $photo->store($dir, ['disk' => 'public']);
    }

    private static function deleteFile(UploadedFile $photo)
    {
        $path = self::photoPath();
        $photoPath = "{$path}/{$photo->hashName()}";
        if (file_exists($photoPath)){
            \File::delete($photoPath);
        }
    }
    private function deletePhoto()
    {
        $dir = self::photoDir();
        \Storage::disk('public')->delete("{$dir}/{$this->photo}");
    }

    private static function photoPath()
    {
        $path = self::CHAT_GROUP_PHOTO_PATH;
        return storage_path($path);
    }

    private static function photoDir()
    {
        $dir = self::DIR_CHAT_GROUPS;
        return $dir;
    }



    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function linkInvitations()
    {
        return $this->hasMany(ChatGroupInvitation::class, 'group_id');
    }

    public function userInvitations()
    {
        return $this->hasManyThrough(
            ChatInvitationUser::class,
            ChatGroupInvitation::class,
            'group_id',
            'invitation_id'
        );
    }

    public function syncFbRemove()
    {
        $this->syncFbSet();
    }

    public function updateInFb() {
        $this->syncFbSet(self::$OPERATION_UPDATE);
    }

    public function syncFbSet($operation = null)
    {
        $data = $this->toArray();
        $data['photo_url'] = $this->photo_url_base;
        unset($data['photo']);
        $this->setTimestamps($data, $operation);
        $this->getModelReference()->set($data);
    }

    public function getPhotoUrlAttribute()
    {

        return asset("storage/{$this->photo_url_base}");
    }

    public function getPhotoUrlBaseAttribute()
    {
        $path = self::photoDir();
        return "{$path}/{$this->photo}";
    }

    protected function syncPivotAttached($model, $relationName, $pivotIds, $pivotIdsAttribute)
    {
        $users = User::whereIn('id', $pivotIds)->get();
        $data = [];
        foreach ($users as $user) {
            $key = $this->chatGroupUsersKey($model, $user);
            $data[$key] = true;
        };
        $this->getFirebaseDatabase()->getReference()->update($data);
    }
    protected function syncPivotDetached($model, $relationName, $pivotIds)
    {
        $users = User::whereIn('id', $pivotIds)->get();
        $data = [];
        foreach ($users as $user) {
            $key = $this->chatGroupUsersKey($model, $user);
            $data[$key] = null;
        };
        $this->getFirebaseDatabase()->getReference()->update($data);
    }
    private function chatGroupUsersKey($model, $user)
    {
        return "chat_groups_users/{$model->id}/{$user->profile->firebase_uid}";
    }
}
