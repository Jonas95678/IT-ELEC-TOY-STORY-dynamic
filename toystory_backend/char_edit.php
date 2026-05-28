<?php
session_start();
if (!isset($_SESSION['username'])) { header("Location: login.php"); exit(); }
require_once "conn.php";

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) { header("Location: dashboard.php"); exit(); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name         = mysqli_real_escape_string($conn, trim($_POST['name']));
    $role         = mysqli_real_escape_string($conn, trim($_POST['role']));
    $quote        = mysqli_real_escape_string($conn, trim($_POST['quote']));
    $description  = mysqli_real_escape_string($conn, trim($_POST['description']));
    $avatar_url   = mysqli_real_escape_string($conn, trim($_POST['avatar_url']));
    $css_class    = mysqli_real_escape_string($conn, trim($_POST['css_class']));
    $is_displayed = (int) $_POST['is_displayed'];

    $sql = "UPDATE $tablechar SET
                name='$name', role='$role', quote='$quote', description='$description',
                avatar_url='$avatar_url', css_class='$css_class', is_displayed=$is_displayed
            WHERE id=$id";

    if (mysqli_query($conn, $sql)) {
        $_SESSION['status'] = "Character \"$name\" updated successfully!";
    } else {
        $_SESSION['status']      = "Error: " . mysqli_error($conn);
        $_SESSION['status_type'] = 'error';
    }
    header("Location: dashboard.php#characters-section");
    exit();
}

$q   = mysqli_query($conn, "SELECT * FROM $tablechar WHERE id=$id");
$row = mysqli_fetch_assoc($q);
if (!$row) { header("Location: dashboard.php"); exit(); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Character | Toy Story Admin</title>
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
                <a href="dashboard.php#characters-section" class="btn btn-secondary cinematic-btn" style="padding:.4rem 1rem; font-size:.85rem;">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h2 style="font-family:'Bangers',cursive; color:#F4C542; font-size:1.8rem; letter-spacing:1px; margin:0;">
                    <i class="fas fa-user-edit me-2"></i>Edit Character
                </h2>
            </div>

            <form action="" method="POST">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control glass-input" name="name" required
                               value="<?php echo htmlspecialchars($row['name']); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Role <span class="text-danger">*</span></label>
                        <input type="text" class="form-control glass-input" name="role" required
                               value="<?php echo htmlspecialchars($row['role']); ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Famous Quote <span class="text-danger">*</span></label>
                        <textarea class="form-control glass-input" name="quote" rows="2" required><?php echo htmlspecialchars($row['quote']); ?></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Description <span class="text-danger">*</span></label>
                        <textarea class="form-control glass-input" name="description" rows="3" required><?php echo htmlspecialchars($row['description']); ?></textarea>
                    </div>
                    <div class="col-md-7">
                        <label class="form-label">Avatar Image Path <span class="text-danger">*</span></label>
                        <input type="text" class="form-control glass-input" name="avatar_url" required
                               value="<?php echo htmlspecialchars($row['avatar_url']); ?>">
                        <small style="color:rgba(255,255,255,.4);">e.g. <code style="color:#F4C542;">img/woody.jpg</code></small>
                    </div>
                    <div class="col-md-5 d-flex flex-column align-items-start">
                        <label class="form-label">Current Avatar</label>
                        <img src="<?php echo htmlspecialchars($row['avatar_url']); ?>" alt="avatar"
                             style="width:60px; height:60px; object-fit:cover; border-radius:50%; border:2px solid rgba(255,255,255,.3);"
                             onerror="this.src='img/woody.jpg'">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">CSS Card Class</label>
                        <select class="form-control glass-input" name="css_class">
                            <?php
                            $classes = ['woody','buzz','jessie','rexy','ham','slinky'];
                            foreach ($classes as $cls) {
                                $sel = ($row['css_class'] === $cls) ? 'selected' : '';
                                echo "<option value=\"$cls\" $sel>$cls</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">Display on Website?</label>
                        <select class="form-control glass-input" name="is_displayed">
                            <option value="1" <?php echo $row['is_displayed'] ? 'selected' : ''; ?>>Yes — Show</option>
                            <option value="0" <?php echo !$row['is_displayed'] ? 'selected' : ''; ?>>No — Hide</option>
                        </select>
                    </div>
                </div>
                <div class="d-flex gap-3 mt-4 justify-content-end">
                    <a href="dashboard.php#characters-section" class="btn btn-secondary cinematic-btn">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary cinematic-btn">
                        <i class="fas fa-save"></i> Update Character <span class="btn-shine"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
