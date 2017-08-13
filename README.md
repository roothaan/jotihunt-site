    ------------------------------------
    _       _   _ _                 _   
    (_)     | | (_) |               | |  
     _  ___ | |_ _| |__  _   _ _ __ | |_ 
    | |/ _ \| __| | '_ \| | | | '_ \| __|
    | | (_) | |_| | | | | |_| | | | | |_ 
    | |\___/ \__|_|_| |_|\__,_|_| |_|\__|
    _/ |                                  
    |__/                                   
    ------------------------------------

# Used technology
BitBucket for git source control
Heroku for hosting
PHP/Apache for running the application
PostgreSQL for database
Codeship for continues integration
Cloud 9 for IDE / testing

# Heroku Plugins
- Heroku Postgres
- Logentries (TryIt)
- Mailgun (Starter)
- New Relic APM (Wayne)
- Proximo (Starter)

# Update tot PHP7
https://community.c9.io/t/laravel-5-3-installation-on-cloud9/9038

`sudo add-apt-repository ppa:ondrej/php`
`sudo apt-get update`
`sudo apt-get install libapache2-mod-php7.0`
`sudo a2dismod php5`
`sudo a2enmod php7.0`
`sudo apt-get install php7.0-curl`
`sudo apt-get install php7.0-pgsql`
`sudo apt-get install php7.0-xml`
`service apache2 restart`

## Optioneel
`sudo apt-get install php7.0-dom`
`sudo apt-get install php7.0-mbstring`
`sudo apt-get install php7.0-zip`

# Update Composer stuff
`./composer.phar update`

# How to configure the Jotihunt site
There are 2 main ways to configure the site.
1) Through environment variables.
See below for the options.

2) Through the `config/user.ini` file.
See the `config/user.ini.example` file on how to use it.

# Required environment variables
DATABASE_URL
DATABASE_OPTIONS (sslmode=require options='--client_encoding=UTF8')

# Strongly recommended environment variables
GOOGLE_JS_API_KEY
GOOGLE_ANALYTICS_KEY

# Optional environment variables
MAILGUN_API_KEY
MAINGUN_API_DOMAIN
MAILGUN_FROM_EMAIL

GOOGLE_GCM_API_KEY
PROXIMO_USER
PROXIMO_PASS
PROXIMO_HOST

# In case you're behind a reverse proxy
PROXY_SERVER_PORT (8443)
PROXY_BASE_URL (/forwarded)

# Jotihunt site settings
SITE_SHOW_ERRORS (1/true or 0/false)