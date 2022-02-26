<?php
/*
Wireguard4VPS v0.1a - Alpha - https://github.com/mvpsnet/wireguard4vps

The MIT License (MIT)
Copyright (c) 2022 MVPS LTD - www.mvps.net

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:
The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.
THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM,
DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE
OR OTHER DEALINGS IN THE SOFTWARE.
*/

class db extends PDO
{
    public function __construct($db_file)
    {
        $options = array(
            PDO::ATTR_PERSISTENT => false,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        );

        try {
            parent::__construct("sqlite:$db_file", null, null, $options);
        } catch (PDOException $e) {
            die("Could not connect to the database.");
        }
    }

    public function run($sql, $bind = [])
    {

        try {
            $query = $this->prepare($sql);
            if ($query->execute($bind) !== false) {
                if (preg_match("/^(select|describe|pragma)/i", $sql)) {
                    return $query->fetchAll(PDO::FETCH_ASSOC);
                } elseif (preg_match("/^(delete|insert|update)/i", $sql)) {
                    return $query->rowCount();
                } else {
                    return true;
                }
            }
        } catch (PDOException $e) {
            echo "\n\n$sql = ".$e->getMessage()."\n\n";

            return false;
        }
    }


}

// credits https://github.com/dimamedia/PHP-Simple-TOTP-and-PubKey

class tfa
{

    // RFC4648 Base32 alphabet
    private $alphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ234567";

    function getOtp($key)
    {

        /* Base32 decoder */

        // Remove spaces from the given public key and converting to an array
        $key = str_split(str_replace(" ", "", $key));

        $n = 0;
        $j = 0;
        $binary_key = "";

        // Decode public key's each character to base32 and save into binary chunks
        foreach ($key as $char) {
            $n = $n << 5;
            $n = $n + stripos($this->alphabet, $char);
            $j += 5;

            if ($j >= 8) {
                $j -= 8;
                $binary_key .= chr(($n & (0xFF << $j)) >> $j);
            }
        }
        /* End of Base32 decoder */

        // current unix time 30sec period as binary
        $binary_timestamp = pack('N*', 0) . pack('N*', floor(microtime(true) / 30));
        // generate keyed hash
        $hash = hash_hmac('sha1', $binary_timestamp, $binary_key, true);

        // generate otp from hash
        $offset = ord($hash[19]) & 0xf;
        $otp = (
                ((ord($hash[$offset + 0]) & 0x7f) << 24) |
                ((ord($hash[$offset + 1]) & 0xff) << 16) |
                ((ord($hash[$offset + 2]) & 0xff) << 8) |
                (ord($hash[$offset + 3]) & 0xff)
            ) % pow(10, 6);

        return $otp;
    }

    function getPubKey()
    {
        $alphabet = str_split($this->alphabet);
        $key = '';
        // generate 16 chars public key from Base32 alphabet
        for ($i = 0; $i < 16; $i++) $key .= $alphabet[random_int(0, 31)];
        // split into 4x4 chunks for easy reading
        return implode(" ", str_split($key, 4));
    }

}


if (!extension_loaded('pdo_sqlite')) {
    die("pdo_sqlite extension not enabled.");
}

// until debian moves to php 8
if (!function_exists('str_contains')) {
    function str_contains(string $haystack, string $needle)
    {
        return empty($needle) || strpos($haystack, $needle) !== false;
    }
}

function csrf_token($ret = false)
{
    $uniq = time();
    $token = hash("sha512", $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_HOST'] . $_SERVER['HTTP_USER_AGENT'] . $_SESSION['nonce'] . $uniq);

    if ($ret) {
        return array("token" => $token, "time" => $uniq);
    }
    echo "<input type='hidden' name='csrf4_tken' value='$token'>";
    echo "<input type='hidden' name='csrf4_time' value='$uniq'>";
}

function csrf_check()
{
    $uniq = $_REQUEST['csrf4_time'];
    $token = hash("sha512", $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_HOST'] . $_SERVER['HTTP_USER_AGENT'] . $_SESSION['nonce'] . $uniq);
    if ($token != $_POST['csrf4_tken']) {
        return false;
    }
    if (time() - $uniq > 1800) {
        return false;
    }
    return true;
}

$db_path = "/etc/wireguard/wireguard4vps.db";
$db = new DB($db_path);
