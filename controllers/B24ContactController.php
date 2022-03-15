<?php

namespace app\controllers;

use app\models\B24Company;
use app\models\B24Contact;
use app\models\B24SpTest;
use app\models\B24SpTestSearch;

class B24ContactController extends B24ActiveRestController
{
    public $modelClass = B24Contact::class;
    //public $modelClassSearch = B24SpTestSearch::class;

    public function actions() {
        $actions = parent::actions();
//        $actions['data']['auth'] = function ()  {
//           $userId = Yii::$app->user->id;
//           $userModel = User::find()->where(['id' => $userId])->one();
//           $auth = $userModel->b24AccessParams;
//           return ArrayHelper::toArray(json_decode($auth));
//        };
//        $actions['data']['prepareSearchQuery'] = function($query, $requestParams){
//            return $query->with(['category']);
//        };
        return $actions;
    }
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
        return $this->modelClass::find()->where(['ID' => [38,58]])->all();
//        return $this->modelClass::find()->where(['id' => 8])->one();
    }

    public function actionOne(){
        return $this->modelClass::find()->where(['id' => 62])->one();
    }
}