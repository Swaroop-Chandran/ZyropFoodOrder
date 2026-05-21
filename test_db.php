<?php
// test_db.php — Database Diagnostic & Initialization Dashboard

session_start();

$host = '127.0.0.1';
$user = 'root';
$pass = '';
$db   = 'zyrop_food_order';
$charset = 'utf8mb4';

// 1. Handle Initialization / Reset Action
if (isset($_GET['action']) && $_GET['action'] === 'init') {
    header('Content-Type: application/json');
    try {
        // Connect without specifying database first
        $dsn = "mysql:host=$host;charset=$charset";
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);

        // Load SQL file
        $sqlFile = 'database.sql';
        if (!file_exists($sqlFile)) {
            echo json_encode(['success' => false, 'message' => 'database.sql schema file not found in current directory.']);
            exit();
        }

        $sql = file_get_contents($sqlFile);
        
        // Execute the database creation and structure
        $pdo->exec($sql);

        echo json_encode(['success' => true, 'message' => 'Database initialized successfully! All tables created. 🎉']);
    } catch (\Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Database initialization failed: ' . $e->getMessage()]);
    }
    exit();
}

// 2. Perform Diagnostics
$mysqlConnected = false;
$dbExists = false;
$tablesStatus = [
    'users' => ['exists' => false, 'count' => 0],
    'orders' => ['exists' => false, 'count' => 0],
    'order_items' => ['exists' => false, 'count' => 0]
];
$errorMessage = '';

try {
    // Check MySQL connection
    $dsnNoDb = "mysql:host=$host;charset=$charset";
    $pdoNoDb = new PDO($dsnNoDb, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 2 // brief timeout for diagnostics
    ]);
    $mysqlConnected = true;

    // Check database existence
    $stmt = $pdoNoDb->query("SHOW DATABASES LIKE '$db'");
    if ($stmt->fetch()) {
        $dbExists = true;

        // Connect to actual database and query tables
        $dsnDb = "mysql:host=$host;dbname=$db;charset=$charset";
        $pdoDb = new PDO($dsnDb, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);

        foreach (array_keys($tablesStatus) as $tableName) {
            try {
                $tableCheck = $pdoDb->query("SHOW TABLES LIKE '$tableName'")->fetch();
                if ($tableCheck) {
                    $tablesStatus[$tableName]['exists'] = true;
                    // Count records
                    $count = $pdoDb->query("SELECT COUNT(*) FROM `$tableName`")->fetchColumn();
                    $tablesStatus[$tableName]['count'] = $count;
                }
            } catch (\Exception $tableError) {
                // Ignore individual table query errors
            }
        }
    }
} catch (\PDOException $e) {
    $errorMessage = $e->getMessage();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Database Diagnostics — ZyropFoodOrder</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
  <script>
    tailwind.config = {
      darkMode: "class",
      theme: {
        extend: {
          colors: {
            "primary": "#a83300",
            "primary-container": "#d24200",
            "background": "#fbf9f8",
            "surface": "#ffffff",
            "outline-variant": "#e5beb2",
            "secondary": "#5f5e5e",
            "tertiary": "#006b29"
          }
        }
      }
    }
  </script>
  <style>
    body { font-family: 'Plus Jakarta Sans', sans-serif; }
  </style>
</head>
<body class="bg-background text-zinc-800 min-h-screen flex items-center justify-center p-6">
  <div class="max-w-2xl w-full bg-white rounded-3xl border border-outline-variant/30 shadow-xl overflow-hidden animate-scale-in">
    <!-- Header banner -->
    <div class="bg-gradient-to-br from-primary to-primary-container p-8 text-white relative">
      <div class="flex items-center gap-3">
        <span class="material-symbols-outlined text-4xl">database</span>
        <div>
          <h1 class="text-2xl font-extrabold">Zyrop DB Setup</h1>
          <p class="text-white/80 text-sm mt-0.5">Database Diagnostic and Control Panel</p>
        </div>
      </div>
    </div>

    <div class="p-8 space-y-6">
      <!-- Status Cards Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- MySQL Server Connection Status -->
        <div class="border rounded-2xl p-5 flex items-start gap-4 <?= $mysqlConnected ? 'bg-emerald-50 border-emerald-200' : 'bg-rose-50 border-rose-200' ?>">
          <span class="material-symbols-outlined text-3xl mt-0.5 <?= $mysqlConnected ? 'text-emerald-600' : 'text-rose-600' ?>">
            <?= $mysqlConnected ? 'check_circle' : 'error' ?>
          </span>
          <div>
            <h3 class="font-bold text-sm text-zinc-950">MySQL Server</h3>
            <p class="text-xs text-zinc-600 mt-0.5">127.0.0.1:3306 (root)</p>
            <span class="inline-block mt-2 text-[10px] font-bold px-2 py-0.5 rounded-full <?= $mysqlConnected ? 'bg-emerald-200/60 text-emerald-800' : 'bg-rose-200/60 text-rose-800' ?>">
              <?= $mysqlConnected ? 'Connected' : 'Disconnected' ?>
            </span>
          </div>
        </div>

        <!-- Database Existence Status -->
        <div class="border rounded-2xl p-5 flex items-start gap-4 <?= $dbExists ? 'bg-emerald-50 border-emerald-200' : 'bg-amber-50 border-amber-200' ?>">
          <span class="material-symbols-outlined text-3xl mt-0.5 <?= $dbExists ? 'text-emerald-600' : 'text-amber-600' ?>">
            <?= $dbExists ? 'layers' : 'layers_clear' ?>
          </span>
          <div>
            <h3 class="font-bold text-sm text-zinc-950">Schema Status</h3>
            <p class="text-xs text-zinc-600 mt-0.5">Database: <code class="font-mono bg-white/60 px-1 py-0.5 rounded"><?= htmlspecialchars($db) ?></code></p>
            <span class="inline-block mt-2 text-[10px] font-bold px-2 py-0.5 rounded-full <?= $dbExists ? 'bg-emerald-200/60 text-emerald-800' : 'bg-amber-200/60 text-amber-800' ?>">
              <?= $dbExists ? 'Database Found' : 'Missing Database' ?>
            </span>
          </div>
        </div>
      </div>

      <!-- Detail Info & Actions -->
      <?php if (!$mysqlConnected): ?>
        <div class="bg-rose-50 border border-rose-200 rounded-2xl p-5 text-rose-800 text-sm">
          <p class="font-bold flex items-center gap-2 mb-2">
            <span class="material-symbols-outlined text-xl">warning</span>
            Cannot connect to MySQL database server!
          </p>
          <p class="text-xs leading-relaxed opacity-90">
            Please make sure that **MySQL** is running in your XAMPP Control Panel.
          </p>
          <div class="mt-4 bg-white/40 p-3 rounded-lg border border-rose-200/60">
            <p class="font-mono text-xs whitespace-pre-wrap break-all"><?= htmlspecialchars($errorMessage) ?></p>
          </div>
        </div>
      <?php else: ?>
        <!-- Tables status -->
        <div class="border border-zinc-200 rounded-2xl p-6">
          <h3 class="font-extrabold text-sm text-zinc-950 mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-primary text-xl">table_chart</span>
            Database Table Diagnostics
          </h3>
          <div class="divide-y divide-zinc-100">
            <?php foreach ($tablesStatus as $tableName => $status): ?>
              <div class="py-3 flex items-center justify-between text-sm">
                <span class="font-mono font-bold text-zinc-700"><?= $tableName ?></span>
                <div class="flex items-center gap-3">
                  <?php if ($status['exists']): ?>
                    <span class="text-xs text-zinc-500 font-semibold"><?= $status['count'] ?> records</span>
                    <span class="material-symbols-outlined text-emerald-600 font-bold text-lg">check</span>
                  <?php else: ?>
                    <span class="text-xs text-amber-600 font-bold">Missing Table</span>
                    <span class="material-symbols-outlined text-amber-600 font-bold text-lg">warning</span>
                  <?php endif; ?>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- Action Button -->
        <div class="text-center pt-4">
          <?php if (!$dbExists || !($tablesStatus['users']['exists'] && $tablesStatus['orders']['exists'] && $tablesStatus['order_items']['exists'])): ?>
            <button onclick="initializeDatabase()" id="btn-init" class="bg-primary hover:bg-primary-container text-white font-bold py-3.5 px-8 rounded-2xl shadow-lg transition-all transform hover:-translate-y-0.5 inline-flex items-center gap-2">
              <span class="material-symbols-outlined">auto_fix_high</span>
              Initialize Database & Create Tables
            </button>
            <p class="text-xs text-secondary mt-3">This will run the SQL schema defined in <code class="font-mono">database.sql</code>.</p>
          <?php else: ?>
            <div class="flex flex-col items-center gap-3">
              <div class="bg-emerald-50 text-emerald-800 border border-emerald-200 text-sm font-semibold rounded-xl px-5 py-3 flex items-center gap-2">
                <span class="material-symbols-outlined">verified</span>
                Database is perfectly configured and ready for orders!
              </div>
              <div class="flex items-center gap-4 mt-2">
                <a href="index.php" class="bg-zinc-800 hover:bg-zinc-900 text-white font-bold py-2.5 px-6 rounded-xl transition-colors text-sm">
                  Go to Homepage
                </a>
                <button onclick="initializeDatabase(true)" id="btn-init" class="text-xs text-rose-600 hover:text-rose-800 font-bold hover:underline">
                  Re-initialize / Reset Database
                </button>
              </div>
            </div>
          <?php endif; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Toast notification system -->
  <div id="toast-container" class="fixed bottom-6 right-6 z-50 flex flex-col gap-2"></div>

  <script>
    function initializeDatabase(isReset = false) {
      if (isReset && !confirm('Are you sure you want to re-initialize? This will clear all existing orders, items, and users.')) {
        return;
      }
      
      const btn = document.getElementById('btn-init');
      const originalText = btn.innerHTML;
      btn.disabled = true;
      btn.innerHTML = `<span class="inline-block animate-spin rounded-full h-4 w-4 border-2 border-white border-t-transparent mr-2"></span> Initializing...`;

      fetch('test_db.php?action=init')
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            showToast(data.message, 'success');
            setTimeout(() => {
              location.reload();
            }, 1500);
          } else {
            showToast(data.message, 'error');
            btn.disabled = false;
            btn.innerHTML = originalText;
          }
        })
        .catch(err => {
          showToast('An unexpected error occurred.', 'error');
          btn.disabled = false;
          btn.innerHTML = originalText;
        });
    }

    function showToast(message, type = 'success') {
      const container = document.getElementById('toast-container');
      const toast = document.createElement('div');
      
      const colors = type === 'success' 
        ? 'bg-emerald-600 border-emerald-700 text-white' 
        : 'bg-rose-600 border-rose-700 text-white';
      
      const icon = type === 'success' ? 'check_circle' : 'error';

      toast.className = `${colors} border rounded-2xl shadow-xl px-5 py-3 flex items-center gap-3 transition-all duration-300 opacity-0 translate-y-2`;
      toast.innerHTML = `
        <span class="material-symbols-outlined text-xl">${icon}</span>
        <span class="text-sm font-semibold">${message}</span>
      `;
      
      container.appendChild(toast);
      
      // Animate in
      setTimeout(() => {
        toast.classList.remove('opacity-0', 'translate-y-2');
      }, 50);

      // Animate out and remove
      setTimeout(() => {
        toast.classList.add('opacity-0', 'translate-y-2');
        setTimeout(() => toast.remove(), 300);
      }, 3500);
    }
  </script>
</body>
</html>
