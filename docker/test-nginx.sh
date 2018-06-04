#!/usr/bin/env bash

SCRIPT_DIR=$(dirname $(readlink -f "$0"))
PROJECT_DIR=$(dirname ${SCRIPT_DIR})

docker run --rm -it --name defacto_test_nginx \
    -v ${PROJECT_DIR}:/var/www:ro \
    -v ${SCRIPT_DIR}/.volumes/php/sock:/sock:ro \
    nginx \
    bash -c 'sed -i s/^user\ .*/user\ www\-data\;/g /etc/nginx/nginx.conf \
    && echo "server {
        listen       80;
        server_name  localhost;
        root   /var/www/public;
        location / {
            index  index.php;
            try_files \$uri /index.php\$is_args\$args;
        }
        location ~ \.php\$ {
            fastcgi_pass   unix:/sock/fpm.sock;
            include        fastcgi_params;
            fastcgi_param  SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
            fastcgi_param  DOCUMENT_ROOT /;
            internal;
        }
    }" > /etc/nginx/conf.d/default.conf \
    && cat /etc/nginx/conf.d/default.conf \
    && echo "daemon off;" >> /etc/nginx/nginx.conf \
    && nginx -t \
    && echo "http://$(cat /etc/hosts | grep 172)" \
    && nginx'


