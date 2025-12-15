<?php
// index.php — Al-Aqsa Grand Jummah Masjid Management System
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

// Fetch site settings dynamically
$settings = $pdo->query("SELECT `key`,`value` FROM site_settings")->fetchAll(PDO::FETCH_KEY_PAIR);
$masjidName     = $settings['masjid_name'] ?? 'Al-Aqsa Grand Jummah Masjid';
$masjidEmail    = $settings['masjid_email'] ?? 'info@masjid.com';
$masjidPhone    = $settings['masjid_phone'] ?? '+94 71 123 4567';
$masjidAddress  = $settings['masjid_address'] ?? 'Batticaloa, Sri Lanka';
$whatsappNumber = $settings['whatsapp_number'] ?? '+94711234567';

// Fetch About Section dynamically from DB
$about = $pdo->query("SELECT * FROM site_about ORDER BY id DESC LIMIT 1")->fetch();

// Fetch Features dynamically from DB
$features = $pdo->query("SELECT * FROM features ORDER BY id ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title><?php echo htmlspecialchars($masjidName); ?> | Management System</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@500;700&display=swap" rel="stylesheet" />
<style>
  body { font-family: 'Cairo', sans-serif; scroll-behavior: smooth; }

  /* Hero Section Background */
  #home {
    background: url('https://upload.wikimedia.org/wikipedia/commons/9/9a/Dome_of_the_Rock_from_Mount_of_Olives2.jpg') center center / cover no-repeat;
    position: relative;
  }

  /* Overlay to make text readable */
  #home::before {
    content: "";
    position: absolute;
    inset: 0;
    background: rgba(0,0,0,0.5);
  }
</style>
</head>
<body class="bg-gray-50 text-gray-800">

<!-- Header -->
<header class="bg-gradient-to-r from-green-800 to-green-500 text-white shadow-md sticky top-0 z-50">
  <div class="container mx-auto flex justify-between items-center px-6 py-4">
    <h1 class="text-2xl font-bold"><?php echo htmlspecialchars($masjidName); ?></h1>
    <nav class="space-x-6 hidden md:flex">
      <a href="#home" class="hover:text-yellow-300">Home</a>
      <a href="#about" class="hover:text-yellow-300">About</a>
      <a href="#features" class="hover:text-yellow-300">Features</a>
      <a href="#contact" class="hover:text-yellow-300">Contact</a>
      <a href="auth/login.php" class="bg-yellow-400 text-green-900 px-4 py-2 rounded-lg font-semibold hover:bg-yellow-300 transition">Login</a>
    </nav>
  </div>
</header>

<!-- Hero Section -->
<section id="home" class="h-screen flex flex-col justify-center items-center text-center text-white relative">
  <div class="relative z-10 px-4">
    <h2 class="text-4xl md:text-6xl font-bold mb-4 drop-shadow-lg">Welcome to <?php echo htmlspecialchars($masjidName); ?></h2>
    <p class="text-lg md:text-2xl mb-6 max-w-3xl mx-auto drop-shadow-md">A modern system to manage prayer schedules, donations, and community events efficiently.</p>
    <div class="flex flex-col md:flex-row gap-4 justify-center">
      <button onclick="window.location.href='auth/login.php?role=admin'" class="bg-yellow-400 text-green-900 px-6 py-3 rounded-full font-semibold hover:bg-yellow-300 transition shadow-lg">Login as Admin</button>
      <button onclick="window.location.href='auth/login.php?role=member'" class="bg-green-700 text-white px-6 py-3 rounded-full font-semibold hover:bg-green-600 transition shadow-lg">Login as Member</button>
    </div>
  </div>
</section>

<!-- About Section -->
<section id="about" class="py-20 bg-white">
  <div class="container mx-auto px-6 text-center md:w-3/4">
    <h3 class="text-3xl font-bold text-green-800 mb-6"><?php echo h($about['title'] ?? 'About the System'); ?></h3>
    <p class="text-gray-600 leading-relaxed text-lg">
      <?php echo h($about['description'] ?? "The $masjidName Management System is a digital platform designed to simplify mosque operations — from prayer schedule updates to donation management, event coordination, and community announcements."); ?>
    </p>
  </div>
</section>

<!-- Features Section -->
<section id="features" class="py-20 bg-green-50">
  <div class="container mx-auto px-6">
    <h3 class="text-3xl font-bold text-center text-green-800 mb-12">Key Features</h3>
    <div class="grid md:grid-cols-3 gap-8">
      <?php if(!empty($features)): ?>
        <?php foreach ($features as $f): ?>
          <div class="bg-white rounded-xl shadow-lg p-6 text-center hover:-translate-y-2 transition transform">
            <?php if($f['icon_url']): ?>
              <img src="<?php echo h($f['icon_url']); ?>" class="w-16 h-16 mx-auto mb-4" alt="">
            <?php endif; ?>
            <h4 class="text-xl font-semibold mb-2"><?php echo h($f['title']); ?></h4>
            <p class="text-gray-600 text-sm"><?php echo h($f['description']); ?></p>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p class="text-gray-600 text-center col-span-3">No features added yet by admin.</p>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- Contact Section -->
<section id="contact" class="py-20 bg-white">
  <div class="container mx-auto px-6 text-center">
    <h3 class="text-3xl font-bold text-green-800 mb-6">Contact Us</h3>
    <p class="text-gray-600 mb-4"><?php echo h($masjidAddress); ?></p>
    <p class="text-gray-600 mb-4">Email: <?php echo h($masjidEmail); ?> | Phone: <?php echo h($masjidPhone); ?></p>
    <a href="https://wa.me/<?php echo h($whatsappNumber); ?>" target="_blank" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-full inline-block mt-2 shadow-lg">Chat on WhatsApp</a>
  </div>
</section>

<!-- Footer -->
<footer class="bg-green-900 text-gray-200 py-6 text-center">
  <p>&copy; <?php echo date("Y"); ?> <?php echo htmlspecialchars($masjidName); ?>. All Rights Reserved.</p>
  <p class="text-sm mt-1">Developed by Group 08 | HNDIT – ATI Batticaloa</p>
</footer>

</body>
</html>
