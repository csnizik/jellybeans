<?php
/**
 * triage-db-and-drupal.php
 * Build stamp (UTC): 2025-08-11T14:25:00Z
 */

header('Content-Type: text/html; charset=utf-8');
if (($_SERVER['HTTP_HOST'] ?? '') !== 'localhost' && !isset($_GET['allow'])) {
  http_response_code(403);
  echo 'Access denied. Add ?allow=1 to URL if you need to test on non-localhost';
  exit;
}

function h($s){return htmlspecialchars((string)$s,ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8');}
function line($k,$v,$ok=null){$m=$ok===true?'✅':($ok===false?'❌':'•');echo "<p><strong>$m ".h($k).":</strong> ".h($v)."</p>";}
function error_block(Throwable $e,$ctx=''){echo "<pre style='color:#b00020;background:#fff3f3;padding:10px;border:1px solid #f1c0c0'>".h($ctx)."\nSQLSTATE: ".h($e->getCode())."\nMessage : ".h($e->getMessage())."\n".(($e instanceof PDOException && $e->errorInfo)?"errorInfo: ".h(json_encode($e->errorInfo))."\n":"")."Trace   :\n".h($e->getTraceAsString())."</pre>";}

echo "<h1>Drupal DB + bootstrap triage</h1>";
$build_stamp="2025-08-11T14:25:00Z";
$fingerprint=sha1_file(__FILE__)?:'n/a';
$mtime=gmdate('c',@filemtime(__FILE__)?:time());
line('Build stamp (UTC)',$build_stamp);
line('File mtime (UTC)',$mtime);
line('File SHA1',$fingerprint);

echo "<h2>Environment variables used for DB connection</h2>";
$env=['DB_HOST','DB_DATABASE','DB_USER','DB_PASSWORD','MYSQL_SSL_CA','MYSQL_SSL_CAPATH'];
foreach($env as $v){$raw=getenv($v);$val=($v==='DB_PASSWORD'&&$raw!==false)?'***hidden***':($raw!==false?$raw:'Not set');line($v,$val,$raw!==false);}

echo "<h2>Test 1: Direct PDO connection and minimal queries</h2>";
$host=getenv('DB_HOST')?:'127.0.0.1';
$db=getenv('DB_DATABASE')?:'';
$user=getenv('DB_USER')?:'';
$pass=getenv('DB_PASSWORD')?:'';
$ssl_ca=getenv('MYSQL_SSL_CA')?:'/etc/ssl/certs/ca-certificates.crt';
$ssl_capath=getenv('MYSQL_SSL_CAPATH')?:'/etc/ssl/certs';
line('Target host',$host);
line('Target database',$db?:'(empty)');
line('Target user',$user?:'(empty)');

$pdo=null;$ok1=false;
try{
  $dsn="mysql:host={$host};port=3306;dbname={$db};charset=utf8mb4";
  $opts=[\PDO::ATTR_ERRMODE=>\PDO::ERRMODE_EXCEPTION,\PDO::ATTR_DEFAULT_FETCH_MODE=>\PDO::FETCH_ASSOC,\PDO::MYSQL_ATTR_SSL_CA=>$ssl_ca,\PDO::MYSQL_ATTR_SSL_CAPATH=>$ssl_capath];
  $pdo=new \PDO($dsn,$user,$pass,$opts);

  $row=$pdo->query('SELECT 1 AS ok')->fetch(); $ok1=((int)($row['ok']??0)===1);
  line('SELECT 1',json_encode($row),$ok1);

  // FIX: avoid CURRENT_USER alias/keyword issues
  $row=$pdo->query('SELECT VERSION() AS server_version, USER() AS user_name, NOW() AS now_ts')->fetch();
  line('Server version',$row['server_version']??'n/a',!empty($row['server_version']));
  line('Current user',$row['user_name']??'n/a');
  line('Server time',$row['now_ts']??'n/a');

  $tc=$pdo->query('SELECT COUNT(*) AS c FROM information_schema.tables WHERE table_schema = DATABASE()')->fetch();
  line('Tables in schema',(string)($tc['c']??'n/a'),isset($tc['c']));

  try{
    $ssl=$pdo->query('SELECT @@session.ssl_cipher AS ssl_cipher')->fetch();
    $cipher=$ssl['ssl_cipher']??''; line('SSL/TLS',$cipher?("Enabled ($cipher)"):'Disabled/Unknown',$cipher!=='');
  }catch(Throwable $e){ line('SSL/TLS','Unknown (no permission to read @@session.ssl_cipher)'); }

}catch(Throwable $e){ line('PDO connection','FAILED',false); error_block($e,'Direct PDO connection or initial queries failed.'); }

echo "<h2>Test 1b: Does this schema look like Drupal?</h2>";
if($pdo){
  try{
    $must=['key_value','config','users_field_data'];
    $missing=[];
    foreach($must as $t){
      $stmt=$pdo->prepare('SELECT COUNT(*) AS c FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?');
      $stmt->execute([$t]); $ex=((int)$stmt->fetch()['c']===1);
      line("Table exists: $t",$ex?'yes':'no',$ex); if(!$ex){$missing[]=$t;}
    }
    line('Schema check',$missing?'Missing: '.implode(',',$missing):'Looks like Drupal',empty($missing));
  }catch(Throwable $e){ error_block($e,'Failed while checking required Drupal tables.'); }
}else{
  echo "<p>Skipping Drupal table checks (PDO failed).</p>";
}

echo "<h2>Test 2: Drupal kernel bootstrap & entity type sanity</h2>";
try{
  $autoloader=require_once __DIR__.'/autoload.php';
  $request=\Symfony\Component\HttpFoundation\Request::createFromGlobals();
  $kernel=\Drupal\Core\DrupalKernel::createFromRequest($request,$autoloader,'prod');
  $kernel->boot();
  echo "<p style='color:#0a7b0a'><strong>✅ Drupal kernel booted.</strong></p>";

  $database=\Drupal::database();
  $ok=$database->query('SELECT 1')->fetchField()==1;
  line('Drupal DB connection',$ok?'OK':'FAILED',$ok);

  // Version, install state, enabled count
  line('Drupal version', \Drupal::VERSION);
  $install_task=\Drupal::state()->get('install_task','(missing)');
  line('Install state (state:install_task)', $install_task, $install_task==='done');

  $etm=\Drupal::entityTypeManager();
  $has_evd=$etm->hasDefinition('entity_view_display');
  line('Entity type "entity_view_display" registered',$has_evd?'yes':'no',$has_evd);

  $mods=\Drupal::service('extension.list.module')->getList();
  $enabled=array_keys(array_filter($mods,fn($i)=>!empty($i->status)));
  line('Enabled modules (count)', (string)count($enabled), count($enabled)>0);
  echo "<details><summary>Enabled modules</summary><pre>".h(implode("\n",$enabled))."</pre></details>";

  // Core extension config present?
  $has_core_ext=(bool)$database->select('config','c')->fields('c',['name'])->condition('name','core.extension')->range(0,1)->execute()->fetchField();
  line('Active config: core.extension row', $has_core_ext?'present':'missing', $has_core_ext);

}catch(Throwable $e){
  echo "<p style='color:#c77700'><strong>⚠️ Drupal bootstrap failed.</strong></p>";
  error_block($e,'Drupal kernel/bootstrap error');
}

echo "<hr><p><small>Remove this file after testing. Version: 20250811 Build: $build_stamp · SHA1: $fingerprint</small></p>";
