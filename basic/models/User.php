<?php

namespace app\models;

use Yii;
use yii\web\IdentityInterface;

class User extends \yii\db\ActiveRecord implements IdentityInterface {

    public static function tableName() {
        return 'users';
    }

    public function rules() {
        return [
            [['login', 'password'], 'required'],
            [['login', 'password'], 'string', 'max' => 100]
        ];
    }

    public static function findIdentity($id) {
        return static::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null) {
        
    }

    public static function findByUsername($username) {
        return static::findOne(['login' => $username]);
    }

    public static function findByPasswordResetToken($token) {
        
    }

    public function getId() {
        return $this->getPrimaryKey();
    }

    public function getAuthKey() {
        
    }

    public function validateAuthKey($authKey) {
        
    }

    public function validatePassword($password) {
        return $this->password === $password;
    }

    public function setPassword($password) {
        $this->password = $password;
    }

    public function generateAuthKey() {
        
    }

    public function generatePasswordResetToken() {
        
    }

    public function removePasswordResetToken() {
        
    }

}
