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

        if(method_exists(__CLASS__,'pivotAttached')){
            static::pivotAttached(function($model, $relationName, $pivotIds, $pivotIdsAttribute){

                $model->syncPivotAttached($model, $relationName, $pivotIds, $pivotIdsAttribute);
            });
        }
        if(method_exists(__CLASS__,'pivotDetached')){
            static::pivotAttached(function($model, $relationName, $pivotIds){
                $model->syncPivotDetached($model, $relationName, $pivotIds);
            });
        }
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

    protected function syncPivotAttached($model, $relationName, $pivotIds, $pivotIdsAttribute)
    {
        throw new \Exception("Not implemented");
    }

    protected function syncPivotDetached($model, $relationName, $pivotIds)
    {
        throw new \Exception("Not implemented");
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
