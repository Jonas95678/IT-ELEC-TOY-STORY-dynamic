<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
require_once "conn.php";

// ── Flash messages ──────────────────────────────────────────
$status = '';
if (isset($_SESSION['status'])) {
    $status = $_SESSION['status'];
    unset($_SESSION['status']);
}
$statusType = isset($_SESSION['status_type']) ? $_SESSION['status_type'] : 'success';
if (isset($_SESSION['status_type'])) unset($_SESSION['status_type']);

// ── Counts for stat cards ────────────────────────────────────
$totalMovies    = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM $tablemovies"));
$totalChars     = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM $tablechar"));
$displayMovies  = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM $tablemovies WHERE is_displayed=1"));
$displayChars   = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM $tablechar WHERE is_displayed=1"));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TOY STORY Admin Dashboard | Manage Content</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="admin-style.css">
    <link href="https://fonts.googleapis.com/css2?family=Bangers&family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .badge-on  { background:#22c55e; color:#fff; padding:3px 10px; border-radius:20px; font-size:.75rem; font-weight:700; }
        .badge-off { background:#ef4444; color:#fff; padding:3px 10px; border-radius:20px; font-size:.75rem; font-weight:700; }
        .btn-toggle-on  { background:rgba(239,68,68,.2); border:1px solid #ef4444; color:#ef4444; }
        .btn-toggle-on:hover  { background:#ef4444; color:#fff; }
        .btn-toggle-off { background:rgba(34,197,94,.2); border:1px solid #22c55e; color:#22c55e; }
        .btn-toggle-off:hover { background:#22c55e; color:#fff; }
        .glass-input {
            background: rgba(255,255,255,0.1) !important;
            border: 1px solid rgba(255,255,255,0.2) !important;
            color: #fff !important;
            border-radius: 10px;
        }
        .glass-input::placeholder { color: rgba(255,255,255,0.4); }
        .glass-input:focus { box-shadow: 0 0 0 2px rgba(244,197,66,.4) !important; outline: none; }
        .modal-overlay {
            position: fixed; inset: 0; background: rgba(0,0,0,.7);
            display: flex; align-items: center; justify-content: center;
            z-index: 9999; padding: 20px;
        }
        .modal-overlay.hidden { display: none !important; }
        .admin-modal { max-width: 580px; width: 100%; padding: 2rem; border-radius: 20px; max-height: 90vh; overflow-y: auto; }
        .admin-modal .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; }
        .admin-modal h2 { color: #F4C542; font-family: 'Bangers', cursive; font-size: 1.6rem; letter-spacing: 1px; margin: 0; }
        .modal-close { background: none; border: none; color: rgba(255,255,255,.6); font-size: 1.2rem; cursor: pointer; }
        .modal-close:hover { color: #ef4444; }
        .form-label { color: rgba(255,255,255,.85); font-size: .85rem; font-weight: 600; margin-bottom: .3rem; display: block; }
        .modal-actions { display: flex; gap: 1rem; justify-content: flex-end; margin-top: 1.5rem; }
        .preview-img { width: 50px; height: 60px; object-fit: cover; border-radius: 6px; border: 2px solid rgba(255,255,255,.2); }
        .avatar-preview img { width: 45px; height: 45px; object-fit: cover; border-radius: 50%; border: 2px solid rgba(255,255,255,.2); }
        .section-anchor { padding-top: 20px; }
        textarea.glass-input { resize: vertical; min-height: 80px; }
    </style>
</head>
<body class="admin-dashboard-body">

    <!-- Animated Background -->
    <div class="background-container cinematic-bg admin-bg">
        <div class="sky-background night-sky"></div>
        <div class="stars-container">
            <div class="star-layer star-layer-1"></div>
            <div class="star-layer star-layer-2"></div>
            <div class="star-layer star-layer-3"></div>
        </div>
        <div class="clouds-container night-clouds">
            <div class="cloud cloud-1"></div><div class="cloud cloud-2"></div><div class="cloud cloud-3"></div>
        </div>
        <div class="moon-glow"></div>
        <div class="lamp-glow"></div>
    </div>

    <div class="custom-cursor" id="customCursor"></div>
    <div class="cursor-glow" id="cursorGlow"></div>

    <!-- Admin Layout -->
    <div class="admin-layout">

        <!-- ═══ SIDEBAR ═══ -->
        <aside class="admin-sidebar" id="adminSidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo"><i class="fas fa-hat-cowboy"></i></div>
                <span class="sidebar-title">TOY STORY</span>
                <button class="sidebar-toggle" id="sidebarToggle"><i class="fas fa-bars"></i></button>
            </div>
            <nav class="sidebar-nav">
                <ul class="nav-list">
                    <li class="nav-item active">
                        <a href="#dashboard" class="nav-link">
                            <i class="fas fa-tachometer-alt"></i><span class="nav-text">Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#movies-section" class="nav-link">
                            <i class="fas fa-film"></i><span class="nav-text">Movies</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#characters-section" class="nav-link">
                            <i class="fas fa-users"></i><span class="nav-text">Characters</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="index.php" class="nav-link" target="_blank">
                            <i class="fas fa-globe"></i><span class="nav-text">View Website</span>
                        </a>
                    </li>
                </ul>
            </nav>
            <div class="sidebar-footer">
                <a href="logout.php" class="nav-link logout-link">
                    <i class="fas fa-sign-out-alt"></i><span class="nav-text">Logout</span>
                </a>
            </div>
        </aside>

        <!-- ═══ MAIN CONTENT ═══ -->
        <main class="admin-main">

            <!-- Top Bar -->
            <header class="admin-topbar glassmorphism-card">
                <div class="topbar-left">
                    <button class="mobile-menu-toggle" id="mobileMenuToggle"><i class="fas fa-bars"></i></button>
                    <div class="admin-greeting">
                        <h1>Welcome back, <span class="highlight"><?php echo htmlspecialchars($_SESSION['username']); ?></span>!</h1>
                        <p>Manage your Toy Story fan site content</p>
                    </div>
                </div>
                <div class="topbar-right">
                    <a href="index.php" target="_blank" class="btn btn-secondary cinematic-btn me-2" style="font-size:.8rem; padding:.4rem 1rem;">
                        <i class="fas fa-external-link-alt me-1"></i> View Site
                    </a>
                    <div class="profile-avatar">
                        <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Ccircle cx='50' cy='50' r='50' fill='%23F4C542'/%3E%3Ctext x='50' y='60' font-size='45' text-anchor='middle' fill='%231a2744' font-family='Bangers'%3E<?php echo strtoupper(substr($_SESSION['username'],0,1)); ?>%3C/text%3E%3C/svg%3E" alt="Avatar" class="avatar-img">
                    </div>
                </div>
            </header>

            <div class="admin-content" id="dashboard">

                <!-- Flash Message -->
                <?php if ($status): ?>
                <div class="alert alert-<?php echo $statusType === 'error' ? 'danger' : 'success'; ?> alert-dismissible fade show mx-3 mt-3" role="alert">
                    <i class="fas fa-<?php echo $statusType === 'error' ? 'exclamation-circle' : 'check-circle'; ?> me-2"></i>
                    <?php echo htmlspecialchars($status); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>

                <!-- ── Stats ── -->
                <section class="stats-section">
                    <div class="stats-grid">
                        <div class="stat-card glassmorphism-card">
                            <div class="stat-icon movie-icon"><i class="fas fa-film"></i></div>
                            <div class="stat-info">
                                <h3 class="stat-number"><?php echo $totalMovies; ?></h3>
                                <p class="stat-label">Total Movies</p>
                            </div>
                            <div class="stat-glow"></div>
                        </div>
                        <div class="stat-card glassmorphism-card">
                            <div class="stat-icon character-icon"><i class="fas fa-users"></i></div>
                            <div class="stat-info">
                                <h3 class="stat-number"><?php echo $totalChars; ?></h3>
                                <p class="stat-label">Total Characters</p>
                            </div>
                            <div class="stat-glow"></div>
                        </div>
                        <div class="stat-card glassmorphism-card">
                            <div class="stat-icon update-icon"><i class="fas fa-eye"></i></div>
                            <div class="stat-info">
                                <h3 class="stat-number"><?php echo $displayMovies; ?></h3>
                                <p class="stat-label">Movies Displayed</p>
                            </div>
                            <div class="stat-glow"></div>
                        </div>
                        <div class="stat-card glassmorphism-card">
                            <div class="stat-icon session-icon"><i class="fas fa-star"></i></div>
                            <div class="stat-info">
                                <h3 class="stat-number"><?php echo $displayChars; ?></h3>
                                <p class="stat-label">Characters Displayed</p>
                            </div>
                            <div class="stat-glow"></div>
                        </div>
                    </div>
                </section>

                <!-- ════════════════════════════════════════════
                     MOVIES MANAGEMENT
                     ════════════════════════════════════════════ -->
                <section class="content-section section-anchor" id="movies-section">
                    <div class="section-header glassmorphism-card">
                        <div class="section-title-wrapper">
                            <h2><i class="fas fa-film"></i> Movies Management</h2>
                            <p>Add, edit, delete and control visibility on the website</p>
                        </div>
                        <button class="btn btn-primary cinematic-btn" id="addMovieBtn">
                            <i class="fas fa-plus"></i> <span>Add New Movie</span>
                            <span class="btn-shine"></span>
                        </button>
                    </div>

                    <div class="table-container glassmorphism-card">
                        <table class="admin-table table table-striped table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Poster</th>
                                    <th>Title</th>
                                    <th>Year</th>
                                    <th>Runtime</th>
                                    <th>Rating</th>
                                    <th>Status</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            $mq = mysqli_query($conn, "SELECT * FROM $tablemovies ORDER BY release_year ASC");
                            if (mysqli_num_rows($mq) > 0):
                                while ($m = mysqli_fetch_assoc($mq)):
                            ?>
                            <tr>
                                <td><?php echo $m['id']; ?></td>
                                <td>
                                    <img src="<?php echo htmlspecialchars($m['poster_url']); ?>"
                                         class="preview-img" alt="<?php echo htmlspecialchars($m['title']); ?>"
                                         onerror="this.src='img/toystory1.webp'">
                                </td>
                                <td><strong><?php echo htmlspecialchars($m['title']); ?></strong></td>
                                <td><?php echo $m['release_year']; ?></td>
                                <td><?php echo $m['runtime']; ?> min</td>
                                <td><i class="fas fa-star" style="color:#F4C542;"></i> <?php echo $m['rating']; ?></td>
                                <td>
                                    <?php if ($m['is_displayed']): ?>
                                        <span class="badge-on"><i class="fas fa-eye me-1"></i>Shown</span>
                                    <?php else: ?>
                                        <span class="badge-off"><i class="fas fa-eye-slash me-1"></i>Hidden</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="action-buttons d-flex gap-1 justify-content-center flex-wrap">
                                        <!-- Display Toggle -->
                                        <a href="movie_toggle.php?id=<?php echo $m['id']; ?>"
                                           class="btn btn-sm <?php echo $m['is_displayed'] ? 'btn-toggle-on' : 'btn-toggle-off'; ?>"
                                           title="<?php echo $m['is_displayed'] ? 'Hide from website' : 'Show on website'; ?>"
                                           style="border-radius:8px; padding:4px 10px;">
                                            <i class="fas fa-<?php echo $m['is_displayed'] ? 'eye-slash' : 'eye'; ?>"></i>
                                            <?php echo $m['is_displayed'] ? 'Hide' : 'Show'; ?>
                                        </a>
                                        <!-- Edit -->
                                        <a href="movie_edit.php?id=<?php echo $m['id']; ?>"
                                           class="btn btn-sm btn-warning" style="border-radius:8px; padding:4px 10px;">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <!-- Delete -->
                                        <a href="movie_delete.php?id=<?php echo $m['id']; ?>"
                                           class="btn btn-sm btn-danger" style="border-radius:8px; padding:4px 10px;"
                                           onclick="return confirm('Delete this movie?');">
                                            <i class="fas fa-trash"></i> Delete
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php
                                endwhile;
                            else:
                            ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="fas fa-film me-2"></i> No movies found. Add one!
                                </td>
                            </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- ════════════════════════════════════════════
                     CHARACTERS MANAGEMENT
                     ════════════════════════════════════════════ -->
                <section class="content-section section-anchor" id="characters-section">
                    <div class="section-header glassmorphism-card">
                        <div class="section-title-wrapper">
                            <h2><i class="fas fa-users"></i> Characters Management</h2>
                            <p>Add, edit, delete and control visibility on the website</p>
                        </div>
                        <button class="btn btn-primary cinematic-btn" id="addCharacterBtn">
                            <i class="fas fa-plus"></i> <span>Add New Character</span>
                            <span class="btn-shine"></span>
                        </button>
                    </div>

                    <div class="table-container glassmorphism-card">
                        <table class="admin-table table table-striped table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Avatar</th>
                                    <th>Name</th>
                                    <th>Role</th>
                                    <th>Quote</th>
                                    <th>Status</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            $cq = mysqli_query($conn, "SELECT * FROM $tablechar ORDER BY id ASC");
                            if ($cq && mysqli_num_rows($cq) > 0):
                                while ($c = mysqli_fetch_assoc($cq)):
                                    // Ensure all expected keys exist with defaults
                                    $charId = $c['id'] ?? 'N/A';
                                    $charName = $c['name'] ?? 'Unknown';
                                    $charRole = $c['role'] ?? 'N/A';
                                    $charQuote = $c['quote'] ?? '';
                                    $charAvatar = $c['avatar_url'] ?? 'img/woody.jpg';
                                    $charDisplayed = $c['is_displayed'] ?? 0;
                            ?>
                            <tr>
                                <td><?php echo $charId; ?></td>
                                <td>
                                    <div class="avatar-preview">
                                        <img src="<?php echo htmlspecialchars($charAvatar); ?>"
                                             alt="<?php echo htmlspecialchars($charName); ?>"
                                             onerror="this.src='img/woody.jpg'">
                                    </div>
                                </td>
                                <td><strong><?php echo htmlspecialchars($charName); ?></strong></td>
                                <td><?php echo htmlspecialchars($charRole); ?></td>
                                <td style="font-style:italic; color:rgba(255,255,255,.7); font-size:.85rem;">
                                    <?php echo htmlspecialchars(substr($charQuote, 0, 40)) . (strlen($charQuote) > 40 ? '...' : ''); ?>
                                </td>
                                <td>
                                    <?php if ($charDisplayed): ?>
                                        <span class="badge-on"><i class="fas fa-eye me-1"></i>Shown</span>
                                    <?php else: ?>
                                        <span class="badge-off"><i class="fas fa-eye-slash me-1"></i>Hidden</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="action-buttons d-flex gap-1 justify-content-center flex-wrap">
                                        <!-- Display Toggle -->
                                        <a href="char_toggle.php?id=<?php echo $charId; ?>"
                                           class="btn btn-sm <?php echo $charDisplayed ? 'btn-toggle-on' : 'btn-toggle-off'; ?>"
                                           title="<?php echo $charDisplayed ? 'Hide from website' : 'Show on website'; ?>"
                                           style="border-radius:8px; padding:4px 10px;">
                                            <i class="fas fa-<?php echo $charDisplayed ? 'eye-slash' : 'eye'; ?>"></i>
                                            <?php echo $charDisplayed ? 'Hide' : 'Show'; ?>
                                        </a>
                                        <!-- Edit -->
                                        <a href="char_edit.php?id=<?php echo $charId; ?>"
                                           class="btn btn-sm btn-warning" style="border-radius:8px; padding:4px 10px;">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <!-- Delete -->
                                        <a href="char_delete.php?id=<?php echo $charId; ?>"
                                           class="btn btn-sm btn-danger" style="border-radius:8px; padding:4px 10px;"
                                           onclick="return confirm('Delete this character?');">
                                            <i class="fas fa-trash"></i> Delete
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php
                                endwhile;
                            else:
                            ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="fas fa-users me-2"></i> No characters found. Add one!
                                </td>
                            </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </section>

            </div><!-- /admin-content -->
        </main>
    </div><!-- /admin-layout -->

    <!-- ═══════════════════════════════════════════════════
         MODAL: Add Movie
         ═══════════════════════════════════════════════════ -->
    <div class="modal-overlay hidden" id="addMovieModal">
        <div class="glassmorphism-card admin-modal">
            <div class="modal-header">
                <h2><i class="fas fa-plus me-2"></i>Add New Movie</h2>
                <button class="modal-close" id="closeMovieModal"><i class="fas fa-times"></i></button>
            </div>
            <form action="movie_add.php" method="POST">
                <div class="row g-3">
                    <div class="col-md-7">
                        <label class="form-label"><i class="fas fa-film me-1"></i> Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control glass-input" name="title" required placeholder="e.g. Toy Story 5">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label"><i class="fas fa-calendar me-1"></i> Release Year <span class="text-danger">*</span></label>
                        <input type="number" class="form-control glass-input" name="release_year" min="1990" max="2030" required placeholder="1995">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label"><i class="fas fa-clock me-1"></i> Runtime (min) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control glass-input" name="runtime" min="30" max="300" required placeholder="81">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label"><i class="fas fa-star me-1"></i> Rating (0–10)</label>
                        <input type="number" class="form-control glass-input" name="rating" min="0" max="10" step="0.1" placeholder="8.3">
                    </div>
                    <div class="col-12">
                        <label class="form-label"><i class="fas fa-quote-left me-1"></i> Tagline <span class="text-danger">*</span></label>
                        <textarea class="form-control glass-input" name="tagline" rows="2" required placeholder="Movie tagline or description"></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label"><i class="fas fa-image me-1"></i> Poster Image Path <span class="text-danger">*</span></label>
                        <input type="text" class="form-control glass-input" name="poster_url" required placeholder="img/toystory1.webp">
                        <small style="color:rgba(255,255,255,.4);">Use relative path like <code style="color:#F4C542;">img/filename.jpg</code></small>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label"><i class="fas fa-eye me-1"></i> Display on Website?</label>
                        <select class="form-control glass-input" name="is_displayed">
                            <option value="1">Yes — Show</option>
                            <option value="0">No — Hide</option>
                        </select>
                    </div>
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary cinematic-btn" id="cancelMovieBtn">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary cinematic-btn">
                        <i class="fas fa-save"></i> Save Movie <span class="btn-shine"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════
         MODAL: Add Character
         ═══════════════════════════════════════════════════ -->
    <div class="modal-overlay hidden" id="addCharacterModal">
        <div class="glassmorphism-card admin-modal">
            <div class="modal-header">
                <h2><i class="fas fa-plus me-2"></i>Add New Character</h2>
                <button class="modal-close" id="closeCharacterModal"><i class="fas fa-times"></i></button>
            </div>
            <form action="char_add.php" method="POST">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label"><i class="fas fa-user me-1"></i> Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control glass-input" name="name" required placeholder="e.g. Forky">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label"><i class="fas fa-tag me-1"></i> Role <span class="text-danger">*</span></label>
                        <input type="text" class="form-control glass-input" name="role" required placeholder="e.g. The Spork">
                    </div>
                    <div class="col-12">
                        <label class="form-label"><i class="fas fa-quote-left me-1"></i> Famous Quote <span class="text-danger">*</span></label>
                        <textarea class="form-control glass-input" name="quote" rows="2" required placeholder='"I am not a toy!"'></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label"><i class="fas fa-align-left me-1"></i> Description <span class="text-danger">*</span></label>
                        <textarea class="form-control glass-input" name="description" rows="3" required placeholder="Character background and personality..."></textarea>
                    </div>
                    <div class="col-md-7">
                        <label class="form-label"><i class="fas fa-image me-1"></i> Avatar Image Path <span class="text-danger">*</span></label>
                        <input type="text" class="form-control glass-input" name="avatar_url" required placeholder="img/woody.jpg">
                        <small style="color:rgba(255,255,255,.4);">Use relative path like <code style="color:#F4C542;">img/filename.jpg</code></small>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label"><i class="fas fa-palette me-1"></i> CSS Card Class</label>
                        <select class="form-control glass-input" name="css_class">
                            <option value="woody">woody</option>
                            <option value="buzz">buzz</option>
                            <option value="jessie">jessie</option>
                            <option value="rexy">rexy</option>
                            <option value="ham">ham</option>
                            <option value="slinky">slinky</option>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label"><i class="fas fa-eye me-1"></i> Display on Website?</label>
                        <select class="form-control glass-input" name="is_displayed">
                            <option value="1">Yes — Show</option>
                            <option value="0">No — Hide</option>
                        </select>
                    </div>
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary cinematic-btn" id="cancelCharacterBtn">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary cinematic-btn">
                        <i class="fas fa-save"></i> Save Character <span class="btn-shine"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="admin-script.js"></script>
    <script>
    // ── Modal open / close ────────────────────────────────
    const addMovieBtn       = document.getElementById('addMovieBtn');
    const addMovieModal     = document.getElementById('addMovieModal');
    const closeMovieModal   = document.getElementById('closeMovieModal');
    const cancelMovieBtn    = document.getElementById('cancelMovieBtn');

    const addCharBtn        = document.getElementById('addCharacterBtn');
    const addCharModal      = document.getElementById('addCharacterModal');
    const closeCharModal    = document.getElementById('closeCharacterModal');
    const cancelCharBtn     = document.getElementById('cancelCharacterBtn');

    addMovieBtn.addEventListener('click',   () => addMovieModal.classList.remove('hidden'));
    closeMovieModal.addEventListener('click',() => addMovieModal.classList.add('hidden'));
    cancelMovieBtn.addEventListener('click',() => addMovieModal.classList.add('hidden'));

    addCharBtn.addEventListener('click',   () => addCharModal.classList.remove('hidden'));
    closeCharModal.addEventListener('click',() => addCharModal.classList.add('hidden'));
    cancelCharBtn.addEventListener('click',() => addCharModal.classList.add('hidden'));

    // Close on backdrop click
    [addMovieModal, addCharModal].forEach(modal => {
        modal.addEventListener('click', e => { if (e.target === modal) modal.classList.add('hidden'); });
    });

    // Sidebar toggle
    const sidebarToggle = document.getElementById('sidebarToggle');
    const adminSidebar  = document.getElementById('adminSidebar');
    if (sidebarToggle) sidebarToggle.addEventListener('click', () => adminSidebar.classList.toggle('collapsed'));

    const mobileToggle  = document.getElementById('mobileMenuToggle');
    if (mobileToggle)  mobileToggle.addEventListener('click', () => adminSidebar.classList.toggle('mobile-open'));
    </script>
</body>
</html>
