#!/bin/bash

##variable
hostname="$HOSTNAME"
mail="$EMAIL"

apt update

apt -y install unattended-upgrades apt-listchanges iptables  wireguard-dkms wireguard-tools
echo unattended-upgrades unattended-upgrades/enable_auto_updates boolean true | debconf-set-selections
dpkg-reconfigure -f noninteractive unattended-upgrades

apt -y install git nginx certbot python3-certbot-nginx php php-fpm php-sqlite3 wireguard sudo

systemctl enable --now apt-daily.timer
systemctl enable --now apt-daily-upgrade.timer
chown www-data:www-data /etc/wireguard


echo "net.ipv4.ip_forward = 1
net.ipv6.conf.all.forwarding = 1" >/etc/sysctl.d/wg.conf
sysctl --system

echo "[Unit]
Description=Watch /etc/wireguard/wg0.conf for changes

[Path]
PathModified=/etc/wireguard/wg0.conf

[Install]
WantedBy=multi-user.target">/etc/systemd/system/wireguard4vps.path

echo "[Unit]
Description=Reload WireGuard
After=network.target

[Service]
Type=oneshot
ExecStart=/usr/bin/systemctl reload wg-quick@wg0.service

[Install]
WantedBy=multi-user.target">/etc/systemd/system/wireguard4vps.service



### website configuration 



cd /var/www/
mv html html_old
git clone https://github.com/mvpsnet/wireguard4vps
mv wireguard4vps html
rm /var/www/html/.htaccess
chown www-data:www-data /var/www/html



### nginx configraion 



cat <<END >/etc/nginx/sites-enabled/wg.conf
server {
    listen 80;
     server_name $hostname;
#     return 301 https://$hostname$request_uri;
      location ~ /.well-known {
        root /var/www/html;
        allow all;
    }

}
END

systemctl restart nginx

certbot certonly  --non-interactive --agree-tos -d $hostname  -m  $mail  --webroot -w /var/www/html/

cat <<END >>/etc/nginx/sites-enabled/wg.conf
server {
    listen 443 ssl http2;
    root /var/www/html/;
    index index.html index.htm index.php login.php;
    server_name $hostname;
 
ssl_certificate /etc/letsencrypt/live/$hostname/fullchain.pem;
ssl_certificate_key /etc/letsencrypt/live/$hostname/privkey.pem;
    
    location ~ /.well-known {
        root /var/www/html;
        allow all;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
     }
 }
END

sed -i -e "s/\#//g" /etc/nginx/sites-enabled/wg.conf

systemctl restart nginx
systemctl enable --now nginx



###



WGPASS=`< /dev/urandom tr -dc _A-Z-a-z-0-9 | head -c16`

sudo -u www-data php /var/www/html/setup.php $WGPASS > /dev/null

systemctl enable --now wg-quick@wg0

systemctl enable --now wireguard4vps.service
systemctl enable --now wireguard4vps.path

echo "The wireguard4vps password is: $WGPASS"
echo "The wireguard4vps username is: admin"

unattended-upgrade > /dev/null &
