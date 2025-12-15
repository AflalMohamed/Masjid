<?php
// admin/event_edit.php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_login('admin');

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM events WHERE id=?");
$stmt->execute([$id]);
$event = $stmt->fetch();

if (!$event) {
    echo "Event not found.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $desc = $_POST['description'] ?? '';
    $start = $_POST['start_datetime'] ?? null;
    $end = $_POST['end_datetime'] ?? null;
    $location = $_POST['location'] ?? '';

    $stmt = $pdo->prepare("UPDATE events SET title=?, description=?, start_datetime=?, end_datetime=?, location=? WHERE id=?");
    $stmt->execute([$title, $desc, $start, $end, $location, $id]);
    flash('success', 'Event updated successfully.');
    header('Location: events.php');
    exit;
}
?>

<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Edit Event</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
  <div class="max-w-4xl mx-auto p-6">
    <h1 class="text-2xl font-bold text-green-800 mb-4">Edit Event</h1>
    <div class="bg-white p-6 rounded shadow">
      <form method="post" class="grid md:grid-cols-2 gap-4">
        <input name="title" value="<?php echo h($event['title']); ?>" placeholder="Event Title" class="border rounded px-3 py-2 w-full" required>
        <input name="location" value="<?php echo h($event['location']); ?>" placeholder="Location" class="border rounded px-3 py-2 w-full">
        <textarea name="description" class="border rounded px-3 py-2 md:col-span-2"><?php echo h($event['description']); ?></textarea>
        <div>
          <label class="block text-gray-600">Start</label>
          <input type="datetime-local" name="start_datetime" value="<?php echo str_replace(' ', 'T', $event['start_datetime']); ?>" class="border rounded px-3 py-2 w-full">
        </div>
        <div>
          <label class="block text-gray-600">End</label>
          <input type="datetime-local" name="end_datetime" value="<?php echo str_replace(' ', 'T', $event['end_datetime']); ?>" class="border rounded px-3 py-2 w-full">
        </div>
        <div class="md:col-span-2 text-right">
          <button class="bg-green-700 text-white px-4 py-2 rounded">Save Changes</button>
          <a href="events.php" class="ml-3 bg-gray-300 px-4 py-2 rounded">Cancel</a>
        </div>
      </form>
    </div>
  </div>
</body>
</html>
