<?php
// material.php - Central routing script for the dashboard feeds

$action = isset($_GET['action']) ? $_GET['action'] : 'feed';

if (in_array($action, ['feed', 'reviewers', 'flashcards_feed'])) {
    // 1. Show the main dashboard feed or the specific feeds
    include 'content/header.php';
    include 'content/navbar.php';
    echo '<main class="app-main">';
    include 'dashboard.php';
    echo '</main>';
    include 'content/footer.php';
    exit;
} else {
    // Unknown action, default to feed
    header("Location: material.php");
    exit;
}
?>