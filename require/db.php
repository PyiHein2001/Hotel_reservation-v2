<?php
 
$host = 'localhost';
$username = 'root';
$password = '';

$mysqli = new mysqli($host, $username, $password);
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    exit();
}

create_database($mysqli);
select_db($mysqli);
create_table($mysqli);

function create_database($mysqli){
    $sql = "CREATE DATABASE IF NOT EXISTS `hotel_sg`
            DEFAULT CHARACTER SET utf8mb4
            COLLATE utf8mb4_unicode_ci";
    if ($mysqli->query($sql) === TRUE){
        return true;
    }
    return false;
}

function select_db($mysqli){
    if ($mysqli->select_db("hotel_sg")){
        return true;
    }
    return false;
}

function create_table($mysqli){

    // users
    $sql = "CREATE TABLE IF NOT EXISTS `hotel_sg`.`users` 
            (
                `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `name` VARCHAR(100) NOT NULL,
                `email` VARCHAR(100) NOT NULL UNIQUE,
                `password` VARCHAR(100) NOT NULL,
                `role` ENUM ('admin', 'user') NOT NULL,
                `phone` VARCHAR(100) NOT NULL,
                `gender` ENUM ('male', 'female') NOT NULL,
                `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )";
    if ($mysqli->query($sql) === FALSE) return false;

    // hotel_settings
    $sql = "CREATE TABLE IF NOT EXISTS `hotel_sg`.`hotel_settings`
            (
                `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `name` VARCHAR(120) NOT NULL,
                `email` VARCHAR(120) NOT NULL,
                `address` LONGTEXT NOT NULL,
                `check_in_time` TIME NOT NULL,
                `check_out_time` TIME NOT NULL,
                `phone` VARCHAR(200) NOT NULL,
                `logo_path` VARCHAR(200) NOT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )";
    if ($mysqli->query($sql) === FALSE) return false;

    // customers
    $sql = "CREATE TABLE IF NOT EXISTS `hotel_sg`.`customers`
            (
                `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `name` VARCHAR(120) NOT NULL,
                `phone` VARCHAR(30) NOT NULL,
                `email` VARCHAR(100) NOT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )";
    if ($mysqli->query($sql) === FALSE) return false;

    // amenities
    $sql = "CREATE TABLE IF NOT EXISTS `hotel_sg`.`amenities`
            (
                `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `name` VARCHAR(200) NOT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )";
    if ($mysqli->query($sql) === FALSE) return false;

    // bed_types
    $sql = "CREATE TABLE IF NOT EXISTS `hotel_sg`.`bed_types`
            (
                `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `name` VARCHAR(100) NOT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )";
    if ($mysqli->query($sql) === FALSE) return false;

    // rooms
    $sql = "CREATE TABLE IF NOT EXISTS `hotel_sg`.`rooms`
            (
                `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `name` VARCHAR(250) NOT NULL UNIQUE,
                `size` DOUBLE NOT NULL,
                `occupancy` MEDIUMINT NOT NULL,
                `bed_type_id` BIGINT UNSIGNED NOT NULL,
                `description` LONGTEXT NOT NULL,
                `detail` LONGTEXT NOT NULL,
                `price_per_day` DECIMAL(10,2) NOT NULL,
                `extra_bed_price_per_day` DECIMAL(10,2) NOT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (bed_type_id) REFERENCES bed_types(id) ON DELETE CASCADE               
            )";
    if ($mysqli->query($sql) === FALSE) return false;

    // room_galleries
    $sql = "CREATE TABLE IF NOT EXISTS `hotel_sg`.`room_galleries`
            (
                `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `room_id` BIGINT UNSIGNED NOT NULL,
                `image_path` VARCHAR(255) NOT NULL,
                `image_name` VARCHAR(255),
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE
            )";
    if ($mysqli->query($sql) === FALSE) return false;

    // room_amenities
    $sql = "CREATE TABLE IF NOT EXISTS `hotel_sg`.`room_amenities`
            (
                `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `room_id` BIGINT UNSIGNED NOT NULL,
                `amenity_id` BIGINT UNSIGNED NOT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE,
                FOREIGN KEY (amenity_id) REFERENCES amenities(id) ON DELETE CASCADE
            )";
    if ($mysqli->query($sql) === FALSE) return false;

    // bookings
    $sql = "CREATE TABLE IF NOT EXISTS `hotel_sg`.`bookings`
            (
                `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `room_id` BIGINT UNSIGNED NOT NULL,
                `customer_id` BIGINT UNSIGNED NOT NULL,
                `is_extra_bed` TINYINT,
                `price` DECIMAL(10,2) NOT NULL,
                `check_in_date` DATE NOT NULL,
                `check_out_date` DATE NOT NULL,
                `status` TINYINT NOT NULL,
                `booked_by_user_id` INT,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE,
                FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
                FOREIGN KEY (booked_by_user_id) REFERENCES users(id) ON DELETE SET NULL
            )";
    if ($mysqli->query($sql) === FALSE) return false;

}
