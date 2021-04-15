<?
$docroot = $docroot ?? $_SERVER['DOCUMENT_ROOT'] ?: '/usr/local/webGui';

// add translations
$_SERVER['REQUEST_URI'] = 'dashboard';
require_once "$docroot/webGui/include/Translations.php";

require_once "$docroot/webGui/include/Helpers.php";

require_once "/usr/local/emhttp/plugins/ZFS-companion/include/constants.php";
require_once "/usr/local/emhttp/plugins/ZFS-companion/include/zfs.php";
require_once "/usr/local/emhttp/plugins/ZFS-companion/include/ZpoolStatus.php";

function my_unit($value,$unit) {
  return ($unit=='F' ? round(9/5*$value+32) : $value)." $unit";
}
switch ($_POST['cmd']) {
  case 'healthy':
    $zfsHealth=getZfsHealth();
    $healthy = $zfsHealth['healthy'];
    $healthStatus = $zfsHealth['status'];
    $orb = $healthColors[$healthy]."-orb";
    $describeUnhealthy = function($status) {
      return $status['pool'].': '.$status['status'];
    };
    $title = 'title="'.($healthy ? ucfirst($healthStatus) : implode('&#10;', array_map($describeUnhealthy, $healthStatus))).'"';
    echo '<i id="zfscompanion-healthy-icon" style="vertical-align:baseline" class="fa fa-circle orb '.$orb.' middle" '.$title.'></i><span '.$title.'>'.$healthDescriptions[$healthy].'</span>';
    break;
  case 'summary':
    $summary=getPoolsStatus();
    foreach ($summary as $pool) {
      $colorClass=array_key_exists($pool['state'], $statusColors) ? $statusColors[$pool['state']]."-text" : '';
      $output = array(
        $pool['pool'],
        "<span class=\"".$colorClass."\">".$pool['state']."</span>",
        "<span>".$pool['scan']."</span>",
      );
      echo implode("\t", $output)."\n";
    };
    break;
  case 'status':
    $status=getPoolsStatus();
    $names = explode(',',$_POST['names']);
    switch ($_POST['com']) {
      case 'smb':
        exec("LANG='en_US.UTF8' lsof -Owl /mnt/disk[0-9]* 2>/dev/null|awk '/^shfs/ && \$0!~/\.AppleD(B|ouble)/ && \$5==\"REG\"'|awk -F/ '{print \$4}'",$lsof);
        $counts = array_count_values($lsof); $count = [];
        foreach ($names as $name) $count[] = $counts[$name] ?? 0;
        echo implode("\0",$count);
        break;
      }
    break;
}
