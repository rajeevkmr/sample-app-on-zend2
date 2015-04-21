### About ###
This is a small sample website on Zend 2 (version 2.2.5) framework that manages company and employee records as a parent-child relation.

It utilizes `InputFilters`, Doctrine annotations, custom validators, jQuery, AJAX, JSON controllers, usage of HTML5 elements and clean code.

See how it looks: http://www.youtube.com/watch?v=7v7x9P_cRyQ

### Purpose ###
I created this app purely to learn Zend 2 in order to show my ZEND skills. I knew nothing about ZEND in the beginning but I got the app running in 4 days. I no longer need it but I guess I can throw it out in the public so someone can have an example app.

### Install ###
  1. To install, run in the root directory, **php composer.phar install**.
  1. Create a database (you need **MySQL**)
  1. Run the file ./companies.sql.
  1. Edit ./config/autoload/global.php and enter the database name.
  1. Create a file ./config/autoload/local.php with the database auth info.:

```
<?php

return array(
     'db' => array(
         'username' => 'username',
         'password' => 'pwd',
     ),
 );
```

### Other info ###
After finishing this stage of the app, I did not finish all the CRUD functionality, nor pagination, nor created unit tests. It was enough as is.

If you find any of the code useful I'll be happy. Drop me a note theastar94@gmail.com!