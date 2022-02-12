<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\models\b24;

interface QueryInterface
{
    public function all($auth = null);

    public function one($auth = null);

    public function count($q = '*', $db = null);

    public function exists($db = null);

    public function indexBy($column);

    public function where($condition);

    public function andWhere($condition);

    //public function orWhere($condition);

    public function filterWhere(array $condition);

    public function andFilterWhere(array $condition);

    //public function orFilterWhere(array $condition);

    public function orderBy($columns);

    public function addOrderBy($columns);

    public function offset($offset);

    //public function emulateExecution($value = true);
}
