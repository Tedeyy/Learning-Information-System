<?php
require_once __DIR__ . '/../config/database.php';

$messages = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // === 1. ADD SUBJECT ===
    if ($action === 'add_subject') {
        $subject_name = trim($_POST['subject_name']);
        $description = trim($_POST['description']);
        
        if (!empty($subject_name)) {
            $stmt = $pdo->prepare("INSERT INTO subjects (subject_name, description) VALUES (?, ?)");
            if ($stmt->execute([$subject_name, $description])) {
                $messages[] = ['type' => 'success', 'text' => 'Subject added successfully.'];
            } else {
                $messages[] = ['type' => 'error', 'text' => 'Failed to add subject.'];
            }
        } else {
            $messages[] = ['type' => 'error', 'text' => 'Subject name is required.'];
        }
    } 
    // === 2. ADD FLASHCARD ===
    elseif ($action === 'add_flashcard') {
        $subject_id = $_POST['subject_id'];
        $question = trim($_POST['question']);
        $opt_a = trim($_POST['option_a']);
        $opt_b = trim($_POST['option_b']);
        $opt_c = trim($_POST['option_c']);
        $opt_d = trim($_POST['option_d']);
        $correct = $_POST['correct_option'];
        $explanation = trim($_POST['explanation']);

        if (!empty($subject_id) && !empty($question) && !empty($opt_a) && !empty($opt_b) && !empty($correct)) {
            $stmt = $pdo->prepare("INSERT INTO flashcards (subject_id, question, option_a, option_b, option_c, option_d, correct_option, explanation) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            if ($stmt->execute([$subject_id, $question, $opt_a, $opt_b, $opt_c, $opt_d, $correct, $explanation])) {
                $messages[] = ['type' => 'success', 'text' => 'Flashcard added successfully.'];
            } else {
                $messages[] = ['type' => 'error', 'text' => 'Failed to add flashcard.'];
            }
        } else {
            $messages[] = ['type' => 'error', 'text' => 'Please fill in all required flashcard fields.'];
        }
    }
    // === 3. ADD LEARNING MATERIAL ===
    elseif ($action === 'add_material') {
        $subject_id = $_POST['subject_id'];
        $title = trim($_POST['title']);
        $description = trim($_POST['description']);
        $material_type = $_POST['material_type'];
        $video_url = trim($_POST['video_url']);
        
        $file_url = null;
        $upload_ok = true;

        if ($material_type === 'file' && isset($_FILES['material_file']) && $_FILES['material_file']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = __DIR__ . '/../uploads/materials/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
            
            $filename = time() . '_' . basename($_FILES['material_file']['name']);
            $target_file = $upload_dir . $filename;
            
            if (move_uploaded_file($_FILES['material_file']['tmp_name'], $target_file)) {
                $file_url = 'uploads/materials/' . $filename;
            } else {
                $upload_ok = false;
                $messages[] = ['type' => 'error', 'text' => 'Failed to upload the file. Check folder permissions.'];
            }
        } elseif ($material_type === 'file' && (!isset($_FILES['material_file']) || $_FILES['material_file']['error'] !== UPLOAD_ERR_OK)) {
            $upload_ok = false;
            $messages[] = ['type' => 'error', 'text' => 'Please select a valid file to upload.'];
        }

        if ($upload_ok && !empty($subject_id) && !empty($title) && !empty($material_type)) {
            $stmt = $pdo->prepare("INSERT INTO learning_materials (subject_id, title, content_description, video_url, file_url, material_type) VALUES (?, ?, ?, ?, ?, ?)");
            if ($stmt->execute([$subject_id, $title, $description, $video_url, $file_url, $material_type])) {
                $messages[] = ['type' => 'success', 'text' => 'Learning material added successfully.'];
            } else {
                $messages[] = ['type' => 'error', 'text' => 'Failed to add learning material.'];
            }
        } elseif ($upload_ok) {
            $messages[] = ['type' => 'error', 'text' => 'Please fill in all required material fields.'];
        }
    }
}

// Fetch subjects for dropdowns
$stmt_subj = $pdo->query("SELECT id, subject_name FROM subjects ORDER BY subject_name ASC");
$subjects = $stmt_subj->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduReady - Admin Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #0056b3;
            --bg-light: #f4f7f6;
            --text-dark: #212529;
            --text-muted: #6c757d;
            --border-color: #e9ecef;
        }
        body { font-family: 'Inter', sans-serif; background-color: var(--bg-light); color: var(--text-dark); margin: 0; padding: 0; }
        .admin-header { background: #fff; padding: 1.5rem 2rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; position: sticky; top: 0; z-index: 100; box-shadow: 0 2px 4px rgba(0,0,0,0.02); }
        .admin-header h1 { margin: 0; font-size: 1.5rem; color: var(--primary-color); display: flex; align-items: center; gap: 0.5rem; }
        .admin-container { max-width: 900px; margin: 2rem auto; padding: 0 1rem; }
        .message-alert { padding: 1rem; border-radius: 6px; margin-bottom: 1.5rem; font-weight: 500; }
        .msg-success { background: #e6fcf5; color: #0ca678; border: 1px solid #20c997; }
        .msg-error { background: #fff5f5; color: #e03131; border: 1px solid #fa5252; }
        .card { background: #fff; border-radius: 8px; border: 1px solid var(--border-color); box-shadow: 0 2px 8px rgba(0,0,0,0.04); margin-bottom: 2rem; overflow: hidden; }
        .card-header { background: #f8f9fa; padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border-color); }
        .card-header h2 { margin: 0; font-size: 1.15rem; color: #343a40; }
        .card-body { padding: 1.5rem; }
        .form-group { margin-bottom: 1.25rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; font-weight: 600; font-size: 0.9rem; color: #495057; }
        .form-control { width: 100%; padding: 0.75rem; border: 1px solid #ced4da; border-radius: 4px; font-family: inherit; font-size: 0.95rem; box-sizing: border-box; transition: border-color 0.2s; }
        .form-control:focus { outline: none; border-color: var(--primary-color); }
        textarea.form-control { resize: vertical; }
        .btn { display: inline-flex; align-items: center; justify-content: center; padding: 0.75rem 1.5rem; border-radius: 4px; font-weight: 500; cursor: pointer; text-decoration: none; border: none; font-size: 0.95rem; transition: background-color 0.2s; }
        .btn-primary { background: var(--primary-color); color: #fff; }
        .btn-primary:hover { background: #004494; }
        .btn-secondary { background: #e9ecef; color: var(--text-dark); border: 1px solid #ced4da; }
        .btn-secondary:hover { background: #dee2e6; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
        .required { color: #e03131; }
    </style>
</head>
<body>

<div class="admin-header">
    <h1>⚙️ Admin Dashboard</h1>
    <!-- Link back to the student feed as requested -->
    <a href="material.php" class="btn btn-secondary">&larr; Return to Student Feed</a>
</div>

<div class="admin-container">
    <?php foreach ($messages as $msg): ?>
        <div class="message-alert msg-<?php echo $msg['type']; ?>">
            <?php echo htmlspecialchars($msg['text']); ?>
        </div>
    <?php endforeach; ?>

    <!-- 1. Add Subject -->
    <div class="card">
        <div class="card-header"><h2>1. Add New Subject</h2></div>
        <div class="card-body">
            <form method="POST" action="">
                <input type="hidden" name="action" value="add_subject">
                <div class="form-group">
                    <label>Subject Name <span class="required">*</span></label>
                    <input type="text" name="subject_name" class="form-control" required placeholder="e.g. Data Structures & Algorithms">
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" class="form-control" rows="3" placeholder="Brief subject description..."></textarea>
                </div>
                <button type="submit" class="btn btn-primary">+ Save Subject</button>
            </form>
        </div>
    </div>

    <!-- 2. Add Flashcard -->
    <div class="card">
        <div class="card-header"><h2>2. Add Flashcard</h2></div>
        <div class="card-body">
            <?php if (count($subjects) === 0): ?>
                <div class="message-alert msg-error">Please add a Subject first before creating Flashcards.</div>
            <?php else: ?>
                <form method="POST" action="">
                    <input type="hidden" name="action" value="add_flashcard">
                    <div class="form-group">
                        <label>Select Subject <span class="required">*</span></label>
                        <select name="subject_id" class="form-control" required>
                            <option value="">-- Choose Subject --</option>
                            <?php foreach ($subjects as $s): ?>
                                <option value="<?php echo $s['id']; ?>"><?php echo htmlspecialchars($s['subject_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Question <span class="required">*</span></label>
                        <textarea name="question" class="form-control" rows="3" required placeholder="Enter the flashcard question here..."></textarea>
                    </div>
                    <div class="grid-2">
                        <div class="form-group"><label>Option A <span class="required">*</span></label><input type="text" name="option_a" class="form-control" required></div>
                        <div class="form-group"><label>Option B <span class="required">*</span></label><input type="text" name="option_b" class="form-control" required></div>
                        <div class="form-group"><label>Option C</label><input type="text" name="option_c" class="form-control"></div>
                        <div class="form-group"><label>Option D</label><input type="text" name="option_d" class="form-control"></div>
                    </div>
                    <div class="form-group">
                        <label>Correct Option <span class="required">*</span></label>
                        <select name="correct_option" class="form-control" required>
                            <option value="A">Option A</option>
                            <option value="B">Option B</option>
                            <option value="C">Option C</option>
                            <option value="D">Option D</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Explanation (Shown after answering)</label>
                        <textarea name="explanation" class="form-control" rows="2" placeholder="Why is this the correct answer?"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">+ Save Flashcard</button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <!-- 3. Add Learning Material -->
    <div class="card">
        <div class="card-header"><h2>3. Add Learning Material / Reviewer</h2></div>
        <div class="card-body">
            <?php if (count($subjects) === 0): ?>
                <div class="message-alert msg-error">Please add a Subject first before creating Learning Materials.</div>
            <?php else: ?>
                <form method="POST" action="" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="add_material">
                    
                    <div class="grid-2">
                        <div class="form-group">
                            <label>Select Subject <span class="required">*</span></label>
                            <select name="subject_id" class="form-control" required>
                                <option value="">-- Choose Subject --</option>
                                <?php foreach ($subjects as $s): ?>
                                    <option value="<?php echo $s['id']; ?>"><?php echo htmlspecialchars($s['subject_name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Material Type <span class="required">*</span></label>
                            <select name="material_type" class="form-control" id="materialType" required onchange="toggleInputs()">
                                <option value="text">Text Only</option>
                                <option value="file">File Document (PDF, Word, etc.)</option>
                                <option value="video">Video Lesson (URL)</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Material Title <span class="required">*</span></label>
                        <input type="text" name="title" class="form-control" required placeholder="e.g. Chapter 1 PDF Reviewer">
                    </div>
                    
                    <div class="form-group" id="fileGroup" style="display: none; padding: 1rem; background: #f8f9fa; border: 1px dashed #ced4da; border-radius: 4px;">
                        <label>Upload File Document <span class="required">*</span></label>
                        <input type="file" name="material_file" class="form-control" style="background: #fff;">
                        <small style="color: var(--text-muted); display: block; margin-top: 0.5rem;">Accepted formats: PDF, DOCX, TXT. Ensure the uploads directory is writable.</small>
                    </div>
                    
                    <div class="form-group" id="videoGroup" style="display: none;">
                        <label>Video URL <span class="required">*</span></label>
                        <input type="url" name="video_url" class="form-control" placeholder="https://www.youtube.com/watch?v=...">
                    </div>

                    <div class="form-group">
                        <label>Content Description</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Additional details about this material..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">+ Save Learning Material</button>
                </form>

                <script>
                    function toggleInputs() {
                        const type = document.getElementById('materialType').value;
                        document.getElementById('fileGroup').style.display = type === 'file' ? 'block' : 'none';
                        document.getElementById('videoGroup').style.display = type === 'video' ? 'block' : 'none';
                        
                        // Toggle required attributes to prevent form submission errors
                        document.querySelector('input[name="video_url"]').required = (type === 'video');
                        // File input shouldn't strictly be required via HTML if they want to update, but for creation it is.
                        document.querySelector('input[name="material_file"]').required = (type === 'file');
                    }
                    // Run on load
                    document.addEventListener('DOMContentLoaded', toggleInputs);
                </script>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>
