<?php
/**
 * Project:     CT_RaidTrackerImport [EQdkp Plugin]
 * License:     http://opensource.org/licenses/gpl-license.php
 * -----------------------------------------------------------------------
 * File:        items.php
 * Description	Items Data Accessor for CTRT_RaidTracker
 * Date:        $Date: 2009-10-18 10:12:45 +0000 (Sun, 18 Oct 2009) $
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
require_once($eqdkp_root_path.'plugins/ctrt/admin/dao/dataaccessor.php');

/**
 * CRUD Class Items table
 * @subpackage CTRTDataAccessors
 */
class CTRT_Items extends CTRT_DataAccessor {

	var $_myTableName = '__ctrt_items';
	var $_mySortOrder = 'items_name';
	
	/**
	 * Insert a record
	 * @var array $data Contains all column data to insert
	 */
	function insert($data) {
		if (!$this->isDuplicate($data)) {
			$this->doInsert ($data);
		} else {
			return 0;
		}
		return 1;
	}

	//========================================
	// Getter / Setter methods
	//========================================

}
?>