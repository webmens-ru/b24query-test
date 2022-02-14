<?php

namespace app\controllers;

use app\models\B24SpTest;
use app\models\B24SpTestSearch;

class B24SpTestController extends B24ActiveRestController
{
    public $modelClass = B24SpTest::class;
    public $modelClassSearch = B24SpTestSearch::class;

//    public function actions() {
//        $actions = parent::actions();
//        $actions['data']['auth'] = function ()  {
//           $userId = Yii::$app->user->id;
//           $userModel = User::find()->where(['id' => $userId])->one();
//           $auth = $userModel->b24AccessParams;
//           return ArrayHelper::toArray(json_decode($auth));
//        };
//        return $actions;
//    }
//
//    public function actions() {
//        $actions = parent::actions();
//        unset($actions['data']['auth']);
//        return $actions;
//    }
//
//
//
    public function actionTest()
    {
        return $this->modelClass::find()->where(['id' => 8])->all();
    }
}