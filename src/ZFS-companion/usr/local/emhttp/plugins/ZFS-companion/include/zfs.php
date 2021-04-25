<?php
require_once 'constants.php';
require_once 'ZpoolStatus.php';

function getZfsHealth() {
  $stdout = shell_exec('zpool status -x 2>&1');
  // $stdout = shell_exec('cat /usr/local/emhttp/plugins/ZFS-companion/include/test.txt 2>&1');
  $cleanStdout = trim($stdout);
  $describeUnhealthy = function($status) {
    return $status['pool'].': '.$status['status'];
  };
  $healthy = strcmp($cleanStdout,'all pools are healthy') == 0;
  $status = $healthy ? $cleanStdout : processPoolStatus($stdout);
  $summary = $healthy ? ucfirst($cleanStdout) : implode('\n', array_map($describeUnhealthy, $status));
  return array(
    'healthy' => $healthy,
    'status' => $status,
    'summary' => $summary,
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