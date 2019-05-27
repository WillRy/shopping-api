<?php
declare (strict_types = 1);
namespace CodeShopping\Firebase;

use Kreait\Firebase;

trait FirebaseSync
{

    public static function  bootFirebaseSync()
    {
        static::created(function ($model) {
            $model->syncFbCreate();
        });

        static::updated(function ($model) {
            $model->syncFbUpdate();
        });

        static::deleted(function ($model) {
            $model->syncFbRemove();
        });
    }

    public function syncFbCreate()
    {
        $this->syncFbSet();
    }

    public function syncFbUpdate()
    {
        $this->syncFbSet();
    }

    public function syncFbRemove()
    {
        $this->getModelReference()->remove();
    }

    public function syncFbSet()
    {
        $this->getModelReference()->update($this->toArray());
    }

    protected function getFirebaseDatabase(): Firebase\Database
    {
        $firebase = app(Firebase::class);
        return $firebase->getDatabase();
    }

    protected function getModelReference(): Firebase\Database\Reference
    {
        $path = $this->getTable() . '/' . $this->getKey();
        return $this->getFirebaseDatabase()->getReference($path);
    }
}
