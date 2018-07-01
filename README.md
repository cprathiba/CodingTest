# Student Management System
A simple one page web application to manage student data

## Features
Basic Students data can be captured.
Details are saved in a table format on the same page 

## Requirements
* PHP Version - 5.6.23 or above
* MySQL Version - 5.6.30 or above

## SetUp
1. Unzip the source files and place the directory in the web root.
2. Create a database in the MySQL server and restore the database backup (/database/sprii-database-backup.sql) to it.
3. Open the config.xml file and change the following tags:
... <installPath> - Physical path of the source files. (Eg: /var/www/html/sprii)
... <systemUrl> - URL of the app: http://localhost/sprii/web
... Under <db>, the host, port, database name, username and the password
4. Access the application from the web browser. (Eg: http://localhost/sprii/web)
