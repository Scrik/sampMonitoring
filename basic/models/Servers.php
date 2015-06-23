<?php

namespace app\models;

use yii\db\ActiveRecord;

class Servers extends ActiveRecord {

    public $server = [
        'ip' => '', 'port' => null, 'status' => 0, 'name' => '', 'gamemode' => '',
        'map' => '', 'players' => 0, 'playersmax' => 0, 'password' => '',
    ];
    public $server_ip;
    public $server_port;
    public $fp;
    public $need = true;

    public static function tableName() {
        return 'servers';
    }

    public function getInfo() {
        $this->server['ip'] = $this->server_ip;
        $this->server['port'] = $this->server_port;

        $response = $this->queryDirect();


        if (empty($this->server['map'])) {
            $this->server['map'] = '-';
        }
        
        $this->server['players'] = intval($this->server['players']);
        $this->server['playersmax'] = intval($this->server['playersmax']);

        if (isset($this->server['password'][0])) {
            $this->server['password'] = (strtolower($this->server['password'][0]) == 't') ? 1 : 0;
        } else {
            $this->server['password'] = (int) $this->server['password'];
        }

        return $this->server;
    }

    function queryDirect() {
        $this->fp = fsockopen('udp://' . $this->server['ip'], $this->server['port'], $errno, $errstr, 1);

        if (!$this->fp)
            return false;

        stream_set_timeout($this->fp, 0, 1000000);
        stream_set_blocking($this->fp, true);

        $i = 0;

        do {
            $need_check = $this->need;
            $response = $this->query_12();

            if ($response)
                $i++;

            if (!$response) {
                $this->server['status'] = 0;
                break;
            }
            if ($need_check == $this->need)
                break;

            if ($this->server['players'] == '0')
                $this->need = false;
        }

        while ($this->need == true);

        fclose($this->fp);

        return $response;
    }

    function query_12() {
        $challenge_packet = "SAMP\x21\x21\x21\x21\x00\x00";
        $challenge_packet.="i";

        fwrite($this->fp, $challenge_packet);

        $buffer = fread($this->fp, 4096);

        if (!$buffer)
            return $this->need ? false : true;

        $buffer = substr($buffer, 10); // REMOVE HEADER

        $this->cutByte($buffer, 1);

        $this->need = false;
        $this->server['password'] = ord($this->cutByte($buffer, 1));
        $this->server['players'] = $this->lmUnpack($this->cutByte($buffer, 2), "S");
        $this->server['playersmax'] = $this->lmUnpack($this->cutByte($buffer, 2), "S");
        $this->server['name'] = $this->cutPascal($buffer, 4);
        $this->server['gamemode'] = $this->cutPascal($buffer, 4);
        $this->server['map'] = $this->cutPascal($buffer, 4);

        return true;
    }

    function lmUnpack($str, $format) {
        list(, $str) = unpack($format, $str);
        return $str;
    }

    function cutByte(&$buffer, $length) {
        $string = substr($buffer, 0, $length);
        $buffer = substr($buffer, $length);
        return $string;
    }

    function cutPascal(&$buffer, $start_byte = 1, $length_adjust = 0, $end_byte = 0) {
        $length = ord(substr($buffer, 0, $start_byte)) + $length_adjust;
        $string = substr($buffer, $start_byte, $length);
        $buffer = substr($buffer, $start_byte + $length + $end_byte);
        return $string;
    }

}
