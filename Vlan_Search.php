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

class Vlan_Search {

  public static function menu() {
    echo('<li><a href="plugin/p='.get_class().'">'.get_class().'</a></li>');
  }

}

?>
