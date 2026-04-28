<?php
// views/flashcards.php - View a specific flashcard interactively
require_once __DIR__ . '/../config/database.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

include 'content/header.php';
include 'content/navbar.php';
echo '<main class="app-main">';

// === VIEW FLASHCARD ===
$stmt = $pdo->prepare("SELECT * FROM flashcards WHERE id = ?");
$stmt->execute([$id]);
$fc = $stmt->fetch();

if (!$fc) {
    echo "<div style='padding: 2rem; background: #fff; border-radius: 8px; text-align: center;'><h2>Flashcard not found.</h2><a href='material.php' class='btn btn-primary'>Back to Feed</a></div>";
} else {
    ?>
    <div class="flashcard-view-container"
        style="background: #fff; padding: 3rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border: 1px solid var(--border-color); text-align: center; max-width: 600px; margin: 0 auto;">
        <span
            style="display: inline-block; padding: 0.25rem 0.75rem; background: #e9ecef; color: var(--text-muted); border-radius: 999px; font-size: 0.85rem; font-weight: 600; margin-bottom: 1.5rem;">Flashcard
            #<?php echo $id; ?></span>

        <h2 style="font-size: 1.5rem; margin-bottom: 2rem; color: var(--text-dark);">
            <?php echo nl2br(htmlspecialchars($fc['question'])); ?></h2>

        <div style="display: flex; flex-direction: column; gap: 1rem; text-align: left;">
            <div style="padding: 1rem; border: 1px solid var(--border-color); border-radius: 6px; background: #f8f9fa;">
                <strong>A:</strong> <?php echo htmlspecialchars($fc['option_a']); ?></div>
            <div style="padding: 1rem; border: 1px solid var(--border-color); border-radius: 6px; background: #f8f9fa;">
                <strong>B:</strong> <?php echo htmlspecialchars($fc['option_b']); ?></div>
            <?php if (!empty($fc['option_c'])): ?>
                <div style="padding: 1rem; border: 1px solid var(--border-color); border-radius: 6px; background: #f8f9fa;">
                    <strong>C:</strong> <?php echo htmlspecialchars($fc['option_c']); ?></div>
            <?php endif; ?>
            <?php if (!empty($fc['option_d'])): ?>
                <div style="padding: 1rem; border: 1px solid var(--border-color); border-radius: 6px; background: #f8f9fa;">
                    <strong>D:</strong> <?php echo htmlspecialchars($fc['option_d']); ?></div>
            <?php endif; ?>
        </div>

        <div style="margin-top: 3rem; padding-top: 2rem; border-top: 1px dashed var(--border-color);">
            <button onclick="document.getElementById('answer').style.display='block'; this.style.display='none';"
                class="btn btn-primary"
                style="padding: 0.75rem 2rem; font-size: 1rem; cursor: pointer; border: none; border-radius: 4px;">Show
                Answer</button>
            <div id="answer"
                style="display: none; background: #e6fcf5; border: 1px solid #20c997; padding: 1.5rem; border-radius: 6px; text-align: left;">
                <h3 style="margin: 0 0 0.5rem 0; color: #0ca678;">Correct Answer:
                    <?php echo htmlspecialchars($fc['correct_option']); ?></h3>
                <?php if (!empty($fc['explanation'])): ?>
                    <p style="margin: 0; color: #2b8a3e; font-size: 0.95rem;"><strong></strong>
                        <?php echo nl2br(htmlspecialchars($fc['explanation'])); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div style="margin-top: 2rem;">
            <a href="material.php" class="btn" style="color: var(--text-muted); text-decoration: none;">&larr; Back to
                Dashboard</a>
        </div>
    </div>
    <?php
}

echo '</main>';
include 'content/footer.php';
?>
