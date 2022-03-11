<?php

namespace app\controllers;

use app\models\B24Status;
use app\models\B24StatusSearch;

class B24StatusController extends B24ActiveRestController
{
    public $modelClass = B24Status::class;
    public $modelClassSearch = B24StatusSearch::class;
}