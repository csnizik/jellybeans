<?php
/**
 * triage-db-and-drupal.php
 * Purpose: Definitively identify DB connectivity vs. Drupal/bootstrap/config issues.
 * Build stamp (UTC): 2025-08-11T13:00:00Z  // <-- update on deploy so you know it's new
 */

header('Content-Type: text/html; charset=utf-8');

// ---- Guard (keep prod locked unless allow=1) ----
if (($_SERVER['HTTP_HOST'] ?? '') !== 'localhost' && !isset($_GET['allow'])) {
  http_response_code(403);
  echo 'Access denied. Add ?allow=1 to URL if you need to test on non-localhost';
  exit;
}

// ---- Helpers ----
function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function section($t) { echo "<h2>".h($t)."</h2>\n"; }
function line($label, $value, $ok=null) {
  $status = $ok === true ? "✅" : ($ok === false ? "❌" : "•");
  echo "<p><strong>$status ".h($label).":</strong> ".h($value)."</p>\n";
}
function error_block(\Throwable $e, $context = '') {
  echo "<pre style='color:#b00020;background:#fff3f3;padding:10px;border:1px solid #f1c0c0;white-space:pre-wrap'>";
  if ($context) echo h($context)."\n";
  echo "SQLSTATE: ".h(method_exists($e,'getCode') ? $e->getCode() : '')."\n";
  echo "Message : ".h($e->getMessage())."\n";
  if ($e instanceof PDOException && $e->errorInfo) {
    echo "errorInfo: ".h(json_encode($e->errorInfo))."\n";
  }
  echo "Trace   :\n".h($e->getTraceAsString())."\n";
  echo "</pre>";
}

// ---- Header / Build fingerprint ----
echo "<h1>Drupal DB + bootstrap triage</h1>";
$build_stamp = "2025-08-11T13:00:00Z";                 // <-- bump this each deploy
$fingerprint = sha1_file(__FILE__) ?: 'n/a';
$mtime = gmdate('c', @filemtime(__FILE__) ?: time());
line('Build stamp (UTC)', $build_stamp);
line('File mtime (UTC)', $mtime);
line('File SHA1', $fingerprint);

// ---- Env snapshot (so we know what the app sees) ----
section('Environment variables used for DB connection');
$env_vars = ['DB_HOST','DB_DATABASE','DB_USER','DB_PASSWORD','MYSQL_SSL_CA','MYSQL_SSL_CAPATH'];
foreach ($env_vars as $v) {
  $raw = getenv($v);
  $val = ($v === 'DB_PASSWORD' && $raw !== false) ? '***hidden***' : ($raw !== false ? $raw : 'Not set');
  line($v, $val, $raw !== false);
}

// ---- Test 1: Raw PDO to MySQL (no false “success” until we actually run SELECT 1) ----
section('Test 1: Direct PDO connection and minimal queries');

$host = getenv('DB_HOST') ?: '127.0.0.1';
$db   = getenv('DB_DATABASE') ?: '';
$user = getenv('DB_USER') ?: '';
$pass = getenv('DB_PASSWORD') ?: '';

$ssl_ca     = getenv('MYSQL_SSL_CA')     ?: '/etc/ssl/certs/ca-certificates.crt';
$ssl_capath = getenv('MYSQL_SSL_CAPATH') ?: '/etc/ssl/certs';

line('Target host', $host);
line('Target database', $db ?: '(empty)');
line('Target user', $user ?: '(empty)');

$pdo = null;
try {
  $dsn = "mysql:host={$host};port=3306;dbname={$db};charset=utf8mb4";
  $options = [
    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
    // On PHP 8.2+ VERIFY_SERVER_CERT is ignored; CA/hostname verification happens automatically.
    \PDO::MYSQL_ATTR_SSL_CA     => $ssl_ca,
    \PDO::MYSQL_ATTR_SSL_CAPATH => $ssl_capath,
  ];

  $pdo = new \PDO($dsn, $user, $pass, $options);

  // 1a) Minimal “is alive”
  $v = $pdo->query('SELECT 1 AS ok')->fetch();
  line('SELECT 1', json_encode($v), isset($v['ok']) && (int)$v['ok'] === 1);

  // 1b) Server, user, time
  $row = $pdo->query('SELECT VERSION() AS version, CURRENT_USER() AS current_user, NOW() AS now_ts')->fetch();
  line('Server version', $row['version'] ?? 'n/a', !empty($row['version']));
  line('Current user', $row['current_user'] ?? 'n/a');
  line('Server time', $row['now_ts'] ?? 'n/a');

  // 1c) Active DB and table count
  $row = $pdo->query('SELECT DATABASE() AS db')->fetch();
  line('DATABASE()', $row['db'] ?? 'n/a', !empty($row['db']));
  $tc = $pdo->query('SELECT COUNT(*) AS c FROM information_schema.tables WHERE table_schema = DATABASE()')->fetch();
  line('Tables in schema', (string)($tc['c'] ?? 'n/a'), isset($tc['c']));

  // 1d) TLS cipher (no SHOW STATUS; use @@session.ssl_cipher to avoid privilege issues)
  try {
    $ssl = $pdo->query('SELECT @@session.ssl_cipher AS ssl_cipher')->fetch();
    $cipher = $ssl['ssl_cipher'] ?? '';
    line('SSL/TLS', $cipher ? "Enabled ($cipher)" : 'Disabled/Unknown', $cipher !== '');
  } catch (\Throwable $e) {
    line('SSL/TLS', 'Unknown (no permission to read @@session.ssl_cipher)', null);
  }

} catch (\Throwable $e) {
  line('PDO connection', 'FAILED', false);
  error_block($e, 'Direct PDO connection or initial queries failed.');
}

// ---- If PDO looked good, sanity-check that this is REALLY a Drupal DB ----
section('Test 1b: Does this schema look like Drupal?');
if ($pdo) {
  try {
    $must_have = ['key_value','config','users_field_data'];
    $missing = [];
    foreach ($must_have as $t) {
      $stmt = $pdo->prepare('SELECT COUNT(*) AS c FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?');
      $stmt->execute([$t]);
      $exists = (int)$stmt->fetch()['c'] === 1;
      line("Table exists: $t", $exists ? 'yes' : 'no', $exists);
      if (!$exists) $missing[] = $t;
    }
    if ($missing) {
      echo "<p style='color:#b00020'><strong>Conclusion:</strong> This database does not look like an installed Drupal site. Wrong DB name/creds, or the DB is empty.</p>";
    } else {
      line('Schema check', 'Looks like Drupal (key tables found)', true);
    }
  } catch (\Throwable $e) {
    error_block($e, 'Failed while checking required Drupal tables.');
  }
} else {
  echo "<p>Skipping Drupal table checks (PDO failed).</p>";
}

// ---- Test 2: Bootstrap Drupal kernel and ask Drupal what it thinks ----
section('Test 2: Drupal kernel bootstrap & entity type sanity');

try {
  // Composer autoload (from web root).
  $autoloader = require_once __DIR__ . '/autoload.php';
  $request = \Symfony\Component\HttpFoundation\Request::createFromGlobals();
  $kernel = \Drupal\Core\DrupalKernel::createFromRequest($request, $autoloader, 'prod');
  $kernel->boot();

  echo "<p style='color:#0a7b0a'><strong>✅ Drupal kernel booted.</strong></p>";

  // a) Is there a DB connection via Drupal?
  $database = \Drupal::database();
  $ok = $database->query('SELECT 1 AS ok')->fetchField() == 1;
  line('Drupal DB connection', $ok ? 'OK' : 'FAILED', $ok);

  // b) Do we have the entity_view_display entity type definition?
  $etm = \Drupal::entityTypeManager();
  $has_evd = $etm->hasDefinition('entity_view_display');
  line('Entity type "entity_view_display" registered', $has_evd ? 'yes' : 'no', $has_evd);

  // c) If missing, surface module list + config presence to explain why.
  if (!$has_evd) {
    // core.extension presence in config table?
    $has_core_ext = (bool) $database->select('config','c')
      ->fields('c',['name'])
      ->condition('name','core.extension')
      ->range(0,1)
      ->execute()
      ->fetchField();
    line('Active config: core.extension row', $has_core_ext ? 'present' : 'missing', $has_core_ext);
    $modules = \Drupal::service('extension.list.module')->getList();
    $enabled = array_keys(array_filter($modules, fn($i) => !empty($i->status)));
    echo "<details><summary>Enabled modules (via extension.list.module)</summary><pre>".h(implode("\n",$enabled))."</pre></details>";
    echo "<p style='color:#b00020'><strong>Conclusion:</strong> If Drupal booted but the entity type is missing, the site might be <em>not fully installed</em> or the active config is empty/wrong DB.</p>";
  }

  // d) Count tables via Drupal’s connection (should match PDO)
  $count_tables = $database->select('information_schema.tables','t')
    ->fields('t',['TABLE_NAME'])
    ->condition('t.TABLE_SCHEMA', $database->query('SELECT DATABASE()')->fetchField())
    ->countQuery()
    ->execute()
    ->fetchField();
  line('Tables in schema (via Drupal connection)', (string)$count_tables, $count_tables > 0);

} catch (\Throwable $e) {
  echo "<p style='color:#c77700'><strong>⚠️ Drupal bootstrap failed.</strong></p>";
  error_block($e, 'Drupal kernel/bootstrap error');
  echo "<p>If this failed while your PDO test succeeded, your DB looks OK but Drupal cannot bootstrap (often wrong DB <em>name</em> in settings.php, missing vendor, or config tables absent).</p>";
}

// ---- Final summary / verdicts ----
section('Verdicts');

$verdicts = [];
$verdicts[] = [
  'label' => 'Was PDO able to run SELECT 1?',
  'ok' => isset($v['ok']) && (int)$v['ok'] === 1,
];
$verdicts[] = [
  'label' => 'Did schema contain key Drupal tables (key_value, config, users_field_data)?',
  'ok' => isset($tc['c']) && !empty($pdo)
          ? null  // we already printed per-table; leave as neutral
          : false,
];
echo "<ul>";
foreach ($verdicts as $vd) {
  $icon = $vd['ok'] === true ? '✅' : ($vd['ok'] === false ? '❌' : '•');
  echo "<li>$icon ".h($vd['label'])."</li>";
}
echo "</ul>";

echo "<hr><p><small>Remove this file after testing. Build: {$build_stamp} · SHA1: {$fingerprint}</small></p>";
