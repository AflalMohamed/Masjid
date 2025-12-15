<?php
// admin/prayers.php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_login('admin');

$action = $_GET['action'] ?? '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $_POST['date'] ?? null;
    $fajr = $_POST['fajr'] ?: null;
    $dhuhr = $_POST['dhuhr'] ?: null;
    $asr = $_POST['asr'] ?: null;
    $maghrib = $_POST['maghrib'] ?: null;
    $isha = $_POST['isha'] ?: null;
    $jummah = $_POST['jummah'] ?: null;
    $note = $_POST['note'] ?: null;

    if (isset($_POST['id']) && $_POST['id']) {
        $stmt = $pdo->prepare("UPDATE prayer_times SET fajr=?, dhuhr=?, asr=?, maghrib=?, isha=?, jummah=?, note=? WHERE id=?");
        $stmt->execute([$fajr,$dhuhr,$asr,$maghrib,$isha,$jummah,$note,$_POST['id']]);
        flash('success','Prayer time updated.');
    } else {
        $stmt = $pdo->prepare("INSERT INTO prayer_times (date,fajr,dhuhr,asr,maghrib,isha,jummah,note) VALUES (?,?,?,?,?,?,?,?)");
        $stmt->execute([$date,$fajr,$dhuhr,$asr,$maghrib,$isha,$jummah,$note]);
        flash('success','Prayer time added.');
    }
    header('Location: prayers.php');
    exit;
}

if ($action === 'delete') {
    $id = (int)($_GET['id'] ?? 0);
    if ($id) {
        $pdo->prepare("DELETE FROM prayer_times WHERE id=?")->execute([$id]);
        flash('success','Deleted.');
    }
    header('Location: prayers.php');
    exit;
}

$items = $pdo->query("SELECT * FROM prayer_times ORDER BY date DESC LIMIT 100")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Prayer Schedule - Admin</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@500;700&display=swap" rel="stylesheet">
<style>body { font-family: 'Cairo', sans-serif; }</style>
</head>
<body class="bg-gray-50 min-h-screen">

<!-- Header -->
<header class="bg-gradient-to-r from-green-800 to-green-500 text-white shadow-md sticky top-0 z-50">
  <div class="container mx-auto flex justify-between items-center px-6 py-4">
    <h1 class="text-2xl font-bold">Prayer Schedule</h1>
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
      <li class="mb-3"><a href="prayers.php" class="font-semibold underline">Prayer Schedule</a></li>
      <li class="mb-3"><a href="events.php" class="hover:underline">Events</a></li>
      <li class="mb-3"><a href="donations.php" class="hover:underline">Donations</a></li>
      <li class="mb-3"><a href="finance.php" class="hover:underline">Finance</a></li>
      <li class="mb-3"><a href="booking.php" class="hover:underline">Facility Booking</a></li>
      <li class="mb-3"><a href="announcements.php" class="hover:underline">Announcements</a></li>
      <li class="mb-3"><a href="members.php" class="hover:underline">Members</a></li>
    </ul>
  </aside>

  <!-- Main Content -->
  <main class="flex-1 p-6 md:p-10">

    <!-- Success Message -->
    <?php if ($msg = flash('success')): ?>
      <div class="bg-green-100 text-green-700 p-4 rounded mb-6 shadow">
        <?php echo h($msg); ?>
      </div>
    <?php endif; ?>

    <!-- Add / Edit Form -->
    <div class="bg-white p-6 rounded shadow mb-8">
      <h2 class="text-xl font-semibold mb-4 text-green-800">Add / Update Prayer Time</h2>
      <form method="post" class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <input type="date" name="date" class="border px-3 py-2 rounded w-full" required>
        <input type="time" name="fajr" class="border px-3 py-2 rounded w-full" placeholder="Fajr">
        <input type="time" name="dhuhr" class="border px-3 py-2 rounded w-full" placeholder="Dhuhr">
        <input type="time" name="asr" class="border px-3 py-2 rounded w-full" placeholder="Asr">
        <input type="time" name="maghrib" class="border px-3 py-2 rounded w-full" placeholder="Maghrib">
        <input type="time" name="isha" class="border px-3 py-2 rounded w-full" placeholder="Isha">
        <input type="time" name="jummah" class="border px-3 py-2 rounded w-full" placeholder="Jummah">
        <input type="text" name="note" class="border px-3 py-2 rounded w-full md:col-span-2" placeholder="Note (optional)">
        <div class="md:col-span-3 text-right">
          <button class="bg-green-700 text-white px-6 py-2 rounded hover:bg-green-600 transition">Add / Update</button>
        </div>
      </form>
    </div>

    <!-- Prayer Times Table -->
    <div class="bg-white rounded shadow overflow-x-auto">
      <table class="w-full table-auto">
        <thead class="bg-green-100">
          <tr>
            <th class="p-3 text-left">Date</th>
            <th class="p-3 text-left">Fajr</th>
            <th class="p-3 text-left">Dhuhr</th>
            <th class="p-3 text-left">Asr</th>
            <th class="p-3 text-left">Maghrib</th>
            <th class="p-3 text-left">Isha</th>
            <th class="p-3 text-left">Jummah</th>
            <th class="p-3 text-left">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if($items): ?>
            <?php foreach($items as $it): ?>
            <tr class="border-t hover:bg-gray-50">
              <td class="p-3"><?php echo h($it['date']); ?></td>
              <td class="p-3"><?php echo h($it['fajr']); ?></td>
              <td class="p-3"><?php echo h($it['dhuhr']); ?></td>
              <td class="p-3"><?php echo h($it['asr']); ?></td>
              <td class="p-3"><?php echo h($it['maghrib']); ?></td>
              <td class="p-3"><?php echo h($it['isha']); ?></td>
              <td class="p-3"><?php echo h($it['jummah']); ?></td>
              <td class="p-3 space-x-2">
                <a href="prayers_edit.php?id=<?php echo $it['id']; ?>" class="text-blue-600 hover:underline">Edit</a>
                <a href="prayers.php?action=delete&id=<?php echo $it['id']; ?>" class="text-red-600 hover:underline" onclick="return confirm('Delete this prayer time?')">Delete</a>
              </td>
            </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="8" class="p-3 text-center text-gray-500">No prayer times added yet.</td>
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
