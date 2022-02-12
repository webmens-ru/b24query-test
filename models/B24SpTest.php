<?php
namespace app\models;

use app\models\b24\SpActiveRecord;

class B24SpTest extends SpActiveRecord {
    public static function entityTypeId() {
        return 174;
    }

    //переделать
    public function rules() {
        $parentRules = parent::rules();
        $rules = [];
        return array_merge($parentRules, $rules);
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        $parentAttributeLabels = parent::attributeLabels();
        $attributeLabels = [];
        return array_merge($parentAttributeLabels, $attributeLabels);
    }

    public function fields()
    {
        return [
            'id',
            'title'
        ];
        //return parent::fields();
    }

    /**
     * Переопределяет столбцы которые нужно выбирать из битрикс24 для оптинизации запроса
     * @return array
     */
    public function attributes()
    {
        return ['id', 'title'];
    }
}
