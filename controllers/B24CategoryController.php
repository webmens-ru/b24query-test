<?php

namespace app\controllers;

use app\models\B24Category;
use app\models\B24CategorySearch;

class B24CategoryController extends B24ActiveRestController
{
    public $modelClass = B24Category::class;
    public $modelClassSearch = B24CategorySearch::class;
}