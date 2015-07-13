<?php

namespace app\models;

use Yii;
use yii\base\Model;
use app\models\User;

class UserForm extends Model {

    public $login;
    public $password;

    public function rules() {
        return [
            ['login', 'required', 'message' => ''],
            ['password', 'required', 'message' => ''],
        ];
    }

    public function register() {
        $user = new User;
        $user->login = $this->login;
        $user->password = $this->password;
        $user->save();
        return Yii::$app->authManager->assign(Yii::$app->authManager->getRole('user'), $user->getId());
    }

    public function login() {
        return Yii::$app->user->login(User::findByUsername($this->login));
    }

}
