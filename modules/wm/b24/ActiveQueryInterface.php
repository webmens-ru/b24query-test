<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\modules\wm\b24;

interface ActiveQueryInterface extends QueryInterface
{
    public function asArray($value = true);

    public function one($auth = null);

//    public function indexBy($column);

//    public function with();

//    public function via($relationName, callable $callable = null);

//    public function findFor($name, $model);
}
