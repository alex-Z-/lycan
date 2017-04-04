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

Note: If the PHP container does not start. Make sure that you do not have supervisor autostarting. It will fail if the database does not already exist and created causing the container to instantly exit because of error code 0.


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

**Provider Example**

This consumer takes big batch jobs and executions individual mini-jobs. For example, a full provider syncronization will be comprised of hundreds of individual listing syncronizations. 

`php app/console  rabbitmq:consumer pull_provider`

**Listing Example**


`php app/console  rabbitmq:consumer pull_listing`

Switch out `pull_provider.rentivo` with the actual consumer that you wish to run. 

Useful Commands
===============
`cat dev_missing_amenities.log | sort | uniq -c | sort -k2nr |  sed -e 's/^[ \t]*//' | sed -r 's/\s+/,/'`