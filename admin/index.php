<?php
// admin/index.php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

// Ensure only admin can access
require_login('admin');

// Fetch site settings
$settings = $pdo->query("SELECT `key`,`value` FROM site_settings")->fetchAll(PDO::FETCH_KEY_PAIR);
$masjidName = $settings['masjid_name'] ?? 'Al-Aqsa Grand Jummah Masjid';

// Quick stats
$prayersCount   = $pdo->query("SELECT COUNT(*) FROM prayer_times")->fetchColumn();
$eventsCount    = $pdo->query("SELECT COUNT(*) FROM events")->fetchColumn();
$donationsTotal = $pdo->query("SELECT IFNULL(SUM(amount),0) FROM donations")->fetchColumn();
$financeIncome  = $pdo->query("SELECT IFNULL(SUM(amount),0) FROM finance WHERE type='income'")->fetchColumn();
$financeExpense = $pdo->query("SELECT IFNULL(SUM(amount),0) FROM finance WHERE type='expense'")->fetchColumn();
$financeBalance = $financeIncome - $financeExpense;

// Recent donations
$recentDonations = $pdo->query("SELECT donor_name, amount, method, donated_at FROM donations ORDER BY donated_at DESC LIMIT 6")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard - <?php echo h($masjidName); ?></title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@500;700&display=swap" rel="stylesheet">
<style>body { font-family: 'Cairo', sans-serif; }</style>
</head>
<body class="bg-gray-50 min-h-screen">

<!-- Header -->
<header class="bg-gradient-to-r from-green-800 to-green-500 text-white shadow-md sticky top-0 z-50">
  <div class="container mx-auto flex justify-between items-center px-6 py-4">
    <h1 class="text-2xl font-bold"><?php echo h($masjidName); ?></h1>
    <nav class="space-x-4 hidden md:flex">
      <a href="index.php" class="hover:text-yellow-300">Dashboard</a>
      <a href="../auth/logout.php" class="bg-red-500 px-4 py-2 rounded font-semibold hover:bg-red-600 transition">Logout</a>
    </nav>
    <!-- Mobile menu button -->
    <button id="menuBtn" class="md:hidden bg-green-700 px-3 py-2 rounded">Menu</button>
  </div>
</header>

<div class="flex">

  <!-- Sidebar -->
  <aside id="sidebar" class="w-64 bg-green-900 text-white min-h-screen p-6 hidden md:block">
    <h2 class="text-xl font-bold mb-6">Admin Panel</h2>
    <ul>
      <li class="mb-3"><a href="index.php" class="font-semibold hover:underline">Dashboard</a></li>
      <li class="mb-3"><a href="prayers.php" class="hover:underline">Prayer Schedule</a></li>
      <li class="mb-3"><a href="events.php" class="hover:underline">Events</a></li>
      <li class="mb-3"><a href="donations.php" class="hover:underline">Donations</a></li>
      <li class="mb-3"><a href="finance.php" class="hover:underline">Finance</a></li>
      <li class="mb-3"><a href="booking.php" class="hover:underline">Facility Booking</a></li>
      <li class="mb-3"><a href="announcements.php" class="hover:underline">Announcements</a></li>
      <li class="mb-3"><a href="members.php" class="hover:underline">Members</a></li>
      <li class="mb-3"><a href="about.php" class="hover:underline">About Section</a></li>
      <li class="mb-3"><a href="features.php" class="hover:underline">Features</a></li>
      <li class="mb-3"><a href="contact.php" class="hover:underline">Contact Info</a></li>
    </ul>
  </aside>

  <!-- Main Content -->
  <main class="flex-1 p-6 md:p-10">
    <h1 class="text-3xl font-bold text-green-800 mb-6">Dashboard</h1>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div class="bg-white p-6 rounded shadow hover:shadow-md transition">
        <div class="text-sm text-gray-500">Prayer Entries</div>
        <div class="text-2xl font-bold"><?php echo h($prayersCount); ?></div>
      </div>
      <div class="bg-white p-6 rounded shadow hover:shadow-md transition">
        <div class="text-sm text-gray-500">Upcoming Events</div>
        <div class="text-2xl font-bold"><?php echo h($eventsCount); ?></div>
      </div>
      <div class="bg-white p-6 rounded shadow hover:shadow-md transition">
        <div class="text-sm text-gray-500">Donations (Total)</div>
        <div class="text-2xl font-bold">Rs. <?php echo number_format($donationsTotal,2); ?></div>
      </div>
    </div>

    <!-- Finance Summary -->
    <section class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
      <div class="bg-green-100 p-6 rounded shadow text-center">
        <h2 class="text-lg font-semibold text-green-800">Total Income</h2>
        <p class="text-2xl font-bold">Rs. <?php echo number_format($financeIncome,2); ?></p>
      </div>
      <div class="bg-red-100 p-6 rounded shadow text-center">
        <h2 class="text-lg font-semibold text-red-800">Total Expense</h2>
        <p class="text-2xl font-bold">Rs. <?php echo number_format($financeExpense,2); ?></p>
      </div>
      <div class="bg-yellow-100 p-6 rounded shadow text-center">
        <h2 class="text-lg font-semibold text-yellow-800">Balance</h2>
        <p class="text-2xl font-bold">Rs. <?php echo number_format($financeBalance,2); ?></p>
      </div>
    </section>

    <!-- Quick Links -->
    <section class="mt-10">
      <h2 class="text-xl font-semibold mb-4">Quick Access</h2>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <a href="finance.php" class="block bg-green-700 text-white p-6 rounded shadow hover:bg-green-800 transition text-center">Finance</a>
        <a href="booking.php" class="block bg-blue-700 text-white p-6 rounded shadow hover:bg-blue-800 transition text-center">Facility Booking</a>
        <a href="announcements.php" class="block bg-yellow-700 text-white p-6 rounded shadow hover:bg-yellow-800 transition text-center">Announcements</a>
      </div>
    </section>

    <!-- Recent Donations -->
    <section class="mt-10">
      <h2 class="text-xl font-semibold mb-4">Recent Donations</h2>
      <div class="bg-white rounded shadow overflow-x-auto">
        <table class="w-full text-left">
          <thead class="bg-gray-50">
            <tr>
              <th class="p-3">Donor</th>
              <th class="p-3">Amount</th>
              <th class="p-3">Method</th>
              <th class="p-3">Date</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($recentDonations): ?>
              <?php foreach ($recentDonations as $r): ?>
                <tr class="border-t hover:bg-gray-50">
                  <td class="p-3"><?php echo h($r['donor_name'] ?: 'Anonymous'); ?></td>
                  <td class="p-3">Rs. <?php echo number_format($r['amount'],2); ?></td>
                  <td class="p-3"><?php echo h($r['method']); ?></td>
                  <td class="p-3"><?php echo h($r['donated_at']); ?></td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="4" class="p-3 text-center text-gray-500">No donations yet.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </section>

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
