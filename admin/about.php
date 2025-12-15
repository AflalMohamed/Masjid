<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_login('admin');

// Fetch current About info
$about = $pdo->query("SELECT * FROM site_about ORDER BY id DESC LIMIT 1")->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $desc  = $_POST['description'] ?? '';

    if ($about) {
        $stmt = $pdo->prepare("UPDATE site_about SET title=?, description=? WHERE id=?");
        $stmt->execute([$title, $desc, $about['id']]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO site_about (title, description) VALUES (?, ?)");
        $stmt->execute([$title, $desc]);
    }

    flash('success', 'About section updated successfully.');
    header('Location: about.php');
    exit;
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Manage About Section</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
<div class="max-w-3xl mx-auto">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold text-green-800">Manage About Section</h1>
        <a href="index.php" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded transition">← Back to Dashboard</a>
    </div>

    <?php if($msg = flash('success')): ?>
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4"><?php echo h($msg); ?></div>
    <?php endif; ?>

    <div class="bg-white p-6 rounded shadow">
        <form method="post" class="space-y-4">
            <input type="text" name="title" placeholder="About Section Title" class="w-full border p-2 rounded" value="<?php echo h($about['title'] ?? ''); ?>" required>
            <textarea name="description" placeholder="Description" class="w-full border p-2 rounded" rows="6" required><?php echo h($about['description'] ?? ''); ?></textarea>
            <div class="text-right">
                <button class="bg-green-700 text-white px-4 py-2 rounded hover:bg-green-800 transition">Save</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>
