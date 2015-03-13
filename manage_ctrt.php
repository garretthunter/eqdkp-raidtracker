<?php
/**
 * Project:     CT_RaidTrackerImport [EQdkp Plugin]
 * License:     http://opensource.org/licenses/gpl-license.php
 * -----------------------------------------------------------------------
 * File:        manage_ctrt.php
 * Description	Main controller for the CT_RaidTracker plugin
 * Date:        $Date: 2009-10-18 10:12:45 +0000 (Sun, 18 Oct 2009) $
 * -----------------------------------------------------------------------
 * @author 		Garrett Hunter <loganfive@blacktower.com>
 * @copyright   (c) 2008 Garrett Hunter
 * @link        http://code.google.com/p/eqdkp-raidtracker/
 * @package     CT_RaidTrackerImport
 * @version     $id$
 */

// EQdkp required files/vars
define('EQDKP_INC', true);
define('IN_ADMIN', true);
define('PLUGIN', 'ctrt');

$eqdkp_root_path = './../../';
require_once($eqdkp_root_path . 'common.php');

if ( !$pm->check(PLUGIN_INSTALLED, 'ctrt') )
{
    header('HTTP/1.0 404 Not Found');
    exit;
}

require_once('settings.php');
require_once('admin/ctrt_base.php');

/**
 * Handle CT_RaidTracker Import admin events
 * @subpackage ManageCTRT
 */
class CTRT_ManageCTRT extends CTRT_Base {

    /**
     * class specific vars
     */
    var $_ctrt_controllers = array("settings"           => "manage_settings.php",
                                   "event_triggers"     => "manage_event_triggers.php",
                                   "raid_note_triggers" => "manage_raid_note_triggers.php",
                                   "own_raids"          => "manage_own_raids.php",
                                   "add_items"          => "manage_add_items.php",
                                   "ignore_items"       => "manage_ignore_items.php",
                                   "aliases"            => "manage_alias.php",
                                   );

    /**
     * List of available modes
     */
    var $_myModes = array("list", "add", "export", "import");

    var $_myMode;
    var $_myParam;
    var $_controller;

    function CTRT_ManageCTRT()
    {
		$this->loadGlobals ();
        $this->assoc_buttons(array(
            'form' => array(
                'name'    => '',
                'process' => 'list'.$this->getMyParam(),
                'check'   => 'a_raid_add'))
        );

        foreach ($this->getMyModes() as $mode) {
            $this->assoc_params(array(
                $mode => array(
                    'name'    => 'mode',
                    'value'   => $mode,
                    'process' => $mode.$this->getMyParam(),
                    'check'   => 'a_raid_add')
            ));
        }
    }

    function getControllerLink ($param, $mode="list") {
        return (plugin_path('ctrt',$this->_ctrt_controllers[$param]) . path_params("mode",$mode));
    }

    /**
     * Returns main menu links
     */
    function getMainMenu () {
        $mainMenuLinks = array();
        foreach ($this->getAllControllers() as $param => $controller) {

            $mainMenuLinks[] = array ("param" => $param,
                                      "text" => $this->_user->lang['ctrt_'.$param.'_adminmenu'],
                                      "link" => ($this->getControllerLink($param))
                                 );
        }
        return $mainMenuLinks;
    }

    /**
     * Returns formatted submenu links
     * @var string $active currently selected  main menu link
     */
    function getFormattedMainMenu ($active) {
        $formattedMainMenu = array();
        foreach ($this->getMainMenu() as $mainMenuItem) {

            if (!strcmp($mainMenuItem["param"],$active)) {
                $mainMenu["TAG"] = "th";
                $mainMenu["MENU"] = $mainMenuItem["text"];
            } else {
                $mainMenu["TAG"] = "td";
                $mainMenu["MENU"] = "<a href=\"".$mainMenuItem["link"]."\">".$mainMenuItem["text"]."</a>";
            }
            $formattedMainMenu[] = $mainMenu;
        }

        return $formattedMainMenu;
    }

    /**
     * Returns submenu links
     */
    function getSubmenu () {
        $submenuLinks = array();
        foreach ($this->getMyModes() as $mode) {

            $submenuLinks[] = array ("param" => $this->getMyParam()."_".$mode,
                                     "text" => $this->_user->lang['ctrt_adminmenu_'.$mode],
                                     "link" => ($this->getControllerLink($this->getMyParam(),$mode))
                                    );
        }
        return $submenuLinks;
    }

    /**
     * Returns formatted submenu links
     * @var string $active currently selected submenu link
     */
    function getFormattedSubmenu ($active) {

        $formattedSubmenu = array();
        foreach ($this->getSubmenu() as $submenuItem) {

            if (!strcmp($submenuItem["param"],$active)) {
                $submenu["TAG"] = "th";
                $submenu["MENU"] = $submenuItem["text"];
            } else {
                $submenu["TAG"] = "td";
                $submenu["MENU"] = "<a href=\"".$submenuItem["link"]."\">".$submenuItem["text"]."</a>";
            }
            $formattedSubmenu[] = $submenu;
        }
        return $formattedSubmenu;
    }

    /**
     * Create the main & sub menus
     */
    function createMenus() {
        foreach ($this->getFormattedMainMenu($this->getMyParam()) as $mainMenu) {
            $this->_tpl->assign_block_vars('mainmenu_row',$mainMenu);
        }
        foreach ($this->getFormattedSubmenu($this->getMyParam()."_".$this->getMyMode()) as $exportMenu) {
            $this->_tpl->assign_block_vars('submenu_row',$exportMenu);
        }

    }

    /************************************
     * Getter / Setter methods
     *///////////////////////////////////

    /**
     * @var string $_myParam
     */
    function getMyParam() { return $this->_myParam; }
    function setMyParam($param) { $this->_myParam = $param; }

    /**
     * @var string $_myMode
     */
    function getMyMode() { return $this->_myMode; }
    function setMyMode($mode) { $this->_myMode = $mode; }

    /**
     * @var array $_myModes
     */
    function getMyModes() { return $this->_myModes; }
    function setMyModes($modes) { $this->_myModes = $modes; }

    /**
     * @var array $_ctrt_controllers
     */
    function getAllControllers() { return $this->_ctrt_controllers; }
}
?>