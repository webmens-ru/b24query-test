<?php

namespace app\models;

use app\models\b24\crm\CrmCompanyActiveRecord;
use app\models\b24\crm\CrmCategoryActiveRecord;
use app\models\b24\crm\CrmContactActiveRecord;

class B24Contact extends CrmContactActiveRecord
{

    //переделать
//    public function rules()
//    {
//
//        return [
//            // атрибут required указывает, что name, email, subject, body обязательны для заполнения
//            [[
//                "id",
//                "title",
//                "opened",
//            ], 'safe'],
//        ];
//    }

    /**
     * @return array
     */
//    public function attributeLabels()
//    {
//        return [
//            "id" => '',
//            "title" => '',
//            "opened" => '',
//        ];
//    }

//    public function fields()
//    {
//        return [
//            'id',
//            'title' => 'name',
//            //'opened',
//            //'test' => 'title'
//        ];
//    }

    /**
     * Переопределяет столбцы которые нужно выбирать из битрикс24 для оптинизации запроса
     * @return array
     */
    public function attributes()
    {
        return [
            'ID',
            'NAME',
            'LAST_NAME',
        ];
    }
}
