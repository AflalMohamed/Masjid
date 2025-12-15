<?php
// register.php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

// If already logged in, redirect based on role
if (isset($_SESSION['user'])) {
    $role = $_SESSION['user']['role'] ?? 'member';
    header('Location: ' . ($role === 'admin' ? '/masjid/admin/index.php' : '/masjid/member/index.php'));
    exit;
}

$err = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';
    $role     = $_POST['role'] ?? 'member';

    // Basic validation
    if (!$fullname || !$username || !$email || !$password || !$confirm) {
        $err = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $err = "Invalid email address.";
    } elseif ($password !== $confirm) {
        $err = "Passwords do not match.";
    } else {
        // Check if username or email exists
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username=? OR email=?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetchColumn() > 0) {
            $err = "Username or Email already exists.";
        } else {
            // Insert user
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (fullname, username, email, password, role) VALUES (?,?,?,?,?)");
            if ($stmt->execute([$fullname, $username, $email, $hashed, $role])) {
                $success = "Registration successful! You can now <a href='login.php' class='text-green-700 underline'>login</a>.";
            } else {
                $err = "Database error, try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Register - Masjid Management</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex items-center justify-center bg-green-50">

<div class="max-w-md w-full bg-white p-8 rounded-lg shadow">
    <h2 class="text-2xl font-bold text-green-800 mb-4">Register</h2>

    <?php if ($err): ?>
        <div class="bg-red-100 text-red-700 p-2 rounded mb-4"><?php echo h($err); ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="bg-green-100 text-green-700 p-2 rounded mb-4"><?php echo $success; ?></div>
    <?php endif; ?>

    <form method="post" class="space-y-4">
        <input type="text" name="fullname" placeholder="Full Name" value="<?php echo h($_POST['fullname'] ?? ''); ?>" class="w-full border px-3 py-2 rounded" required>
        <input type="text" name="username" placeholder="Username" value="<?php echo h($_POST['username'] ?? ''); ?>" class="w-full border px-3 py-2 rounded" required>
        <input type="email" name="email" placeholder="Email" value="<?php echo h($_POST['email'] ?? ''); ?>" class="w-full border px-3 py-2 rounded" required>
        <input type="password" name="password" placeholder="Password" class="w-full border px-3 py-2 rounded" required>
        <input type="password" name="confirm_password" placeholder="Confirm Password" class="w-full border px-3 py-2 rounded" required>

          <!-- Top-left link to front page -->
  <div class="absolute top-6 left-6 flex flex-col gap-2">
    <a href="/masjid/index.php" class="bg-yellow-400 text-green-900 px-4 py-2 rounded font-semibold hover:bg-yellow-300 transition">
      Go to Front Page
    </a>
  </div>
        <!-- Role Selection -->
        <select name="role" class="w-full border px-3 py-2 rounded">
            <option value="member" <?php echo (($_POST['role'] ?? '') === 'member') ? 'selected' : ''; ?>>Member</option>
        </select>

        <button type="submit" class="w-full bg-green-700 hover:bg-green-800 text-white py-2 rounded transition">Register</button>
    </form>

    <p class="text-sm text-gray-500 mt-3 text-center">
        Already have an account? <a href="login.php" class="text-green-700 underline">Login here</a>
    </p>
</div>

</body>
</html>
