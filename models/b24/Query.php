<?php

namespace app\models\b24;

use Bitrix24\B24Object;
use wm\b24tools\b24Tools;
use Yii;
use yii\base\Component;
use yii\helpers\ArrayHelper;

//Код не универсален а направлен на смарт процессы стоит перенести в другой класс
class Query extends Component implements QueryInterface {

    use QueryTrait;
    public $select;
    public $selectOption;
    public $distinct;
    public $from;
    public $groupBy;
    public $join;
    public $having;
    public $union;
    public $withQueries;

    /**
     * Список массивов @var значений параметров запроса, проиндексированных заполнителями параметров.
     * Например, `[':name' => 'Дэн', ':age' => 31]`.
     */
    public $params = [];

    public $queryCacheDuration;
    public $queryCacheDependency;

    //public $emulateExecution;//  изучить тип и место где должен быть.
    public $indexBy;




//Подумать о конструкторе
    const EVENT_INIT = 'init';

//    public function __construct( $config = [])
//    {
//        $this->modelClass = $modelClass;
//        parent::__construct($config);
//    }

    public function init()
    {
        parent::init();
        $this->trigger(self::EVENT_INIT);
    }

//    public $params = [
//        'entityTypeId' => 174
//        /*, 'filter' => $query->filter*/];

    //public $params = [];

    //private $_filter = [];

    //private $_method = '';

    public $method = '';

    //private $_start = 0;

    //public $limit = 0;

    public $entityTypeId;

    //private $_entityTypeId = 0;

    //private $_entityTypeId = 174;

    public $modelClass;

    public static function oneDataSelector()
    {
        return 'result';
    }

    public function all($auth = null){
//        Yii::warning('function all($auth = null)');
//        Yii::warning($this->where, '$this->where1');
//        Yii::warning($this->params, '$this->params1');
        if ($this->emulateExecution) {
            return [];
        }
        $this->prepairParams();
//        Yii::warning($this->where, '$this->where2');
//        Yii::warning($this->params, '$this->params2');
        //TODO вынести часть логики
        $component = new b24Tools();
        $b24App = null;// $component->connectFromUser($auth);
        if($auth === null){
            Yii::warning('connectFromAdmin');
            $b24App = $component->connectFromAdmin();
        }else{
            Yii::warning('connectFromUser');
            $b24App = $component->connectFromUser($auth);
        }
        $obB24 = new B24Object($b24App);
        $rows = [];
        //TODO Исправить
        if(!$this->limit){
            Yii::warning($this->limit, '$this->limit');
            //TODO передавать в функцию limit и ofset
            $rows = $this->getFullData($obB24);
        }else{
            Yii::warning($this->limit, '$this->limit');
            $rows = $this->getData($obB24);
        }
        //TODO Нужно ли здесь делать populate
        return $this->populate($rows);
    }

    public function populate($rows)
    {
        //$result = $rows;
        if ($this->indexBy === null) {
            return $rows;
        }
        $result = [];
        foreach ($rows as $row) {
            $result[ArrayHelper::getValue($row, $this->indexBy)] = $row;

        }

        return $result;
    }

//    private function getData($obB24){
//        $request =  $obB24->client->call($this->method, $this->params);
//        return ArrayHelper::getValue($request, 'result.items');
//
//    }

//    public function params(){
//        return [];
//    }

//    private function getFullData($obB24){
//        Yii::warning('getFullData($obB24)');
//        $request = $obB24->client->call($this->method, $this->params);;
//        $countCalls = (int)ceil($request['total'] / $obB24->client::MAX_BATCH_CALLS);
//        $data = ArrayHelper::getValue($request, 'result.items');
//        Yii::warning($data, '$data');
//        for ($i = 1; $i < $countCalls; $i++)
//            $obB24->client->addBatchCall('crm.item.list', [
//                'entityTypeId' => $this->entityTypeId,
//                'filter' => $this->where,
//                'start' => $obB24->client::MAX_BATCH_CALLS * $i,
//            ], function ($result) use (&$data) {
//                $data = array_merge($data, ArrayHelper::getValue($result, 'result.items'));
//            });
//        $obB24->client->processBatchCalls();
//        Yii::warning($data, '$data');
//        return $data; //Добавить вывод дополнительной информации
//    }

//    public function andFilterCompare($name, $value, $defaultOperator = '=') {
//        $arr = [];
//        //убираем '[ и ']' в начале и в конце строки в запросе
//        if ((substr($value, 0, 1) == '[') && (substr($value, -1, 1) == ']')) {
//            $data = substr($value, 1, -1);
//            $arr = explode(',', $data);
//            foreach ($arr as $var) {
//                $this->andFilterCompare($name, $var);
//            }
//            return $this;
//        } else {
//            if (preg_match('/^(<>|>=|>|<=|<|=)/', $value, $matches)) {
//                $operator = $matches[1];
//                $value = substr($value, strlen($operator));
//            } elseif ($value == 'isNull') {
//                return $this->andWhere([$name => null]);
//            } elseif (preg_match('/^(%%)/', $value, $matches)) {
//                $operator = $matches[1];
//                $value = substr($value, strlen($operator));
//                $operator = 'like';
//            } elseif (preg_match('/^(in\[.*])/', $value, $matches)) {
//                $operator = 'in';
//                $value = explode(',', mb_substr($value, 3, -1));
//            } else {
//                $operator = $defaultOperator;
//            }
//            return $this->andFilterWhere([$operator, $name, $value]);
//        }
//    }

    public function andFilterCompare($name, $value, $defaultOperator = '=') {
        Yii::warning([$name, $value], 'andFilterCompare');
//        if (preg_match('/^(<>|>=|>|<=|<|=)/', (string)$value, $matches)) {
//            $operator = $matches[1];
//            $value = substr($value, strlen($operator));
//        } else {
//            $operator = $defaultOperator;
//        }
//
//        return $this->andFilterWhere([$operator, $name, $value]);


        //$filter = [];
        //убираем '[' и ']' в начале и в конце строки в запросе
        if ((substr($value, 0, 1) == '[') && (substr($value, -1, 1) == ']')) {
            $data = substr($value, 1, -1);
            $arr = explode(',', $data);
            foreach ($arr as $var) {
                $this->andFilterCompare($name, $var);
            }
//            Yii::warning($arr, 'return1');
            return $this;
        } else {
            if (preg_match('/^(>=|>|<=|<|=)/', $value, $matches)) {
                $operator = $matches[1];
                $value = substr($value, strlen($operator));
            }
            elseif (preg_match('/^(<>)/', $value, $matches)) {
                $operator = '!=';
                $value = substr($value, strlen($operator));
            }
//            elseif ($str == 'isNull') {
//                return $this->andWhere([$name => null]);
//            } elseif (preg_match('/^(%%)/', $str, $matches)) {
//                $operator = $matches[1];
//                $value = substr($str, strlen($operator));
//                $operator = 'like';
//            } elseif (preg_match('/^(in\[.*\])/', $str, $matches)) {
//                $operator = 'in';
//                $value = explode(',', mb_substr($str, 3, -1));
//            }
            else {
                $operator = $defaultOperator;
            }
//            $c = $operator.$name." ".$value;
//            Yii::warning([$operator, $name, $value], 'return2');
            $this->andFilterWhere([$operator, $name, $value]);
            Yii::warning(ArrayHelper::toArray($this),'ArrayHelper::toArray($this)');
            return $this;

        }
    }

//    public function andFilterWhere($params){
//        if($params[2]){
//            $this->filter[$params[0].$params[1]] = $params[2];
//        }
//
//        return $this;
//
//    }

//    public function getEntityTypeIdUsedInFrom()
//    {
//        if (empty($this->entityTypeId)) {
//            $this->entityTypeId = $this->getPrimaryTableName();
//        }
//
//        return $this->entityTypeId;
//
//        //return parent::getEntityTypeIdUsedInFrom();
//    }

//    protected function getPrimaryTableName()
//    {
//        $modelClass = $this->modelClass;
//        //return $modelClass::tableName();
//        return $modelClass::entityTypeId();
//    }

    protected function prepairParams(){
        //$this->getEntityTypeIdUsedInFrom();/
        $data = [
            //'entityTypeId' => $this->entityTypeId,
            'filter' => $this->where,
            'order' => $this->orderBy

//            Остальные параметры
        ];
        Yii::warning($data, '$data');
        $this->params = $data;
    }

    protected function prepairOneParams(){
        //$this->getEntityTypeIdUsedInFrom();/
        $data = [
        ];
        Yii::warning($data, '$data');
        $this->params = $data;
    }

    public function __toString()
    {
        return serialize($this);
    }

//    public function union($sql, $all = false)
//    {
//        $this->union[] = ['query' => $sql, 'all' => $all];
//        return $this;
//    }

//    public function addGroupBy($columns)
//    {
//        if ($columns instanceof ExpressionInterface) {
//            $columns = [$columns];
//        } elseif (!is_array($columns)) {
//            $columns = preg_split('/\s*,\s*/', trim($columns), -1, PREG_SPLIT_NO_EMPTY);
//        }
//        if ($this->groupBy === null) {
//            $this->groupBy = $columns;
//        } else {
//            $this->groupBy = array_merge($this->groupBy, $columns);
//        }
//
//        return $this;
//    }

//    public function groupBy($columns)
//    {
//        if ($columns instanceof ExpressionInterface) {
//            $columns = [$columns];
//        } elseif (!is_array($columns)) {
//            $columns = preg_split('/\s*,\s*/', trim($columns), -1, PREG_SPLIT_NO_EMPTY);
//        }
//        $this->groupBy = $columns;
//        return $this;
//    }

//    public function rightJoin($table, $on = '', $params = [])
//    {
//        $this->join[] = ['RIGHT JOIN', $table, $on];
//        return $this->addParams($params);
//    }

//    public function leftJoin($table, $on = '', $params = [])
//    {
//        $this->join[] = ['LEFT JOIN', $table, $on];
//        return $this->addParams($params);
//    }

//    public function innerJoin($table, $on = '', $params = [])
//    {
//        $this->join[] = ['INNER JOIN', $table, $on];
//        return $this->addParams($params);
//    }

//    public function join($type, $table, $on = '', $params = [])
//    {
//        $this->join[] = [$type, $table, $on];
//        return $this->addParams($params);
//    }

//    public function orWhere($condition, $params = [])
//    {
//        if ($this->where === null) {
//            $this->where = $condition;
//        } else {
//            $this->where = ['or', $this->where, $condition];
//        }
//        $this->addParams($params);
//        return $this;
//    }

    public function addParams($params)
    {
        //TODO Проверить
        if (!empty($params)) {
            if (empty($this->params)) {
                $this->params = $params;
            } else {
                foreach ($params as $name => $value) {
                    if (is_int($name)) {
                        $this->params[] = $value;
                    } else {
                        $this->params[$name] = $value;
                    }
                }
            }
        }

        return $this;
    }

//    public function andWhere($condition, $params = [])
//    {
//        //TODO Переписать
//        if ($this->where === null) {
//            $this->where = $condition;
//        } elseif (is_array($this->where) && isset($this->where[0]) && strcasecmp($this->where[0], 'and') === 0) {
//            $this->where[] = $condition;
//        } else {
//            $this->where = ['and', $this->where, $condition];
//        }
//        $this->addParams($params);
//        return $this;
//    }

    public function where($condition, $params = [])
    {
        $this->where = $this->conditionPrepare($condition);
        Yii::warning($this->where, '$this->where');
        $this->addParams($params);
        return $this;
    }

//    public function from($tables)
//    {
//        if ($tables instanceof ExpressionInterface) {
//            $tables = [$tables];
//        }
//        if (is_string($tables)) {
//            $tables = preg_split('/\s*,\s*/', trim($tables), -1, PREG_SPLIT_NO_EMPTY);
//        }
//        $this->from = $tables;
//        return $this;
//    }

//    public function distinct($value = true)
//    {
//        $this->distinct = $value;
//        return $this;
//    }

//    protected function getUnaliasedColumnsFromSelect()
//    {
//        $result = [];
//        if (is_array($this->select)) {
//            foreach ($this->select as $name => $value) {
//                if (is_int($name)) {
//                    $result[] = $value;
//                }
//            }
//        }
//        return array_unique($result);
//    }

//    protected function getUniqueColumns($columns)
//    {
//        $unaliasedColumns = $this->getUnaliasedColumnsFromSelect();
//
//        $result = [];
//        foreach ($columns as $columnAlias => $columnDefinition) {
//            if (!$columnDefinition instanceof Query) {
//                if (is_string($columnAlias)) {
//                    $existsInSelect = isset($this->select[$columnAlias]) && $this->select[$columnAlias] === $columnDefinition;
//                    if ($existsInSelect) {
//                        continue;
//                    }
//                } elseif (is_int($columnAlias)) {
//                    $existsInSelect = in_array($columnDefinition, $unaliasedColumns, true);
//                    $existsInResultSet = in_array($columnDefinition, $result, true);
//                    if ($existsInSelect || $existsInResultSet) {
//                        continue;
//                    }
//                }
//            }
//
//            $result[$columnAlias] = $columnDefinition;
//        }
//        return $result;
//    }

//    protected function normalizeSelect($columns)
//    {
//        if ($columns instanceof ExpressionInterface) {
//            $columns = [$columns];
//        } elseif (!is_array($columns)) {
//            $columns = preg_split('/\s*,\s*/', trim($columns), -1, PREG_SPLIT_NO_EMPTY);
//        }
//        $select = [];
//        foreach ($columns as $columnAlias => $columnDefinition) {
//            if (is_string($columnAlias)) {
//                // Already in the normalized format, good for them
//                $select[$columnAlias] = $columnDefinition;
//                continue;
//            }
//            if (is_string($columnDefinition)) {
//                if (
//                    preg_match('/^(.*?)(?i:\s+as\s+|\s+)([\w\-_\.]+)$/', $columnDefinition, $matches) &&
//                    !preg_match('/^\d+$/', $matches[2]) &&
//                    strpos($matches[2], '.') === false
//                ) {
//                    // Using "columnName as alias" or "columnName alias" syntax
//                    $select[$matches[2]] = $matches[1];
//                    continue;
//                }
//                if (strpos($columnDefinition, '(') === false) {
//                    // Normal column name, just alias it to itself to ensure it's not selected twice
//                    $select[$columnDefinition] = $columnDefinition;
//                    continue;
//                }
//            }
//            // Either a string calling a function, DB expression, or sub-query
//            $select[] = $columnDefinition;
//        }
//        return $select;
//    }

//    public function addSelect($columns)
//    {
//        if ($this->select === null) {
//            return $this->select($columns);
//        }
//        if (!is_array($this->select)) {
//            $this->select = $this->normalizeSelect($this->select);
//        }
//        $this->select = array_merge($this->select, $this->normalizeSelect($columns));
//
//        return $this;
//    }

//    public function select($columns, $option = null)
//    {
//        $this->select = $this->normalizeSelect($columns);
//        $this->selectOption = $option;
//        return $this;
//    }

    public function exists($db = null)
    {
        //TODO Переписать
        if ($this->emulateExecution) {
            return false;
        }
        $command = $this->createCommand($db);
        $params = $command->params;
        $command->setSql($command->db->getQueryBuilder()->selectExists($command->getSql()));
        $command->bindValues($params);
        return (bool) $command->queryScalar();
    }

//    public function max($q, $db = null)
//    {
//        return $this->queryScalar("MAX($q)", $db);
//    }

//    public function min($q, $db = null)
//    {
//        return $this->queryScalar("MIN($q)", $db);
//    }

//    public function average($q, $db = null)
//    {
//        if ($this->emulateExecution) {
//            return 0;
//        }
//
//        return $this->queryScalar("AVG($q)", $db);
//    }

//    public function sum($q, $db = null)
//    {
//        if ($this->emulateExecution) {
//            return 0;
//        }
//
//        return $this->queryScalar("SUM($q)", $db);
//    }

    public function count($q = '*', $db = null)
    {
        //TODO Переписать
        if ($this->emulateExecution) {
            return 0;
        }

        return $this->queryScalar("COUNT($q)", $db);
    }

//    public function column($db = null)
//    {
//        if ($this->emulateExecution) {
//            return [];
//        }
//
//        if ($this->indexBy === null) {
//            return $this->createCommand($db)->queryColumn();
//        }
//
//        if (is_string($this->indexBy) && is_array($this->select) && count($this->select) === 1) {
//            if (strpos($this->indexBy, '.') === false && count($tables = $this->getTablesUsedInFrom()) > 0) {
//                $this->select[] = key($tables) . '.' . $this->indexBy;
//            } else {
//                $this->select[] = $this->indexBy;
//            }
//        }
//        $rows = $this->createCommand($db)->queryAll();
//        $results = [];
//        $column = null;
//        if (is_string($this->indexBy)) {
//            if (($dotPos = strpos($this->indexBy, '.')) === false) {
//                $column = $this->indexBy;
//            } else {
//                $column = substr($this->indexBy, $dotPos + 1);
//            }
//        }
//        foreach ($rows as $row) {
//            $value = reset($row);
//
//            if ($this->indexBy instanceof \Closure) {
//                $results[call_user_func($this->indexBy, $row)] = $value;
//            } else {
//                $results[$row[$column]] = $value;
//            }
//        }
//
//        return $results;
//    }

//    public function scalar($db = null)
//    {
//        if ($this->emulateExecution) {
//            return null;
//        }
//
//        return $this->createCommand($db)->queryScalar();
//    }

    public function one($auth = null)
    {
        //TODO Переписать
        if ($this->emulateExecution) {
            return false;
        }

        $this->prepairOneParams();

        $component = new b24Tools();
        $b24App = null;// $component->connectFromUser($auth);
        if($auth === null){
            Yii::warning('connectFromAdmin');
            $b24App = $component->connectFromAdmin();
        }else{
            Yii::warning('connectFromUser');
            $b24App = $component->connectFromUser($auth);
        }
        $obB24 = new B24Object($b24App);


//        $this->method = call_user_func([$this->modelClass, 'listMethod']);
//        $this->listDataSelector = $this->getListDataSelector();
//        $request = $obB24->client->call($this->method, $this->params);
//        $countCalls = (int)ceil($request['total'] / $obB24->client::MAX_BATCH_CALLS);
//        $data = ArrayHelper::getValue($request, $this->listDataSelector);
//        Yii::warning($data, '$data');
//        if (count($data) != $request['total']) {
//            for ($i = 1; $i < $countCalls; $i++)
//                $obB24->client->addBatchCall($this->method,
//                    array_merge($this->params, ['start' => $obB24->client::MAX_BATCH_CALLS * $i]),
//                    function ($result) use (&$data) {
//                        $data = array_merge($data, ArrayHelper::getValue($result, $this->listDataSelector));
//                        Yii::warning($data, '$data1');
//                    }
//                );
//            $obB24->client->processBatchCalls();
//        }
//        return $data; //Добавить вывод дополнительной информации

        $this->dataSelector = $this->oneDataSelector();
        $this->method = call_user_func([$this->modelClass, 'oneMethod']);
        $data = $obB24->client->call($this->method, $this->params);
        $row = ArrayHelper::getValue($data, $this->dataSelector);
        Yii::warning($row, '$result');
        return $row;
        //TODO Нужно ли здесь делать populate
        //return $this->populate([$rows]);

//        return $this->createCommand($db)->queryOne();
    }

//    public function prepare($builder)
//    {
//        return $this;
//    }

//    public function createCommand($db = null)
//    {
//        if ($db === null) {
//            $db = Yii::$app->getDb();
//        }
//        list($sql, $params) = $db->getQueryBuilder()->build($this);
//
//        $command = $db->createCommand($sql, $params);
//        $this->setCommandCache($command);
//
//        return $command;
//    }

}
