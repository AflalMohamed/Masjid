<?php
// member/events.php
session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

// Ensure only member can access
require_login('member');

// Fetch all events ordered by start_datetime
$events = $pdo->query("SELECT * FROM events ORDER BY start_datetime ASC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Events - <?php echo h($masjidName ?? 'Masjid'); ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@500;700&display=swap" rel="stylesheet">
  <style>body { font-family: 'Cairo', sans-serif; }</style>
</head>
<body class="bg-gray-50 min-h-screen">

<!-- Header -->
<header class="bg-gradient-to-r from-green-800 to-green-500 text-white shadow-md sticky top-0 z-50">
  <div class="container mx-auto flex justify-between items-center px-6 py-4">
    <h1 class="text-2xl font-bold"><?php echo h($masjidName ?? 'Masjid'); ?> - Member</h1>
    <nav class="space-x-6 hidden md:flex">
      <a href="index.php#home" class="hover:text-yellow-300">Home</a>
      <a href="prayers.php" class="hover:text-yellow-300">Prayers</a>
      <a href="events.php" class="font-semibold hover:text-yellow-300">Events</a>
      <a href="donations.php" class="hover:text-yellow-300">Donations</a>
      <a href="profile.php" class="hover:text-yellow-300">Profile</a>
      <a href="../auth/logout.php" class="bg-red-500 px-4 py-2 rounded font-semibold hover:bg-red-600 transition">Logout</a>
    </nav>
  </div>
</header>

<div class="flex">

  <!-- Sidebar -->
  <aside class="w-64 bg-green-900 text-white min-h-screen p-6 hidden md:block">
    <h2 class="text-xl font-bold mb-6">Member Panel</h2>
    <ul>
      <li class="mb-3"><a href="index.php" class="hover:underline">Dashboard</a></li>
      <li class="mb-3"><a href="prayers.php" class="hover:underline">Prayer Schedule</a></li>
      <li class="mb-3"><a href="events.php" class="font-semibold hover:underline">Events</a></li>
      <li class="mb-3"><a href="donations.php" class="hover:underline">My Donations</a></li>
      <li class="mb-3"><a href="profile.php" class="hover:underline">Profile</a></li>
    </ul>
  </aside>

  <!-- Main Content -->
  <main class="flex-1 p-6 md:p-10">
    <h1 class="text-3xl font-bold text-green-800 mb-6">Upcoming Events</h1>

    <div class="bg-white rounded shadow overflow-x-auto">
      <table class="w-full text-left">
        <thead class="bg-gray-50">
          <tr>
            <th class="p-3">Title</th>
            <th class="p-3">Description</th>
            <th class="p-3">Start Date & Time</th>
            <th class="p-3">End Date & Time</th>
            <th class="p-3">Location</th>
          </tr>
        </thead>
        <tbody>
          <?php if($events): ?>
            <?php foreach($events as $e): ?>
              <tr class="border-t hover:bg-gray-50">
                <td class="p-3"><?php echo h($e['title']); ?></td>
                <td class="p-3"><?php echo h($e['description']); ?></td>
                <td class="p-3"><?php echo h($e['start_datetime']); ?></td>
                <td class="p-3"><?php echo h($e['end_datetime']); ?></td>
                <td class="p-3"><?php echo h($e['location']); ?></td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="5" class="p-3 text-center text-gray-500">No upcoming events.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </main>
</div>

</body>
</html>
