<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\Servers;
use app\models\UserForm;
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

    public function actionUpdate($redirect = true) {
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
            $server_sql->status = 1;
            $server_sql->save();
            if ($redirect)
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
        $modelForm = new ServerForm();
        if ($modelForm->load(Yii::$app->request->post()) && $modelForm->validate()) {
            $modelDb = new Servers();
            $modelDb->attributes = [
                'ip' => $modelForm->ip,
                'port' => $modelForm->port,
                'owner' => Yii::$app->user->identity->id,
                'rating' => 0
            ];
            $modelDb->save();
            $this->actionUpdate(false);
            return $this->goHome();
        } else {
            return $this->render('add_server', ['model' => $modelForm]);
        }
    }

    public function actionDelete($id) {
        Servers::find()->where(['id' => $id, 'owner' => Yii::$app->user->identity->id])->one()->delete();
        return $this->goBack();
    }

    public function actionMyserver() {
        return $this->render('my', ['servers' => Servers::find()->where(['owner' => Yii::$app->user->identity->id, 'status' => 1])->orderBy('rating DESC')]);
    }

    public function actionLogin() {
        $model = new UserForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->login()) {
            return $this->goHome();
        } else {
            return $this->render('login', ['model' => $model]);
        }
    }

    public function actionReg() {
        $model = new UserForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->register()) {
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
