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

In 2016 heeft de Scouting Roothaangroep besloten op de Jotihunt site en de Jotihunt Android app beschikbaar te stellen aan alle groepen die meedoen.

Groepen zijn welkom om via de Roothaan in de gehoste versie mee te doen (zodat er geen hosting of setup nodig is) of zelf met deze code een prive site op te zetten.

# Technologie

## Gebruikte technologie
- BitBucket (/Github) - Git source control
- Heroku - Cloud hosting ("Platform as a service")
- PHP (7)/Apache - Applicatie technologie
- PostgreSQL - Database
- Codeship - Continues integration
- Cloud 9 - Cloud PHP IDE / testing


## Heroku Plugins
- Heroku Postgres
- Logentries (TryIt)
- Mailgun (Starter)
- New Relic APM (Wayne)
- Proximo (Starter)

# Requirements and setup
## Requirements
De Jotihunt site vereist een Postgres database en een Apache/PHP7 omgeving.

## Setup
Er zijn twee manieren op de site te configeren.
1) Via "environment variables"

Hieronder staan alle verschillende ondersteunde variabelen.

2) Via het `config/user.ini` bestand

Zie  `config/user.ini.example` voor voorbeelden. Kopieer dit naar naar `config/user.ini` (deze wordt vervolgens door de code automatisch opgepikt.
# Environment variables


## Required environment variables
`DATABASE_URL`\
Dit moet in Postgres formaat: `postgres://username:password@hostname:port/database-name`

`DATABASE_OPTIONS`\
Voor Heroku: `sslmode=require options='--client_encoding=UTF8'`

# Strongly recommended environment variables
`GOOGLE_JS_API_KEY`\
`GOOGLE_ANALYTICS_KEY`

# Optional environment variables
`GOOGLE_GCM_API_KEY`

Google GCM wordt gebruikt om notificaties naar de Android app te sturen.

## Mailgun
Mailgun is een gratis dienst om e-mail te versturen.

`MAILGUN_API_KEY`\
`MAINGUN_API_DOMAIN`\
`MAILGUN_FROM_EMAIL`

## Proximo
Promino is een service waardoor je gegarandeerd een vast IP naar buiten na gebruikt.\
Dit is handig als in een cloud dienst (Amazon, Heroku) toch een vast IP (voor Google GCM bijvoorbeeld) nodig hebt.

`PROXIMO_USER`\
`PROXIMO_PASS`\
`PROXIMO_HOST`

# In case you're behind a reverse proxy
`PROXY_SERVER_PORT`\
Bijvoorbeeld `8443`

`PROXY_BASE_URL`\
Bijvoorbeeld `/forwarded`

# Jotihunt site settings
`SITE_SHOW_ERRORS`\
Dit is een `boolean` waarde ((`1`/`true` of `0`/`false`).\
Standaard staan de errors uit.


# Overig
## Update tot PHP7
Om in Cloud9 van de "oude" PHP naar de nieuwe over te stappen, zijn de volgende stappen nodig (handleiding gebaseerd op https://community.c9.io/t/laravel-5-3-installation-on-cloud9/9038).

```bash
sudo add-apt-repository ppa:ondrej/php
sudo apt-get update
sudo apt-get install libapache2-mod-php7.0
sudo a2dismod php5
sudo a2enmod php7.0
sudo apt-get install php7.0-curl
sudo apt-get install php7.0-pgsql
sudo apt-get install php7.0-xml
service apache2 restart
```

### Optioneel
```bash
sudo apt-get install php7.0-dom
sudo apt-get install php7.0-mbstring
sudo apt-get install php7.0-zip
```

# Update Composer stuff
De site gebruikt `composer` als PHP Dependency manager.\
Als deze geupdate moet worden of nieuwe dependencies gedownload, run dan\
`./composer.phar update`
