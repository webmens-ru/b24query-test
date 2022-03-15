<?php

namespace app\modules\wm\b24\user;

//use yii\base\Model;
use Bitrix24\B24Object;
use wm\b24tools\b24Tools;
use Yii;
use yii\helpers\ArrayHelper;
use app\modules\wm\b24\TableSchema;


class UserActiveRecord extends \app\modules\wm\b24\ActiveRecord
{
    public static function listMethod()
    {
        return 'user.get';
    }

    public static function oneMethod()
    {
        return 'user.get';
    }

    public static function fieldsMethod()
    {
        return 'user.fields';
    }

    public function fields()
    {
        return $this->attributes();
    }
//TODO getFooter($models) точно нужно? тут
    public static function getFooter($models)
    {
        return [];
    }

    public static function find()
    {
        return Yii::createObject(UserActiveQuery::className(), [get_called_class()]);
    }

    public static function listDataSelector()
    {
        return 'result';
    }

    public static function oneDataSelector()
    {
        return 'result.0';
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
//            TODO Кэширование
        }, 300);
//        $tableSchema = new TableSchema($schemaData);
        //Yii::warning(ArrayHelper::toArray($tableSchema), '$tableSchema');
        return $tableSchema;
    }
//TODO Подумать о переносе в родительский класс
    public static function internalGetTableSchema(){
        $b24Obj = self::getConnect();
        $schemaData =   ArrayHelper::getValue($b24Obj->client->call(
            static::fieldsMethod(), []
        ), 'result');
        return new TableSchema($schemaData);
    }

}
