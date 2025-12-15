<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$SITE_NAME = $SITE_NAME ?? 'Masjid Management';

// If already logged in, redirect based on role
if (isset($_SESSION['user'])) {
    $role = $_SESSION['user']['role'] ?? 'member';
    header('Location: ' . ($role === 'admin' ? '/masjid/admin/index.php' : '/masjid/member/index.php'));
    exit;
}

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $err = "Please fill username & password.";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                // Set session
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'role' => $user['role'],
                    'fullname' => $user['fullname'],
                ];

                // Redirect based on role
                if ($user['role'] === 'admin') {
                    header('Location: /masjid/admin/index.php');
                } else {
                    header('Location: /masjid/member/index.php');
                }
                exit;
            } else {
                $err = "Invalid username or password.";
            }
        } catch (PDOException $e) {
            $err = "Database error: " . $e->getMessage();
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Login - <?php echo h($SITE_NAME); ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex items-center justify-center bg-green-50 relative">

  <!-- Top-left link to front page -->
  <div class="absolute top-6 left-6 flex flex-col gap-2">
    <a href="/masjid/index.php" class="bg-yellow-400 text-green-900 px-4 py-2 rounded font-semibold hover:bg-yellow-300 transition">
      Go to Front Page
    </a>
  </div>

  <!-- Login Form -->
  <div class="max-w-md w-full bg-white p-8 rounded-lg shadow z-10">
    <h2 class="text-2xl font-bold text-green-800 mb-4">Sign In</h2>

    <?php if ($err): ?>
      <div class="bg-red-100 text-red-700 p-2 rounded mb-4"><?php echo h($err); ?></div>
    <?php endif; ?>

    <form method="post" class="space-y-4">
      <input name="username" class="w-full border rounded px-3 py-2" placeholder="Username" required />
      <input name="password" type="password" class="w-full border rounded px-3 py-2" placeholder="Password" required />
      <button type="submit" class="w-full bg-green-700 hover:bg-green-800 text-white py-2 rounded transition">Login</button>
    </form>

    <p class="text-sm text-gray-500 mt-4 text-center">
      Don't have an account? <a href="/masjid/auth/register.php" class="text-green-700 underline">Register here</a>.
    </p>
  </div>

</body>
</html>
