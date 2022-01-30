<?php
/*
The MIT License (MIT)
Copyright (c) 2022 MVPS LTD - www.mvps.net

Project page: https://github.com/mvpsnet/wireguard4vps

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

ini_set('session.cookie_secure', '1');
ini_set('session.cookie_httponly', '1');
ini_set('session.use_only_cookies', '1');
session_start();
$_SESSION['timeout']=0;
$_SESSION['ip']="";
$_SESSION['logged']=0;
$_SESSION['key']="";

require_once("db.php");
$r = $db->run("SELECT name FROM sqlite_master WHERE type='table' AND name='config'");
if (count($r) == 0) {
    die("Configuration not initiated. Run the setup cli.");
}

$error_msg="";
if(!empty($_POST['user'])&&!empty($_POST['pass'])){
    do {
        if ($_POST['user'] !== "admin") {
            $error_msg="Invalid username or password";
            break;
        }

        $test=$db->run("SELECT COUNT(1) as t FROM logins WHERE ip=:ip AND data>:time",[':ip'=>$_SERVER['REMOTE_ADDR'], ":time"=>time()-3600]);

        if($test[0]['t']>5){
            $error_msg="Too many failed login requests from this IP. You need to wait 1h or reset the password by running in the console: <br>php /var/www/html/setup.php NEW-PASSWORD";
            break;
        }
        $test=$db->run("SELECT COUNT(1) as t FROM logins WHERE ip=:ip AND data>:time",[':ip'=>$_SERVER['REMOTE_ADDR'], ":time"=>time()-3600*24]);
        if($test[0]['t']>10){
            $error_msg="Too many failed login requests from this IP. You need to wait 24h or reset the password by running in the console: <br>php /var/www/html/setup.php NEW-PASSWORD";
            break;
        }
        $test=$db->run("SELECT COUNT(1) as t FROM logins WHERE data>:time", [":time"=>time()-3600*24]);
        if($test[0]['t']>30){
            $error_msg="Too many failed login requests. You need to wait 24h or reset the password by running in the console: <br>php /var/www/html/setup.php NEW-PASSWORD";
            break;
        }
        $passw=$db->run("SELECT val FROM config WHERE id='password'");

        if(!password_verify($_POST['pass'],$passw[0]['val'])){

            $error_msg="Invalid username or password";
            break;
        }

        $otp=$db->run("SELECT val FROM config WHERE id='twofa'");
        if($otp[0]['val']!="0"){
            $otpkey=$db->run("SELECT val FROM config WHERE id='tfa_key'");
            $tfa = new tfa();
               if ($_POST['tfa'] != $tfa->getOtp($otpkey[0]['val'])) {
                $error_msg="Invalid TOTP code";
                break;
            }
        }
        $_SESSION['logged']=1;
        $_SESSION['timeout']=time()+1800;
        $_SESSION['ip']=$_SERVER['REMOTE_ADDR'];
        $_SESSION['key']=$passw[0]['val'];

        $db->run("DELETE FROM logins");
        header("Location: index.php");
        exit;
    } while(0);

}
if(!empty($error_msg)){
    $db->run("INSERT into logins (ip,data) VALUES (:ip, :time)",[':ip'=>$_SERVER['REMOTE_ADDR'], ":time"=>time()]);
}

include("template/login.php");