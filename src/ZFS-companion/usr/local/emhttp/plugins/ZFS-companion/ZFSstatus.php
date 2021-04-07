<?php
include 'include/zfs.php';

$stdout = shell_exec('/usr/sbin/zpool status 2>&1');

$re = '/(?<key>[^:]+):\s+\'*(?<value>[^\n\']+)\'*\s*/';
preg_match_all($re, $stdout, $matches, PREG_SET_ORDER, 0);

foreach ($matches as $match)
    $data[trim($match['key'])] = $match['value'];
if (!isset($data))
    exit(json_encode(array($stdout)));

// Adds the keys and values to an array, named appropiately
$json = array(
    'healthy' => isHealthy(),
    'pools' => getPools(),
    'pool' => $data['pool'],
    'state' => $data['state'],
    'scan' => $data['scan'],
    'config' => $data['config'],
    'errors' => $data['errors'],
);

header('Content-Type: application/json');
echo json_encode($json);
?>