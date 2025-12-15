<?php
// admin/booking.php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_login('admin');

// Flash messages helper
$msg = flash('success');

// Handle new booking submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['member_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $hall = trim($_POST['hall_name'] ?? '');
    $event = trim($_POST['event_type'] ?? 'other');
    $date = $_POST['event_date'] ?? '';

    if ($name && $hall && $event && $date) {
        $stmt = $pdo->prepare("INSERT INTO facility_booking (member_name,email,phone,hall_name,event_type,event_date,status,created_at) VALUES (?,?,?,?,?,?,?,NOW())");
        $stmt->execute([$name, $email, $phone, $hall, $event, $date, 'pending']); // fixed PDO parameter count
        flash('success', 'Booking request added successfully.');
        header('Location: booking.php');
        exit;
    } else {
        flash('success', 'Please fill in all required fields.');
        header('Location: booking.php');
        exit;
    }
}

// Approve / Reject booking
if (isset($_GET['action']) && in_array($_GET['action'], ['approve','reject'])) {
    $id = (int)($_GET['id'] ?? 0);
    if ($id) {
        $status = $_GET['action'] === 'approve' ? 'approved' : 'rejected';
        $pdo->prepare("UPDATE facility_booking SET status=? WHERE id=?")->execute([$status,$id]);
        flash('success', 'Booking status updated.');
    }
    header('Location: booking.php');
    exit;
}

// Fetch all bookings
$bookings = $pdo->query("SELECT * FROM facility_booking ORDER BY event_date DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Facility Booking - Admin</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@500;700&display=swap" rel="stylesheet">
<style>body { font-family: 'Cairo', sans-serif; }</style>
</head>
<body class="bg-gray-50 min-h-screen">

<!-- Header -->
<header class="bg-gradient-to-r from-green-800 to-green-500 text-white shadow-md sticky top-0 z-50">
  <div class="container mx-auto flex justify-between items-center px-6 py-4">
    <h1 class="text-2xl font-bold">Facility Booking</h1>
    <nav class="space-x-4 hidden md:flex">
      <a href="index.php" class="bg-yellow-500 px-4 py-2 rounded font-semibold hover:bg-yellow-600 transition">Dashboard</a>
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
      <li class="mb-3"><a href="booking.php" class="font-semibold underline">Facility Booking</a></li>
      <li class="mb-3"><a href="announcements.php" class="hover:underline">Announcements</a></li>
      <li class="mb-3"><a href="members.php" class="hover:underline">Members</a></li>
    </ul>
  </aside>

  <!-- Main Content -->
  <main class="flex-1 p-6 md:p-10">

    <?php if($msg): ?>
      <div class="bg-green-100 text-green-700 p-3 rounded mb-4"><?php echo h($msg); ?></div>
    <?php endif; ?>

    <!-- Add Booking Form -->
    <div class="bg-white p-6 rounded shadow mb-6">
      <h2 class="text-xl font-semibold mb-4">Add New Booking</h2>
      <form method="post" class="grid md:grid-cols-2 gap-4">
        <input type="text" name="member_name" placeholder="Member Name" class="border rounded px-3 py-2 w-full" required>
        <input type="email" name="email" placeholder="Email" class="border rounded px-3 py-2 w-full">
        <input type="text" name="phone" placeholder="Phone" class="border rounded px-3 py-2 w-full">
        <input type="text" name="hall_name" placeholder="Hall Name" class="border rounded px-3 py-2 w-full" required>
        <select name="event_type" class="border rounded px-3 py-2 w-full" required>
          <option value="nikah">Nikah</option>
          <option value="janazah">Janazah</option>
          <option value="other">Other</option>
        </select>
        <input type="date" name="event_date" class="border rounded px-3 py-2 w-full" required>
        <div class="md:col-span-2 text-right">
          <button class="bg-blue-700 text-white px-4 py-2 rounded hover:bg-blue-800 transition">Add Booking</button>
        </div>
      </form>
    </div>

    <!-- Booking List -->
    <div class="bg-white rounded shadow overflow-x-auto">
      <h2 class="text-xl font-semibold p-4 border-b">All Bookings</h2>
      <table class="w-full text-left">
        <thead class="bg-gray-50">
          <tr>
            <th class="p-3">Member</th>
            <th class="p-3">Hall</th>
            <th class="p-3">Event</th>
            <th class="p-3">Date</th>
            <th class="p-3">Status</th>
            <th class="p-3">Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach($bookings as $b): ?>
          <tr class="border-t hover:bg-gray-50">
            <td class="p-3"><?= h($b['member_name']) ?></td>
            <td class="p-3"><?= h($b['hall_name']) ?></td>
            <td class="p-3"><?= ucfirst($b['event_type']) ?></td>
            <td class="p-3"><?= date('d M Y', strtotime($b['event_date'])) ?></td>
            <td class="p-3">
              <?php
                $statusClass = match($b['status']){
                  'pending' => 'bg-yellow-100 text-yellow-800',
                  'approved' => 'bg-green-100 text-green-800',
                  'rejected' => 'bg-red-100 text-red-800',
                  default => 'bg-gray-100 text-gray-700',
                };
              ?>
              <span class="px-2 py-1 rounded <?= $statusClass ?>"><?= ucfirst($b['status']) ?></span>
            </td>
            <td class="p-3">
              <?php if($b['status'] === 'pending'): ?>
                <a href="booking.php?action=approve&id=<?= $b['id'] ?>" class="text-green-600 mr-2">Approve</a>
                <a href="booking.php?action=reject&id=<?= $b['id'] ?>" class="text-red-600">Reject</a>
              <?php else: ?>
                <span class="text-gray-500">N/A</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
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
