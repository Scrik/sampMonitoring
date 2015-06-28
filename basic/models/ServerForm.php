<?php

namespace app\models;

class ServerForm extends \yii\base\Model {

    public $ip;
    public $port;

    public function rules() {
        return [
            ['ip', 'required', 'message' => ''],
            ['port', 'required', 'message' => ''],
        ];
    }

}
