<?php

namespace app\models;

use app\modules\wm\b24\user\UserActiveRecord;

class B24User extends UserActiveRecord
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
