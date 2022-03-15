<?php

namespace app\modules\wm\b24\crm;

//Код не универсален а направлен на смарт процессы стоит перенести в другой класс
use yii\helpers\ArrayHelper;
use app\modules\wm\b24\ActiveQuery;

class SourceActiveQuery extends ActiveQuery {

    public $entityId;

    public function getEntityIdUsedInFrom()
    {
        if (empty($this->entityId)) {
            $this->entityId = $this->modelClass::entityId();
        }

        return $this->entityId;
    }

    public static function oneDataSelector()
    {
        return 'result';
    }

//    protected function getPrimaryTableName()
//    {
////        Yii::warning($this->modelClass, '$this->modelClass');
//        $modelClass = $this->modelClass;
//        //return $modelClass::tableName();
//        return $modelClass::entityTypeId();
//    }

    protected function prepairParams(){
        $this->getEntityIdUsedInFrom();
        \Yii::warning($this->orderBy, '$this->orderBy');
        $data = [
            'filter' => array_merge($this->where, ['ENTITY_ID' => $this->entityId]),
            'order' => $this->orderBy,
            //'select' => $this->select,
            //Остальные параметры
        ];
        //Yii::warning($data, '$data');
        $this->params = $data;
    }

    protected function prepairOneParams(){
        \Yii::warning($this->orderBy, '$this->orderBy');
        $id = null;
        if(ArrayHelper::getValue($this->where, 'id')){
            $id = ArrayHelper::getValue($this->where, 'id');
        }
        if(ArrayHelper::getValue($this->link, 'id')){
            $id = ArrayHelper::getValue($this->where, 'inArray.0');
        }
        $data = [
            'id' => $id
        ];
        $this->params = $data;
    }
}
