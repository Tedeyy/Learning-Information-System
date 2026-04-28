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
            <li><a href="material.php"
                    class="nav-link <?php echo ($current_action == 'feed') ? 'active' : ''; ?>">Subjects</a></li>
            <li><a href="../index.html" class="nav-link" style="color: var(--primary-color);">Home</a></li>
        </ul>
    </div>
</nav>