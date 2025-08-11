<?php
/**
 * triage-db-and-drupal.php
 * Build stamp (UTC): 2025-08-11T21:00:00Z
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
$build_stamp="2025-08-11T21:00:00Z";
$fingerprint=sha1_file(__FILE__)?:'n/a';
$mtime=gmdate('c',@filemtime(__FILE__)?:time());
line('Build stamp (UTC)',$build_stamp);
line('File mtime (UTC)',$mtime);
line('File SHA1',$fingerprint);

echo "<h2>Test 2: Drupal kernel bootstrap & entity type sanity</h2>";
try{
  $autoloader=require_once __DIR__.'/web/autoload.php';
  $request=\Symfony\Component\HttpFoundation\Request::createFromGlobals();
  $kernel=\Drupal\Core\DrupalKernel::createFromRequest($request,$autoloader,'prod');
  $kernel->boot();
  echo "<p style='color:#0a7b0a'><strong>✅ Drupal kernel booted.</strong></p>";

  // Optional: trigger cache/container rebuild by URL param (?do=cr)
  if (isset($_GET['do']) && $_GET['do']==='cr') {
    // Ensure procedural helper is available (contains drupal_flush_all_caches()).
    if (!function_exists('drupal_flush_all_caches')) {
      require_once __DIR__ . '/web/core/includes/common.inc';
    }
    // Invalidate & rebuild the container, router, and caches (Drush CR equivalent).
    \Drupal::service('kernel')->invalidateContainer();
    drupal_flush_all_caches();
    echo "<p><strong>🧹 Performed container & cache rebuild (via web).</strong></p>";
  }

  // Report the autoload and entity definitions, so we can see the fix in effect.
  $exists = class_exists(\Drupal\Core\Entity\Display\EntityViewDisplay::class);
  line('class_exists(EntityViewDisplay)', $exists ? 'true' : 'false', $exists);

  $etm=\Drupal::entityTypeManager();
  $has_evd=$etm->hasDefinition('entity_view_display');
  $has_efd=$etm->hasDefinition('entity_form_display');
  line('hasDefinition(entity_view_display)', (string)(int)$has_evd, $has_evd);
  line('hasDefinition(entity_form_display)', (string)(int)$has_efd, $has_efd);

  // Also show install state and enabled modules count to rule out “not installed”.
  line('Install state (state:install_task)', \Drupal::state()->get('install_task','(missing)'));
  $mods=\Drupal::service('extension.list.module')->getList();
  $enabled=array_keys(array_filter($mods,fn($i)=>!empty($i->status)));
  line('Enabled modules (count)', (string)count($enabled), count($enabled)>0);

}catch(Throwable $e){
  echo "<p style='color:#c77700'><strong>⚠️ Drupal bootstrap failed.</strong></p>";
  error_block($e,'Drupal kernel/bootstrap error');
}

echo "<hr><p><small>Remove this file after testing. Version: 202508111604 Build: '.$build_stamp.' · SHA1: '.$fingerprint.'</small></p>";
