<?php

namespace Core;

use PDO;
use PDOException;

require "functions.php";

class Database

{
   public $conn;
   public $stmt;

   public function __construct($config)
   {
      $this->connect($config);
   }

   protected function connect($config)
   {
      try {
         $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['dbname']};charset={$config['charset']}";
         $this->conn = new PDO(
            $dsn,
            $username = "root",
            $password = "",
            [PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
         );
      } catch (PDOException $err) {
         abort(500, "Failed connect to database" . $err->getMessage());
      }
   }



   public function query($query, $params = [])
   {
      $this->stmt = $this->conn->prepare($query);
      $this->stmt->execute($params);
      return $this;
   }


   public function get()
   {
      return $this->stmt->fetchAll();
   }

   public function find()
   {
      return $this->stmt->fetch();
   }

   public function createTables()
   {
      try {
         $this->query("
            CREATE TABLE IF NOT EXISTS users (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                email VARCHAR(255) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB;
        ");


         $this->conn->query("
               CREATE TABLE IF NOT EXISTS categories (
                   id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                   name VARCHAR(255) NOT NULL,
                   created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                   updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
               ) ENGINE=InnoDB;
           ");

         $this->conn->query("
               CREATE TABLE IF NOT EXISTS products (
                   id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                   SKU VARCHAR(100) NOT NULL UNIQUE,
                   name VARCHAR(255) NOT NULL,
                   description TEXT,
                   price DECIMAL(10, 2) NOT NULL,
                   type ENUM('physical', 'virtual') NOT NULL,
                   category_id BIGINT UNSIGNED,
                   user_id BIGINT UNSIGNED,
                   image_url VARCHAR(255),
                   created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                   updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                   FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
                   FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
               ) ENGINE=InnoDB;
           ");

         $this->conn->query("
               CREATE TABLE IF NOT EXISTS attributes (
                   id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                   product_id BIGINT UNSIGNED NOT NULL,
                   attribute VARCHAR(100) NOT NULL,
                   value VARCHAR(255) NOT NULL,
                   FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
               ) ENGINE=InnoDB;
           ");
      } catch (PDOException $err) {
         abort(500, "Failed to create table: " . $err->getMessage());
      }
   }

   public function insertDataIntoTable($data, $sql)
   {

      try {
         $this->stmt = $this->conn->prepare($sql);

         foreach ($data as $item) {
            $this->stmt->execute($item);
         }
      } catch (PDOException $err) {
         abort(500, $err->getMessage());
      }
   }
}