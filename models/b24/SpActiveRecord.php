<?php

namespace app\models\b24;

//use yii\base\Model;
use Bitrix24\B24Object;
use wm\b24tools\b24Tools;
use Yii;
use yii\helpers\ArrayHelper;


class SpActiveRecord extends \app\models\b24\ActiveRecord
{
    public static function entityTypeId()
    {
        return null;
    }

    public static function listMethod()
    {
        return 'crm.item.list';
    }
    public static function fieldsMethod()
    {
        return 'crm.item.fields';
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
        return 'result.items';
    }

    /**
     * Возвращает все столбцы сущности, может быть переопределена для оптимизации запроса
     * @return array
     */
    public function attributes()
    {
        return array_keys(static::getTableSchema()->columns);

    }
//TODO function getTableSchema() Ни чего не понимаю

//    public static function getTableSchema()
//    {
//        $tableSchema = static::getDb()
//            ->getSchema()
//            ->getTableSchema(static::tableName());
//
//        if ($tableSchema === null) {
//            throw new InvalidConfigException('The table does not exist: ' . static::tableName());
//        }
//
//        return $tableSchema;
//    }

    public static function getTableSchema()
    {
//        $schema = new Schema();
//        $tableSchema = $schema->getTableSchema(static::fieldsMethod(), ['entityTypeId' => static::entityTypeId()]);
//
//        if ($tableSchema === null) {
//            throw new InvalidConfigException('The table does not exist: ' . static::tableName());
//        }
//
//        return $tableSchema;

        $cache = Yii::$app->cache;
        $key = static::fieldsMethod()._.static::entityTypeId();
        return $cache->getOrSet($key, function () {
            return static::internalGetTableSchema();
        }, 60);
    }

    public static function internalGetTableSchema(){
        $b24Obj = self::getConnect();
        $fields = $b24Obj->client->call(
            static::fieldsMethod(), ['entityTypeId' => static::entityTypeId()]
        );
        $fields = ArrayHelper::getValue($fields, 'result.fields');
        return Yii::createObject([
            'class' => TableSchema::className(),
            'columns' => $fields,
        ]);
//-----------------------------------------------------
//        $schema = new Schema();
//        $schema->
    }

}
