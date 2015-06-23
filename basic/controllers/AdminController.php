<?php

namespace app\controllers;

use yii\web\Controller;
use yii\filters\AccessControl;
use app\models\Servers;

class AdminController extends Controller {

    public $layout = 'admin';

    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'delete'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex() {
        return $this->render('index', ['servers' => Servers::find()->all()]);
    }

    public function actionDelete($id) {
        $server = Servers::findOne($id);
        $server->delete();
        return $this->goBack();
    }

}
