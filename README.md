# wireguard4vps


A simple GUI and installation script for Wireguard.

The installation script requires a clean installation of Debian 11. 

This application is designed to run on an entire VPS without any other applications running on the vps.

This is an early Alpha release. Use with care!

Installation:

`apt update && apt -y install curl && curl https://raw.githubusercontent.com/mvpsnet/wireguard4vps/main/wireguard4vps-install.sh|bash`

The login username is: admin
The password is randomly generated and printed when the script finishes the installation.

To reset the password and disable the 2FA, run:

`php /var/www/html/setup <NEW-PASSWORD>`
