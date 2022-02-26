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
session_start();

if (empty($_SESSION['logged']) || empty($_SESSION['timeout']) || empty($_SESSION['ip']) || $_SESSION['logged'] !== 1 || $_SESSION['timeout'] < time() || $_SESSION['ip'] != $_SERVER['REMOTE_ADDR']) {
    header("Location: login.php");
    exit;
}

require("db.php");
$_config = [];
$r = $db->run("SELECT * FROM config");
foreach ($r as $x) {
    $_config[$x['id']] = $x['val'];
}

// just in case the password was changed
if ($_config['password'] != $_SESSION['key']) {
    header("Location: login.php");
    exit;
}

$rewrite_config = false;
$q = $_GET['q'] ?? "";

if ($q == "password") {
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    do {
        if(!csrf_check()){
            $error_msg = "Invalid csrf token. Please refresh and try again.";
            break;
        }
        if ($new_password != $_POST['repeat_password']) {
            $error_msg = "The new passwords do not match.";
            break;
        }
        if (strlen($new_password) < 8) {
            $error_msg = "The new passwords needs to be at least 8 characters in length.";
            break;
        }
        if (!password_verify($old_password, $_config['password'])) {
            $error_msg = "The old password is incorrect";
            break;
        }

        $passw = password_hash($new_password, PASSWORD_ARGON2ID);
        $_SESSION['key'] = $passw;
        $db->run("INSERT into config (id,val) VALUES (:id,:val) ON CONFLICT(id) DO UPDATE SET val=:val2", [':val' => $passw, ':val2' => $passw, ':id' => "password"]);
        $success_msg = "The password has been changed successfully.";
    } while (0);
}

if ($_config['twofa'] == "0" && $q != "2fa" && csrf_check()) {
    $tfa = new tfa();
    $_config['tfa_key'] = $tfa->getPubKey();
    $db->run("INSERT into config (id,val) VALUES (:id,:val) ON CONFLICT(id) DO UPDATE SET val=:val2", [':val' => $_config['tfa_key'], ':val2' => $_config['tfa_key'], ':id' => "tfa_key"]);
}
if ($_config['twofa'] == "0" && $q == "2fa" && csrf_check()) {
    $tfa = new tfa();

    if ($_POST['tfa'] == $tfa->getOtp($_config['tfa_key'])) {

        $db->run("UPDATE config SET val=1 WHERE id='twofa'");
        $success_msg = "The Two-Factor Authentication has been enabled.";
        $_config['twofa'] = "1";
    } else {
        $error_msg = "Invalid TOTP code.";
    }

}

if ($_config['twofa'] == "1" && $q == "disable2fa" && csrf_check()) {
    $tfa = new tfa();
    if ($_POST['tfa'] == $tfa->getOtp($_config['tfa_key'])) {

        $db->run("UPDATE config SET val=0 WHERE id='twofa'");
        $success_msg = "The Two-Factor Authentication has been disabled.";
        $_config['twofa'] = "0";
    } else {
        $error_msg = "Invalid TOTP code.";
    }
}

if ($q == "add") {
    do {
        if(!csrf_check()){
            $error_msg = "Invalid csrf token. Please refresh and try again.";
            break;
        }
        $r = $db->run("SELECT COUNT(1) as c from profiles");
        if ($r[0]['c'] > 250) {
            $error_msg = "You've reached the maximum number of active profiles - 250";
            break;
        }
        $r = $db->run("select MAX(rowid) as c FROM profiles");
        if ($r[0]['c'] < 250) {
            $current_id = $r[0]['c'] + 2;
            $ip = "10.190.190.$current_id";
        } else {
            for ($i = 2; $i < 254; $i++) {
                $ip = "10.190.190.$i";
                $current_id = $i;
                $x = $db->run("SELECT COUNT(1)  as c FROM profiles WHERE ip=:ip", [':ip' => $ip]);
                if ($x[0]['c'] == 0) {
                    break;
                }
            }
        }

        $private_key = trim(shell_exec("wg genkey"));
        if (strlen($private_key) < 10) {
            $error_msg = "Could not generate the private key.";
            break;
        }
        $public_key = trim(shell_exec("echo \"$private_key\"|wg pubkey"));
        $preshared = trim(shell_exec("wg genpsk"));
        $name = htmlspecialchars($_POST['profile_name'], ENT_QUOTES | ENT_HTML5);
        if (empty($name)) {
            $name = "Profile-" . $current_id;
        }
        $ipv6 = "fd42:190:190::" . $current_id;

        $db->run("INSERT into profiles (name,preshared,public_key,private_key,ip,ipv6) VALUES (:name, :preshared,:public_key,:private_key,:ip,:ipv6)",
            [':name' => $name, ":preshared" => $preshared, ":public_key" => $public_key, ":private_key" => $private_key, ":ip" => $ip, ":ipv6" => $ipv6]);
        $success_msg = "The wireguard profile has been created.";
        $rewrite_config = true;
    } while (0);
}

if ($q == "edit" && csrf_check()) {
    $ip = preg_replace("/[^0-9.]/", "", $_GET['ip']);
    if (!empty($_POST['edit']) && $_POST['edit'] == 1 && !empty($_POST['name'])) {
        $name = htmlspecialchars($_POST['name'], ENT_QUOTES | ENT_HTML5);
        $db->run("UPDATE profiles SET name=:name WHERE ip=:ip", [':name' => $name, ":ip" => $ip]);
    } elseif (!empty($_POST['delete']) && $_POST['delete'] == 1) {
        $db->run("DELETE FROM profiles WHERE ip=:ip", [':ip' => $ip]);
        $rewrite_config = true;
    }

}


if ($q == "download" && !empty($_GET['ip'])) {
    $ip = preg_replace("/[^0-9.]/", "", $_GET['ip']);

    $profiles = $db->run("SELECT * FROM profiles WHERE ip=:ip", [':ip' => $ip]);
    if (count($profiles) == 0) {
        die("Invalid profile");
    }

    $profile = $profiles[0];
    header('Content-Type: application/octet-stream');
    header("Content-Transfer-Encoding: Binary");
    header("Content-disposition: attachment; filename=\"$profile[name].conf\"");

    echo "[Interface]
PrivateKey = $profile[private_key]
Address = $profile[ip]/32,$profile[ipv6]/128
DNS = 8.8.8.8,8.8.4.4

[Peer]
PublicKey = $_config[public_key]
PresharedKey = $profile[preshared]
Endpoint = $_SERVER[SERVER_ADDR]:$_config[port]
AllowedIPs = 0.0.0.0/0,::/0";

    exit;

}

$profiles = $db->run("SELECT * FROM profiles");

if ($rewrite_config == true) {
    $wg = "[Interface]
Address = 10.190.190.1/24,fd42:190:190::1/64
ListenPort = $_config[port]
PrivateKey = $_config[private_key]
PostUp = iptables -A FORWARD -i $_config[net] -o wg0 -j ACCEPT; iptables -A FORWARD -i wg0 -j ACCEPT; iptables -t nat -A POSTROUTING -o $_config[net] -j MASQUERADE; ip6tables -A FORWARD -i wg0 -j ACCEPT; ip6tables -t nat -A POSTROUTING -o $_config[net] -j MASQUERADE
PostDown = iptables -D FORWARD -i $_config[net] -o wg0 -j ACCEPT; iptables -D FORWARD -i wg0 -j ACCEPT; iptables -t nat -D POSTROUTING -o $_config[net] -j MASQUERADE; ip6tables -D FORWARD -i wg0 -j ACCEPT; ip6tables -t nat -D POSTROUTING -o $_config[net] -j MASQUERADE

";
    foreach ($profiles as $profile) {
        $wg .= "\n# $profile[name]
[Peer]
PublicKey = $profile[public_key]
PresharedKey = $profile[preshared]
AllowedIPs = $profile[ip]/32,$profile[ipv6]/128
";

    }


    @file_put_contents("/etc/wireguard/wg0.conf", $wg);
    unset($wg);
}

require("template/index.php");
