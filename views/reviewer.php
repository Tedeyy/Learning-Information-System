<?php
// views/reviewer.php - View a specific learning material
require_once __DIR__ . '/../config/database.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

include 'content/header.php';
include 'content/navbar.php';
echo '<main class="app-main">';

// === VIEW LEARNING MATERIAL ===
$stmt = $pdo->prepare("SELECT * FROM learning_materials WHERE id = ?");
$stmt->execute([$id]);
$material = $stmt->fetch();

if (!$material) {
    echo "<div style='padding: 2rem; background: #fff; border-radius: 8px; text-align: center;'><h2>Material not found.</h2><a href='material.php' class='btn btn-primary'>Back to Feed</a></div>";
} else {
    ?>
    <link rel="stylesheet" href="../assets/css/reviewer.css">
    <div class="material-view-container">
        <header class="material-header">
            <h1><?php echo htmlspecialchars($material['title']); ?></h1>
            <p><?php echo htmlspecialchars($material['content_description'] ?? 'No description provided.'); ?></p>
        </header>

        <div class="material-content">
            <?php if ($material['material_type'] === 'file'): ?>
                <!-- Securely load the file via serve_file.php proxy to hide real path -->
                <iframe src="serve_file.php?id=<?php echo $id; ?>" width="100%" height="600px"></iframe>
            <?php elseif ($material['material_type'] === 'video'): ?>
                <div class="video-container">
                    <p>Video URL: <a href="<?php echo htmlspecialchars($material['video_url']); ?>" target="_blank"><?php echo htmlspecialchars($material['video_url']); ?></a></p>
                </div>
            <?php else: ?>
                <p>Unsupported material type.</p>
            <?php endif; ?>
        </div>

        <div class="material-actions">
            <a href="material.php" class="btn btn-back">&larr; Back to Dashboard</a>
            <?php if ($material['material_type'] === 'file'): ?>
                <a href="serve_file.php?id=<?php echo $id; ?>" download class="btn btn-primary btn-download">Download Securely</a>
            <?php endif; ?>
        </div>
    </div>
    <?php
}

echo '</main>';
include 'content/footer.php';
?>
