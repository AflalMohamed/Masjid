<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_login('admin');

$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $masjidName = $_POST['masjid_name'];
    $masjidEmail = $_POST['masjid_email'];
    $masjidPhone = $_POST['masjid_phone'];
    $masjidAddress = $_POST['masjid_address'];
    $whatsappNumber = $_POST['whatsapp_number'];

    $fields = [
        'masjid_name' => $masjidName,
        'masjid_email' => $masjidEmail,
        'masjid_phone' => $masjidPhone,
        'masjid_address' => $masjidAddress,
        'whatsapp_number' => $whatsappNumber
    ];

    $stmt = $pdo->prepare("INSERT INTO site_settings (`key`, `value`) VALUES (:key, :value) 
        ON DUPLICATE KEY UPDATE `value` = :value");

    foreach ($fields as $key => $val) {
        $stmt->execute(['key'=>$key,'value'=>$val]);
    }

    $msg = "Contact details updated successfully!";
}

// Fetch existing values
$settings = $pdo->query("SELECT `key`,`value` FROM site_settings")->fetchAll(PDO::FETCH_KEY_PAIR);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Contact Info - Admin</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">

<div class="max-w-lg mx-auto mt-10">

  <!-- Header & Back Button -->
  <div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold text-green-800">Manage Contact Info</h1>
    <a href="index.php" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded flex items-center">
      &#8592; Back
    </a>
  </div>

  <!-- Flash Message -->
  <?php if($msg): ?>
    <div class="bg-green-100 text-green-700 p-3 rounded mb-6 shadow"><?php echo h($msg); ?></div>
  <?php endif; ?>

  <!-- Form -->
  <div class="bg-white p-6 rounded shadow">
    <form method="post" class="space-y-4">
        <div>
            <label class="block text-gray-700 font-semibold mb-1">Masjid Name</label>
            <input type="text" name="masjid_name" value="<?php echo h($settings['masjid_name'] ?? ''); ?>" class="w-full border p-2 rounded">
        </div>
        <div>
            <label class="block text-gray-700 font-semibold mb-1">Email</label>
            <input type="email" name="masjid_email" value="<?php echo h($settings['masjid_email'] ?? ''); ?>" class="w-full border p-2 rounded">
        </div>
        <div>
            <label class="block text-gray-700 font-semibold mb-1">Phone</label>
            <input type="text" name="masjid_phone" value="<?php echo h($settings['masjid_phone'] ?? ''); ?>" class="w-full border p-2 rounded">
        </div>
        <div>
            <label class="block text-gray-700 font-semibold mb-1">Address</label>
            <input type="text" name="masjid_address" value="<?php echo h($settings['masjid_address'] ?? ''); ?>" class="w-full border p-2 rounded">
        </div>
        <div>
            <label class="block text-gray-700 font-semibold mb-1">WhatsApp Number</label>
            <input type="text" name="whatsapp_number" value="<?php echo h($settings['whatsapp_number'] ?? ''); ?>" class="w-full border p-2 rounded">
        </div>
        <div class="text-right">
            <button type="submit" class="bg-green-700 text-white px-6 py-2 rounded hover:bg-green-800 transition">Save Changes</button>
        </div>
    </form>
  </div>

</div>

</body>
</html>
