<?php

namespace app\modules\wm\b24;

use Bitrix24\B24Object;
use wm\b24tools\b24Tools;
use Yii;
use yii\base\Component;
use yii\base\NotSupportedException;
use yii\helpers\ArrayHelper;

//Код не универсален а направлен на смарт процессы стоит перенести в другой класс
class Query extends Component implements QueryInterface {


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

    /**
     * @var string|array|ExpressionInterface|пустое условие запроса. Это относится к предложению WHERE в операторе SQL.
     * Например, `['возраст' => 31, 'команда' => 1]`.
     * @see where() для корректного синтаксиса при указании этого значения.
     */
    public $where;
    /**
     * @var int|ExpressionInterface|null maximum number of records to be returned. May be an instance of [[ExpressionInterface]].
     * If not set or less than 0, it means no limit.
     */
    public $limit;
    /**
     * @var int|ExpressionInterface|null zero-based offset from where the records are to be returned.
     * May be an instance of [[ExpressionInterface]]. If not set or less than 0, it means starting from the beginning.
     */
    public $offset;
    /**
     * @var array|null how to sort the query results. This is used to construct the ORDER BY clause in a SQL statement.
     * The array keys are the columns to be sorted by, and the array values are the corresponding sort directions which
     * can be either [SORT_ASC](https://www.php.net/manual/en/array.constants.php#constant.sort-asc)
     * or [SORT_DESC](https://www.php.net/manual/en/array.constants.php#constant.sort-desc).
     * The array may also contain [[ExpressionInterface]] objects. If that is the case, the expressions
     * will be converted into strings without any change.
     */
    public $orderBy;
    /**
     * @var string|callable|null the name of the column by which the query results should be indexed by.
     * This can also be a callable (e.g. anonymous function) that returns the index value based on the given
     * row data. For more details, see [[indexBy()]]. This property is only used by [[QueryInterface::all()|all()]].
     */
    public $indexBy;
    /**
     * @var bool whether to emulate the actual query execution, returning empty or false results.
     * @see emulateExecution()
     * @since 2.0.11
     */
    public $emulateExecution = false;




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





    /**
     * Sets the [[indexBy]] property.
     * @param string|callable $column the name of the column by which the query results should be indexed by.
     * This can also be a callable (e.g. anonymous function) that returns the index value based on the given
     * row data. The signature of the callable should be:
     *
     * ```php
     * function ($row)
     * {
     *     // return the index value corresponding to $row
     * }
     * ```
     *
     * @return $this the query object itself
     */
    public function indexBy($column)
    {
        $this->indexBy = $column;
        return $this;
    }

    /**
     * Adds an additional WHERE condition to the existing one.
     * The new condition and the existing one will be joined using the 'AND' operator.
     * @param string|array|ExpressionInterface $condition the new WHERE condition. Please refer to [[where()]]
     * on how to specify this parameter.
     * @return $this the query object itself
     * @see where()
     * @see orWhere()
     */
    public function andWhere($condition)
    {
        \Yii::warning($condition, 'andWhere($condition)');
        $condition = $this->conditionPrepare($condition);
        if ($this->where === null) {
            $this->where = $condition;
        } else {
            $this->where = array_merge($this->where, $condition);
        }
        \Yii::warning($this->where, '$this->where3');
        return $this;
    }

    public function conditionPrepare($condition){
        if(array_key_exists(0, $condition)){
            if(count($condition)==3){
                $arr = [];
                $operator = array_shift($condition);
                $arr[$operator.$condition[0]] = $condition[1];
                return $arr;
            }
            return [];
        }else{
            return $condition;
        }

    }

//    public function conditionPrepare($condition){
//        if(count($condition)==3){
//            $operator = array_shift($condition);
//            $condition[0] = $operator.$condition[0];
//        }
//        return $condition;
//    }

    /**
     * Adds an additional WHERE condition to the existing one.
     * The new condition and the existing one will be joined using the 'OR' operator.
     * @param string|array|ExpressionInterface $condition the new WHERE condition. Please refer to [[where()]]
     * on how to specify this parameter.
     * @return $this the query object itself
     * @see where()
     * @see andWhere()
     */
    public function orWhere($condition)
    {
        if ($this->where === null) {
            $this->where = $condition;
        } else {
            $this->where = ['or', $this->where, $condition];
        }

        return $this;
    }

    /**
     * Sets the WHERE part of the query but ignores [[isEmpty()|empty operands]].
     *
     * This method is similar to [[where()]]. The main difference is that this method will
     * remove [[isEmpty()|empty query operands]]. As a result, this method is best suited
     * for building query conditions based on filter values entered by users.
     *
     * The following code shows the difference between this method and [[where()]]:
     *
     * ```php
     * // WHERE `age`=:age
     * $query->filterWhere(['name' => null, 'age' => 20]);
     * // WHERE `age`=:age
     * $query->where(['age' => 20]);
     * // WHERE `name` IS NULL AND `age`=:age
     * $query->where(['name' => null, 'age' => 20]);
     * ```
     *
     * Note that unlike [[where()]], you cannot pass binding parameters to this method.
     *
     * @param array $condition the conditions that should be put in the WHERE part.
     * See [[where()]] on how to specify this parameter.
     * @return $this the query object itself
     * @see where()
     * @see andFilterWhere()
     * @see orFilterWhere()
     */
    public function filterWhere(array $condition)
    {
        $condition = $this->filterCondition($condition);
        if ($condition !== []) {
            $this->where($condition);
        }

        return $this;
    }

    /**
     * Adds an additional WHERE condition to the existing one but ignores [[isEmpty()|empty operands]].
     * The new condition and the existing one will be joined using the 'AND' operator.
     *
     * This method is similar to [[andWhere()]]. The main difference is that this method will
     * remove [[isEmpty()|empty query operands]]. As a result, this method is best suited
     * for building query conditions based on filter values entered by users.
     *
     * @param array $condition the new WHERE condition. Please refer to [[where()]]
     * on how to specify this parameter.
     * @return $this the query object itself
     * @see filterWhere()
     * @see orFilterWhere()
     */
    public function andFilterWhere(array $condition)
    {
//        \Yii::warning('andFilterWhere');
        $condition = $this->filterCondition($condition);
        if ($condition !== []) {
            $this->andWhere($condition);
        }

        return $this;
    }

    /**
     * Adds an additional WHERE condition to the existing one but ignores [[isEmpty()|empty operands]].
     * The new condition and the existing one will be joined using the 'OR' operator.
     *
     * This method is similar to [[orWhere()]]. The main difference is that this method will
     * remove [[isEmpty()|empty query operands]]. As a result, this method is best suited
     * for building query conditions based on filter values entered by users.
     *
     * @param array $condition the new WHERE condition. Please refer to [[where()]]
     * on how to specify this parameter.
     * @return $this the query object itself
     * @see filterWhere()
     * @see andFilterWhere()
     */
    public function orFilterWhere(array $condition)
    {
        $condition = $this->filterCondition($condition);
        if ($condition !== []) {
            $this->orWhere($condition);
        }

        return $this;
    }

    /**
     * Removes [[isEmpty()|empty operands]] from the given query condition.
     *
     * @param array $condition the original condition
     * @return array the condition with [[isEmpty()|empty operands]] removed.
     * @throws NotSupportedException if the condition operator is not supported
     */
    protected function filterCondition($condition)
    {
        if (!is_array($condition)) {
            return $condition;
        }

        if (!isset($condition[0])) {
            \Yii::warning($condition, 'if (!isset($condition[0]))');
            // hash format: 'column1' => 'value1', 'column2' => 'value2', ...
            foreach ($condition as $name => $value) {
                if ($this->isEmpty($value)) {
                    unset($condition[$name]);
                }
            }

            return $condition;
        }

        // operator format: operator, operand 1, operand 2, ...

        $operator = array_shift($condition);

        switch (strtoupper($operator)) {
            case 'NOT':
            case 'AND':
            case 'OR':
                foreach ($condition as $i => $operand) {
                    $subCondition = $this->filterCondition($operand);
                    if ($this->isEmpty($subCondition)) {
                        unset($condition[$i]);
                    } else {
                        $condition[$i] = $subCondition;
                    }
                }

                if (empty($condition)) {
                    return [];
                }
                break;
            case 'BETWEEN':
            case 'NOT BETWEEN':
                if (array_key_exists(1, $condition) && array_key_exists(2, $condition)) {
                    if ($this->isEmpty($condition[1]) || $this->isEmpty($condition[2])) {
                        return [];
                    }
                }
                break;
            default:
//                \Yii::warning($condition, 'default');
                if (array_key_exists(1, $condition) && $this->isEmpty($condition[1])) {
//                    \Yii::warning($condition, 'default1');
                    return [];
                }
        }

        array_unshift($condition, $operator);

        return $condition;
    }

    /**
     * Returns a value indicating whether the give value is "empty".
     *
     * The value is considered "empty", if one of the following conditions is satisfied:
     *
     * - it is `null`,
     * - an empty string (`''`),
     * - a string containing only whitespace characters,
     * - or an empty array.
     *
     * @param mixed $value
     * @return bool if the value is empty
     */
    protected function isEmpty($value)
    {
        return $value === '' || $value === [] || $value === null || is_string($value) && trim($value) === '';
    }

    /**
     * Sets the ORDER BY part of the query.
     * @param string|array|ExpressionInterface $columns the columns (and the directions) to be ordered by.
     * Columns can be specified in either a string (e.g. `"id ASC, name DESC"`) or an array
     * (e.g. `['id' => SORT_ASC, 'name' => SORT_DESC]`).
     *
     * The method will automatically quote the column names unless a column contains some parenthesis
     * (which means the column contains a DB expression).
     *
     * Note that if your order-by is an expression containing commas, you should always use an array
     * to represent the order-by information. Otherwise, the method will not be able to correctly determine
     * the order-by columns.
     *
     * Since version 2.0.7, an [[ExpressionInterface]] object can be passed to specify the ORDER BY part explicitly in plain SQL.
     * @return $this the query object itself
     * @see addOrderBy()
     */
    public function orderBy($columns)
    {
        $this->orderBy = $this->normalizeOrderBy($columns);
        return $this;
    }

    /**
     * Adds additional ORDER BY columns to the query.
     * @param string|array|ExpressionInterface $columns the columns (and the directions) to be ordered by.
     * Columns can be specified in either a string (e.g. "id ASC, name DESC") or an array
     * (e.g. `['id' => SORT_ASC, 'name' => SORT_DESC]`).
     *
     * The method will automatically quote the column names unless a column contains some parenthesis
     * (which means the column contains a DB expression).
     *
     * Note that if your order-by is an expression containing commas, you should always use an array
     * to represent the order-by information. Otherwise, the method will not be able to correctly determine
     * the order-by columns.
     *
     * Since version 2.0.7, an [[ExpressionInterface]] object can be passed to specify the ORDER BY part explicitly in plain SQL.
     * @return $this the query object itself
     * @see orderBy()
     */
    public function addOrderBy($columns)//['id'=>4]
    {
        $columns = $this->normalizeOrderBy($columns);
        foreach ($columns as $key=>$value){
            $temp = [];
            if($value == SORT_ASC){
                $temp[$key] = 'ASC';
            }elseif($value == SORT_DESC){
                $temp[$key] = 'DESC';
            }
            $columns = array_merge($columns, $temp);
        }
        if ($this->orderBy === null) {
            $this->orderBy = $columns;
        } else {
            $this->orderBy = array_merge($this->orderBy, $columns);
        }

        return $this;
    }

    /**
     * Normalizes format of ORDER BY data.
     *
     * @param array|string|ExpressionInterface $columns the columns value to normalize. See [[orderBy]] and [[addOrderBy]].
     * @return array
     */
    protected function normalizeOrderBy($columns)
    {
        if ($columns instanceof ExpressionInterface) {
            return [$columns];
        } elseif (is_array($columns)) {
            return $columns;
        }

        $columns = preg_split('/\s*,\s*/', trim($columns), -1, PREG_SPLIT_NO_EMPTY);
        $result = [];
        foreach ($columns as $column) {
            if (preg_match('/^(.*?)\s+(asc|desc)$/i', $column, $matches)) {
                $result[$matches[1]] = strcasecmp($matches[2], 'desc') ? SORT_ASC : SORT_DESC;
            } else {
                $result[$column] = SORT_ASC;
            }
        }

        return $result;
    }

    /**
     * Sets the LIMIT part of the query.
     * @param int|ExpressionInterface|null $limit the limit. Use null or negative value to disable limit.
     * @return $this the query object itself
     */
    public function limit($limit)
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * Устанавливает часть запроса OFFSET.
     * @param int|ExpressionInterface|null $offset смещение. Используйте нулевое или отрицательное значение, чтобы отключить смещение.
     * @return $это сам объект запроса
     */
    public function offset($offset)
    {
        //TODO Переписать-проверить
        $this->offset = $offset;
        return $this;
    }

    /**
     * Sets whether to emulate query execution, preventing any interaction with data storage.
     * After this mode is enabled, methods, returning query results like [[QueryInterface::one()]],
     * [[QueryInterface::all()]], [[QueryInterface::exists()]] and so on, will return empty or false values.
     * You should use this method in case your program logic indicates query should not return any results, like
     * in case you set false where condition like `0=1`.
     * @param bool $value whether to prevent query execution.
     * @return $this the query object itself.
     * @since 2.0.11
     */
    public function emulateExecution($value = true)
    {
        $this->emulateExecution = $value;
        return $this;
    }

}
