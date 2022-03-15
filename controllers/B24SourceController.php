<?php

namespace app\controllers;

use app\models\B24Source;
//use app\models\B24CategorySearch;

class B24SourceController extends B24ActiveRestController
{
    public $modelClass = B24Source::class;
    //public $modelClassSearch = B24CategorySearch::class;

    public function actionTest(){
        return $this->modelClass::find()->where(['id' => [11, 13]])->all();
    }

    public function actionOne(){
        return $this->modelClass::find()->where(['id' => 35])->one();
    }
}