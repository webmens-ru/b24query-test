<?php

namespace app\models;

use app\models\b24\crm\SpActiveRecord;

class B24SpTest extends SpActiveRecord
{
    public static function entityTypeId()
    {
        return 174;
    }

    public function getCategory()
    {
        return $this->hasOne(B24Category::class, ['id' => 'categoryId']);
    }

    public function getCompany()
    {
        return $this->hasOne(B24Company::class, ['ID' => 'companyId']);
    }

    public function getContact()
    {
        return $this->hasOne(B24Contact::class, ['ID' => 'contactId']);
    }

    public function getCreatedBy0()
    {
        return $this->hasOne(B24User::class, ['ID' => 'createdBy']);
    }

    public function getUpdatedBy0()
    {
        return $this->hasOne(B24User::class, ['ID' => 'updatedBy']);
    }

    public function getAssignedBy()
    {
        return $this->hasOne(B24User::class, ['ID' => 'assignedById']);
    }
    public function getObservers0()
    {
        return $this->hasOne(B24User::class, ['ID' => 'observers']);
    }

    public function getMovedBy0()
    {
        return $this->hasOne(B24User::class, ['ID' => 'movedBy']);
    }

    public function getStage()
    {
        return $this->hasOne(B24Stage::class, ['STATUS_ID' => 'stageId']);
    }

    public function getPreviousStage()
    {
        return $this->hasOne(B24Stage::class, ['STATUS_ID' => 'previousStageId']);
    }

    public function getSource()
    {
        return $this->hasOne(B24Source::class, ['STATUS_ID' => 'sourceId']);
    }

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
//            'title',
//            'xmlId',
//            'createdTime',
//            'updatedTime',
//            'createdBy' => function () {
//                $res = [
//                    'id' => null,
//                    'title' => ''
//                ];
//                if ($this->createdBy0) {
//                    $res['id'] = $this->createdBy0->ID;
//                    $res['title'] = $this->createdBy0->LAST_NAME.' '.$this->createdBy0->NAME;
//                }
//                return $res;
//            },
//            //'createdBy0',
//            'updatedBy'=> function () {
//                $res = [
//                    'id' => null,
//                    'title' => ''
//                ];
//                if ($this->updatedBy0) {
//                    $res['id'] = $this->updatedBy0->ID;
//                    $res['title'] = $this->updatedBy0->LAST_NAME.' '.$this->updatedBy0->NAME;
//                }
//                return $res;
//            },
////        'updatedBy0',
//            'assignedById',
//            'assignedBy' => function () {
//                $res = [
//                    'id' => null,
//                    'title' => ''
//                ];
//                if ($this->assignedBy) {
//                    $res['id'] = $this->assignedBy->ID;
//                    $res['title'] = $this->assignedBy->LAST_NAME.' '.$this->assignedBy->NAME;
//                }
//                return $res;
//            },
//            'opened',
//            'webformId',
////            'categoryId',
//            'category',
////            'companyId',
////            'company',
//            'company' => function () {
//                $res = [
//                    'id' => null,
//                    'title' => ''
//                ];
//                if ($this->company) {
//                    $res['id'] = $this->company->ID;
//                    $res['title'] = $this->company->TITLE;
//                }
//                return $res;
//            },
////            'contactId',
//            'contact'=> function () {
//                $res = [
//                    'id' => null,
//                    'title' => ''
//                ];
//                if ($this->contact) {
//                    $res['id'] = $this->contact->ID;
//                    $res['title'] = $this->contact->LAST_NAME.' '.$this->contact->NAME;
//                }
//                return $res;
//            },
//            'observers'=> function () {
//                $res = [
//                    'id' => null,
//                    'title' => ''
//                ];
//                if ($this->observers) {
//                    $res['id'] = $this->observers0->ID;
//                    $res['title'] = $this->observers0->LAST_NAME.' '.$this->observers0->NAME;
//                }
//                return $res;
//            },
//            'movedBy'=> function () {
//                $res = [
//                    'id' => null,
//                    'title' => ''
//                ];
//                if ($this->movedBy) {
//                    $res['id'] = $this->movedBy0->ID;
//                    $res['title'] = $this->movedBy0->LAST_NAME.' '.$this->movedBy0->NAME;
//                }
//                return $res;
//            },
//            'stageId',
//            'stage'=> function () {
//                $res = [
//                    'id' => null,
//                    'title' => ''
//                ];
//                if ($this->stage) {
//                    $res['id'] = $this->stage->STATUS_ID;
//                    $res['title'] = $this->stage->NAME;
//                }
//                return $res;
//            },
//            'previousStageId',
//            'previousStage'=> function () {
//                $res = [
//                    'id' => null,
//                    'title' => ''
//                ];
//                if ($this->previousStageId) {
//                    $res['id'] = $this->previousStage->STATUS_ID;
//                    $res['title'] = $this->previousStage->NAME;
//                }
//                return $res;
//            },
//            'sourceId',
//            'source' => function () {
//                $res = [
//                    'id' => null,
//                    'title' => ''
//                ];
//                if ($this->sourceId) {
//                    $res['id'] = $this->source->STATUS_ID;
//                    $res['title'] = $this->source->NAME;
//                }
//                return $res;
//            },
//
//        ];
//    }

    /**
     * Переопределяет столбцы которые нужно выбирать из битрикс24 для оптинизации запроса
     * @return array
     */
//    public function attributes()
//    {
//        return [
//            'id',
//            'title',
//            'xmlId',
//            'createdTime',
//            'updatedTime',
//            'createdBy',
//            'updatedBy',
//            'assignedById',
//            'opened',
//            'webformId',
//            'categoryId',
//            'companyId',
//            'contactId',
//            'observers'
//            'movedTime'
//            'movedBy'
//            'stageId'
//        ];
//    }
}
