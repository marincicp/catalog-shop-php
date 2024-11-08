

# Product Catalog Shop API

- **Product Listing and Filtering**: 
  - Retrieve all products or view specific items by SKU.
  - Filter products by:
    - **Name**
    - **Category**
    - **Product Type**
    - **Price Range** (min and max)

- **Product Management**:
  - Authenticated users can add new products
  - Users can edit or delete only the products they own

- **User Authentication and Access Control**:
  - Secure registration and login with JWT-based authorization
  - Only authenticated users can perform product management actions

### Frontend Repository :  [Catalog Shop React](https://github.com/marincicp/catalog-shop-react)

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


