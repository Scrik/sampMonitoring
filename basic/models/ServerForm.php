<?php

namespace app\models;

use Yii;
use yii\base\Model;
use app\models\Servers;

class ServerForm extends Model {

    public $ip;
    public $port;

    public function rules() {
        return [
            ['ip', 'required', 'message' => ''],
            ['port', 'required', 'message' => ''],
        ];
    }

    public function add() {
        $server = new Servers();
        $server->ip = $this->ip;
        $server->port = $this->port;
        $server->server_ip = $this->ip;
        $server->server_port = $this->port;
        $server->owner = Yii::$app->user->identity->id;
        $server_info = $server->getinfo();
        $server->hostname = $server_info['name'];
        $server->players = $server_info['players'];
        $server->maxplayers = $server_info['playersmax'];
        $server->gamemode = $server_info['gamemode'];
        $server->map = $server_info['map'];
        $server->status = 1;
        $server->save();
        return true;
    }

}
