#!/bin/sh

## Application vars
DB_ROOT_PASS="password"
DB_NAME="bookwebsite"
DB_USER="bookwebsite"
DB_PASS="password"

HOST_NAME="bookwebsite"

## Update packages
apt-get update

## Don't prompt for MySQL password choice during installation
echo mysql-server mysql-server/root_password       password $DB_ROOT_PASS | debconf-set-selections
echo mysql-server mysql-server/root_password_again password $DB_ROOT_PASS | debconf-set-selections

## Install necessary packages
apt-get -y install   \
	supervisor	     \
	apache2	     	\
	libapache2-mod-php \
	beanstalkd	     \
	build-essential  \
	curl             \
	fail2ban         \
	git              \
	memcached        \
	mysql-server     \
	mysql-client     \
	php             \
	php-cli         \
	php-curl        \
	php-json        \
	php-mcrypt      \
	php-memcached   \
	php-mysql       \
	php-mbstring       \
	php-sqlite3       \
	phpunit          \
	php7.0-zip          \
	ssl-cert

/etc/init.d/beanstalkd start

## Install Composer
curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer

## Setup firewall
ufw --force enable
ufw logging on
ufw allow 22
ufw allow 80
ufw allow 443

## Create database and user
mysql -u root -p"$DB_ROOT_PASS" -Bse "CREATE DATABASE $DB_NAME"
mysql -u root -p"$DB_ROOT_PASS" -Bse "GRANT ALL ON $DB_NAME.* to $DB_USER@localhost  IDENTIFIED BY '$DB_PASS'"
mysql -u root -p"$DB_ROOT_PASS" -Bse "FLUSH PRIVILEGES"

## Allow for remote connections
sed -i "s/^bind-address/bind-address\t\t= 0.0.0.0 #/" /etc/mysql/mysql.conf.d/mysqld.cnf

## Restart MySQL
service mysql restart

## Setup Apache VirtualHosts
# HTTP version that just redirects to HTTPS
cat > /etc/apache2/sites-available/$HOST_NAME.conf <<DELIM
<VirtualHost *:80>
	ServerName $HOST_NAME
	RewriteEngine on
	RewriteRule (.*) https://%{HTTP_HOST} [R=301,QSA,L]
</VirtualHost>
DELIM

## HTTPS site
cat > /etc/apache2/sites-available/$HOST_NAME-ssl.conf <<DELIM
<VirtualHost *:443>
	ServerName $HOST_NAME
	DocumentRoot /var/www/$HOST_NAME/code/public
	<Directory "/var/www/$HOST_NAME/code/public">
		Options Indexes FollowSymLinks MultiViews
		AllowOverride all
	</Directory>
	SSLEngine on
	SSLCertificateFile    /etc/ssl/certs/ssl-cert-snakeoil.pem
	SSLCertificateKeyFile /etc/ssl/private/ssl-cert-snakeoil.key
</VirtualHost>
DELIM

## Set ServerName in Apache config
echo "ServerName $HOST_NAME" >> /etc/apache2/apache2.conf

## Enable sites
a2dissite 000-default
a2ensite $HOST_NAME.conf $HOST_NAME-ssl.conf

## Enable the necessary Apache modules
a2enmod expires rewrite ssl

## Enable the necessary PHP modules
phpenmod mcrypt

## Restart Apache
service apache2 restart

## beanstalkd
cat > /etc/default/beanstalkd <<DELIM
## Defaults for the beanstalkd init script, /etc/init.d/beanstalkd on
## Debian systems. Append ``-b /var/lib/beanstalkd'' for persistent
## storage.
BEANSTALKD_LISTEN_ADDR=0.0.0.0
BEANSTALKD_LISTEN_PORT=11300
DAEMON_OPTS="-l $BEANSTALKD_LISTEN_ADDR -p $BEANSTALKD_LISTEN_PORT"

## Uncomment to enable startup during boot.
START=yes
DELIM

/etc/init.d/beanstalkd start

## Supervisor, replaces Upstart
cat >> /etc/supervisor/conf.d/laravel-worker.conf <<DELIM
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/$HOST_NAME/code/artisan queue:work beanstalkd --sleep=3 --tries=3
autostart=true
autorestart=true
user=worker
numprocs=8
redirect_stderr=true
stdout_logfile=/var/log/laravel-worker.log
DELIM

useradd worker
service supervisor start
supervisord -c /etc/supervisor/supervisord.conf
supervisorctl -c /etc/supervisor/supervisord.conf reload
supervisorctl -c /etc/supervisor/supervisord.conf start laravel-worker:*

composer update


## Development only
apt-get -y install npm
ln -s /usr/bin/nodejs /usr/bin/node
npm install -g gulp
cd /var/www/$HOST_NAME/code
npm install --no-optional
mysql -u root -p"$DB_ROOT_PASS" -Bse "GRANT ALL ON $DB_NAME.* to $DB_USER@'%.%.%.%'  IDENTIFIED BY '$DB_PASS'"
mysql -u root -p"$DB_ROOT_PASS" -Bse "GRANT ALL ON $DB_NAME.* to root@'%.%.%.%'  IDENTIFIED BY '$DB_ROOT_PASS'"
mysql -u root -p"$DB_ROOT_PASS" -Bse "FLUSH PRIVILEGES"
