#!/bin/bash

if [ "$DBROOT_USER" == "" ]; then
    echo "set DBROOT_USER , DBROOT_PASSWD";
    exit
fi

export WP_CLI_CONFIG_PATH=$PWD
mkdir -p conf www run logs

SERVER_NAME=$(cat wp-cli.yml | shyaml get-value url | sed -r 's/http:\/\///;s|\/.*||')
# SOCK=$PWD/run/php-fpm.sock
SOCK=/tmp/dev-php-fpm.sock
LOGDIR=$PWD/logs
HOME=$PWD/www

tools(){
    curl -sS https://getcomposer.org/installer | php
    ./composer.phar require psy/psysh
    ./composer.phar require wp-cli/wp-cli
}

createdb(){
    NAME=$(cat wp-cli.yml | shyaml get-value "core config".dbname)
    USER=$(cat wp-cli.yml | shyaml get-value "core config".dbuser)
    PASSWORD=$(cat wp-cli.yml | shyaml get-value "core config".dbpass)
    HOST=localhost

    cat << EOF | mysql -u $DBROOT_USER --password=$DBROOT_PASSWD
    CREATE DATABASE $NAME
        DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
    GRANT ALL on $NAME.*
        to '$USER'@'$HOST'
        identified by '$PASSWORD' WITH GRANT OPTION;

EOF
}

wpdownload(){
    vendor/bin/wp core download --locale=ja
}

wpconfig(){
    vendor/bin/wp core config
}

wpinstall(){
    PARAMS=''
    for i in url title admin_user admin_password admin_email ; do
        PARAMS="--$i=$(cat wp-cli.yml|shyaml get-value $i) $PARAMS"
    done
    vendor/bin/wp core install $PARAMS
}

conf_phpfpm(){

    cat > conf/php-fpm.conf << EOF
[wordpress]
listen = $SOCK
listen.mode = 0666
user = vagrant
group = vagrant
pm = dynamic
pm.max_children = 5
pm.start_servers = 2
pm.min_spare_servers = 1
pm.max_spare_servers = 3

EOF
}

conf_nginx(){
    cat > conf/nginx.conf << EOF
upstream php-fpm-wordpress {
  ip_hash;
  server unix:$SOCK;
}

server {
    listen 80;
    listen [::]:80;

    server_name $SERVER_NAME;

    root $HOME;
    index index.php index.html;

    access_log $LOGDIR/access.log;
    error_log  $LOGDIR/error.log debug;

    location ~ \.php$ {
        fastcgi_pass  php-fpm-wordpress;
        fastcgi_index  index.php;
        fastcgi_param  SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
        fastcgi_buffer_size  128k;
        fastcgi_buffers  256 16k;
        fastcgi_busy_buffers_size  256k;
        fastcgi_temp_file_write_size  256k;
        include  fastcgi_params;
    }
}
EOF

}

if [ ! -f composer.phar ]; then
    tools
fi

createdb
wpdownload
wpconfig
wpinstall
conf_phpfpm
conf_nginx
