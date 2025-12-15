<?php
// admin/members.php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_login('admin');

// Ensure upload folder exists
$uploadDir = __DIR__ . '/../uploads/members';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

// Delete member
if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    $id = (int)($_GET['id'] ?? 0);
    if ($id) {
        $pdo->prepare("DELETE FROM members WHERE id=?")->execute([$id]);
        flash('success', 'Member deleted successfully.');
    }
    header('Location: members.php');
    exit;
}

// Add member
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['fullname'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $address = $_POST['address'] ?? '';
    $role = $_POST['role'] ?? 'member';

    $photo = null;
    if (!empty($_FILES['photo']['name'])) {
        $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $filename = time() . '_' . rand(1000,9999) . '.' . $ext;
        $target = $uploadDir . '/' . $filename;
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $target)) {
            $photo = $filename;
        }
    }

    // Create user
    $password = password_hash('123456', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (username, password, fullname, email, phone, role) VALUES (?,?,?,?,?,?)");
    $stmt->execute([$email, $password, $name, $email, $phone, $role]);
    $userId = $pdo->lastInsertId();

    // Create member record
    $stmt = $pdo->prepare("INSERT INTO members (user_id, address, photo) VALUES (?,?,?)");
    $stmt->execute([$userId, $address, $photo]);

    flash('success', 'Member added successfully.');
    header('Location: members.php');
    exit;
}

// Fetch members
$rows = $pdo->query("SELECT m.*, u.fullname, u.email, u.phone FROM members m LEFT JOIN users u ON m.user_id=u.id ORDER BY u.fullname ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Members Management - Admin</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@500;700&display=swap" rel="stylesheet">
<style>body { font-family: 'Cairo', sans-serif; }</style>
</head>
<body class="bg-gray-50 min-h-screen">

<!-- Header -->
<header class="bg-gradient-to-r from-green-800 to-green-500 text-white shadow-md sticky top-0 z-50">
  <div class="container mx-auto flex justify-between items-center px-6 py-4">
    <h1 class="text-2xl font-bold">Members Management</h1>
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
      <li class="mb-3"><a href="announcements.php" class="hover:underline">Announcements</a></li>
      <li class="mb-3"><a href="members.php" class="font-semibold underline">Members</a></li>
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

    <!-- Add Member Form -->
    <div class="bg-white p-6 rounded shadow mb-8">
      <h2 class="text-xl font-semibold mb-4 text-green-800">Add New Member</h2>
      <form method="post" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <input name="fullname" placeholder="Full Name" class="border rounded px-3 py-2 w-full" required>
        <input name="email" type="email" placeholder="Email (used as username)" class="border rounded px-3 py-2 w-full" required>
        <input name="phone" placeholder="Phone" class="border rounded px-3 py-2 w-full">
        <input name="address" placeholder="Address" class="border rounded px-3 py-2 w-full">
        <div>
          <label class="block text-gray-600">Profile Photo</label>
          <input type="file" name="photo" accept="image/*" class="w-full">
        </div>
        <div>
          <label class="block text-gray-600">Role</label>
          <select name="role" class="border rounded px-3 py-2 w-full">
            <option value="member">Member</option>
            <option value="admin">Admin</option>
          </select>
        </div>
        <div class="md:col-span-2 text-right">
          <button class="bg-green-700 text-white px-6 py-2 rounded hover:bg-green-600 transition">Add Member</button>
        </div>
      </form>
      <p class="text-gray-500 text-sm mt-2">Default password for new members: <b>123456</b></p>
    </div>

    <!-- Members Table -->
    <div class="bg-white rounded shadow overflow-x-auto">
      <table class="w-full table-auto">
        <thead class="bg-green-100">
          <tr>
            <th class="p-3 text-left">Photo</th>
            <th class="p-3 text-left">Name</th>
            <th class="p-3 text-left">Email</th>
            <th class="p-3 text-left">Phone</th>
            <th class="p-3 text-left">Address</th>
            <th class="p-3 text-left">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($rows): ?>
            <?php foreach ($rows as $r): ?>
            <tr class="border-t hover:bg-gray-50">
              <td class="p-3">
                <?php if ($r['photo']): ?>
                  <img src="../uploads/members/<?php echo h($r['photo']); ?>" class="w-10 h-10 rounded-full object-cover">
                <?php else: ?>
                  <div class="w-10 h-10 rounded-full bg-gray-300 flex items-center justify-center text-gray-600">–</div>
                <?php endif; ?>
              </td>
              <td class="p-3 font-semibold"><?php echo h($r['fullname']); ?></td>
              <td class="p-3"><?php echo h($r['email']); ?></td>
              <td class="p-3"><?php echo h($r['phone']); ?></td>
              <td class="p-3"><?php echo h($r['address']); ?></td>
              <td class="p-3 space-x-2">
                <a href="members_edit.php?id=<?php echo $r['id']; ?>" class="text-blue-600 hover:underline">Edit</a>
                <a href="members.php?action=delete&id=<?php echo $r['id']; ?>" class="text-red-600 hover:underline" onclick="return confirm('Delete this member?')">Delete</a>
              </td>
            </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="6" class="p-3 text-center text-gray-500">No members found.</td>
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
