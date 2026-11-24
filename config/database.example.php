<?php

define("BASE_URL", "/cisalada-go/");

$host = "localhost";
$user = "root";
$password = "";
$database = "nama_database_kamu";

$conn = mysqli_connect(
    $host,
    $user,
    $password,
    $database
);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}