<?php

namespace app\controllers;
use app\models\B24SpTest;
use app\models\B24SpTestSearch;

class B24SpTestController extends B24spController {
    public $modelClass = B24SpTest::class;
    public $modelClassSearch = B24SpTestSearch::class;
}