<?php
/**
 * Project:     CT_RaidTrackerImport [EQdkp Plugin]
 * License:     http://opensource.org/licenses/gpl-license.php
 * -----------------------------------------------------------------------
 * File:        manage_ignore_items.php
 * Description	List, Add, update, or delete an ignored item
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
class CTRT_ManageIgnoreItems extends CTRT_ManageCTRT {

    var $_myParam = 'ignore_items';
    var $daoIgnoreItem;                 // Data accessor object for IgnoreItems

    function CTRT_ManageIgnoreItems()
    {
        parent::CTRT_ManageCTRT();
    }

    function addignore_items () {
        require_once('admin/ignoreitems/add.php');
        $extension = new CTRT_AddIgnoreItem;
        $extension->process();
    }

    function listignore_items () {
        require_once('admin/ignoreitems/list.php');
        $extension = new CTRT_ListIgnoreItems;
        $extension->process();
    }

    function exportignore_items () {
        require_once('admin/ignoreitems/export.php');
        $extension = new CTRT_ExportIgnoreItems;
        $extension->process();
    }

    function importignore_items () {
        require_once('admin/ignoreitems/import.php');
        $extension = new CTRT_ImportIgnoreItems;
        $extension->process();
    }
}

$CTRT_ManageIgnoreItems = new CTRT_ManageIgnoreItems;
$CTRT_ManageIgnoreItems->process();