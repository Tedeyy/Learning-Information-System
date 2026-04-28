<?php
session_start();
// views/reviewer.php - View a specific learning material or list materials for a subject
require_once __DIR__ . '/../config/database.php';

$subject_id = $_SESSION['subject_id'] ?? 0;
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

include 'content/header.php';
include 'content/navbar.php';
echo '<main class="app-main">';

if ($id > 0) {
    // === VIEW SINGLE LEARNING MATERIAL ===
    $stmt = $pdo->prepare("SELECT * FROM learning_materials WHERE id = ?");
    $stmt->execute([$id]);
    $material = $stmt->fetch();

    if (!$material) {
        echo "<div style='padding: 2rem; background: #fff; border-radius: 8px; text-align: center;'><h2>Material not found.</h2><a href='reviewer.php' class='btn btn-primary'>Back to List</a></div>";
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
                <a href="reviewer.php" class="btn btn-back">&larr; Back to Materials List</a>
                <?php if ($material['material_type'] === 'file'): ?>
                    <a href="serve_file.php?id=<?php echo $id; ?>" download class="btn btn-primary btn-download">Download Securely</a>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }
} else {
    // === LIST ALL LEARNING MATERIALS FOR SUBJECT ===
    $stmt = $pdo->prepare("SELECT * FROM learning_materials WHERE subject_id = ? ORDER BY created_at DESC");
    $stmt->execute([$subject_id]);
    $materials = $stmt->fetchAll();
    ?>
    <div class="dashboard-wrapper">
        <header class="dashboard-header">
            <h1>Subject Materials</h1>
            <p class="text-muted">Reviewers and learning materials for this subject.</p>
        </header>

        <section class="content-section mb-2">
            <div class="section-header">
                <h2>Materials</h2>
                <a href="material.php" class="view-all">&larr; Back to Subjects</a>
            </div>
            
            <div class="materials-list">
                <?php if (count($materials) > 0): ?>
                    <?php foreach ($materials as $mat): ?>
                        <div class="material-card">
                            <div class="material-icon"><?php echo $mat['material_type'] === 'video' ? '▶️' : '📄'; ?></div>
                            <div class="material-details">
                                <h4 class="material-title"><?php echo htmlspecialchars($mat['title']); ?></h4>
                                <span class="material-meta">Type: <?php echo ucfirst($mat['material_type']); ?></span>
                            </div>
                            <div class="material-actions">
                                <a href="reviewer.php?id=<?php echo $mat['id']; ?>" class="btn btn-primary">Open Material</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="empty-state">No materials available for this subject.</p>
                <?php endif; ?>
            </div>
        </section>
    </div>
    <?php
}

echo '</main>';
include 'content/footer.php';
?>
