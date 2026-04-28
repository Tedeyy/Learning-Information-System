<?php
$current_action = isset($_GET['action']) ? $_GET['action'] : 'feed';
?>
<nav class="top-navbar">
    <div class="nav-container">
        <a href="../index.html" class="nav-brand">
            <img src="../assets/img/logo.jpg" alt="EduReady Logo" class="brand-logo">
            EduReady
        </a>
        <ul class="nav-menu">
            <li><a href="material.php" class="nav-link <?php echo ($current_action == 'feed') ? 'active' : ''; ?>">Dashboard</a></li>
            <li><a href="material.php?action=reviewers" class="nav-link <?php echo ($current_action == 'reviewers') ? 'active' : ''; ?>">Reviewers</a></li>
            <li><a href="material.php?action=flashcards_feed" class="nav-link <?php echo ($current_action == 'flashcards_feed') ? 'active' : ''; ?>">Flashcards</a></li>
        </ul>
    </div>
</nav>