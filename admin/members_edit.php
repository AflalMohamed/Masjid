<?php
// admin/members_edit.php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_login('admin');

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT m.*, u.fullname, u.email, u.phone FROM members m LEFT JOIN users u ON m.user_id=u.id WHERE m.id=?");
$stmt->execute([$id]);
$m = $stmt->fetch();

if (!$m) { echo "Member not found."; exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['fullname'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $address = $_POST['address'] ?? '';

    $photo = $m['photo'];
    if (!empty($_FILES['photo']['name'])) {
        $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $filename = time() . '_' . rand(1000,9999) . '.' . $ext;
        $target = __DIR__ . '/../uploads/members/' . $filename;
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $target)) {
            $photo = $filename;
        }
    }

    $pdo->prepare("UPDATE users SET fullname=?, email=?, phone=? WHERE id=?")->execute([$name, $email, $phone, $m['user_id']]);
    $pdo->prepare("UPDATE members SET address=?, photo=? WHERE id=?")->execute([$address, $photo, $id]);

    flash('success', 'Member updated successfully.');
    header('Location: members.php');
    exit;
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Edit Member</title><script src="https://cdn.tailwindcss.com"></script></head>
<body class="bg-gray-100">
  <div class="max-w-4xl mx-auto p-6">
    <h1 class="text-2xl font-bold text-green-800 mb-4">Edit Member</h1>
    <div class="bg-white p-6 rounded shadow">
      <form method="post" enctype="multipart/form-data" class="grid md:grid-cols-2 gap-4">
        <input name="fullname" value="<?php echo h($m['fullname']); ?>" class="border rounded px-3 py-2 w-full" required>
        <input name="email" value="<?php echo h($m['email']); ?>" class="border rounded px-3 py-2 w-full">
        <input name="phone" value="<?php echo h($m['phone']); ?>" class="border rounded px-3 py-2 w-full">
        <input name="address" value="<?php echo h($m['address']); ?>" class="border rounded px-3 py-2 w-full">
        <div>
          <label class="block text-gray-600">New Photo (optional)</label>
          <input type="file" name="photo" accept="image/*">
        </div>
        <div class="md:col-span-2 text-right">
          <button class="bg-green-700 text-white px-4 py-2 rounded">Save Changes</button>
          <a href="members.php" class="ml-3 bg-gray-300 px-4 py-2 rounded">Cancel</a>
        </div>
      </form>
    </div>
  </div>
</body>
</html>
