<?php
// admin/events.php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_login('admin');

// Add Event
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $desc = $_POST['description'] ?? '';
    $start = $_POST['start_datetime'] ?? null;
    $end = $_POST['end_datetime'] ?? null;
    $location = $_POST['location'] ?? '';

    if ($title && $start) {
        $stmt = $pdo->prepare("INSERT INTO events (title, description, start_datetime, end_datetime, location) VALUES (?,?,?,?,?)");
        $stmt->execute([$title, $desc, $start, $end, $location]);
        flash('success', 'Event added successfully.');
    }
    header('Location: events.php');
    exit;
}

// Delete Event
if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    $id = (int)($_GET['id'] ?? 0);
    if ($id) {
        $pdo->prepare("DELETE FROM events WHERE id=?")->execute([$id]);
        flash('success', 'Event deleted.');
    }
    header('Location: events.php');
    exit;
}

$events = $pdo->query("SELECT * FROM events ORDER BY start_datetime DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Events Management - Admin</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@500;700&display=swap" rel="stylesheet">
<style>body { font-family: 'Cairo', sans-serif; }</style>
</head>
<body class="bg-gray-50 min-h-screen">

<!-- Header -->
<header class="bg-gradient-to-r from-green-800 to-green-500 text-white shadow-md sticky top-0 z-50">
  <div class="container mx-auto flex justify-between items-center px-6 py-4">
    <h1 class="text-2xl font-bold">Events Management</h1>
    <nav class="space-x-4 hidden md:flex">
      <a href="index.php" class="bg-gray-200 text-gray-800 px-4 py-2 rounded hover:bg-gray-300 transition">&#8592; Back to Dashboard</a>
      <a href="../auth/logout.php" class="bg-red-500 px-4 py-2 rounded hover:bg-red-600 transition">Logout</a>
    </nav>
    <button id="menuBtn" class="md:hidden bg-green-700 px-3 py-2 rounded">Menu</button>
  </div>
</header>

<div class="flex">

  <!-- Sidebar -->
  <aside id="sidebar" class="w-64 bg-green-900 text-white min-h-screen p-6 hidden md:block">
    <h2 class="text-xl font-bold mb-6">Admin Panel</h2>
    <ul>
      <li class="mb-3"><a href="index.php" class="hover:underline">Dashboard</a></li>
      <li class="mb-3"><a href="prayers.php" class="hover:underline">Prayer Schedule</a></li>
      <li class="mb-3"><a href="events.php" class="font-semibold underline">Events</a></li>
      <li class="mb-3"><a href="donations.php" class="hover:underline">Donations</a></li>
      <li class="mb-3"><a href="booking.php" class="hover:underline">Facility Booking</a></li>
      <li class="mb-3"><a href="announcements.php" class="hover:underline">Announcements</a></li>
      <li class="mb-3"><a href="members.php" class="hover:underline">Members</a></li>
    </ul>
  </aside>

  <!-- Main Content -->
  <main class="flex-1 p-6 md:p-10">

    <!-- Flash Messages -->
    <?php if ($msg = flash('success')): ?>
      <div class="bg-green-100 text-green-700 p-4 rounded mb-6 shadow">
        <?php echo h($msg); ?>
      </div>
    <?php endif; ?>

    <!-- Add Event Form -->
    <div class="bg-white p-6 rounded shadow mb-8">
      <h2 class="text-xl font-semibold mb-4 text-green-800">Add New Event</h2>
      <form method="post" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <input name="title" placeholder="Event Title" class="border rounded px-3 py-2 w-full" required>
        <input name="location" placeholder="Location" class="border rounded px-3 py-2 w-full">
        <textarea name="description" placeholder="Description" class="border rounded px-3 py-2 md:col-span-2"></textarea>
        <div>
          <label class="block text-gray-600 mb-1">Start Date & Time</label>
          <input type="datetime-local" name="start_datetime" class="border rounded px-3 py-2 w-full" required>
        </div>
        <div>
          <label class="block text-gray-600 mb-1">End Date & Time</label>
          <input type="datetime-local" name="end_datetime" class="border rounded px-3 py-2 w-full">
        </div>
        <div class="md:col-span-2 text-right">
          <button class="bg-green-700 text-white px-6 py-2 rounded hover:bg-green-600 transition">Add Event</button>
        </div>
      </form>
    </div>

    <!-- Events Table -->
    <div class="bg-white rounded shadow overflow-x-auto">
      <table class="w-full table-auto">
        <thead class="bg-green-100">
          <tr>
            <th class="p-3 text-left">Title</th>
            <th class="p-3 text-left">Start</th>
            <th class="p-3 text-left">End</th>
            <th class="p-3 text-left">Location</th>
            <th class="p-3 text-left">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($events): ?>
            <?php foreach ($events as $e): ?>
            <tr class="border-t hover:bg-gray-50">
              <td class="p-3"><?php echo h($e['title']); ?></td>
              <td class="p-3"><?php echo h($e['start_datetime']); ?></td>
              <td class="p-3"><?php echo h($e['end_datetime']); ?></td>
              <td class="p-3"><?php echo h($e['location']); ?></td>
              <td class="p-3 space-x-2">
                <a href="event_edit.php?id=<?php echo $e['id']; ?>" class="text-blue-600 hover:underline">Edit</a>
                <a href="events.php?action=delete&id=<?php echo $e['id']; ?>" class="text-red-600 hover:underline" onclick="return confirm('Delete this event?')">Delete</a>
              </td>
            </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="5" class="p-3 text-center text-gray-500">No events added yet.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

  </main>
</div>

<script>
  const menuBtn = document.getElementById('menuBtn');
  const sidebar = document.getElementById('sidebar');
  menuBtn.addEventListener('click', () => {
    sidebar.classList.toggle('hidden');
  });
</script>

</body>
</html>
