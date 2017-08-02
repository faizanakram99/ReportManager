# ReportManager
Report Manager in PHP and AngularJS

A simple single page application with Front-end in AngularJS and back-end in PHP and MySQL.

Requirements
-------------
- PHP, MySQL, Apache or Nginx (Install [XAMPP](https://www.apachefriends.org/download.html) to get them all)
- [composer](https://getcomposer.org/download)


Instructions
----------------
- Create a database in MySQL named `reportmanager` (create database `reportmanager`)
- Import `reportmanager.sql` to update schema 
  - From root directory login to `mysql` in Command Prompt or Bash (usually `mysql -u root`)
  - Import `reportmanager.sql` (`source reportmanager.sql`) 
- Run `composer install`
- Update `src/Reports/Parameters.php` with your email, password, etc
- If your database has some root password, update `src/Reports/ReportEntity.php` accordingly.
