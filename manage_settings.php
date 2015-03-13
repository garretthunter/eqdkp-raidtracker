<?php
/**
 * Project:     CT_RaidTrackerImport [EQdkp Plugin]
 * License:     http://opensource.org/licenses/gpl-license.php
 * -----------------------------------------------------------------------
 * File:        manage_settings.php
 * Description	Update CT_RaidTracker configuration settings
 * Date:        $Date: 2009-10-18 10:12:45 +0000 (Sun, 18 Oct 2009) $
 * -----------------------------------------------------------------------
 * @author 		Garrett Hunter <loganfive@blacktower.com>
 * @copyright   (c) 2008 Garrett Hunter
 * @link        http://code.google.com/p/eqdkp-raidtracker/
 * @package     CT_RaidTrackerImport
 * @version     $id$
 */

require_once('settings.php');
require_once('manage_ctrt.php');

$user->check_auth('a_raid_add');

if ( !$pm->check(PLUGIN_INSTALLED, 'ctrt') )
{
    header('HTTP/1.0 404 Not Found');
    exit;
}

/**
 * Handle CT_RaidTracker Import admin events
 * @subpackage ManageCTRT
 */
class CTRT_ManageSettings extends CTRT_ManageCTRT {

	var $_myParam = 'settings';

    function listsettings () {
        require_once('admin/settings.php');
        $extension = new CTRT_Settings;
        $extension->process();
    }
}

$CTRT_ManageSettings = new CTRT_ManageSettings();
$CTRT_ManageSettings->process();

?>