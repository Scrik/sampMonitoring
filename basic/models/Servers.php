<?php

namespace app\models;

class Servers extends \yii\db\ActiveRecord {

    public static function tableName() {
        return 'servers';
    }

    public function getInfo($ip, $port) {
        $socket = fsockopen('udp://' . $ip, $port, $errno, $errstr, 1);

        if (!$socket)
            return false;

        stream_set_timeout($socket, 0, 1000000);
        stream_set_blocking($socket, true);

        fwrite($socket, "SAMP\x21\x21\x21\x21\x00\x00i");

        $buffer = fread($socket, 4096);
        $buffer = substr($buffer, 12);

        $server['players'] = intval(self::sunpack(self::cutByte($buffer, 2), "S"));
        $server['playersmax'] = intval(self::sunpack(self::cutByte($buffer, 2), "S"));
        $server['name'] = self::cutPascal($buffer, 4);

        fclose($socket);

        return $server;
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
