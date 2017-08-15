#!/bin/bash
set -e

DIRECTORY=/var/www/jotihunt-site

if [ ! -d "$DIRECTORY" ]; then
  cd $DIRECTORY
  if [ ! -z "$PROXY_BASE_URL" ]; then
      echo "PROXY_BASE_URL set, need to rewrite .htaccess"
      REGEX_TO_ADD="RewriteRule ^$PROXY_BASE_URL/(.*)$ \$1 [QSA]"
      sed -i "s:#PROXY_BASE_URL#:$REGEX_TO_ADD:g" $DIRECTORY/.htaccess
  fi
fi

exec "$@"
