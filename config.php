<?php
$host = "localhost";
$username = "museum_usr";
$password = "Zshotaeff96";
$database = "museum";

$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
// Языки
$language = isset($_GET['lang']) ? $_GET['lang'] : 'ru'; // Default to Russian
if ($language == 'kk') {
    include 'lang_kk.php';
} elseif ($language == 'en') {
    include 'lang_en.php';
} else {
    include 'lang_ru.php'; // Default language is Russian
}
