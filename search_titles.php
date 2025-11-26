<?php
header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', 0);
require_once __DIR__.'/assets/db/db.php';

$q = trim(isset($_GET['q']) ? $_GET['q'] : (isset($_POST['q']) ? $_POST['q'] : ''));
if ($q === '') { echo json_encode(['items'=>[]]); exit; }

/* ---------- helpers (mbstring-safe) ---------- */
function tolower_utf8($s){ return function_exists('mb_strtolower') ? mb_strtolower($s,'UTF-8') : strtolower($s); }
function strlen_utf8($s){ return function_exists('mb_strlen') ? mb_strlen($s,'UTF-8') : strlen($s); }
function normalize($s){
  $s = tolower_utf8($s);
  $s = preg_replace('/[^a-z0-9\s]/u',' ', $s);
  $s = preg_replace('/\s+/u',' ', trim($s));
  return $s;
}
function jaccard_tokens($a,$b){
  $A = array_unique(array_filter(explode(' ', normalize($a))));
  $B = array_unique(array_filter(explode(' ', normalize($b))));
  if (!$A || !$B) return 0.0;
  $i = count(array_intersect($A,$B));
  $u = count(array_unique(array_merge($A,$B)));
  return $u ? ($i/$u) : 0.0;
}
function lev_ratio($a,$b){
  $la = strlen_utf8($a); $lb = strlen_utf8($b);
  if ($la===0 || $lb===0) return 0.0;
  $d = levenshtein(strtolower($a), strtolower($b));
  $m = max($la,$lb);
  return max(0.0, 1.0 - ($d / $m));
}
function score_similarity($needle,$hay){
  return (0.6*jaccard_tokens($needle,$hay) + 0.4*lev_ratio($needle,$hay));
}

/* ---------- build tokenized LIKE (AND) query ---------- */
$norm = normalize($q);
$rawTokens = array_filter(explode(' ', $norm));
$tokens = array();
foreach ($rawTokens as $t) {
  if (strlen($t) >= 3) $tokens[] = $t; // ignore super short noise
}
if (empty($tokens)) { $tokens = array($norm); } // fallback

$whereParts = array();
$params = array();
$types  = '';
for ($i=0; $i<count($tokens); $i++){
  $whereParts[] = 'title LIKE ?';
  $params[] = '%'.$tokens[$i].'%';
  $types .= 's';
}
$whereSql = implode(' AND ', $whereParts);
$sql = "SELECT titleid, title FROM tblresearches WHERE $whereSql LIMIT 200";

/* helper for older PHP to bind params dynamically */
function mysqli_stmt_bind_params_array($stmt, $types, $params){
  // Build array: first element is $types, rest are references to values
  $bind_names = array();
  $bind_names[] = $types;
  for ($i=0; $i<count($params); $i++){
    $bind_name = 'bind_' . $i;
    $$bind_name = $params[$i];
    $bind_names[] = &$$bind_name; // pass by reference
  }
  return call_user_func_array(array($stmt, 'bind_param'), $bind_names);
}

$items = array();

try {
  $stmt = $conn->prepare($sql);
  if ($stmt === false) { throw new Exception('Prepare failed'); }
  mysqli_stmt_bind_params_array($stmt, $types, $params);
  if ($stmt->execute()) {
    $res = $stmt->get_result();
    while($row = $res->fetch_assoc()){ $items[] = $row; }
  }
  $stmt->close();
} catch (Exception $e) {
  // fallback: single LIKE on whole query
  $like = '%'.$conn->real_escape_string($q).'%';
  $res = $conn->query("SELECT titleid, title FROM tblresearches WHERE title LIKE '$like' LIMIT 200");
  if ($res){
    while($r = $res->fetch_assoc()){ $items[] = $r; }
  }
}

/* ---------- re-rank and trim to top 7 ---------- */
for ($i=0; $i<count($items); $i++){
  $items[$i]['score'] = score_similarity($q, $items[$i]['title']);
}
usort($items, function($a,$b){
  if ($a['score'] == $b['score']) return 0;
  return ($a['score'] < $b['score']) ? 1 : -1; // desc
});
$items = array_slice($items, 0, 7);

echo json_encode(array('query'=>$q,'items'=>$items), JSON_UNESCAPED_UNICODE);
