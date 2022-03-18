<?php


namespace app\modules\wm\b24\crm;


use Yii;


class DealActiveRecord extends \app\modules\wm\b24\ActiveRecord
{
    public static function fieldsMethod()
    {
        return 'crm.deal.fields';
    }

    public function fields()
    {
        return $this->attributes();
    }

    public static function getFooter($models)
    {
        return [];
    }

    public static function find()
    {
        return Yii::createObject(DealActiveQuery::className(), [get_called_class()]);
    }
}