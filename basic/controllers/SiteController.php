<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\Servers;
use app\models\LoginForm;
use app\models\RegForm;
use app\models\ServerForm;
use yii\filters\AccessControl;
use app\models\Activity;

class SiteController extends Controller {

    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'reg'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['index', 'server'],
                        'allow' => true,
                        'roles' => ['?', '@'],
                    ],
                    [
                        'actions' => ['addserver', 'myserver', 'logout', 'up', 'down', 'delete'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['update'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex() {
        return $this->render('index');
    }

    public function actionUpdate() {
        $model = new Servers();
        $servers = Servers::find()->all();
        foreach ($servers as $server) {
            $model->server_ip = $server->ip;
            $model->server_port = $server->port;
            $server_sql = Servers::findOne($server->id);
            $serverInfo = $model->getinfo();
            $server_sql->hostname = $serverInfo['name'];
            $server_sql->players = $serverInfo['players'];
            $server_sql->maxplayers = $serverInfo['playersmax'];
            $server_sql->gamemode = $serverInfo['gamemode'];
            $server_sql->map = $serverInfo['map'];
            $server_sql->status = 1;
            $server_sql->save();
            $this->redirect('/admin');
        }
    }

    public function actionUp($id) {
        if (Activity::rating_change($id)) {
            $rating = Servers::findOne($id);
            $rating->updateCounters(['rating' => 1]);
        }
        return $this->render('index');
    }

    public function actionDown($id) {
        if (Activity::rating_change($id)) {
            $rating = Servers::findOne($id);
            $rating->updateCounters(['rating' => -1]);
        }
        return $this->render('index');
    }

    public function actionServer($id) {
        return $this->render('server', ['server' => Servers::findOne($id)]);
    }

    public function actionAddserver() {
        $model = new ServerForm;
        if ($model->load(Yii::$app->request->post()) && $model->add()) {
            return $this->goHome();
        } else {
            return $this->render('add_server', ['model' => $model]);
        }
    }

    public function actionDelete($id) {
        $server = Servers::find()->where(['id' => $id, 'owner' => Yii::$app->user->identity->id])->one();
        $server->delete();
        return $this->goBack();
    }

    public function actionMyserver() {
        return $this->render('my', ['servers' => Servers::find()->where(['owner' => Yii::$app->user->identity->id])->all()]);
    }

    public function actionLogin() {
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goHome();
        } else {
            return $this->render('login', ['model' => $model]);
        }
    }

    public function actionReg() {
        $model = new RegForm();
        if ($model->load(Yii::$app->request->post()) && $model->register()) {
            return $this->goHome();
        } else {
            return $this->render('reg', ['model' => $model]);
        }
    }

    public function actionLogout() {
        Yii::$app->user->logout();
        return $this->goHome();
    }

}
