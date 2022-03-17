<?php


namespace app\modules\wm\b24\crm;


class ContactActiveQuery extends \app\modules\wm\b24\ActiveQuery
{
    //    public $entityTypeId;

    protected $listMethodName = 'crm.contact.list';

    protected $oneMethodName = 'crm.contact.get';

    public function getEntityTypeIdUsedInFrom()
    {
//        if (empty($this->entityTypeId)) {
//            $this->entityTypeId = $this->modelClass::entityTypeId();
//        }

        return '';
    }

//    protected function getPrimaryTableName()
//    {
////        Yii::warning($this->modelClass, '$this->modelClass');
//        $modelClass = $this->modelClass;
//        //return $modelClass::tableName();
//        return $modelClass::entityTypeId();
//    }

    protected function prepairParams(){
//        $this->getEntityTypeIdUsedInFrom();
//        \Yii::warning($this->orderBy, '$this->orderBy');
        $data = [
//            'entityTypeId' => $this->entityTypeId,
            'filter' => $this->where,
            'order' => $this->orderBy?$this->orderBy:null,
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
//            'entityTypeId' => $this->entityTypeId,
            'id' => $id
        ];
        $this->params = $data;
    }
}