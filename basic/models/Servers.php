<?php

namespace app\models;

class Servers extends \yii\db\ActiveRecord {

    public $server = [
        'ip' => '', 'port' => null, 'status' => 0, 'name' => '', 'players' => 0, 'playersmax' => 0,
    ];
    public $server_ip;
    public $server_port;

    public static function tableName() {
        return 'servers';
    }

    public function getInfo() {
        $this->server['ip'] = $this->server_ip;
        $this->server['port'] = $this->server_port;

        $socket = fsockopen('udp://' . $this->server['ip'], $this->server['port'], $errno, $errstr, 1);

        if (!$socket)
            return false;

        stream_set_timeout($socket, 0, 1000000);
        stream_set_blocking($socket, true);

        fwrite($socket, "SAMP\x21\x21\x21\x21\x00\x00i");

        $buffer = fread($socket, 4096);
        $buffer = substr($buffer, 12);

        $this->server['players'] = intval($this->sunpack($this->cutByte($buffer, 2), "S"));
        $this->server['playersmax'] = intval($this->sunpack($this->cutByte($buffer, 2), "S"));
        $this->server['name'] = $this->cutPascal($buffer, 4);

        fclose($socket);

        return $this->server;
    }

    function sunpack($str, $format) {
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
