<?php

namespace CodeShopping\Models;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChatGroup extends Model
{

    use SoftDeletes;

    const BASE_PATH = 'app/public';
    const DIR_CHAT_GROUPS = 'chat_groups';


    const CHAT_GROUP_PHOTO_PATH = self::BASE_PATH . '/' . self::DIR_CHAT_GROUPS;

    protected $fillable = ['name', 'photo'];

    protected $dates = ['deleted_at'];


    public static function createWithPhoto(array $data): ChatGroup
    {
        try {
            Self::uploadPhoto($data['photo']);
            $data['photo'] = $data['photo']->hashName();

            DB::beginTransaction();
            $chatGroup = Self::create($data);
            DB::commit();
            return $chatGroup;
        } catch (\Exception $e) {
            Self::deleteFile($data['photo']);
            DB::rollBack();
            throw $e;
        }
    }

    public function updateWithPhoto(array $data): ChatGroup
    {
        try {
            if (isset($data['photo'])) {
                Self::uploadPhoto($data['photo']);
                $this->deletePhoto();
                $data['photo'] = $data['photo']->hashName();
            }
            DB::beginTransaction();
            $this->fill($data)->save();
            DB::commit();
            return $this;
        } catch (\Exception $e) {
            if (isset($data['photo'])) {
                Self::deleteFile($data['photo']);
            }
            DB::rollBack();
            throw $e;
        }
    }

    public static function uploadPhoto(UploadedFile $photo)
    {
        $dir = self::photoDir();
        $photo->store($dir, ['disk' => 'public']);
    }

    public static function deleteFile(UploadedFile $photo)
    {
        $path = self::photoPath();
        $photoPath = "{$path}/{$photo->hashName()}";
        if (file_exists($photoPath)) {
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

    public function getPhotoUrlAttribute()
    {
        $path = self::photoDir();
        return asset("storage/{$path}/{$this->photo}");
    }
}
