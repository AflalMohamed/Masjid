<?php
// admin/prayers_edit.php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_login('admin');

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM prayer_times WHERE id=?");
$stmt->execute([$id]);
$item = $stmt->fetch();

if (!$item) {
    echo "Prayer record not found.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fajr = $_POST['fajr'] ?: null;
    $dhuhr = $_POST['dhuhr'] ?: null;
    $asr = $_POST['asr'] ?: null;
    $maghrib = $_POST['maghrib'] ?: null;
    $isha = $_POST['isha'] ?: null;
    $jummah = $_POST['jummah'] ?: null;
    $note = $_POST['note'] ?: null;

    $stmt = $pdo->prepare("UPDATE prayer_times SET fajr=?, dhuhr=?, asr=?, maghrib=?, isha=?, jummah=?, note=? WHERE id=?");
    $stmt->execute([$fajr, $dhuhr, $asr, $maghrib, $isha, $jummah, $note, $id]);

    flash('success', 'Prayer record updated successfully.');
    header('Location: prayers.php');
    exit;
}
?>

<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Edit Prayer Time</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
  <div class="max-w-4xl mx-auto p-6">
    <h1 class="text-2xl font-bold text-green-800 mb-4">Edit Prayer Time - <?php echo h($item['date']); ?></h1>
    <div class="bg-white p-6 rounded shadow">
      <form method="post" class="grid md:grid-cols-3 gap-4">
        <div>
          <label class="block text-gray-600">Fajr</label>
          <input type="time" name="fajr" value="<?php echo h($item['fajr']); ?>" class="border rounded px-3 py-2 w-full">
        </div>
        <div>
          <label class="block text-gray-600">Dhuhr</label>
          <input type="time" name="dhuhr" value="<?php echo h($item['dhuhr']); ?>" class="border rounded px-3 py-2 w-full">
        </div>
        <div>
          <label class="block text-gray-600">Asr</label>
          <input type="time" name="asr" value="<?php echo h($item['asr']); ?>" class="border rounded px-3 py-2 w-full">
        </div>
        <div>
          <label class="block text-gray-600">Maghrib</label>
          <input type="time" name="maghrib" value="<?php echo h($item['maghrib']); ?>" class="border rounded px-3 py-2 w-full">
        </div>
        <div>
          <label class="block text-gray-600">Isha</label>
          <input type="time" name="isha" value="<?php echo h($item['isha']); ?>" class="border rounded px-3 py-2 w-full">
        </div>
        <div>
          <label class="block text-gray-600">Jummah</label>
          <input type="time" name="jummah" value="<?php echo h($item['jummah']); ?>" class="border rounded px-3 py-2 w-full">
        </div>
        <div class="md:col-span-3">
          <label class="block text-gray-600">Note</label>
          <input type="text" name="note" value="<?php echo h($item['note']); ?>" class="border rounded px-3 py-2 w-full">
        </div>
        <div class="md:col-span-3 text-right">
          <button class="bg-green-700 text-white px-4 py-2 rounded">Save Changes</button>
          <a href="prayers.php" class="ml-3 bg-gray-300 px-4 py-2 rounded">Cancel</a>
        </div>
      </form>
    </div>
  </div>
</body>
</html>
