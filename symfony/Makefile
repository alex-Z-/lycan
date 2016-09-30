all: ready

ready: 
	@echo "update composer to last version"
	php composer.phar self-update

install:
	@echo "create database schema"
	php app/console doctrine:database:create
	@echo "install vendors"
	php composer.phar install
	@echo "create assets dump"
	php app/console assets:install --symlink
	@echo "exec database migration"
	php app/console doctrine:migrations:migrate
	@echo "load default fixtures"
	php app/console doctrine:fixtures:load
	
upgrade:
	php composer.phar update