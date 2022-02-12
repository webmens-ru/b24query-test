<?php

namespace app\models\b24;

//Код не универсален а направлен на смарт процессы стоит перенести в другой класс
class SpActiveQuery extends ActiveQuery {
    public $entityTypeId;

    public function getEntityTypeIdUsedInFrom()
    {
        if (empty($this->entityTypeId)) {
            $this->entityTypeId = $this->modelClass::entityTypeId();
        }

        return $this->entityTypeId;
    }

//    protected function getPrimaryTableName()
//    {
////        Yii::warning($this->modelClass, '$this->modelClass');
//        $modelClass = $this->modelClass;
//        //return $modelClass::tableName();
//        return $modelClass::entityTypeId();
//    }

    protected function prepairParams(){
        $this->getEntityTypeIdUsedInFrom();
        \Yii::warning($this->orderBy, '$this->orderBy');
        $data = [
            'entityTypeId' => $this->entityTypeId,
            'filter' => $this->where,
            'order' => $this->orderBy,
            //Остальные параметры
        ];
        //Yii::warning($data, '$data');
        $this->params = $data;
    }
}
