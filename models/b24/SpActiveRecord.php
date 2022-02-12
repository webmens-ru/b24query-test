<?php

namespace app\models\b24;

//use yii\base\Model;
use Yii;


class SpActiveRecord extends \app\models\b24\ActiveRecord
{
    public static function entityTypeId()
    {
        return null;
    }

    public static function listMethod()
    {
        return 'crm.item.list';
    }


    public $id;
    public $xmlId;
    public $title;
    public $createdBy;
    public $updatedBy;
    public $movedBy;
    public $createdTime;
    public $updatedTime;
    public $movedTime;
    public $categoryId;
    public $opened;
    public $stageId;
    public $previousStageId;
    public $begindate;
    public $closedate;
    public $companyId;
    public $contactId;
    public $opportunity;
    public $isManualOpportunity;
    public $taxValue;
    public $currencyId;
    public $mycompanyId;
    public $sourceId;
    public $sourceDescription;
    public $webformId;
    public $assignedById;
    public $utmSource;
    public $utmMedium;
    public $utmCampaign;
    public $utmContent;
    public $utmTerm;
    public $entityTypeId;

    //переделать
    public function rules()
    {

        return [
            // атрибут required указывает, что name, email, subject, body обязательны для заполнения
            [[
                "id",
                "xmlId",
                "title",
                "createdBy",
                "updatedBy",
                "movedBy",
                "createdTime",
                "updatedTime",
                "movedTime",
                "categoryId",
                "opened",
                "stageId",
                "previousStageId",
                "begindate",
                "closedate",
                "companyId",
                "contactId",
                "opportunity",
                "isManualOpportunity",
                "taxValue",
                "currencyId",
                "mycompanyId",
                "sourceId",
                "sourceDescription",
                "webformId",
                "assignedById",
                "utmSource",
                "utmMedium",
                "utmCampaign",
                "utmContent",
                "utmTerm",
                "entityTypeId"
            ], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            "id" => '',
            "xmlId" => '',
            "title" => '',
            "createdBy" => '',
            "updatedBy" => '',
            "movedBy" => '',
            "createdTime" => '',
            "updatedTime" => '',
            "movedTime" => '',
            "categoryId" => '',
            "opened" => '',
            "stageId" => '',
            "previousStageId" => '',
            "begindate" => '',
            "closedate" => '',
            "companyId" => '',
            "contactId" => '',
            "opportunity" => '',
            "isManualOpportunity" => '',
            "taxValue" => '',
            "currencyId" => '',
            "mycompanyId" => '',
            "sourceId" => '',
            "sourceDescription" => '',
            "webformId" => '',
            "assignedById" => '',
            "utmSource" => '',
            "utmMedium" => '',
            "utmCampaign" => '',
            "utmContent" => '',
            "utmTerm" => '',
            "entityTypeId" => '',
        ];
    }

    public static function getFooter($models)
    {
        return [];
    }

    public static function find()
    {
        return Yii::createObject(SpActiveQuery::className(), [get_called_class()]);
    }

    public static function listDataSelector()
    {
        return 'result.items';
    }

    /**
     * Возвращает все столбцы сущности, может быть переопределена для оптимизации запроса
     * @return array
     */
    public function attributes()
    {
        // TODO loadDefaultValues($skipIfSet = true)
        //  Переписать для b24
        return array_keys(static::getTableSchema()->columns);
    }
}
