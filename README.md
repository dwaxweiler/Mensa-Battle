# Mensa Battle Web
prototype of a RESTFul API and a Facebook App for Mensa Battle - a platform for sharing images of art created with food leftovers from university canteens

released under The MIT License, see LICENSE.txt file for more details

developed in winter term 2012 / 2013 at Saarland University

Mensa Battle is based on the PHP framework [Symfony](https://symfony.com/) 2.1.1 Standard Edition.

## Setup
1. Download this code to a folder.
2. Run an HTTP and a database server.
3. Visit `http://localhost/mensabattle/web/config.php` from a browser or run `php app/check.php` from the command line to check your system configuration.
Fix everything.
4. Create the file `app/config/parameters.yml` based on `app/config/parameters.yml.dist`.
5. Create the database by running `php app/console doctrine:database:create` and all the tables by running `php app/console doctrine:schema:update --force` from the command line.
