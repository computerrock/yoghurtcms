Yoghurt CMS
===========

A lightweight CMS built around the Symfony 2 PHP Webframework.


Yoghurt is a simple CMS backend based on Symfony and Doctrine.
It's intended purpouse is to speed up custom solutions development by providing 
out of the box backend. It allows user to define his own content types without 
any programming.

1) Installation
---------------

### 1) Download Yoghurt CMS and place it into your web root

### 2) Check your System Configuration

Before you begin, make sure that your local system is properly configured
for Symfony. To do this, execute the following:

    php app/check.php

If you get any warnings or recommendations, fix these now before moving on.

### 3) Install the Vendor Libraries

Run the following:

    php bin/vendors install

Note that you **must** have git installed and be able to execute the `git`
command to execute this script. If you don't have git available, either install
it or upload a compleate vendor folder with all declared dependancies (you can 
setup the CMS on a local computer and use that vendor directory).

### 4) Setup database access

Edit app/config/parameters.ini. Set the appropriate database parameters.

### 5) Create database tables

Run the following:

    php app/console doctrine:schema:update --force

### 6) Load basic data

Run the following:

    php app/console doctrine:fixtures:load

### 7) Access the Application via the Browser

Go to http://yourdomain.com/admin and login with admin credentials
username: admin
password: pass

3) Take a look at the user's manual!
------------------------------------

    https://docs.google.com/a/spoiledmilk.com/document/d/1bg0cnbBeGgMkfYjGtZHEk2C8x5cLex_joAH10K89ZRQ/edit?usp=sharing

