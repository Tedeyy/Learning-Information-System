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
    <div class="material-view-container"
        style="background: #fff; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); border: 1px solid var(--border-color);">
        <header class="material-header"
            style="margin-bottom: 2rem; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem;">
            <h1 style="margin: 0 0 0.5rem 0; color: var(--text-dark);"><?php echo htmlspecialchars($material['title']); ?>
            </h1>
            <p style="margin: 0; color: var(--text-muted);">
                <?php echo htmlspecialchars($material['content_description'] ?? 'No description provided.'); ?>
            </p>
        </header>

        <div class="material-content"
            style="min-height: 500px; display: flex; flex-direction: column; align-items: center; justify-content: center; background: #f8f9fa; border: 1px dashed #cbd5e1; border-radius: 8px;">
            <?php if ($material['material_type'] === 'file'): ?>
                <!-- Securely load the file via serve_file.php proxy to hide real path -->
                <iframe src="serve_file.php?id=<?php echo $id; ?>" width="100%" height="600px"
                    style="border: none; border-radius: 4px;"></iframe>
            <?php elseif ($material['material_type'] === 'video'): ?>
                <div class="video-container">
                    <p>Video URL: <a href="<?php echo htmlspecialchars($material['video_url']); ?>" target="_blank"
                            style="color: var(--primary-color);"><?php echo htmlspecialchars($material['video_url']); ?></a></p>
                </div>
            <?php else: ?>
                <p>Unsupported material type.</p>
            <?php endif; ?>
        </div>

        <div class="material-actions"
            style="margin-top: 2rem; display: flex; justify-content: space-between; align-items: center;">
            <a href="material.php" class="btn"
                style="padding: 0.5rem 1rem; border: 1px solid var(--border-color); border-radius: 4px; text-decoration: none; color: var(--text-dark); background: #f8f9fa;">&larr;
                Back to Dashboard</a>
            <?php if ($material['material_type'] === 'file'): ?>
                <a href="serve_file.php?id=<?php echo $id; ?>" download class="btn btn-primary"
                    style="margin-left: 1rem;">Download Securely</a>
            <?php endif; ?>
        </div>
    </div>
    <?php
}

echo '</main>';
include 'content/footer.php';
?>
