<?php
session_start();
// views/flashcards.php - View a specific flashcard interactively
require_once __DIR__ . '/../config/database.php';

$subject_id = $_SESSION['subject_id'] ?? 0;

// Initialize random flashcard sequence
if (isset($_GET['start'])) {
    $stmt = $pdo->prepare("SELECT * FROM flashcards WHERE subject_id = ? ORDER BY RAND()");
    $stmt->execute([$subject_id]);
    $_SESSION['flashcards'] = $stmt->fetchAll();
    $_SESSION['fc_index'] = 0;
    header("Location: flashcards.php");
    exit;
}

// Proceed to next flashcard
if (isset($_GET['next'])) {
    $_SESSION['fc_index'] = ($_SESSION['fc_index'] ?? 0) + 1;
    header("Location: flashcards.php");
    exit;
}

include 'content/header.php';
include 'content/navbar.php';
echo '<main class="app-main">';

$flashcards = $_SESSION['flashcards'] ?? [];
$index = $_SESSION['fc_index'] ?? 0;

if (empty($flashcards)) {
    echo "<div style='padding: 2rem; background: #fff; border-radius: 8px; text-align: center;'><h2>No flashcards available for this subject.</h2><a href='material.php' class='btn btn-primary'>Back to Subjects</a></div>";
} elseif ($index >= count($flashcards)) {
    echo "<div style='padding: 2rem; background: #fff; border-radius: 8px; text-align: center;'><h2>You've completed all flashcards for this subject! 🎉</h2><a href='flashcards.php?start=1' class='btn btn-primary' style='margin-right: 1rem;'>Review Again</a> <a href='material.php' class='btn btn-outline'>Back to Subjects</a></div>";
} else {
    $fc = $flashcards[$index];
    ?>
    <link rel="stylesheet" href="../assets/css/flashcards.css">
    <div class="flashcard-view-container">
        <!-- Removed flashcard number as requested -->
        <span class="flashcard-badge">Subject Flashcard</span>

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
                <div style="margin-top: 1.5rem;">
                    <a href="flashcards.php?next=1" class="btn btn-primary">Next Flashcard &rarr;</a>
                </div>
            </div>
        </div>

        <div class="flashcard-back">
            <a href="material.php" class="btn">&larr; Back to Subjects</a>
        </div>
    </div>
    <script src="../assets/js/flashcards.js"></script>
    <?php
}

echo '</main>';
include 'content/footer.php';
?>
