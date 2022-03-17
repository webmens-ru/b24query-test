<?php

namespace app\modules\wm\b24\crm;

//Код не универсален а направлен на смарт процессы стоит перенести в другой класс
use yii\helpers\ArrayHelper;
use app\modules\wm\b24\ActiveQuery;

class CategoryActiveQuery extends ActiveQuery {
    public $entityTypeId;

    protected $listMethodName = 'crm.category.list';

    protected $oneMethodName = 'crm.category.get';

    protected $listDataSelectorName = 'result.categories';

    protected $oneDataSelectorName = 'result.category';

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
            'select' => $this->select,
            //Остальные параметры
        ];
        //Yii::warning($data, '$data');
        $this->params = $data;
    }

    protected function prepairOneParams(){
        $this->getEntityTypeIdUsedInFrom();
        \Yii::warning($this->orderBy, '$this->orderBy');
        $id = null;
        if(ArrayHelper::getValue($this->where, 'id')){
            $id = ArrayHelper::getValue($this->where, 'id');
        }
        if(ArrayHelper::getValue($this->link, 'id')){
            $id = ArrayHelper::getValue($this->where, 'inArray.0');
        }
        $data = [
            'entityTypeId' => $this->entityTypeId,
            'id' => $id
        ];
        $this->params = $data;
    }
}
