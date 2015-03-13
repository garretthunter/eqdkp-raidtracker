<?php
/**
 * Project:     CT_RaidTrackerImport [EQdkp Plugin]
 * License:     http://opensource.org/licenses/gpl-license.php
 * -----------------------------------------------------------------------
 * File:        manage_own_raids.php
 * Description	List, Add, update, or delete own raids
 * Date:        $Date: 2009-10-18 10:12:45 +0000 (Sun, 18 Oct 2009) $
 * -----------------------------------------------------------------------
 * @author 		Garrett Hunter <loganfive@blacktower.com>
 * @copyright   (c) 2008 Garrett Hunter
 * @link        http://code.google.com/p/eqdkp-raidtracker/
 * @package     CT_RaidTrackerImport
 * @version     $id$
 */

require_once('manage_ctrt.php');
require_once('admin/dao/ownraids.php');

$user->check_auth('a_raid_add');

if ( !$pm->check(PLUGIN_INSTALLED, 'ctrt') )
{
    header('HTTP/1.0 404 Not Found');
    exit;
}

/**
 * Own Raid Controller
 * @subpackage ManageCTRT
 */
class CTRT_ManageOwnRaids extends CTRT_ManageCTRT {

    var $_myParam = 'own_raids';
    var $daoOwnRaids;                    // Data accessor object for OwnRaids

    function addown_raids () {
        require_once('admin/ownraids/add.php');
        $extension = new CTRT_AddOwnRaids;
        $extension->process();
    }

    function listown_raids () {
        require_once('admin/ownraids/list.php');
        $extension = new CTRT_ListOwnRaids;
        $extension->process();
    }

    function exportown_raids () {
        require_once('admin/ownraids/export.php');
        $extension = new CTRT_ExportOwnRaids;
        $extension->process();
    }

    function importown_raids () {
        require_once('admin/ownraids/import.php');
        $extension = new CTRT_ImportOwnRaids;
        $extension->process();
    }
}

$CTRT_ManageOwnRaids = new CTRT_ManageOwnRaids;
$CTRT_ManageOwnRaids->process();