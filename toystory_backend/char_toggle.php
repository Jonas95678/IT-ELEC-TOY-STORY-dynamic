<?php
session_start();
if (!isset($_SESSION['username'])) { header("Location: login.php"); exit(); }

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id) {
    require_once "conn.php";
    mysqli_query($conn, "UPDATE $tablechar SET is_displayed = 1 - is_displayed WHERE id=$id");
    $q   = mysqli_query($conn, "SELECT name, is_displayed FROM $tablechar WHERE id=$id");
    $row = mysqli_fetch_assoc($q);
    $state = $row['is_displayed'] ? 'now shown on website' : 'now hidden from website';
    $_SESSION['status'] = "\"" . $row['name'] . "\" is $state.";
}
header("Location: dashboard.php#characters-section");
exit();
?>
