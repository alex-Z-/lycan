Lycan
==

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