<?php


namespace app\models\b24;


use yii\base\Model;

class TableSchema extends Model
{
    public $primaryKey = 'id';

    public $columns = [];

    /**
     * @return array
     */
    public function getColumnNames(): array
    {
        return [];
    }

}