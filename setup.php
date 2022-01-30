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

if (php_sapi_name() !== 'cli') {
    die("This should only be run as cli");
}

require(__DIR__ . "/db.php");

if (empty($argv[1]) || strlen($argv[1]) < 8) {
    die("The password is invalid. It must be at least 8 characters long.\n\tphp setup.php <PASSWORD>");
}
$password = trim($argv[1]);


$r = $db->run("SELECT name FROM sqlite_master WHERE type='table' AND name='config'");
if (count($r) == 0) {
    $private_key=trim(shell_exec("wg genkey"));
    if(strlen($private_key)<10){
        die("Could not generate the keys. Make sure wireguard is installed.");
    }
    $public_key=trim(shell_exec("echo \"$private_key\"|wg pubkey"));

    $port=rand(20000,60000);

    $net=trim(shell_exec("ip -4 route ls | grep default | grep -Po '(?<=dev )(\S+)' | head -1"));
    if(empty($net)){
        die("Could not identify the network interface");
    }

    $db->run("CREATE TABLE `config` (
      `id` varchar(32) NOT NULL PRIMARY KEY,
      `val` varchar(128) NOT NULL
    )");
    $db->run("CREATE TABLE `profiles` (
      `name` varchar(64) NOT NULL,
      `preshared` varchar(128) NOT NULL,
      `public_key` varchar(128) NOT NULL,
      `private_key` varchar(128) NOT NULL,
      `ip` varchar(16) NOT NULL,
      `ipv6` varchar(16) NOT NULL
    )");


    $db->run("CREATE TABLE `logins` (
      `ip` varchar(128) NOT NULL,
      `data` int(11) NOT NULL
    )");


    $db->run("INSERT into config (id,val) VALUES (:id,:val) ON CONFLICT(id) DO UPDATE SET val=:val2", [':val' => $public_key, ":val2" => $public_key, ":id" => "public_key"]);
    $db->run("INSERT into config (id,val) VALUES (:id,:val) ON CONFLICT(id) DO UPDATE SET val=:val2", [':val' => $private_key, ":val2" => $private_key, ":id" => "private_key"]);
    $db->run("INSERT into config (id,val) VALUES (:id,:val) ON CONFLICT(id) DO UPDATE SET val=:val2", [':val' => $port, ":val2" => $port, ":id" => "port"]);
    $db->run("INSERT into config (id,val) VALUES (:id,:val) ON CONFLICT(id) DO UPDATE SET val=:val2", [':val' => $net, ":val2" => $net, ":id" => "net"]);


    $wg = "[Interface]
Address = 10.190.190.1/24,fd42:190:190::1/64
ListenPort = $port
PrivateKey = $private_key
PostUp = iptables -A FORWARD -i $net -o wg0 -j ACCEPT; iptables -A FORWARD -i wg0 -j ACCEPT; iptables -t nat -A POSTROUTING -o $net -j MASQUERADE; ip6tables -A FORWARD -i wg0 -j ACCEPT; ip6tables -t nat -A POSTROUTING -o $net -j MASQUERADE
PostDown = iptables -D FORWARD -i $net -o wg0 -j ACCEPT; iptables -D FORWARD -i wg0 -j ACCEPT; iptables -t nat -D POSTROUTING -o $net -j MASQUERADE; ip6tables -D FORWARD -i wg0 -j ACCEPT; ip6tables -t nat -D POSTROUTING -o $net -j MASQUERADE

";
    @file_put_contents("/etc/wireguard/wg0.conf", $wg);
    
echo "The setup has been completed.\n";
    echo "The network interface is: $net\n";
    echo "The wireguard port is: $port\n\n";

    
    
    
}

$passw=password_hash($password,PASSWORD_ARGON2ID);
$db->run("INSERT into config (id,val) VALUES (:id,:val) ON CONFLICT(id) DO UPDATE SET val=:val2", [':val' => $passw, ':val2' => $passw, ':id' => "password"]);
$db->run("INSERT into config (id,val) VALUES (:id,:val) ON CONFLICT(id) DO UPDATE SET val=:val2", [':val' => "", ":val2" => "", ":id" => "twofa_key"]);
$db->run("INSERT into config (id,val) VALUES (:id,:val) ON CONFLICT(id) DO UPDATE SET val=:val2", [':val' => "0", ":val2" => "0", ":id" => "twofa"]);
$db->run("DELETE from logins");

echo "The password has been set and the 2FA is deactivated.\n";
echo "The login username is: admin\n";
echo "The login password is: $password\n";
