# ReportManager
Report Manager in PHP and AngularJS

A simple SPA (Single Page Application) in AngularJS with REST API (using PHP ).

Requirements
-------------
- PHP, MySQL, Apache or Nginx (Install [XAMPP](https://www.apachefriends.org/download.html) to get them all)
- [composer](https://getcomposer.org/download)


Instructions
----------------
- Update `config/db.yaml` with database connection parameters (like `username`, `host`, `password`, etc)
- Create a database with the same as value of `dbname` in `config/db.yml`
- Run `composer install` (database schema shall update automatically as scripts for updating schema is included in `composer.json`)
- Update `email` parameters in `config/email.yaml`
