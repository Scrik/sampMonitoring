<?php

namespace app\models;

use Yii;
use yii\base\Model;
use app\models\User;

class UserForm extends Model {

    public $login;
    public $password;
    private $_user = false;

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
        Yii::$app->authManager->assign(Yii::$app->authManager->getRole('user'), $user->getId());
        return true;
    }

    public function login() {
        return Yii::$app->user->login($this->getUser(), 0);
    }

    public function getUser() {
        if ($this->_user === false) {
            $this->_user = User::findByUsername($this->login);
        }

        return $this->_user;
    }

}
