<?php

namespace app\models\b24;

//use yii\base\Model;
use Bitrix24\B24Object;
use wm\b24tools\b24Tools;
use Yii;
use yii\helpers\ArrayHelper;


class CrmCategoryActiveRecord extends \app\models\b24\ActiveRecord
{
    public static function entityTypeId()
    {
        return null;
    }

    public static function listMethod()
    {
        return 'crm.category.list';
    }
    public static function fieldsMethod()
    {
        return 'crm.category.fields';
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
        return Yii::createObject(SpActiveQuery::className(), [get_called_class()]);
    }

    public static function listDataSelector()
    {
        return 'result.categories';
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
        $key = static::fieldsMethod()._.static::entityTypeId();
        $tableSchema =  $cache->getOrSet($key, function () {
            return static::internalGetTableSchema();
        }, 30);
//        $tableSchema = new TableSchema($schemaData);
        //Yii::warning(ArrayHelper::toArray($tableSchema), '$tableSchema');
        return $tableSchema;
    }

    public static function internalGetTableSchema(){
        $b24Obj = self::getConnect();
        $schemaData =   ArrayHelper::getValue($b24Obj->client->call(
            static::fieldsMethod(), ['entityTypeId' => static::entityTypeId()]
        ), 'result.fields');
        return new TableSchema($schemaData);
    }

}
