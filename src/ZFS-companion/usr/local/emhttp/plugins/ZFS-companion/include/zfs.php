<?php
require_once 'constants.php';
require_once 'ZpoolStatus.php';

function getZfsHealth() {
  $stdout = shell_exec('zpool status -x 2>&1');
  $healthy = strcmp(trim($stdout),'all pools are healthy') == 0;
  return array(
    'healthy' => $healthy,
    'status' => $healthy ? trim($stdout) : processPoolStatus($stdout),
  );
}

function getPools() {
  $fields = ['name','health'];
  $stdout = shell_exec('/usr/sbin/zpool list -Ho '.implode(',', $fields));
  $lines = preg_split('/\n/', $stdout, NULL, PREG_SPLIT_NO_EMPTY);
  $data = [];
  foreach ($lines as $index => $line) {
    $values = explode("\t", $line);
    for ($i=1; $i < count($fields); $i++) { 
      $data[$values[0]][$fields[$i]] = $values[$i];
    }
  }
  return $data;
}

function getPoolNames() {
  $stdout = shell_exec('/usr/sbin/zpool list -Ho name');
  $lines = preg_split('/\n/', $stdout, NULL, PREG_SPLIT_NO_EMPTY);
  return $lines;
}

?>