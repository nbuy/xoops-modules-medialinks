<?php
// $Id: notification.inc.php,v 1.1 2006/07/12 16:27:26 nobu Exp $
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <http://www.xoops.org/>                             //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //

function medialinks_notify_iteminfo($category, $item_id)
{
    global $xoopsDB;

    $item = array('name'=>'');
    if ($category=='content' && $item_id!=0) {
	// Assume we have a valid story id
	$sql = 'SELECT title FROM '.$xoopsDB->prefix('medialinks') . " WHERE status='N' AND mid=".$item_id;
	$result = $xoopsDB->query($sql); // TODO: error check
	
	list($item['name']) = $xoopsDB->fetchRow($result);
	$item['url'] = XOOPS_URL.'/modules/'.basename(dirname(dirname(__FILE__))).'/detail.php?mid='.$item_id;
    }
    return $item;
}
?>