<?php
// member/donations.php
session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

// Ensure only member can access
require_login('member');

// Get logged-in member ID
$userId = $_SESSION['user']['id'] ?? 0;

// Fetch donations only for the logged-in member
$stmt = $pdo->prepare("SELECT * FROM donations WHERE user_id = ? ORDER BY donated_at DESC");
$stmt->execute([$userId]);
$donations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Donations - <?php echo h($masjidName ?? 'Masjid'); ?></title>
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
      <a href="events.php" class="hover:text-yellow-300">Events</a>
      <a href="donations.php" class="font-semibold hover:text-yellow-300">My Donations</a>
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
      <li class="mb-3"><a href="events.php" class="hover:underline">Events</a></li>
      <li class="mb-3"><a href="donations.php" class="font-semibold hover:underline">My Donations</a></li>
      <li class="mb-3"><a href="profile.php" class="hover:underline">Profile</a></li>
    </ul>
  </aside>

  <!-- Main Content -->
  <main class="flex-1 p-6 md:p-10">
    <h1 class="text-3xl font-bold text-green-800 mb-6">My Donations</h1>

    <div class="bg-white rounded shadow overflow-x-auto">
      <table class="w-full text-left">
        <thead class="bg-gray-50">
          <tr>
            <th class="p-3">Donor Name</th>
            <th class="p-3">Phone</th>
            <th class="p-3">Amount</th>
            <th class="p-3">Method</th>
            <th class="p-3">Note</th>
            <th class="p-3">Date</th>
          </tr>
        </thead>
        <tbody>
          <?php if($donations): ?>
            <?php foreach($donations as $d): ?>
              <tr class="border-t hover:bg-gray-50">
                <td class="p-3"><?php echo h($d['donor_name']); ?></td>
                <td class="p-3"><?php echo h($d['donor_phone']); ?></td>
                <td class="p-3">Rs. <?php echo number_format($d['amount'], 2); ?></td>
                <td class="p-3"><?php echo h($d['method']); ?></td>
                <td class="p-3"><?php echo h($d['note']); ?></td>
                <td class="p-3"><?php echo h($d['donated_at']); ?></td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="6" class="p-3 text-center text-gray-500">You have not made any donations yet.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </main>
</div>

</body>
</html>
