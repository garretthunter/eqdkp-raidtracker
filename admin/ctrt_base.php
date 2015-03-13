<?php
/**
 * Project:     CT_RaidTrackerImport [EQdkp Plugin]
 * License:     http://opensource.org/licenses/gpl-license.php
 * -----------------------------------------------------------------------
 * File:        ctrt_base.php
 * Description	Base object for CTRT_RaidTracker
 * Date:        $Date: 2008-03-08 07:29:17 -0800 (Sat, 08 Mar 2008) $
 * -----------------------------------------------------------------------
 * @author 		Garrett Hunter <loganfive@blacktower.com>
 * @copyright   (c) 2008 Garrett Hunter
 * @link        http://code.google.com/p/eqdkp-raidtracker/
 * @package     CT_RaidTrackerImport
 * @version     $id$
 */

if ( !defined('EQDKP_INC') )
{
    header('HTTP/1.0 404 Not Found');
    exit;
}
$eqdkp_root_path = './../../';
require_once($eqdkp_root_path.'plugins/ctrt/settings.php');

/**
 * Base class for all CTRT classes
 * @subpackage CTRTAdmin
 */
class CTRT_Base extends EQdkp_Admin {

    /**
     * global eqdkp system vars
     */
    var $_db;
    var $_eqdkp;
    var $_tpl;
    var $_pm;
    var $_user;
    var $_SID;

    function CTRT_Base () {}

    /**
     * loads EQdkp global vars into the class for easy reference
     */
    function loadGlobals () {
        global $db, $eqdkp, $user, $tpl, $pm, $in;

        $this->_db    	= &$db;
        $this->_eqdkp 	= &$eqdkp;
        $this->_user  	= &$user;
        $this->_tpl   	= &$tpl;
        $this->_pm    	= &$pm;
		$this->_in		= &$in;
    }

    /**
     * Cleans and capitalizes a word
     */
    function getProperName($word) {
        // Make sure the name is properly capitalized
        return ucwords(trim($word));
    }

    function getInput ($key, $default = '') {
		return $this->_in->get($key,$default);
	}

	function getUserLang($text) {
		return $this->_user->lang[$text];
	}
}