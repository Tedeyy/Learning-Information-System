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
    <link rel="stylesheet" href="../assets/css/flashcards.css">
    <div class="flashcard-view-container">
        <span class="flashcard-badge">Flashcard #<?php echo $id; ?></span>

        <h2 class="flashcard-question"><?php echo nl2br(htmlspecialchars($fc['question'])); ?></h2>

        <div class="flashcard-options">
            <div class="flashcard-option"><strong>A:</strong> <?php echo htmlspecialchars($fc['option_a']); ?></div>
            <div class="flashcard-option"><strong>B:</strong> <?php echo htmlspecialchars($fc['option_b']); ?></div>
            <?php if (!empty($fc['option_c'])): ?>
                <div class="flashcard-option"><strong>C:</strong> <?php echo htmlspecialchars($fc['option_c']); ?></div>
            <?php endif; ?>
            <?php if (!empty($fc['option_d'])): ?>
                <div class="flashcard-option"><strong>D:</strong> <?php echo htmlspecialchars($fc['option_d']); ?></div>
            <?php endif; ?>
        </div>

        <div class="flashcard-answer-section">
            <button id="btn-show-answer" onclick="showAnswer()" class="btn btn-primary btn-show-answer">Show Answer</button>
            <div id="answer" class="flashcard-answer">
                <h3>Correct Answer: <?php echo htmlspecialchars($fc['correct_option']); ?></h3>
                <?php if (!empty($fc['explanation'])): ?>
                    <p><strong>Explanation:</strong> <?php echo nl2br(htmlspecialchars($fc['explanation'])); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="flashcard-back">
            <a href="material.php" class="btn">&larr; Back to Dashboard</a>
        </div>
    </div>
    <script src="../assets/js/flashcards.js"></script>
    <?php
}

echo '</main>';
include 'content/footer.php';
?>
