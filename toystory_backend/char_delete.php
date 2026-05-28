<?php
session_start();
if (!isset($_SESSION['username'])) { header("Location: login.php"); exit(); }

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id) {
    require_once "conn.php";
    $q   = mysqli_query($conn, "SELECT name FROM $tablechar WHERE id=$id");
    $row = mysqli_fetch_assoc($q);
    $name = $row ? $row['name'] : 'Character';
    mysqli_query($conn, "DELETE FROM $tablechar WHERE id=$id");
    $_SESSION['status'] = "\"$name\" deleted successfully.";
}
header("Location: dashboard.php#characters-section");
exit();
?>
