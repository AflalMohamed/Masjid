<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_login('admin');

// Add announcement
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $message = $_POST['message'] ?? '';
    $type = $_POST['type'] ?? 'general';
    $date = $_POST['date'] ?? date('Y-m-d');

    if($title && $message){
        $stmt = $pdo->prepare("INSERT INTO announcements (title,message,type,date) VALUES (?,?,?,?)");
        $stmt->execute([$title,$message,$type,$date]);
        flash('success','Announcement added successfully.');
    }
    header('Location: announcements.php');
    exit;
}

// Delete announcement
if(isset($_GET['action']) && $_GET['action']=='delete'){
    $id = (int)($_GET['id'] ?? 0);
    if($id){
        $pdo->prepare("DELETE FROM announcements WHERE id=?")->execute([$id]);
        flash('success','Announcement deleted.');
    }
    header('Location: announcements.php');
    exit;
}

$announcements = $pdo->query("SELECT * FROM announcements ORDER BY date DESC")->fetchAll();
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Announcements - Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@500;700&display=swap" rel="stylesheet">
  <style>body { font-family: 'Cairo', sans-serif; }</style>
</head>
<body class="bg-gray-50 min-h-screen">

<!-- Header -->
<header class="bg-gradient-to-r from-green-800 to-green-500 text-white shadow-md sticky top-0 z-50">
  <div class="container mx-auto flex justify-between items-center px-6 py-4">
    <h1 class="text-2xl font-bold">Announcements</h1>
    <nav class="space-x-4 hidden md:flex">
      <a href="index.php" class="bg-yellow-500 px-4 py-2 rounded font-semibold hover:bg-yellow-600 transition">Back to Dashboard</a>
      <a href="../auth/logout.php" class="bg-red-500 px-4 py-2 rounded font-semibold hover:bg-red-600 transition">Logout</a>
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
      <li class="mb-3"><a href="events.php" class="hover:underline">Events</a></li>
      <li class="mb-3"><a href="donations.php" class="hover:underline">Donations</a></li>
      <li class="mb-3"><a href="finance.php" class="hover:underline">Finance</a></li>
      <li class="mb-3"><a href="booking.php" class="hover:underline">Facility Booking</a></li>
      <li class="mb-3"><a href="announcements.php" class="font-semibold underline">Announcements</a></li>
      <li class="mb-3"><a href="members.php" class="hover:underline">Members</a></li>
    </ul>
  </aside>

  <!-- Main Content -->
  <main class="flex-1 p-6 md:p-10">
    <?php if($msg = flash('success')): ?>
      <div class="bg-green-100 text-green-700 p-3 rounded mb-4"><?= h($msg) ?></div>
    <?php endif; ?>

    <!-- Add Announcement Form -->
    <div class="bg-white p-6 rounded shadow mb-6">
      <h2 class="text-xl font-semibold mb-4">Add New Announcement</h2>
      <form method="post" class="grid md:grid-cols-2 gap-4">
        <input type="text" name="title" placeholder="Title" class="border rounded px-3 py-2 w-full" required>
        <select name="type" class="border rounded px-3 py-2 w-full">
          <option value="general">General</option>
          <option value="alert">Alert</option>
          <option value="event">Event</option>
        </select>
        <input type="date" name="date" class="border rounded px-3 py-2 w-full" value="<?= date('Y-m-d') ?>" required>
        <textarea name="message" placeholder="Message" class="border rounded px-3 py-2 w-full md:col-span-2" required></textarea>
        <div class="md:col-span-2 text-right">
          <button class="bg-yellow-700 text-white px-4 py-2 rounded hover:bg-yellow-800 transition">Add Announcement</button>
        </div>
      </form>
    </div>

    <!-- Announcement List -->
    <div class="bg-white rounded shadow overflow-x-auto">
      <h2 class="text-xl font-semibold p-4 border-b">All Announcements</h2>
      <table class="w-full text-left">
        <thead class="bg-gray-50">
          <tr>
            <th class="p-3">Date</th>
            <th class="p-3">Title</th>
            <th class="p-3">Type</th>
            <th class="p-3">Message</th>
            <th class="p-3">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($announcements as $a): ?>
          <tr class="border-t hover:bg-gray-50">
            <td class="p-3"><?= h($a['date']) ?></td>
            <td class="p-3"><?= h($a['title']) ?></td>
            <td class="p-3"><?= ucfirst($a['type']) ?></td>
            <td class="p-3"><?= h($a['message']) ?></td>
            <td class="p-3">
              <a href="announcements.php?action=delete&id=<?= $a['id'] ?>" class="text-red-600" onclick="return confirm('Delete this announcement?')">Delete</a>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php if(empty($announcements)): ?>
          <tr><td colspan="5" class="p-3 text-center text-gray-500">No announcements yet.</td></tr>
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
