<?php

namespace app\models\b24;

use Bitrix24\B24Object;
use wm\b24tools\b24Tools;
use Yii;
use yii\base\Component;
use yii\helpers\ArrayHelper;

//Код не универсален а направлен на смарт процессы стоит перенести в другой класс
class ActiveQuery extends Query {
    use ActiveQueryTrait;
    use ActiveRelationTrait;

//    public $sql;
//    public $on;
//    public $joinWith;

    const EVENT_INIT = 'init';

    public $params = [
        'entityTypeId' => 174
        /*, 'filter' => $query->filter*/];

    //public $params = [];

    private $_filter = [];

    //private $_method = '';

    private $_method = 'crm.item.list';

    private $_start = 0;

    private $_limit = 0;

    public $entityTypeId;

    //private $_entityTypeId = 0;

    private $_entityTypeId = 174;

    public $modelClass;

    public function __construct($modelClass, $config = [])
    {
//        Yii::warning($modelClass, '$modelClass');
        $this->modelClass = $modelClass;
        parent::__construct($config);
    }

    public function init()
    {
        parent::init();
        $this->trigger(self::EVENT_INIT);
    }



    public function setFilter($filter){
        $_filter = $filter;
        return $this;
    }

//    public function getFilter(){
//        return
//    }

//    public function all($auth = null){
//        if ($this->emulateExecution) {
//            return [];
//        }
//        $this->prepairParams();
//        $component = new b24Tools();
//        $b24App = null;// $component->connectFromUser($auth);
//        if($auth === null){
//            $b24App = $component->connectFromAdmin();
//        }else{
//            $b24App = $component->connectFromUser($auth);
//        }
//        $obB24 = new B24Object($b24App);
//        $rows = [];
//        if($this->_limit = 0){
//            $rows = $this->getFullData($obB24);
//        }else{
//            $rows = $this->getData($obB24);
//        }
//        return $this->populate($rows);
//    }

    public function all($auth = null)
    {
        return parent::all($auth);
    }

    public function populate($rows)
    {
        if (empty($rows)) {
            return [];
        }

        $models = $this->createModels($rows);
        if (!empty($this->join) && $this->indexBy === null) {
            $models = $this->removeDuplicatedModels($models);
        }
        if (!empty($this->with)) {
            $this->findWith($this->with, $models);
        }

        if ($this->inverseOf !== null) {
            $this->addInverseRelations($models);
        }

        if (!$this->asArray) {
            foreach ($models as $model) {
                $model->afterFind();
            }
        }

        return parent::populate($models);
    }

    private function getData($obB24){
        return $obB24->client->call($this->_method, $this->params);
    }

    private function getFullData($obB24){
        $request = $this->getData($obB24);
        $countCalls = (int)ceil($request['total'] / $obB24->client::MAX_BATCH_CALLS);
        $data = ArrayHelper::getValue($request, 'result.items');
        //Yii::warning($data, '$data');
        for ($i = 1; $i < $countCalls; $i++)
            $obB24->client->addBatchCall('crm.item.list', [
                'entityTypeId' => $this->_entityTypeId,
                'filter' => $this->_filter,
                'start' => $obB24->client::MAX_BATCH_CALLS * $i,
            ], function ($result) use (&$data) {
                $data = array_merge($data, ArrayHelper::getValue($result, 'result.items'));
            });
        $obB24->client->processBatchCalls();
        return $data; //Добавить вывод дополнительной информации
    }

    public function andFilterCompare($name, $value, $defaultOperator = '=') {
        $arr = [];
        //убираем '[ и ']' в начале и в конце строки в запросе
        if ((substr($value, 0, 1) == '[') && (substr($value, -1, 1) == ']')) {
            $data = substr($value, 1, -1);
            $arr = explode(',', $data);
            foreach ($arr as $var) {
                $this->andFilterCompare($name, $var);
            }
            return $this;
        } else {
            if (preg_match('/^(<>|>=|>|<=|<|=)/', $value, $matches)) {
                $operator = $matches[1];
                $value = substr($value, strlen($operator));
            } elseif ($value == 'isNull') {
                return $this->andWhere([$name => null]);
            } elseif (preg_match('/^(%%)/', $value, $matches)) {
                $operator = $matches[1];
                $value = substr($value, strlen($operator));
                $operator = 'like';
            } elseif (preg_match('/^(in\[.*])/', $value, $matches)) {
                $operator = 'in';
                $value = explode(',', mb_substr($value, 3, -1));
            } else {
                $operator = $defaultOperator;
            }
            return $this->andFilterWhere([$operator, $name, $value]);
        }
    }

    public function andFilterWhere($params){
        if($params[2]){
            $this->filter[$params[0].$params[1]] = $params[2];
        }

        return $this;

    }

    public function getEntityTypeIdUsedInFrom()
    {
        if (empty($this->entityTypeId)) {
            $this->entityTypeId = $this->getPrimaryTableName();
        }

        return $this->entityTypeId;

        //return parent::getEntityTypeIdUsedInFrom();
    }

    protected function getPrimaryTableName()
    {
//        Yii::warning($this->modelClass, '$this->modelClass');
        $modelClass = $this->modelClass;
        //return $modelClass::tableName();
        return $modelClass::entityTypeId();
    }

    protected function prepairParams(){
        $this->getEntityTypeIdUsedInFrom();
        $data = [
            'entityTypeId' => $this->entityTypeId,
            //Остальные параметры
        ];
        $this->params = $data;
    }

//    private function removeDuplicatedModels($models)
//    {
//        $hash = [];
//        /* @var $class ActiveRecord */
//        $class = $this->modelClass;
//        $pks = $class::primaryKey();
//
//        if (count($pks) > 1) {
//            // composite primary key
//            foreach ($models as $i => $model) {
//                $key = [];
//                foreach ($pks as $pk) {
//                    if (!isset($model[$pk])) {
//                        // do not continue if the primary key is not part of the result set
//                        break 2;
//                    }
//                    $key[] = $model[$pk];
//                }
//                $key = serialize($key);
//                if (isset($hash[$key])) {
//                    unset($models[$i]);
//                } else {
//                    $hash[$key] = true;
//                }
//            }
//        } elseif (empty($pks)) {
//            throw new InvalidConfigException("Primary key of '{$class}' can not be empty.");
//        } else {
//            // single column primary key
//            $pk = reset($pks);
//            foreach ($models as $i => $model) {
//                if (!isset($model[$pk])) {
//                    // do not continue if the primary key is not part of the result set
//                    break;
//                }
//                $key = $model[$pk];
//                if (isset($hash[$key])) {
//                    unset($models[$i]);
//                } elseif ($key !== null) {
//                    $hash[$key] = true;
//                }
//            }
//        }
//
//        return array_values($models);
//    }

    public function one($db = null)
    {
        $row = parent::one($db);
        if ($row !== false) {
            $models = $this->populate([$row]);
            return reset($models) ?: null;
        }

        return null;
    }

//    public function createCommand($db = null)
//    {
//        /* @var $modelClass ActiveRecord */
//        $modelClass = $this->modelClass;
//        if ($db === null) {
//            $db = $modelClass::getDb();
//        }
//
//        if ($this->sql === null) {
//            list($sql, $params) = $db->getQueryBuilder()->build($this);
//        } else {
//            $sql = $this->sql;
//            $params = $this->params;
//        }
//
//        $command = $db->createCommand($sql, $params);
//        $this->setCommandCache($command);
//
//        return $command;
//    }

//    protected function queryScalar($selectExpression, $db)
//    {
//        /* @var $modelClass ActiveRecord */
//        $modelClass = $this->modelClass;
//        if ($db === null) {
//            $db = $modelClass::getDb();
//        }
//
//        if ($this->sql === null) {
//            return parent::queryScalar($selectExpression, $db);
//        }
//
//        $command = (new Query())->select([$selectExpression])
//            ->from(['c' => "({$this->sql})"])
//            ->params($this->params)
//            ->createCommand($db);
//        $this->setCommandCache($command);
//
//        return $command->queryScalar();
//    }

//    public function joinWith($with, $eagerLoading = true, $joinType = 'LEFT JOIN')
//    {
//        $relations = [];
//        foreach ((array) $with as $name => $callback) {
//            if (is_int($name)) {
//                $name = $callback;
//                $callback = null;
//            }
//
//            if (preg_match('/^(.*?)(?:\s+AS\s+|\s+)(\w+)$/i', $name, $matches)) {
//                // relation is defined with an alias, adjust callback to apply alias
//                list(, $relation, $alias) = $matches;
//                $name = $relation;
//                $callback = function ($query) use ($callback, $alias) {
//                    /* @var $query ActiveQuery */
//                    $query->alias($alias);
//                    if ($callback !== null) {
//                        call_user_func($callback, $query);
//                    }
//                };
//            }
//
//            if ($callback === null) {
//                $relations[] = $name;
//            } else {
//                $relations[$name] = $callback;
//            }
//        }
//        $this->joinWith[] = [$relations, $eagerLoading, $joinType];
//        return $this;
//    }

//    public function innerJoinWith($with, $eagerLoading = true)
//    {
//        return $this->joinWith($with, $eagerLoading, 'INNER JOIN');
//    }

//    private function joinWithRelations($model, $with, $joinType)
//    {
//        $relations = [];
//
//        foreach ($with as $name => $callback) {
//            if (is_int($name)) {
//                $name = $callback;
//                $callback = null;
//            }
//
//            $primaryModel = $model;
//            $parent = $this;
//            $prefix = '';
//            while (($pos = strpos($name, '.')) !== false) {
//                $childName = substr($name, $pos + 1);
//                $name = substr($name, 0, $pos);
//                $fullName = $prefix === '' ? $name : "$prefix.$name";
//                if (!isset($relations[$fullName])) {
//                    $relations[$fullName] = $relation = $primaryModel->getRelation($name);
//                    $this->joinWithRelation($parent, $relation, $this->getJoinType($joinType, $fullName));
//                } else {
//                    $relation = $relations[$fullName];
//                }
//                /* @var $relationModelClass ActiveRecordInterface */
//                $relationModelClass = $relation->modelClass;
//                $primaryModel = $relationModelClass::instance();
//                $parent = $relation;
//                $prefix = $fullName;
//                $name = $childName;
//            }
//
//            $fullName = $prefix === '' ? $name : "$prefix.$name";
//            if (!isset($relations[$fullName])) {
//                $relations[$fullName] = $relation = $primaryModel->getRelation($name);
//                if ($callback !== null) {
//                    call_user_func($callback, $relation);
//                }
//                if (!empty($relation->joinWith)) {
//                    $relation->buildJoinWith();
//                }
//                $this->joinWithRelation($parent, $relation, $this->getJoinType($joinType, $fullName));
//            }
//        }
//    }

//    private function getJoinType($joinType, $name)
//    {
//        if (is_array($joinType) && isset($joinType[$name])) {
//            return $joinType[$name];
//        }
//
//        return is_string($joinType) ? $joinType : 'INNER JOIN';
//    }

//    protected function getTableNameAndAlias()
//    {
//        if (empty($this->from)) {
//            $tableName = $this->getPrimaryTableName();
//        } else {
//            $tableName = '';
//            // if the first entry in "from" is an alias-tablename-pair return it directly
//            foreach ($this->from as $alias => $tableName) {
//                if (is_string($alias)) {
//                    return [$tableName, $alias];
//                }
//                break;
//            }
//        }
//
//        if (preg_match('/^(.*?)\s+({{\w+}}|\w+)$/', $tableName, $matches)) {
//            $alias = $matches[2];
//        } else {
//            $alias = $tableName;
//        }
//
//        return [$tableName, $alias];
//    }

//    private function joinWithRelation($parent, $child, $joinType)
//    {
//        $via = $child->via;
//        $child->via = null;
//        if ($via instanceof self) {
//            // via table
//            $this->joinWithRelation($parent, $via, $joinType);
//            $this->joinWithRelation($via, $child, $joinType);
//            return;
//        } elseif (is_array($via)) {
//            // via relation
//            $this->joinWithRelation($parent, $via[1], $joinType);
//            $this->joinWithRelation($via[1], $child, $joinType);
//            return;
//        }
//
//        list($parentTable, $parentAlias) = $parent->getTableNameAndAlias();
//        list($childTable, $childAlias) = $child->getTableNameAndAlias();
//
//        if (!empty($child->link)) {
//            if (strpos($parentAlias, '{{') === false) {
//                $parentAlias = '{{' . $parentAlias . '}}';
//            }
//            if (strpos($childAlias, '{{') === false) {
//                $childAlias = '{{' . $childAlias . '}}';
//            }
//
//            $on = [];
//            foreach ($child->link as $childColumn => $parentColumn) {
//                $on[] = "$parentAlias.[[$parentColumn]] = $childAlias.[[$childColumn]]";
//            }
//            $on = implode(' AND ', $on);
//            if (!empty($child->on)) {
//                $on = ['and', $on, $child->on];
//            }
//        } else {
//            $on = $child->on;
//        }
//        $this->join($joinType, empty($child->from) ? $childTable : $child->from, $on);
//
//        if (!empty($child->where)) {
//            $this->andWhere($child->where);
//        }
//        if (!empty($child->having)) {
//            $this->andHaving($child->having);
//        }
//        if (!empty($child->orderBy)) {
//            $this->addOrderBy($child->orderBy);
//        }
//        if (!empty($child->groupBy)) {
//            $this->addGroupBy($child->groupBy);
//        }
//        if (!empty($child->params)) {
//            $this->addParams($child->params);
//        }
//        if (!empty($child->join)) {
//            foreach ($child->join as $join) {
//                $this->join[] = $join;
//            }
//        }
//        if (!empty($child->union)) {
//            foreach ($child->union as $union) {
//                $this->union[] = $union;
//            }
//        }
//    }

//    public function onCondition($condition, $params = [])
//    {
//        $this->on = $condition;
//        $this->addParams($params);
//        return $this;
//    }

//    public function andOnCondition($condition, $params = [])
//    {
//        if ($this->on === null) {
//            $this->on = $condition;
//        } else {
//            $this->on = ['and', $this->on, $condition];
//        }
//        $this->addParams($params);
//        return $this;
//    }

//    public function orOnCondition($condition, $params = [])
//    {
//        if ($this->on === null) {
//            $this->on = $condition;
//        } else {
//            $this->on = ['or', $this->on, $condition];
//        }
//        $this->addParams($params);
//        return $this;
//    }

//    public function viaTable($tableName, $link, callable $callable = null)
//    {
//        $modelClass = $this->primaryModel ? get_class($this->primaryModel) : $this->modelClass;
//        $relation = new self($modelClass, [
//            'from' => [$tableName],
//            'link' => $link,
//            'multiple' => true,
//            'asArray' => true,
//        ]);
//        $this->via = $relation;
//        if ($callable !== null) {
//            call_user_func($callable, $relation);
//        }
//
//        return $this;
//    }

//    public function alias($alias)
//    {
//        if (empty($this->from) || count($this->from) < 2) {
//            list($tableName) = $this->getTableNameAndAlias();
//            $this->from = [$alias => $tableName];
//        } else {
//            $tableName = $this->getPrimaryTableName();
//
//            foreach ($this->from as $key => $table) {
//                if ($table === $tableName) {
//                    unset($this->from[$key]);
//                    $this->from[$alias] = $tableName;
//                }
//            }
//        }
//
//        return $this;
//    }

//    public function getTablesUsedInFrom()
//    {
//        if (empty($this->from)) {
//            return $this->cleanUpTableNames([$this->getPrimaryTableName()]);
//        }
//
//        return parent::getTablesUsedInFrom();
//    }


//    protected function getPrimaryTableName()
//    {
//        /* @var $modelClass ActiveRecord */
//        $modelClass = $this->modelClass;
//        return $modelClass::tableName();
//    }

}
