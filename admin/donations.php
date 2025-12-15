<?php
// admin/donations.php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_login('admin');

// Add Donation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $donor = $_POST['donor_name'] ?: null;
    $phone = $_POST['donor_phone'] ?: null;
    $amount = floatval($_POST['amount']);
    $method = $_POST['method'] ?? 'cash';
    $note = $_POST['note'] ?? null;

    if($amount > 0){
        $stmt = $pdo->prepare("INSERT INTO donations (donor_name, donor_phone, amount, method, note) VALUES (?,?,?,?,?)");
        $stmt->execute([$donor, $phone, $amount, $method, $note]);
        flash('success','Donation recorded.');
    }
    header('Location: donations.php');
    exit;
}

// Fetch donations
$rows = $pdo->query("SELECT * FROM donations ORDER BY donated_at DESC LIMIT 200")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Donations - Admin</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@500;700&display=swap" rel="stylesheet">
<style>body { font-family: 'Cairo', sans-serif; }</style>
</head>
<body class="bg-gray-50 min-h-screen">

<!-- Header -->
<header class="bg-gradient-to-r from-green-800 to-green-500 text-white shadow-md sticky top-0 z-50">
  <div class="container mx-auto flex justify-between items-center px-6 py-4">
    <h1 class="text-2xl font-bold">Donations</h1>
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
      <li class="mb-3"><a href="events.php" class="hover:underline">Events</a></li>
      <li class="mb-3"><a href="donations.php" class="font-semibold underline">Donations</a></li>
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

    <!-- Add Donation Form -->
    <div class="bg-white p-6 rounded shadow mb-8">
      <h2 class="text-xl font-semibold mb-4 text-green-800">Record New Donation</h2>
      <form method="post" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <input name="donor_name" placeholder="Donor Name" class="border rounded px-3 py-2 w-full">
        <input name="donor_phone" placeholder="Phone" class="border rounded px-3 py-2 w-full">
        <input name="amount" type="number" step="0.01" placeholder="Amount" class="border rounded px-3 py-2 w-full" required>
        <select name="method" class="border rounded px-3 py-2 w-full">
          <option value="cash">Cash</option>
          <option value="online">Online</option>
          <option value="other">Other</option>
        </select>
        <input name="note" placeholder="Note (optional)" class="border rounded px-3 py-2 md:col-span-4">
        <div class="md:col-span-4 text-right">
          <button class="bg-green-700 text-white px-6 py-2 rounded hover:bg-green-600 transition">Record Donation</button>
        </div>
      </form>
    </div>

    <!-- Donations Table -->
    <div class="bg-white rounded shadow overflow-x-auto">
      <table class="w-full table-auto">
        <thead class="bg-green-100">
          <tr>
            <th class="p-3 text-left">Donor</th>
            <th class="p-3 text-left">Amount</th>
            <th class="p-3 text-left">Method</th>
            <th class="p-3 text-left">Date</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($rows): ?>
            <?php foreach ($rows as $r): ?>
            <tr class="border-t hover:bg-gray-50">
              <td class="p-3"><?php echo h($r['donor_name'] ?: 'Anonymous'); ?></td>
              <td class="p-3">Rs. <?php echo number_format($r['amount'],2); ?></td>
              <td class="p-3"><?php echo h($r['method']); ?></td>
              <td class="p-3"><?php echo h($r['donated_at']); ?></td>
            </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="4" class="p-3 text-center text-gray-500">No donations recorded yet.</td>
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
