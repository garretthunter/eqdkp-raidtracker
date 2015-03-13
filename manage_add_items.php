<?php
/**
 * Project:     CT_RaidTrackerImport [EQdkp Plugin]
 * License:     http://opensource.org/licenses/gpl-license.php
 * -----------------------------------------------------------------------
 * File:        manage_add_items.php
 * Description	List, Add, update, or delete an always added item
 * Date:        $Date: 2009-10-18 10:12:45 +0000 (Sun, 18 Oct 2009) $
 * -----------------------------------------------------------------------
 * @author 		Garrett Hunter <loganfive@blacktower.com>
 * @copyright   (c) 2008 Garrett Hunter
 * @link        http://code.google.com/p/eqdkp-raidtracker/
 * @package     CT_RaidTrackerImport
 * @version     $id$
 */

require_once('manage_ctrt.php');
require_once('admin/dao/items.php');

$user->check_auth('a_raid_add');

if ( !$pm->check(PLUGIN_INSTALLED, 'ctrt') )
{
    header('HTTP/1.0 404 Not Found');
    exit;
}

/**
 * CT_RaidTracker Add Item Controller
 * @subpackage ManageCTRT
 */
class CTRT_ManageAddItems extends CTRT_ManageCTRT {

    var $_myParam = 'add_items';
    var $daoAddItem;                    // Data accessor object for AddItems

//gehDEBUG
//    function CTRT_ManageAddItems()
//    {
//        parent::CTRT_ManageCTRT();
//    }

    function addadd_items () {
        require_once('admin/additems/add.php');
        $extension = new CTRT_AddAddItem;
        $extension->process();
    }

    function listadd_items () {
        require_once('admin/additems/list.php');
        $extension = new CTRT_ListAddItems;
        $extension->process();
    }

    function exportadd_items () {
        require_once('admin/additems/export.php');
        $extension = new CTRT_ExportAddItems;
        $extension->process();
    }

    function importadd_items () {
        require_once('admin/additems/import.php');
        $extension = new CTRT_ImportAddItems;
        $extension->process();
    }
}

$CTRT_ManageAddItems = new CTRT_ManageAddItems;
$CTRT_ManageAddItems->process();