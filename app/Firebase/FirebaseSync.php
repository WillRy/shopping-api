<?php
declare (strict_types = 1);
namespace CodeShopping\Firebase;

use Kreait\Firebase;

trait FirebaseSync
{

    private static $OPERATION_CREATE = 1;
    private static $OPERATION_UPDATE = 2;


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

        if (method_exists(__CLASS__, 'pivotAttached')) {
            static::pivotAttached(function ($model, $relationName, $pivotIds, $pivotIdsAttribute) {
                $model->syncPivotAttached($model, $relationName, $pivotIds, $pivotIdsAttribute);
            });
        }
        if (method_exists(__CLASS__, 'pivotDetached')) {
            static::pivotDetached(function ($model, $relationName, $pivotIds) {
                $model->syncPivotDetached($model, $relationName, $pivotIds);
            });
        }
    }

    public function syncFbCreate()
    {
        $this->syncFbSet(self::$OPERATION_CREATE);
    }

    public function syncFbUpdate()
    {
        $this->syncFbSet(self::$OPERATION_UPDATE);
    }

    public function syncFbRemove()
    {
        $this->getModelReference()->remove();
    }

    public function syncFbSet($operation = null)
    {

        $data = $this->toArray();
        $this->setTimestamps($data, $operation);
        $this->getModelReference()->update($this->toArray());
    }

    private function setTimestamps(&$data, $operation)
    {
        if($operation === self::$OPERATION_CREATE){
            $data['created_at'] = ['.sv'=>'timestamp'];
            $data['updated_at'] = ['.sv'=>'timestamp'];
        }
        if($operation === self::$OPERATION_UPDATE){
            if(isset($data['created_at'])){
                unset($data['created_at']);
            }
            $data['updated_at'] = ['.sv'=>'timestamp'];
        }
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
