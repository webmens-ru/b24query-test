<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\models\b24;

use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\data\BaseDataProvider;
use yii\db\ActiveQueryInterface;
use yii\db\Connection;
use yii\db\QueryInterface;
use yii\di\Instance;
use yii\helpers\ArrayHelper;
use Yii;

class DataProvider extends BaseDataProvider
{
    public $query;
    public $key;
    public $autch;

    private $_sort;
    private $_pagination;
    private $_keys;
    private $_models;
    private $_totalCount;

//    public $db;
    protected function prepareTotalCount()
    {
        //Реализовать функцию.
//        return is_array($this->allModels) ? count($this->allModels) : 0;
//        =============================================================================
//        if (!$this->query instanceof QueryInterface) {
//            throw new InvalidConfigException('The "query" property must be an instance of a class that implements the QueryInterface e.g. yii\db\Query or its subclasses.');
//        }
//        $query = clone $this->query;
//        return (int)$query->limit(-1)->offset(-1)->orderBy([])->count('*', $this->db);
        return 0;
    }

    public function getModels()
    {
        $this->prepare();

        return $this->_models;
    }

    public function prepare($forcePrepare = false)
    {
        if ($forcePrepare || $this->_models === null) {
            $this->_models = $this->prepareModels();
        }
        if ($forcePrepare || $this->_keys === null) {
            $this->_keys = $this->prepareKeys($this->_models);
        }
    }

    protected function prepareModels()
    {
//        Yii::warning('prepareModels', 'prepareModels');

//        if (!$this->query instanceof QueryInterface) {
//            throw new InvalidConfigException('The "query" property must be an instance of a class that implements the QueryInterface e.g. yii\db\Query or its subclasses.');
//        }
        $query = clone $this->query;
//        Yii::warning($query, 'dp');
//        if (($pagination = $this->getPagination()) !== false) {
//            $pagination->totalCount = $this->getTotalCount();
//            if ($pagination->totalCount === 0) {
//                return [];
//            }
//            $query->limit($pagination->getLimit())->offset($pagination->getOffset());
//        }
//        if (($sort = $this->getSort()) !== false) {
//            $query->addOrderBy($sort->getOrders());
//        }
//
        return $query/*->asArray()*/->all($this->autch);
        //return [];
    }

    protected function prepareKeys($models)
    {
        if ($this->key !== null) {
            $keys = [];
            foreach ($models as $model) {
                if (is_string($this->key)) {
                    $keys[] = $model[$this->key];
                } else {
                    $keys[] = call_user_func($this->key, $model);
                }
            }

            return $keys;
        }

        return array_keys($models);
    }
}