<?php
session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

// Ensure only member can access
require_login('member');

$userId = $_SESSION['user']['id'] ?? null;
$msg = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $userId) {
    $fullname = trim($_POST['fullname'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($fullname) {
        $stmt = $pdo->prepare("UPDATE users SET fullname = ? WHERE id = ?");
        $stmt->execute([$fullname, $userId]);
        $_SESSION['user']['fullname'] = $fullname;
        $msg = "Profile updated successfully.";
    }

    if ($password) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$hash, $userId]);
        $msg = $msg ? $msg . " Password updated successfully." : "Password updated successfully.";
    }
}

// Fetch current user info
$user = [];
if ($userId) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Profile - Member</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@500;700&display=swap" rel="stylesheet">
<style>body { font-family: 'Cairo', sans-serif; }</style>
</head>
<body class="bg-gray-50 min-h-screen">

<!-- Include Member Header -->
<?php require_once __DIR__ . '/member_header.php'; ?>

<main class="flex-1 p-6 md:p-10">
  <h1 class="text-3xl font-bold text-green-800 mb-6">My Profile</h1>

  <?php if ($msg): ?>
    <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
      <?php echo h($msg); ?>
    </div>
  <?php endif; ?>

  <form method="post" class="bg-white p-6 rounded shadow max-w-md space-y-4">
    <label class="block">
      <span class="text-gray-700">Full Name</span>
      <input type="text" name="fullname" class="w-full border rounded px-3 py-2 mt-1" value="<?php echo h($user['fullname'] ?? ''); ?>" required placeholder="Full Name">
    </label>
    <label class="block">
      <span class="text-gray-700">New Password</span>
      <input type="password" name="password" class="w-full border rounded px-3 py-2 mt-1" placeholder="Leave blank to keep current password">
    </label>
    <button type="submit" class="bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded transition">
      Update Profile
    </button>
  </form>
</main>

</body>
</html>
