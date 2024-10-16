# Instructions for Setting Up the Project

For the local Nginx and MySQL server, as well as for creating the project (Quick App -> Blank), I used Laragon. [Laragon](https://laragon.org)
The project should be accessible at the URL: http://catalog-shop.test:8080.

After that, you need to create a database named "catalog" or you can modify the settings in **Core/config.php**.

Install **composer**
- composer init
- composer install
- composer dump-autoload 

Then, in **index.php**, you need to run the commented-out code once to create the tables and insert the data.

![image](https://github.com/user-attachments/assets/2064a466-3e37-4def-9ced-08c83ab204cb)
