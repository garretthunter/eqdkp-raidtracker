<?php
/**
 * Project:     CT_RaidTrackerImport [EQdkp Plugin]
 * License:     http://opensource.org/licenses/gpl-license.php
 * -----------------------------------------------------------------------
 * File:        manage_raid_note_triggers.php
 * Description	List, Add, update, or delete an raid note triggers
 * Date:        $Date: 2009-10-18 10:12:45 +0000 (Sun, 18 Oct 2009) $
 * -----------------------------------------------------------------------
 * @author 		Garrett Hunter <loganfive@blacktower.com>
 * @copyright   (c) 2008 Garrett Hunter
 * @link        http://code.google.com/p/eqdkp-raidtracker/
 * @package     CT_RaidTrackerImport
 * @version     $id$
 */

require_once('manage_ctrt.php');
require_once('admin/dao/raidnotetrigger.php');

$user->check_auth('a_raid_add');

if ( !$pm->check(PLUGIN_INSTALLED, 'ctrt') )
{
    header('HTTP/1.0 404 Not Found');
    exit;
}

/**
 * CT_RaidTracker Raid Note Trigger Controller
 * @subpackage ManageCTRT
 */
class CTRT_ManageRaidNoteTriggers extends CTRT_ManageCTRT {

    var $_myParam = 'raid_note_triggers';
    var $daoRaidNoteTrigger;                    // Data accessor object for RaidNoteTriggers

    function addraid_note_triggers () {
        require_once('admin/raidnotetriggers/add.php');
        $extension = new CTRT_AddRaidNoteTrigger;
        $extension->process();
    }

    function listraid_note_triggers () {
        require_once('admin/raidnotetriggers/list.php');
        $extension = new CTRT_ListRaidNoteTriggers;
        $extension->process();
    }

    function exportraid_note_triggers () {
        require_once('admin/raidnotetriggers/export.php');
        $extension = new CTRT_ExportRaidNoteTriggers;
        $extension->process();
    }

    function importraid_note_triggers () {
        require_once('admin/raidnotetriggers/import.php');
        $extension = new CTRT_ImportRaidNoteTriggers;
        $extension->process();
    }
}

$CTRT_ManageRaidNoteTriggers = new CTRT_ManageRaidNoteTriggers;
$CTRT_ManageRaidNoteTriggers->process();