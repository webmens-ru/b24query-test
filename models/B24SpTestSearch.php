<?php

namespace app\models;

use Bitrix24\B24Object;
use wm\b24tools\b24Tools;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use app\models\UsersKr;
use app\models\User;
use Yii;
use yii\data\ArrayDataProvider;

class B24SpTestSearch extends B24SpTest {

    public function rules() {
        return [[array_keys($this->attributeLabels()), 'string']];
    }

    public function prepareSearchQuery($query, $requestParams){
        Yii::warning($requestParams, 'prepareSearchQuery($query, $requestParams)');
        $this->load($requestParams,'');
        if (!$this->validate()) {
            $query->where('0=1');
            return $query;
        }
        foreach ($this->rules()[0][0] as $value) {
            $query->andFilterCompare($value, $this->{$value});
        }
        return $query;
    }

//$b24App = $component->connectFromUser($auth);
//$obB24 = new B24Object($b24App);

    public function search($params, $auth = null){
        $component = new b24Tools();
        $b24App = null;// $component->connectFromUser($auth);
        if($auth === null){
            $b24App = $component->connectFromAdmin();
        }else{
            $b24App = $component->connectFromUser($auth);
        }
        $obB24 = new B24Object($b24App);

        $this->load($params, '');

        $query = new B24Query();
        foreach ($this->rules()[0][0] as $value) {
            Yii::warning($value, '$value');
            $query->andFilterCompare($value, $this->{$value});
        }



        //$obB24Company = self::b24Connect();
        $request = $obB24->client->call('crm.item.list', ['entityTypeId' => self::entityTypeId(), 'filter' => $query->filter]);
        $countCalls = (int)ceil($request['total'] / $obB24->client::MAX_BATCH_CALLS);
        $data = $request['result']['items'];
        Yii::warning($data, '$data');
        for ($i = 1; $i < $countCalls; $i++)
            $obB24->client->addBatchCall('crm.item.list', [
                'entityTypeId' => self::entityTypeId(),
                'filter' => $query->filter,
                'start' => $obB24->client::MAX_BATCH_CALLS * $i,
            ], function ($result) use (&$data) {
                Yii::warning($data, '$data1');
                Yii::warning($result['result']['items'], '$result[result][items]');
                $data = array_merge($data, $result['result']['items']);
                Yii::warning($data, '$data2');
            });
        $obB24->client->processBatchCalls();

//        $dataProvider = new \yii\data\ArrayDataProvider(
//            [
//                'allModels' => $data,
//                'pagination' => false,
//                'sort'=> [
//                    'attributes' => ['title'],
//                ],
//            ]
//        );

        $dataProvider = new ArrayDataProvider([
            'allModels' => $data,
            'pagination' => [
                'pageSize' => false,
            ],
//            'sort' => [
//                'attributes' => ['title'],
//                //'params' => $params,
//            ],
        ]);

        return $dataProvider;

    }



    public static function getUserId($params) {
        $user = [];
        if (ArrayHelper::getValue($params, 'property111')) {
            preg_match('/^(in\[.*])/', $params['property111'], $matches);
            $operator = 'in';
            $user = explode(',', mb_substr($params['property111'], 3, -1));
            if ($user[0] == '') {
                $userId = Yii::$app->user->id;
                $userModel = User::find()->where(['id' => $userId])->one();
                $b24UserId = $userModel->b24_user_id;
                $res = UsersKr::getSubUsers($b24UserId);
                $user = ArrayHelper::getColumn($res, 'id');
            }
        } else {
            $userId = Yii::$app->user->id;
            $userModel = User::find()->where(['id' => $userId])->one();
            $b24UserId = $userModel->b24_user_id;
            $res = UsersKr::getSubUsers($b24UserId);
            $user = ArrayHelper::getColumn($res, 'id');
        }
        return $user;
    }

    public static function getDate($params) {
        $date = [];
//        if (ArrayHelper::getValue($params, 'dateCreate')) {
            //Yii::warning($params, '$params');
            $data = substr($params['dateCreate'], 1, -1);
            //Yii::warning($data, '$data');
            $temp = explode(',', $data);
            foreach ($temp as $value) {
                preg_match('/^(<>|>=|>|<=|<|=)/', $value, $matches);
                $operator = $matches[1];
                $number = substr($value, strlen($operator));
                $date[] = $operator . '"' . $number . '"';
            }
//        }
//        else {
//            $finish = date('Y-m-d');
//            $nextDate = time() - (1 * 24 * 60 * 60);
//            $start = date('Y-m-d', $nextDate);
//            $date[0] = '>=' . $start;
//            $date[1] = '<' . $finish;
//        }
        return $date;
    }

}
