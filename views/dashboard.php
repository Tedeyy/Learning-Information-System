<?php
// Connect to DB using centralized configuration
require_once __DIR__ . '/../config/database.php';

try {
    // Fetch subjects
    $stmt_subj = $pdo->query("SELECT * FROM subjects ORDER BY subject_name ASC");
    $subjects = $stmt_subj->fetchAll();
} catch (\PDOException $e) {
    $subjects = [];
}
?>

<div class="dashboard-wrapper">
    <header class="dashboard-header">
        <h1>Learning Subjects</h1>
        <p class="text-muted">Select a subject below to view its reviewers and flashcards.</p>
    </header>

    <section class="content-section mb-2">
        <div class="section-header">
            <h2>Available Subjects</h2>
        </div>

        <div class="materials-list">
            <?php if (count($subjects) > 0): ?>
                <?php foreach ($subjects as $subj): ?>
                    <div class="material-card">
                        <div class="material-icon" style="background-color: var(--primary-color); color: white;">📚</div>
                        <div class="material-details">
                            <h4 class="material-title"><?php echo htmlspecialchars($subj['subject_name']); ?></h4>
                            <span
                                class="material-meta"><?php echo htmlspecialchars($subj['description'] ?? 'No description provided.'); ?></span>
                        </div>
                        <div class="material-actions action-buttons">
                            <a href="material.php?action=subject_materials&id=<?php echo $subj['id']; ?>"
                                class="btn btn-primary btn-with-icon">Materials</a>
                            <a href="material.php?action=subject_flashcards&id=<?php echo $subj['id']; ?>"
                                class="btn btn-outline btn-with-icon">Flashcards</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="empty-state">No subjects available yet. Please add them from the Admin Dashboard.</p>
            <?php endif; ?>
        </div>
    </section>
</div>