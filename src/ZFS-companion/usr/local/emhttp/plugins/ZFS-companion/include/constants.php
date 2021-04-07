<?php
$healthColors = array(
  true => 'green',
  false => 'red',
  null => 'grey',
);

$healthDescriptions = array(
  true => 'Healthy',
  false => 'Unhealthy',
  null => 'Unknown',
);

$fieldLabels = array(
  'size' => 'Size',
  'alloc' => 'Used',
  'free' => 'Free',
  'cap' => 'Utilization',
  'health' => 'Status',
);

$statusColors = array(
  'ONLINE' => 'green',
  'DEGRADED' => 'orange',
  'FAULTED' => 'red',
  'OFFLINE' => 'blue',
  'UNAVAIL' => 'grey',
  'REMOVED' => 'grey',
);

?>