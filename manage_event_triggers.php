<?php
/**
 * Project:     CT_RaidTrackerImport [EQdkp Plugin]
 * License:     http://opensource.org/licenses/gpl-license.php
 * -----------------------------------------------------------------------
 * File:        manage_event_triggers.php
 * Description	List, Add, update, or delete an event trigger
 * Date:        $Date: 2009-10-18 10:12:45 +0000 (Sun, 18 Oct 2009) $
 * -----------------------------------------------------------------------
 * @author 		Garrett Hunter <loganfive@blacktower.com>
 * @copyright   (c) 2008 Garrett Hunter
 * @link        http://code.google.com/p/eqdkp-raidtracker/
 * @package     CT_RaidTrackerImport
 * @version     $id$
 */

require_once('manage_ctrt.php');
require_once('admin/dao/eventtrigger.php');

$user->check_auth('a_raid_add');

if ( !$pm->check(PLUGIN_INSTALLED, 'ctrt') )
{
    header('HTTP/1.0 404 Not Found');
    exit;
}

/**
 * Handle CT_RaidTracker event trigger admin events
 * @subpackage ManageCTRT
 */
class CTRT_ManageEventTriggers extends CTRT_ManageCTRT {

    var $_myParam = 'event_triggers';
    var $daoEventTrigger;                   // Data accessor object for EventTriggers

    function addevent_triggers () {
        require_once('admin/eventtriggers/add.php');
        $extension = new CTRT_AddEventTrigger;
        $extension->process();
    }

    function listevent_triggers () {
        require_once('admin/eventtriggers/list.php');
        $extension = new CTRT_ListEventTriggers;
        $extension->process();
    }

    function exportevent_triggers () {
        require_once('admin/eventtriggers/export.php');
        $extension = new CTRT_ExportEventTriggers;
        $extension->process();
    }

    function importevent_triggers () {
        require_once('admin/eventtriggers/import.php');
        $extension = new CTRT_ImportEventTriggers;
        $extension->process();
    }
}

$CTRT_ManageEventTriggers = new CTRT_ManageEventTriggers;
$CTRT_ManageEventTriggers->process();