<?php
// admin/finance.php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_login('admin');

// Add record
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type'] ?? 'income';
    $category = $_POST['category'] ?? '';
    $amount = (float)($_POST['amount'] ?? 0);
    $desc = $_POST['description'] ?? '';

    if ($amount > 0) {
        $stmt = $pdo->prepare("INSERT INTO finance (type, category, amount, description, created_at) VALUES (?,?,?,?,NOW())");
        $stmt->execute([$type, $category, $amount, $desc]);
        flash('success', ucfirst($type) . ' record added successfully.');
    }
    header('Location: finance.php');
    exit;
}

// Delete record
if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    $id = (int)($_GET['id'] ?? 0);
    if ($id) {
        $pdo->prepare("DELETE FROM finance WHERE id=?")->execute([$id]);
        flash('success', 'Record deleted successfully.');
    }
    header('Location: finance.php');
    exit;
}

// Fetch records
$records = $pdo->query("SELECT * FROM finance ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);

// Calculate totals
$totalIncome = $pdo->query("SELECT SUM(amount) FROM finance WHERE type='income'")->fetchColumn() ?: 0;
$totalExpense = $pdo->query("SELECT SUM(amount) FROM finance WHERE type='expense'")->fetchColumn() ?: 0;
$balance = $totalIncome - $totalExpense;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Finance Management - Admin</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@500;700&display=swap" rel="stylesheet">
<style>body { font-family: 'Cairo', sans-serif; }</style>
</head>
<body class="bg-gray-50 min-h-screen">

<!-- Header -->
<header class="bg-gradient-to-r from-green-800 to-green-500 text-white shadow-md sticky top-0 z-50">
  <div class="container mx-auto flex justify-between items-center px-6 py-4">
    <h1 class="text-2xl font-bold">Finance Management</h1>
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
      <li class="mb-3"><a href="donations.php" class="hover:underline">Donations</a></li>
      <li class="mb-3"><a href="booking.php" class="hover:underline">Facility Booking</a></li>
      <li class="mb-3"><a href="finance.php" class="font-semibold underline">Finance</a></li>
      <li class="mb-3"><a href="announcements.php" class="hover:underline">Announcements</a></li>
      <li class="mb-3"><a href="members.php" class="hover:underline">Members</a></li>
    </ul>
  </aside>

  <!-- Main Content -->
  <main class="flex-1 p-6 md:p-10">

    <!-- Flash Message -->
    <?php if ($msg = flash('success')): ?>
      <div class="bg-green-100 text-green-700 p-4 rounded mb-6 shadow">
        <?php echo h($msg); ?>
      </div>
    <?php endif; ?>

    <!-- Summary Cards -->
    <div class="grid md:grid-cols-3 gap-4 mb-6">
      <div class="bg-green-100 p-4 rounded shadow text-center">
        <h2 class="text-lg font-semibold text-green-800">Total Income</h2>
        <p class="text-2xl font-bold">Rs. <?php echo number_format($totalIncome,2); ?></p>
      </div>
      <div class="bg-red-100 p-4 rounded shadow text-center">
        <h2 class="text-lg font-semibold text-red-800">Total Expense</h2>
        <p class="text-2xl font-bold">Rs. <?php echo number_format($totalExpense,2); ?></p>
      </div>
      <div class="bg-yellow-100 p-4 rounded shadow text-center">
        <h2 class="text-lg font-semibold text-yellow-800">Balance</h2>
        <p class="text-2xl font-bold">Rs. <?php echo number_format($balance,2); ?></p>
      </div>
    </div>

    <!-- Add New Finance Record Form -->
    <div class="bg-white p-6 rounded shadow mb-8">
      <h2 class="text-xl font-semibold mb-4 text-green-800">Add New Record</h2>
      <form method="post" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-gray-600 mb-1">Type</label>
          <select name="type" class="border rounded px-3 py-2 w-full">
            <option value="income">Income</option>
            <option value="expense">Expense</option>
          </select>
        </div>
        <div>
          <label class="block text-gray-600 mb-1">Category</label>
          <input name="category" placeholder="e.g. Donation, Electricity" class="border rounded px-3 py-2 w-full" required>
        </div>
        <div>
          <label class="block text-gray-600 mb-1">Amount (Rs.)</label>
          <input type="number" step="0.01" name="amount" class="border rounded px-3 py-2 w-full" required>
        </div>
        <div>
          <label class="block text-gray-600 mb-1">Description</label>
          <input name="description" placeholder="Optional note" class="border rounded px-3 py-2 w-full">
        </div>
        <div class="md:col-span-2 text-right">
          <button class="bg-green-700 text-white px-6 py-2 rounded hover:bg-green-600 transition">Add Record</button>
        </div>
      </form>
    </div>

    <!-- Finance Records Table -->
    <div class="bg-white rounded shadow overflow-x-auto">
      <table class="w-full table-auto">
        <thead class="bg-green-100">
          <tr>
            <th class="p-3 text-left">Type</th>
            <th class="p-3 text-left">Category</th>
            <th class="p-3 text-left">Amount</th>
            <th class="p-3 text-left">Description</th>
            <th class="p-3 text-left">Date</th>
            <th class="p-3 text-left">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($records): ?>
            <?php foreach ($records as $r): ?>
            <tr class="border-t hover:bg-gray-50">
              <td class="p-3 font-semibold <?php echo $r['type']=='income'?'text-green-600':'text-red-600'; ?>">
                <?php echo ucfirst($r['type']); ?>
              </td>
              <td class="p-3"><?php echo h($r['category']); ?></td>
              <td class="p-3">Rs. <?php echo number_format($r['amount'],2); ?></td>
              <td class="p-3"><?php echo h($r['description']); ?></td>
              <td class="p-3 text-gray-600 text-sm"><?php echo h($r['created_at']); ?></td>
              <td class="p-3 space-x-2">
                <a href="finance.php?action=delete&id=<?php echo $r['id']; ?>" class="text-red-600 hover:underline" onclick="return confirm('Delete this record?')">Delete</a>
              </td>
            </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="6" class="p-3 text-center text-gray-500">No records found.</td>
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
