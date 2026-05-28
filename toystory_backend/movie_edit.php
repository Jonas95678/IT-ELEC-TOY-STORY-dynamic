<?php
session_start();
if (!isset($_SESSION['username'])) { header("Location: login.php"); exit(); }
require_once "conn.php";

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) { header("Location: dashboard.php"); exit(); }

// Handle POST update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title        = mysqli_real_escape_string($conn, trim($_POST['title']));
    $release_year = (int) $_POST['release_year'];
    $tagline      = mysqli_real_escape_string($conn, trim($_POST['tagline']));
    $runtime      = (int) $_POST['runtime'];
    $rating       = isset($_POST['rating']) && $_POST['rating'] !== '' ? (float) $_POST['rating'] : 0.0;
    $poster_url   = mysqli_real_escape_string($conn, trim($_POST['poster_url']));
    $is_displayed = (int) $_POST['is_displayed'];

    $sql = "UPDATE $tablemovies SET
                title='$title', release_year=$release_year, tagline='$tagline',
                runtime=$runtime, rating=$rating, poster_url='$poster_url', is_displayed=$is_displayed
            WHERE id=$id";

    if (mysqli_query($conn, $sql)) {
        $_SESSION['status'] = "Movie \"$title\" updated successfully!";
    } else {
        $_SESSION['status']      = "Error updating movie: " . mysqli_error($conn);
        $_SESSION['status_type'] = 'error';
    }
    header("Location: dashboard.php#movies-section");
    exit();
}

// Fetch movie for form
$q   = mysqli_query($conn, "SELECT * FROM $tablemovies WHERE id=$id");
$row = mysqli_fetch_assoc($q);
if (!$row) { header("Location: dashboard.php"); exit(); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Movie | Toy Story Admin</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="admin-style.css">
    <link href="https://fonts.googleapis.com/css2?family=Bangers&family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .glass-input { background:rgba(255,255,255,.1)!important; border:1px solid rgba(255,255,255,.2)!important; color:#fff!important; border-radius:10px; }
        .glass-input::placeholder { color:rgba(255,255,255,.4); }
        .glass-input:focus { box-shadow:0 0 0 2px rgba(244,197,66,.4)!important; }
        textarea.glass-input { resize:vertical; min-height:80px; }
        .edit-card { max-width:640px; margin:0 auto; padding:2.5rem; border-radius:20px; }
        .form-label { color:rgba(255,255,255,.85); font-size:.85rem; font-weight:600; }
    </style>
</head>
<body class="admin-dashboard-body">
    <div class="background-container cinematic-bg admin-bg">
        <div class="sky-background night-sky"></div>
        <div class="stars-container"><div class="star-layer star-layer-1"></div><div class="star-layer star-layer-2"></div></div>
        <div class="moon-glow"></div>
    </div>

    <div class="container py-5" style="position:relative;z-index:10;">
        <div class="glassmorphism-card edit-card">
            <div class="d-flex align-items-center gap-3 mb-4">
                <a href="dashboard.php#movies-section" class="btn btn-secondary cinematic-btn" style="padding:.4rem 1rem; font-size:.85rem;">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h2 style="font-family:'Bangers',cursive; color:#F4C542; font-size:1.8rem; letter-spacing:1px; margin:0;">
                    <i class="fas fa-film me-2"></i>Edit Movie
                </h2>
            </div>

            <form action="" method="POST">
                <div class="row g-3">
                    <div class="col-md-7">
                        <label class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control glass-input" name="title" required
                               value="<?php echo htmlspecialchars($row['title']); ?>">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">Release Year <span class="text-danger">*</span></label>
                        <input type="number" class="form-control glass-input" name="release_year" min="1990" max="2030" required
                               value="<?php echo $row['release_year']; ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Runtime (min) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control glass-input" name="runtime" min="30" max="300" required
                               value="<?php echo $row['runtime']; ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Rating (0–10)</label>
                        <input type="number" class="form-control glass-input" name="rating" min="0" max="10" step="0.1"
                               value="<?php echo $row['rating']; ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Tagline <span class="text-danger">*</span></label>
                        <textarea class="form-control glass-input" name="tagline" rows="2" required><?php echo htmlspecialchars($row['tagline']); ?></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Poster Image Path <span class="text-danger">*</span></label>
                        <input type="text" class="form-control glass-input" name="poster_url" required
                               value="<?php echo htmlspecialchars($row['poster_url']); ?>">
                        <small style="color:rgba(255,255,255,.4);">e.g. <code style="color:#F4C542;">img/toystory1.webp</code></small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Current Poster Preview</label><br>
                        <img src="<?php echo htmlspecialchars($row['poster_url']); ?>" alt="poster"
                             style="height:80px; border-radius:8px; border:2px solid rgba(255,255,255,.2);"
                             onerror="this.src='img/toystory1.webp'">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Display on Website?</label>
                        <select class="form-control glass-input" name="is_displayed">
                            <option value="1" <?php echo $row['is_displayed'] ? 'selected' : ''; ?>>Yes — Show</option>
                            <option value="0" <?php echo !$row['is_displayed'] ? 'selected' : ''; ?>>No — Hide</option>
                        </select>
                    </div>
                </div>
                <div class="d-flex gap-3 mt-4 justify-content-end">
                    <a href="dashboard.php#movies-section" class="btn btn-secondary cinematic-btn">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary cinematic-btn">
                        <i class="fas fa-save"></i> Update Movie <span class="btn-shine"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
