<?php
if(!isset($_SESSION)) session_start();
$masjidName = $masjidName ?? 'Masjid';
?>
<header class="bg-gradient-to-r from-green-800 to-green-500 text-white shadow-md sticky top-0 z-50">
  <div class="container mx-auto flex justify-between items-center px-6 py-4">
    <h1 class="text-2xl font-bold"><?php echo h($masjidName); ?> - Member</h1>
    <nav class="space-x-6 hidden md:flex">
      <a href="index.php#home" class="hover:text-yellow-300">Home</a>
      <a href="prayers.php" class="hover:text-yellow-300">Prayers</a>
      <a href="events.php" class="hover:text-yellow-300">Events</a>
      <a href="donations.php" class="hover:text-yellow-300">Donations</a>
      <a href="profile.php" class="hover:text-yellow-300">Profile</a>
      <a href="../auth/logout.php" class="bg-red-500 px-4 py-2 rounded font-semibold hover:bg-red-600 transition">Logout</a>
    </nav>
  </div>
</header>
