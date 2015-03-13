<?php
/**
 * Project:     CT_RaidTrackerImport [EQdkp Plugin]
 * License:     http://opensource.org/licenses/gpl-license.php
 * -----------------------------------------------------------------------
 * File:        manage_alias.php
 * Description	List, Add, update, or delete aliases
 * Date:        $Date: 2009-10-18 10:12:45 +0000 (Sun, 18 Oct 2009) $
 * -----------------------------------------------------------------------
 * @author 		Garrett Hunter <loganfive@blacktower.com>
 * @copyright   (c) 2008 Garrett Hunter
 * @link        http://code.google.com/p/eqdkp-raidtracker/
 * @package     CT_RaidTrackerImport
 * @version     $id$
 */

require_once('manage_ctrt.php');
require_once('admin/dao/playeralias.php');

$user->check_auth('a_raid_add');

if ( !$pm->check(PLUGIN_INSTALLED, 'ctrt') )
{
    header('HTTP/1.0 404 Not Found');
    exit;
}

/**
 * Alias controller
 * @subpackage ManageCTRT
 */
class CTRT_ManageAlias extends CTRT_ManageCTRT {

	var $_myParam = 'aliases';
	
    function addaliases () {
        require_once('admin/aliases/add.php');
        $extension = new CTRT_AddAlias;
        $extension->process();
    }

    function listaliases () {
        require_once('admin/aliases/list.php');
        $extension = new CTRT_ListAliases;
        $extension->process();
    }

    function exportaliases () {
        require_once('admin/aliases/export.php');
        $extension = new CTRT_ExportAliases;
        $extension->process();
    }

    function importaliases () {
        require_once('admin/aliases/import.php');
        $extension = new CTRT_ImportAliases;
        $extension->process();
    }
}

$CTRT_ManageAlias = new CTRT_ManageAlias();
$CTRT_ManageAlias->process();