<?php
session_start();
// material.php - Central routing script for the dashboard feeds

$action = isset($_GET['action']) ? $_GET['action'] : 'feed';

if ($action === 'subject_materials') {
    $_SESSION['subject_id'] = (int)$_GET['id'];
    header("Location: reviewer.php");
    exit;
} elseif ($action === 'subject_flashcards') {
    $_SESSION['subject_id'] = (int)$_GET['id'];
    header("Location: flashcards.php");
    exit;
} elseif (in_array($action, ['feed'])) {
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