<?php
$action = isset($_GET['action']) ? $_GET['action'] : 'feed';
$show_reviewers = in_array($action, ['feed', 'reviewers']);
$show_flashcards = in_array($action, ['feed', 'flashcards_feed']);

// Connect to DB using centralized configuration
require_once __DIR__ . '/../config/database.php';

try {
    // Fetch materials
    $stmt_mat = $pdo->query("SELECT * FROM learning_materials ORDER BY created_at DESC LIMIT 5");
    $materials = $stmt_mat->fetchAll();

    // Fetch flashcards
    $stmt_fc = $pdo->query("SELECT * FROM flashcards ORDER BY id DESC LIMIT 5");
    $flashcards = $stmt_fc->fetchAll();
    
} catch (\PDOException $e) {
    $materials = [];
    $flashcards = [];
}
?>

<div class="dashboard-wrapper">
    <header class="dashboard-header">
        <h1>Welcome Back!</h1>
        <p class="text-muted">Ready to continue your learning journey and ace your exams?</p>
    </header>

    <?php if ($show_reviewers): ?>
    <section class="content-section" style="margin-bottom: 2rem;">
        <div class="section-header">
            <h2>Recent Reviewers</h2>
            <a href="reviewer.php" class="view-all">View All</a>
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
                            <a href="reviewer.php?id=<?php echo $mat['id']; ?>" class="btn btn-primary">Open Reviewer</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="padding: 1.5rem; color: #6c757d; margin: 0;">No reviewers available yet.</p>
            <?php endif; ?>
        </div>
    </section>
    <?php endif; ?>

    <?php if ($show_flashcards): ?>
    <section class="content-section">
        <div class="section-header">
            <h2>Recent Flashcards</h2>
            <a href="flashcards.php" class="view-all">View All</a>
        </div>
        
        <div class="materials-list">
            <?php if (count($flashcards) > 0): ?>
                <?php foreach ($flashcards as $fc): ?>
                    <div class="material-card">
                        <div class="material-icon">🗂️</div>
                        <div class="material-details">
                            <h4 class="material-title">Flashcard #<?php echo $fc['id']; ?></h4>
                            <span class="material-meta"><?php echo htmlspecialchars(substr($fc['question'], 0, 50)) . '...'; ?></span>
                        </div>
                        <div class="material-actions">
                            <a href="flashcards.php?id=<?php echo $fc['id']; ?>" class="btn" style="background-color: #f8f9fa; border: 1px solid #ced4da; color: #212529;">Review Card</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="padding: 1.5rem; color: #6c757d; margin: 0;">No flashcards available yet.</p>
            <?php endif; ?>
        </div>
    </section>
    <?php endif; ?>
</div>