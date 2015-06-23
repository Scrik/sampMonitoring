<?php

namespace app\models;

use Yii;
use yii\base\Model;

class LoginForm extends Model {

    public $login;
    public $password;
    private $_user = false;

    public function rules() {
        return [
            ['login', 'required', 'message' => ''],
            ['password', 'required', 'message' => ''],
            ['password', 'validatePassword'],
        ];
    }

    public function validatePassword($attribute, $params) {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Ошибка');
            }
        }
    }

    public function login() {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), 0);
        } else {
            return false;
        }
    }

    public function getUser() {
        if ($this->_user === false) {
            $this->_user = User::findByUsername($this->login);
        }

        return $this->_user;
    }

}
