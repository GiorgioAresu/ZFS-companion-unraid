<?
$plugin = "ZFS-companion";
$docroot = $docroot ?? $_SERVER['DOCUMENT_ROOT'] ?: '/usr/local/emhttp';

require_once "$docroot/webGui/include/Helpers.php";

require_once "$docroot/plugins/$plugin/include/constants.php";
require_once "$docroot/plugins/$plugin/include/zfs.php";
require_once "$docroot/plugins/$plugin/include/ZpoolStatus.php";

$zfscompanion_cfg = parse_plugin_cfg($plugin,true);
$zfscompanion_ignoredhealth = isset($zfscompanion_cfg['IGNORED_HEALTH']) ? $zfscompanion_cfg['IGNORED_HEALTH'] : "";

switch ($_POST['cmd']) {
  case 'healthy':
    $zfsHealth=getZfsHealth();
    $healthy = $zfsHealth['healthy'];
    $healthStatus = $zfsHealth['status'];
    $healthSummary = $zfsHealth['summary'];
    $regex = '/(\R)|(\\n)/';
    $ignored = strcmp(implode('\n', preg_split($regex, $healthSummary)), implode('\n', preg_split($regex, $zfscompanion_ignoredhealth))) == 0;
    $orb = $healthColors[$healthy || $ignored]."-orb";
    $title = 'title="'.str_replace('\n', '&#10;', $healthSummary).'"';
    echo '<i id="zfscompanion-healthy-icon" style="vertical-align:baseline" class="fa fa-circle orb '.$orb.' middle" '.$title.'></i><span '.$title.'>'.$healthDescriptions[$healthy || $ignored].'</span>';
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
}
