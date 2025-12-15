<?php
// admin/features.php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_login('admin');

// Add Feature
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $desc = $_POST['description'] ?? '';
    $icon = $_POST['icon_url'] ?? '';

    $stmt = $pdo->prepare("INSERT INTO features (title, description, icon_url) VALUES (?,?,?)");
    $stmt->execute([$title, $desc, $icon]);
    flash('success', 'Feature added successfully.');
    header('Location: features.php');
    exit;
}

// Delete Feature
if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    $id = (int)($_GET['id'] ?? 0);
    if ($id) {
        $pdo->prepare("DELETE FROM features WHERE id=?")->execute([$id]);
        flash('success', 'Feature deleted.');
    }
    header('Location: features.php');
    exit;
}

// Fetch all features
$features = $pdo->query("SELECT * FROM features ORDER BY id ASC")->fetchAll();
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Manage Features</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
  <div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-4">
      <h1 class="text-2xl font-bold text-green-800">Manage Features</h1>
      <a href="index.php" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded transition">← Back to Dashboard</a>
    </div>

    <?php if($msg = flash('success')): ?>
      <div class="bg-green-100 text-green-700 p-3 rounded mb-4"><?php echo h($msg); ?></div>
    <?php endif; ?>

    <!-- Add Feature Form -->
    <div class="bg-white p-6 rounded shadow mb-6">
      <form method="post" class="space-y-4">
        <input type="text" name="title" placeholder="Feature Title" class="w-full border p-2 rounded" required>
        <input type="text" name="icon_url" placeholder="Icon URL (optional)" class="w-full border p-2 rounded">
        <textarea name="description" placeholder="Description" class="w-full border p-2 rounded" required></textarea>
        <div class="text-right">
          <button class="bg-green-700 text-white px-4 py-2 rounded hover:bg-green-800 transition">Add Feature</button>
        </div>
      </form>
    </div>

    <!-- Feature List -->
    <div class="bg-white rounded shadow overflow-hidden">
      <table class="w-full text-left">
        <thead class="bg-gray-50">
          <tr>
            <th class="p-3">Title</th>
            <th class="p-3">Description</th>
            <th class="p-3">Icon</th>
            <th class="p-3">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($features as $f): ?>
          <tr class="border-t hover:bg-gray-50">
            <td class="p-3 font-semibold"><?php echo h($f['title']); ?></td>
            <td class="p-3"><?php echo h($f['description']); ?></td>
            <td class="p-3">
              <?php if($f['icon_url']): ?>
                <img src="<?php echo h($f['icon_url']); ?>" class="w-8 h-8 object-contain">
              <?php else: ?>
                <span class="text-gray-400">–</span>
              <?php endif; ?>
            </td>
            <td class="p-3">
              <a href="features.php?action=delete&id=<?php echo $f['id']; ?>" class="text-red-600 hover:underline" onclick="return confirm('Delete this feature?')">Delete</a>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php if(empty($features)): ?>
          <tr>
            <td colspan="4" class="p-3 text-center text-gray-500">No features added yet.</td>
          </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>
