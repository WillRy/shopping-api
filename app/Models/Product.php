<?php

namespace CodeShopping\Models;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Mnabialek\LaravelEloquentFilter\Traits\Filterable;
use Illuminate\Http\UploadedFile;
use PHPUnit\Framework\Error\Error;

class Product extends Model
{
    use Sluggable, SoftDeletes, Filterable;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'name',
        'description',
        'price',
        'active',
        'photo'
    ];

    const BASE_PATH = 'app/public';
    const DIR_PRODUCTS = 'products';
    const PRODUCTS_PATH = self::BASE_PATH . '/' . self::DIR_PRODUCTS;

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name',
                'unique' => true
            ]
        ];
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function photos()
    {
        return $this->hasMany(ProductPhoto::class);
    }

    public static function createWithPhoto (array $data): Product
    {
        try {
            $photo = $data['photo'];
            self::uploadPhoto($data['photo']);
            $data['photo'] = $data['photo']->hashName();
            \DB::beginTransaction();
            $product = self::create($data);
            \DB::commit();
            return $product;
        } catch (\Exception $e) {
            self::deleteFile($photo);
            \DB::rollBack();
            throw $e;
        }
    }

    public function updateWithPhoto (array $data): Product
    {
        try {
            if (isset($data['photo'])){
                $photo = $data['photo'];
                self::uploadPhoto($data['photo']);
                $this->deletePhoto();
                $data['photo'] = $data['photo']->hashName();
            }
            \DB::beginTransaction();
            $this->fill($data)->save();
            \DB::commit();
            return $this;
        } catch (\Exception $e) {
            if (isset($photo)){
                self::deleteFile($photo);
            }
            \DB::rollBack();
            throw $e;
        }
    }

    private function deletePhoto()
    {
        $dir = self::photoDir();
        \Storage::disk('public')->delete("{$dir}/{$this->photo}");
    }

    private static function deleteFile(UploadedFile $photo)
    {
        $path = self::photoPath();
        $photoPath = "{$path}/{$photo->hashName()}";
        if (file_exists($photoPath)){
            \File::delete($photoPath);
        }
    }

    private static function photoPath()
    {
        $path = self::PRODUCTS_PATH;
        return storage_path($path);
    }

    public static function uploadPhoto(UploadedFile $photo)
    {
        $dir = self::photoDir();
        $photo->store($dir, ['disk' => 'public']);
    }

    private static function photoDir()
    {
        $dir = self::DIR_PRODUCTS;
        return $dir;
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
}
