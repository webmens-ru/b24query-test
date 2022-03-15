<?php

namespace app\models\b24\crm;

//use yii\base\Model;
use Bitrix24\B24Object;
use wm\b24tools\b24Tools;
use Yii;
use yii\helpers\ArrayHelper;
use app\models\b24\TableSchema;


class CrmStatusActiveRecord extends \app\models\b24\ActiveRecord
{
//    public static function entityTypeId()
//    {
//        return null;
//    }

    public static function listMethod()
    {
        return 'crm.status.list';
    }
    public static function fieldsMethod()
    {
        return 'crm.status.fields';
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
        return Yii::createObject(CrmActiveQuery::className(), [get_called_class()]);
    }

    public static function listDataSelector()
    {
        return 'result';
    }

    public static function oneDataSelector()
    {
        return 'result';
    }

    /**
     * Возвращает все столбцы сущности, может быть переопределена для оптимизации запроса
     * @return array
     */
    public function attributes()
    {
        return array_keys(static::getTableSchema()->columns);

    }

    public static function getTableSchema()
    {
        $cache = Yii::$app->cache;
        $key = static::fieldsMethod();
        $tableSchema =  $cache->getOrSet($key, function () {
            return static::internalGetTableSchema();
        }, 300);
//        $tableSchema = new TableSchema($schemaData);
        //Yii::warning(ArrayHelper::toArray($tableSchema), '$tableSchema');
        return $tableSchema;
    }

    public static function internalGetTableSchema(){
        $b24Obj = self::getConnect();
        $schemaData =   ArrayHelper::getValue($b24Obj->client->call(
            static::fieldsMethod(), []
        ), 'result');
        return new TableSchema($schemaData);
    }

}
