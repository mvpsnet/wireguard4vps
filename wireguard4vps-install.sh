apt update
apt -y install unattended-upgrades apt-listchanges
echo unattended-upgrades unattended-upgrades/enable_auto_updates boolean true | debconf-set-selections
dpkg-reconfigure -f noninteractive unattended-upgrades
apt -y install git iptables apache2 php php-sqlite3 wireguard sudo
systemctl enable --now apt-daily.timer
systemctl enable --now apt-daily-upgrade.timer
chown www-data:www-data /etc/wireguard
a2enmod ssl
a2enmod rewrite
a2ensite default-ssl
echo "<Directory /var/www/html>
AllowOverride All
</Directory>">/etc/apache2/conf-enabled/htaccess.conf
systemctl restart apache2
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

systemctl enable --now apache2
cd /var/www/
mv html html_old
git clone https://github.com/mvpsnet/wireguard4vps
mv wireguard4vps html
chown www-data:www-data html

WGPASS=`< /dev/urandom tr -dc _A-Z-a-z-0-9 | head -c16`

sudo -u www-data php /var/www/html/setup.php $WGPASS > /dev/null

cat << 'EOF' > /var/www/html/.htaccess
RewriteEngine on
RewriteCond %{HTTPS} off
RewriteCond %{THE_REQUEST} !\s/\.well-known/?[?\s] [NC]
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
EOF

systemctl enable --now wg-quick@wg0

systemctl enable --now wireguard4vps.service
systemctl enable --now wireguard4vps.path

echo "The wireguard4vps password is: $WGPASS"
echo "The wireguard4vps username is: admin"

unattended-upgrade > /dev/null &
