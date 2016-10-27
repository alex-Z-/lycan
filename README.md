Lycan
=====

Using as Docker Container?
--------------------------

You can log in to the specific Docker Container like you would any other container. 

Start up the container by `docker-compose up -d` and then from the same path enter

`docker exec -it lycan_php_1 sh`

The application volume will be accessible from `/var/www/symfony`


First Setup for Developer
-----------

**Step 1.** Run the Doctrine Schema Update

`php app/console doctrine:schema:update --force`

**Step 2.** Load the fixtures (prebuild demo data)

`php app/console doctrine:fixtures:load`

**Step 3.** Load ACL Setup

`php app/console sonata:admin:setup-acl`

All together:
-------------

```
php app/console doctrine:schema:update --force  &&
php app/console doctrine:fixtures:load &&
php app/console sonata:admin:setup-acl
```

First Install will create a user with full ROLE_SUPER_ADMIN permissions with username and password:

username: admin
password: admin

You can log in :

`localhost/app_dev.php/admin/login`

And proceed to change you user login/password on your profile page.


Rabbit MQ
---------

If you are running the RabbitMQ docker container, you can access the control panel via:

http://localhost:15672/

The page will ask you for a username and password, which by default is:

Username: guest
Password: password

You can change this, but when you do, make sure you update the parameters.yml file with the new credentials.

Queues are created automatically, so you don't need to create an exchange any queues. 

Running a Rabbit Consumer
-------------------------

For development and testing, you can run an individual Rabbit Consumer by running the console command

`php app/console  rabbitmq:consumer pull_provider.rentivo`

Switch out `pull_provider.rentivo` with the actual consumer that you wish to run. 
