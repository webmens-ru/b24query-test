<?php
namespace app\models;

use app\models\b24\crm\SpActiveRecord;

class SpTest extends SpActiveRecord {

    public static function entityTypeId() {
        return 174;
    }

    //переделать
    public function rules() {
        $parentRules = parent::rules();
        //$rules = [];
        return array_merge($parentRules, $rules);
    }

    public function attributeLabels() {
        $parentAttributeLabels = parent::attributeLabels();
        $attributeLabels = [];
        return array_merge($parentAttributeLabels, $attributeLabels);
    }
}
