<?php
// ai_match_titles.php
header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', 0);

require_once __DIR__.'/assets/db/db.php'; // $conn

// ---- read user input ----
$input = json_decode(file_get_contents('php://input'), true);
$userTitle = trim($input['title'] ?? '');
if ($userTitle === '') { echo json_encode(['error'=>'missing_title']); exit; }

// ---- fetch titles from DB (id + title only) ----
$titles = [];
$sql = "SELECT titleid, title FROM tblresearches";
$res = $conn->query($sql);
while ($res && $row = $res->fetch_assoc()) {
  // optional truncation to save tokens
  $row['title'] = mb_substr($row['title'], 0, 220, 'UTF-8');
  $titles[] = $row;
}
if (empty($titles)) { echo json_encode(['error'=>'no_titles_in_db']); exit; }

// ---- build prompt for Gemini ----
$system = <<<SYS
You are SKSU Title Matcher.
RULES:
- Use ONLY the provided list of database titles.
- Find the 7 most semantically similar titles to the candidate.
- NEVER invent or add titles not in the provided list.
- Output ONE JSON object only (no markdown, no backticks) with:
  {
    "original_title": "...",
    "top_matches": [
      {"titleid":"...", "title":"...", "reason":"...", "similarity_note":"short text"}
    ]
  }
- top_matches must be <= 7, sorted best to worst.
- "reason" should be 1 short line (keywords/tech/domain overlap).
- If nothing is close, return an empty array.
SYS;

$userPart = "Candidate title: {$userTitle}\n\nHere is the ONLY list of titles from the database (JSON array):\n" . json_encode($titles, JSON_UNESCAPED_UNICODE);

// ---- call Gemini REST API (server-side key) ----
$API_KEY = getenv('GEMINI_API_KEY');



$url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key='.$API_KEY;

$payload = [
  'systemInstruction' => [
    'role' => 'system',
    'parts' => [['text' => $system]]
  ],
  'contents' => [[
    'role' => 'user',
    'parts' => [['text' => $userPart]]
  ]],
  'generationConfig' => [
    'temperature' => 0.1,
    'maxOutputTokens' => 800
  ]
];

$ch = curl_init($url);
curl_setopt_array($ch, [
  CURLOPT_POST => true,
  CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
  CURLOPT_POSTFIELDS => json_encode($payload),
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_TIMEOUT => 30
]);
$resp = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$err  = curl_error($ch);
curl_close($ch);

if ($resp === false || $code >= 400) {
  echo json_encode(['error'=>'gemini_error','http_code'=>$code,'detail'=>$err?:$resp]); exit;
}

// ---- extract plain text from Gemini response ----
$data = json_decode($resp, true);
$text = '';
if (!empty($data['candidates'][0]['content']['parts'][0]['text'])) {
  $text = $data['candidates'][0]['content']['parts'][0]['text'];
}

// Some models sometimes wrap JSON in ```; strip if present
$text = trim($text);
$text = preg_replace('/^```(?:json)?/i', '', $text);
$text = preg_replace('/```$/', '', $text);

// validate JSON
$out = json_decode($text, true);
if (json_last_error() !== JSON_ERROR_NONE) {
  // return raw for debugging
  echo json_encode(['error'=>'bad_json_from_model', 'raw'=>$text]); exit;
}

echo json_encode($out, JSON_UNESCAPED_UNICODE);
