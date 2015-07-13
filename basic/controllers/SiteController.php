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
                ],
            ],
        ];
    }

    public function actionIndex() {
        return $this->render('index');
    }

    public function actionUpdate($redirect = true) {
        foreach (Servers::find()->all() as $server) {
            $info = Servers::getInfo($server->ip, $server->port);
            $query = Servers::findOne($server->id);
            $query->hostname = $info['name'];
            $query->players = $info['players'];
            $query->maxplayers = $info['playersmax'];
            $query->status = 1;
            $query->save();
            if ($redirect)
                $this->redirect('/admin');
        }
    }

    public function actionUp($id) {
        if (Activity::rating_change($id)) {
            Servers::findOne($id)->updateCounters(['rating' => 1]);
        }
        return $this->render('index');
    }

    public function actionDown($id) {
        if (Activity::rating_change($id)) {
            Servers::findOne($id)->updateCounters(['rating' => -1]);
        }
        return $this->render('index');
    }

    public function actionServer($id) {
        return $this->render('server', ['server' => Servers::findOne($id)]);
    }

    public function actionAddserver() {
        $form = new ServerForm();
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            $modelDb = new Servers();
            $modelDb->ip = $form->ip;
            $modelDb->port = $form->port;
            $modelDb->owner = Yii::$app->user->identity->id;
            $modelDb->rating = 0;
            $modelDb->save();
            $this->actionUpdate(false);
            return $this->goHome();
        } else {
            return $this->render('add_server', ['model' => $form]);
        }
    }

    public function actionDelete($id) {
        Servers::findOne(['id' => $id, 'owner' => Yii::$app->user->identity->id])->delete();
        return $this->goBack();
    }

    public function actionMyserver() {
        return $this->render('my', ['servers' => Servers::find()->where(['owner' => Yii::$app->user->identity->id])->orderBy('rating DESC')]);
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
