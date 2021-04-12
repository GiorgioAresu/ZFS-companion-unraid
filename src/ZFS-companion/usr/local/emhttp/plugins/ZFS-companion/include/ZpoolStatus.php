<?php
function removeDuplicateSpaces($input) {
  return preg_replace('/\s+/', ' ', trim($input));
};

function parseScan($input) {
  $regex = "/(?'status'canceled on|in progress since|\d+ errors on) (?'time'.*)/";
  preg_match_all($regex, removeDuplicateSpaces($input), $matches);
  return array([
    'status' => ($matches['status'] ?: ['Unknown'])[0],
    'time' => $matches['time'],
  ]);
};

function parseConfig($input) {
  return $input;
  // preg_match_all($regex, trim($output), $matches, PREG_SPLIT_NO_EMPTY | PREG_SET_ORDER);
  // print_r($matches);
};

function cleanup($matched) {
  global $removeDuplicateSpaces, $parseConfig, $parseScan;

  $result = array(
    'pool' => $matched['pool'],
    'state' => $matched['state'],
    'status' => removeDuplicateSpaces($matched['status']),
    'action' => removeDuplicateSpaces($matched['action']),
    'scan' => removeDuplicateSpaces($matched['scan']),
    'scanParsed' => parseScan($matched['scan']),
    'config' => parseConfig($matched['config']),
    'errors' => removeDuplicateSpaces($matched['errors'])
  );
  return $result;
}

function getPoolsStatus($pool = '') {
  $regex = "/(?(DEFINE)(?'value'(?:.*(?! *[a-z]+: ))))\s*pool: (?'pool'\g'value')\n\s*state: (?'state'\g'value')\n\s*status: (?'status'\g'value')\n\s*action: (?'action'\g'value')\n\s*scan: (?'scan'\g'value')\n\s*config:\n\n(?'config'\g'value')\n\s*errors: (?'errors'\g'value')/s";
  $stdout = shell_exec('/usr/sbin/zpool status -v '.$pool.' 2>&1');
  preg_match_all($regex, $stdout, $matches, PREG_SET_ORDER);
  $cleaned = array_map('cleanup', $matches);
  return $cleaned;
}

// header('Content-Type: application/json');
// echo json_encode(getPoolsStatus($_GET['pool']));
?>