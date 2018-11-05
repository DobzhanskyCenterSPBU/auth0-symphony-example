# auth0-symphony-example
Example of using auth0 authentication lib with symphony via ouuth provider

## Running this app
> cd gwatch
> composer install
> php bin/console server:run 0.0.0.0:8080

### Usefull commands
> cd gwatch
> php bin/console cache:clear

## Installing dependencies for new project

### Updating php5->php7 on c9.io
> sudo add-apt-repository ppa:ondrej/php -y
> sudo apt-get update -y

> sudo apt-get install php7.0-curl php7.0-cli php7.0-dev php7.0-gd php7.0-intl php7.0-mcrypt php7.0-json php7.0-mysql php7.0-opcache php7.0-bcmath php7.0-mbstring php7.0-soap php7.0-xml php7.0-zip -y

> sudo mv /etc/apache2/envvars /etc/apache2/envvars.bak
> sudo apt-get remove libapache2-mod-php5 -y
> sudo apt-get install libapache2-mod-php7.0 -y
> sudo cp /etc/apache2/envvars.bak /etc/apache2/envvars

### Install Doctrine
To communicate to DB Doctrine is used:
> composer require symfony/orm-pack
> composer require annotations
> composer require validator
> composer require template
> composer require security-bundle
> composer require --dev maker-bundle

### Add My sql
> mysql-ctl start
Root User: adavydenko
Database Name: c9

### Update data fixtures
> cd gwatch
> php bin/console doctrine:schema:update --force
> php bin/console doctrine:fixtures:load

### run server localhost
> cd gwatch
> php bin/console

### run server on VM
> cd gwatch
> php bin/console server:run 0.0.0.0:8080