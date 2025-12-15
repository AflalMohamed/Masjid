<?php
// member/index.php
session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

// Ensure only member can access
require_login('member');

$user = $_SESSION['user'] ?? null;
$user_id = $user['id'] ?? 0;

// Quick stats
$prayersCount = $pdo->query("SELECT COUNT(*) FROM prayer_times")->fetchColumn();
$eventsCount = $pdo->query("SELECT COUNT(*) FROM events")->fetchColumn();

// Total donations by logged-in member
$stmt = $pdo->prepare("SELECT IFNULL(SUM(amount),0) FROM donations WHERE user_id = ?");
$stmt->execute([$user_id]);
$donationsTotal = $stmt->fetchColumn();

// Finance summary
$totalIncome = $pdo->query("SELECT IFNULL(SUM(amount),0) FROM finance WHERE type='income'")->fetchColumn();
$totalExpense = $pdo->query("SELECT IFNULL(SUM(amount),0) FROM finance WHERE type='expense'")->fetchColumn();
$balance = $totalIncome - $totalExpense;

// Recent events (limit 5)
$recentEvents = $pdo->query("SELECT title, start_datetime, end_datetime, location FROM events ORDER BY start_datetime DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

// Recent announcements (limit 5)
$announcements = $pdo->query("SELECT title, message FROM announcements ORDER BY id DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

// Member's facility bookings
$memberBookingsStmt = $pdo->prepare("SELECT * FROM facility_booking WHERE member_name=? ORDER BY created_at DESC");
$memberBookingsStmt->execute([$user['fullname'] ?? '']);
$memberBookings = $memberBookingsStmt->fetchAll(PDO::FETCH_ASSOC);

// Site name
$masjidName = $pdo->query("SELECT value FROM site_settings WHERE `key`='masjid_name'")->fetchColumn() ?? 'Masjid';

// Handle facility booking submission
$bookingMessage = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['facility_submit'])) {
    $hall_name = trim($_POST['hall_name']);
    $event_type = trim($_POST['event_type']);
    $event_date = $_POST['event_date'];
    $member_name = trim($user['fullname'] ?? '');
    $email = trim($user['email'] ?? '');
    $phone = trim($user['phone'] ?? '');

    if ($hall_name && $event_type && $event_date && $member_name) {
        $stmt = $pdo->prepare("INSERT INTO facility_booking (member_name,email,phone,hall_name,event_type,event_date,status,created_at) VALUES (?,?,?,?,?,?,?,NOW())");
        if ($stmt->execute([$member_name, $email, $phone, $hall_name, $event_type, $event_date, 'pending'])) {
            $bookingMessage = "Your booking request has been submitted successfully!";
        } else {
            $bookingMessage = "Failed to submit booking. Please try again.";
        }
    } else {
        $bookingMessage = "Please fill in all required fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Member Dashboard - <?php echo h($masjidName); ?></title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@500;700&display=swap" rel="stylesheet">
<style>body { font-family: 'Cairo', sans-serif; }</style>
</head>
<body class="bg-gray-50 min-h-screen">

<!-- Header -->
<header class="bg-gradient-to-r from-green-800 to-green-500 text-white shadow-md sticky top-0 z-50">
  <div class="container mx-auto flex justify-between items-center px-6 py-4">
    <h1 class="text-2xl font-bold"><?php echo h($masjidName); ?> - Member</h1>
    <nav class="space-x-6 hidden md:flex">
      <a href="index.php#home" class="hover:text-yellow-300 transition">Home</a>
      <a href="prayers.php" class="hover:text-yellow-300 transition">Prayers</a>
      <a href="events.php" class="hover:text-yellow-300 transition">Events</a>
      <a href="donations.php" class="hover:text-yellow-300 transition">Donations</a>
      <a href="profile.php" class="hover:text-yellow-300 transition">Profile</a>
      <a href="#facilityBooking" class="hover:text-yellow-300 transition">Facility Booking</a>
      <a href="../auth/logout.php" class="bg-red-500 px-4 py-2 rounded font-semibold hover:bg-red-600 transition">Logout</a>
    </nav>
    <button id="menuBtn" class="md:hidden bg-green-700 px-3 py-2 rounded">Menu</button>
  </div>
</header>

<div class="flex">

  <!-- Sidebar -->
  <aside id="sidebar" class="w-64 bg-green-900 text-white min-h-screen p-6 hidden md:block">
    <h2 class="text-xl font-bold mb-6">Member Panel</h2>
    <ul>
      <li class="mb-3"><a href="index.php" class="font-semibold hover:underline">Dashboard</a></li>
      <li class="mb-3"><a href="prayers.php" class="hover:underline">Prayer Schedule</a></li>
      <li class="mb-3"><a href="events.php" class="hover:underline">Events</a></li>
      <li class="mb-3"><a href="donations.php" class="hover:underline">My Donations</a></li>
      <li class="mb-3"><a href="profile.php" class="hover:underline">Profile</a></li>
      <li class="mb-3"><a href="#facilityBooking" class="hover:underline">Facility Booking</a></li>
    </ul>
  </aside>

  <!-- Main Content -->
  <main class="flex-1 p-6 md:p-10">
    <h1 class="text-3xl font-bold text-green-800 mb-6">Welcome, <?php echo h($user['fullname']); ?></h1>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-8">
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
      <div class="bg-white p-6 rounded shadow hover:shadow-md transition">
        <div class="text-sm text-gray-500">Total Income</div>
        <div class="text-2xl font-bold">Rs. <?php echo number_format($totalIncome,2); ?></div>
      </div>
      <div class="bg-white p-6 rounded shadow hover:shadow-md transition">
        <div class="text-sm text-gray-500">Total Expense</div>
        <div class="text-2xl font-bold">Rs. <?php echo number_format($totalExpense,2); ?></div>
      </div>
    </div>

    <!-- Facility Booking Form -->
    <section id="facilityBooking" class="mb-10">
      <h2 class="text-xl font-semibold mb-4">Facility Booking</h2>

      <?php if($bookingMessage): ?>
        <div class="mb-4 p-3 rounded <?php echo strpos($bookingMessage, 'success')!==false ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
          <?php echo h($bookingMessage); ?>
        </div>
      <?php endif; ?>

      <form method="POST" class="bg-white p-6 rounded shadow space-y-4">
        <div>
          <label class="block font-semibold mb-1">Hall Name</label>
          <input type="text" name="hall_name" class="w-full border border-gray-300 rounded px-3 py-2" required>
        </div>
        <div>
          <label class="block font-semibold mb-1">Event Type</label>
          <input type="text" name="event_type" class="w-full border border-gray-300 rounded px-3 py-2" required>
        </div>
        <div>
          <label class="block font-semibold mb-1">Event Date</label>
          <input type="date" name="event_date" class="w-full border border-gray-300 rounded px-3 py-2" required>
        </div>
        <div>
          <button type="submit" name="facility_submit" class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700 transition">Submit Booking</button>
        </div>
      </form>
    </section>

    <!-- Member Bookings -->
    <section class="mb-10">
      <h2 class="text-xl font-semibold mb-4">My Facility Bookings</h2>
      <div class="bg-white rounded shadow overflow-x-auto">
        <table class="w-full text-left">
          <thead class="bg-gray-50">
            <tr>
              <th class="p-3">Member</th>
              <th class="p-3">Hall</th>
              <th class="p-3">Event Type</th>
              <th class="p-3">Event Date</th>
              <th class="p-3">Status</th>
              <th class="p-3">Submitted At</th>
            </tr>
          </thead>
          <tbody>
            <?php if($memberBookings): ?>
              <?php foreach($memberBookings as $b): ?>
                <tr class="border-t hover:bg-gray-50">
                  <td class="p-3"><?= h($b['member_name']) ?></td>
                  <td class="p-3"><?= h($b['hall_name']) ?></td>
                  <td class="p-3"><?= h($b['event_type']) ?></td>
                  <td class="p-3"><?= date('d M Y', strtotime($b['event_date'])) ?></td>
                  <td class="p-3"><?= ucfirst($b['status']) ?></td>
                  <td class="p-3"><?= date('d M Y h:i A', strtotime($b['created_at'])) ?></td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="6" class="p-3 text-center text-gray-500">No bookings found.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </section>

    <!-- Recent Events -->
    <section class="mb-10">
      <h2 class="text-xl font-semibold mb-4">Recent Events</h2>
      <ul class="bg-white rounded shadow divide-y divide-gray-200">
        <?php foreach($recentEvents as $e): ?>
          <li class="p-4">
            <div class="font-semibold"><?= h($e['title']) ?></div>
            <div class="text-sm text-gray-500"><?= date('d M Y h:i A', strtotime($e['start_datetime'])) ?> - <?= date('d M Y h:i A', strtotime($e['end_datetime'])) ?> | <?= h($e['location']) ?></div>
          </li>
        <?php endforeach; ?>
        <?php if(empty($recentEvents)): ?>
          <li class="p-4 text-gray-500 text-center">No recent events.</li>
        <?php endif; ?>
      </ul>
    </section>

    <!-- Recent Announcements -->
    <section class="mb-10">
      <h2 class="text-xl font-semibold mb-4">Recent Announcements</h2>
      <ul class="bg-white rounded shadow divide-y divide-gray-200">
        <?php foreach($announcements as $a): ?>
          <li class="p-4">
            <div class="font-semibold"><?= h($a['title']) ?></div>
            <div class="text-sm text-gray-500"><?= h($a['message']) ?></div>
          </li>
        <?php endforeach; ?>
        <?php if(empty($announcements)): ?>
          <li class="p-4 text-gray-500 text-center">No announcements.</li>
        <?php endif; ?>
      </ul>
    </section>

  </main>
</div>

<script>
  const menuBtn = document.getElementById('menuBtn');
  const sidebar = document.getElementById('sidebar');
  menuBtn.addEventListener('click', () => sidebar.classList.toggle('hidden'));
</script>
</body>
</html>
