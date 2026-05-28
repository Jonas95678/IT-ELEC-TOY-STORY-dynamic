<?php
session_start();
if (!isset($_SESSION['username'])) { header("Location: login.php"); exit(); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once "conn.php";

    $title        = mysqli_real_escape_string($conn, trim($_POST['title']));
    $release_year = (int) $_POST['release_year'];
    $tagline      = mysqli_real_escape_string($conn, trim($_POST['tagline']));
    $runtime      = (int) $_POST['runtime'];
    $rating       = isset($_POST['rating']) && $_POST['rating'] !== '' ? (float) $_POST['rating'] : 0.0;
    $poster_url   = mysqli_real_escape_string($conn, trim($_POST['poster_url']));
    $is_displayed = isset($_POST['is_displayed']) ? (int)$_POST['is_displayed'] : 1;

    $sql = "INSERT INTO $tablemovies (title, release_year, tagline, runtime, rating, poster_url, is_displayed)
            VALUES ('$title', $release_year, '$tagline', $runtime, $rating, '$poster_url', $is_displayed)";

    if (mysqli_query($conn, $sql)) {
        $_SESSION['status'] = "Movie \"$title\" added successfully!";
    } else {
        $_SESSION['status']      = "Error adding movie: " . mysqli_error($conn);
        $_SESSION['status_type'] = 'error';
    }
    header("Location: dashboard.php#movies-section");
    exit();
}
header("Location: dashboard.php");
exit();
?>
