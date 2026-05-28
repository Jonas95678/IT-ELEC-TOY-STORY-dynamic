<?php
session_start();
if (!isset($_SESSION['username'])) { header("Location: login.php"); exit(); }

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id) {
    require_once "conn.php";
    $q   = mysqli_query($conn, "SELECT title FROM $tablemovies WHERE id=$id");
    $row = mysqli_fetch_assoc($q);
    $title = $row ? $row['title'] : 'Movie';
    mysqli_query($conn, "DELETE FROM $tablemovies WHERE id=$id");
    $_SESSION['status'] = "\"$title\" deleted successfully.";
}
header("Location: dashboard.php#movies-section");
exit();
?>
