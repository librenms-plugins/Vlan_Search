<?php

/*
 * Copyright (c) 2014 Neil Lathwood <https://github.com/librenms-plugins/ http://www.lathwood.co.uk>
 *
 * This program is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation, either version 3 of the License, or (at your
 * option) any later version.  Please see LICENSE.txt at the top level of
 * the source code distribution for details.
 */

if(isset($_POST['vlans']))
{
  $orig_vlans = $_POST['vlans'];
  if(strpos($_POST['vlans'], ',') !== false)
  {
    $vlans = explode(',',mres($_POST['vlans']));
  }
  elseif(strstr($_POST['vlans'], PHP_EOL) !== false)
  {
    $vlans = preg_split("/[\s,]+/",$_POST['vlans']);
  }
  else
  {
    $vlans = array(mres($_POST['vlans']));
  }
}

if(isset($_POST['hostname']))
{
  $hostname = mres($_POST['hostname']);
}
else
{
  $hostname = '';
}
?>

<h4>Search for VLANs</h4>
<form method="post" name="vlan" id="vlan" role="form" class="form-horizontal">
  <input type="hidden" name="search" id="search" value="search">
  <div class="form-group">
    <label for="vlans" class="col-sm-3 control-label">Search for VLANs (seperate by commas or new lines)</label>
    <div class="col-sm-6">
      <textarea name="vlans" id="vlans" class="form-control" rows="3"><?php echo $orig_vlans;?></textarea>
    </div>
  </div>
  <div class="form-group">
    <label for="hostname" class="col-sm-3 control-label">Enter hostname</label>
    <div class="col-sm-6">
      <input name="hostname" id="hostname" class="form-control" placeholder="Hostname" value="<?php echo $hostname;?>">
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-3 col-sm-6">
      <button type="submit" class="btn btn-default">Search</button>
    </div>
  </div>
  <?php
  print csrf_field();
  ?>
</form>

<?php

if(isset($_POST['search']))
{
  $vlans = implode(',',$vlans);
  if(isset($vlans) && !empty($vlans))
  {
    $vlan_sql = "AND V.vlan_vlan IN ($vlans)";
  }
  else
  {
    $vlan_sql = '';
  }

  if(isset($hostname) && !empty($hostname))
  {
    $hostname_sql = " AND D.hostname LIKE '%$hostname%'";
  }
  else
  {
    $hostname_sql = '';
  }

  echo('
  <table class="table table-striped table-condensed table-bordered">
    <tr>
      <th>Device</th>
      <th>VLANs</th>
      <th>VLAN Names</th>
    </tr>
');

  foreach (dbFetchRows("SELECT D.hostname,GROUP_CONCAT(`V`.`vlan_vlan`) AS vlan_vlan,GROUP_CONCAT(`V`.`vlan_name`) AS vlan_name FROM `vlans` AS V JOIN devices AS D ON V.device_id=D.device_id WHERE D.disabled=0 AND D.ignore=0 $vlan_sql $hostname_sql GROUP BY D.hostname") as $vlans)
  {
    $pattern = '/,/';
    $device_vlan_id = preg_replace($pattern,'<br />', $vlans['vlan_vlan']);
    $device_vlan_name = preg_replace($pattern, '<br />', $vlans['vlan_name']);
    echo('
    </tr>
      <td><a href="./device/device='.$vlans['hostname'].'">'.$vlans['hostname'].'</a></td>
      <td>'.$device_vlan_id.'</td>
      <td>'.$device_vlan_name.'</td>
    </tr>
');
  }
}

  echo('
  </table>
');

?>
