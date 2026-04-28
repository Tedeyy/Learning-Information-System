<?php
session_start();
// views/flashcards.php - Interactive flashcard quiz system
require_once __DIR__ . '/../config/database.php';

$subject_id = $_SESSION['subject_id'] ?? 0;

// Initialize random flashcard sequence and results array
if (isset($_GET['start'])) {
    $stmt = $pdo->prepare("SELECT * FROM flashcards WHERE subject_id = ? ORDER BY RAND()");
    $stmt->execute([$subject_id]);
    $_SESSION['flashcards'] = $stmt->fetchAll();
    $_SESSION['fc_index'] = 0;
    $_SESSION['flashcard_results'] = [];
    header("Location: flashcards.php");
    exit;
}

// Process submitted answer
if (isset($_POST['submit_answer'])) {
    $selected_answer = $_POST['selected_answer'] ?? '';
    $index = $_SESSION['fc_index'] ?? 0;
    
    if (isset($_SESSION['flashcards'][$index])) {
        $fc = $_SESSION['flashcards'][$index];
        $_SESSION['flashcard_results'][] = [
            'question' => $fc['question'],
            'option_a' => $fc['option_a'],
            'option_b' => $fc['option_b'],
            'option_c' => $fc['option_c'],
            'option_d' => $fc['option_d'],
            'correct_option' => $fc['correct_option'],
            'selected_answer' => $selected_answer,
            'explanation' => $fc['explanation'],
            'is_correct' => ($selected_answer === $fc['correct_option'])
        ];
    }
    
    // Increment index
    $_SESSION['fc_index'] = $index + 1;
    header("Location: flashcards.php");
    exit;
}

// Clear quiz state to return to dashboard cleanly
if (isset($_GET['finish'])) {
    unset($_SESSION['flashcards']);
    unset($_SESSION['fc_index']);
    unset($_SESSION['flashcard_results']);
    header("Location: material.php");
    exit;
}

include 'content/header.php';
include 'content/navbar.php';
echo '<main class="app-main">';

$flashcards = $_SESSION['flashcards'] ?? null;
$index = $_SESSION['fc_index'] ?? 0;

if ($flashcards === null) {
    // --- ENTRY SCREEN ---
    ?>
    <div style="padding: 3rem; background: #fff; border-radius: 8px; text-align: center; max-width: 600px; margin: 2rem auto; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border: 1px solid var(--border-color);">
        <h2>Start Flashcards?</h2>
        <p style="color: var(--text-muted); margin-bottom: 2rem;">Test your knowledge on this subject. Flashcards will appear in a random order.</p>
        <div>
            <a href="flashcards.php?start=1" class="btn btn-primary" style="margin-right: 1rem; padding: 0.75rem 2rem; font-size: 1.1rem;">Start Flashcards</a>
            <a href="material.php" class="btn btn-outline" style="padding: 0.75rem 2rem; font-size: 1.1rem;">Go Back</a>
        </div>
    </div>
    <?php
} elseif ($index >= count($flashcards)) {
    // --- SUMMARY SCREEN ---
    $results = $_SESSION['flashcard_results'] ?? [];
    $total = count($flashcards);
    $score = 0;
    foreach ($results as $r) {
        if ($r['is_correct']) $score++;
    }
    ?>
    <link rel="stylesheet" href="../assets/css/flashcards.css">
    <div style="padding: 2rem; max-width: 800px; margin: 0 auto;">
        <div style="background: #fff; padding: 2rem; border-radius: 8px; text-align: center; margin-bottom: 2rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border: 1px solid var(--border-color);">
            <h2>Quiz Finished! 🎉</h2>
            <h1 style="color: var(--primary-color); font-size: 3rem; margin: 1rem 0;"><?php echo $score; ?> / <?php echo $total; ?></h1>
            <p style="color: var(--text-muted);">Here is a summary of your answers:</p>
            <div style="margin-top: 1.5rem;">
                <a href="flashcards.php?finish=1" class="btn btn-primary">Return to Subjects</a>
                <a href="flashcards.php?start=1" class="btn btn-outline" style="margin-left: 1rem;">Retry Flashcards</a>
            </div>
        </div>

        <div>
            <?php foreach ($results as $i => $r): ?>
                <div class="summary-item <?php echo $r['is_correct'] ? 'summary-correct' : 'summary-wrong'; ?>">
                    <h4>Q: <?php echo nl2br(htmlspecialchars($r['question'])); ?></h4>
                    
                    <p style="margin: 0.5rem 0;">
                        <strong>Your Answer:</strong> <?php echo htmlspecialchars($r['selected_answer']); ?> - 
                        <?php 
                        $opt_key = 'option_' . strtolower($r['selected_answer']);
                        echo htmlspecialchars($r[$opt_key] ?? '');
                        ?>
                        <?php if ($r['is_correct']): ?>
                            <span style="color: #0ca678; font-weight: bold;">(Correct)</span>
                        <?php else: ?>
                            <span style="color: #e03131; font-weight: bold;">(Wrong)</span>
                        <?php endif; ?>
                    </p>

                    <?php if (!$r['is_correct']): ?>
                        <p style="margin: 0.5rem 0;">
                            <strong>Correct Answer:</strong> <?php echo htmlspecialchars($r['correct_option']); ?> - 
                            <?php 
                            $correct_key = 'option_' . strtolower($r['correct_option']);
                            echo htmlspecialchars($r[$correct_key] ?? '');
                            ?>
                        </p>
                    <?php endif; ?>

                    <?php if (!empty($r['explanation'])): ?>
                        <div style="margin-top: 1rem; padding: 1rem; background: #e9ecef; border-radius: 4px;">
                            <strong>Explanation:</strong> <?php echo nl2br(htmlspecialchars($r['explanation'])); ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
} else {
    // --- QUIZ SCREEN ---
    $fc = $flashcards[$index];
    $correct = $fc['correct_option'];
    $is_last = ($index === count($flashcards) - 1);
    ?>
    <link rel="stylesheet" href="../assets/css/flashcards.css?v=2">
    <div class="flashcard-view-container">
        <span class="flashcard-badge">Subject Flashcard</span>

        <h2 class="flashcard-question"><?php echo nl2br(htmlspecialchars($fc['question'])); ?></h2>

        <div class="flashcard-options">
            <div id="option-A" class="flashcard-option interactive" onclick="selectOption('option-A', 'A', '<?php echo $correct; ?>')">
                <strong>A:</strong> <?php echo htmlspecialchars($fc['option_a']); ?>
            </div>
            <div id="option-B" class="flashcard-option interactive" onclick="selectOption('option-B', 'B', '<?php echo $correct; ?>')">
                <strong>B:</strong> <?php echo htmlspecialchars($fc['option_b']); ?>
            </div>
            <?php if (!empty($fc['option_c'])): ?>
                <div id="option-C" class="flashcard-option interactive" onclick="selectOption('option-C', 'C', '<?php echo $correct; ?>')">
                    <strong>C:</strong> <?php echo htmlspecialchars($fc['option_c']); ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($fc['option_d'])): ?>
                <div id="option-D" class="flashcard-option interactive" onclick="selectOption('option-D', 'D', '<?php echo $correct; ?>')">
                    <strong>D:</strong> <?php echo htmlspecialchars($fc['option_d']); ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="flashcard-answer-section">
            <div id="answer" class="flashcard-answer">
                <?php if (!empty($fc['explanation'])): ?>
                    <p><strong>Explanation:</strong> <?php echo nl2br(htmlspecialchars($fc['explanation'])); ?></p>
                <?php else: ?>
                    <p>No further explanation provided.</p>
                <?php endif; ?>
            </div>
            
            <div id="next-btn-container" style="display: none; margin-top: 1.5rem;">
                <form method="POST" action="flashcards.php">
                    <input type="hidden" name="selected_answer" id="selected_answer" value="">
                    <button type="submit" name="submit_answer" class="btn btn-primary">
                        <?php echo $is_last ? 'Finish Flashcards' : 'Next Flashcard &rarr;'; ?>
                    </button>
                </form>
            </div>
        </div>

        <div class="flashcard-back">
            <a href="flashcards.php?finish=1" class="btn">&larr; Quit Quiz</a>
        </div>
    </div>
    <script src="../assets/js/flashcards.js?v=2"></script>
    <?php
}

echo '</main>';
include 'content/footer.php';
?>
