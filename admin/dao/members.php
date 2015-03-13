<?php
/**
 * Project:     CT_RaidTrackerImport [EQdkp Plugin]
 * License:     http://opensource.org/licenses/gpl-license.php
 * -----------------------------------------------------------------------
 * File:        members.php
 * Description	retrieves member information from EQdkp tables
 * Date:        $Date: 2008-10-22 23:41:55 -0700 (Wed, 22 Oct 2008) $
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
 * CRUD Class Members table
 * @subpackage CTRTDataAccessors
 */
class EQDKP_Members extends CTRT_DataAccessor {

    var $_myTableName = '__members';
    var $_mySortOrder = 'member_name';

    /**
     * Update a record
     * @var array $data Contains all values to be updated
     */
    function update($data) { 
		// do nothing for now
        return 1;
    }

    /**
     * Insert a record
     * @var array $data Contains all column data to insert
     */
    function insert($data) {
		// do nothing for now
        return 1;
    }

    //========================================
    // Getter / Setter methods
    //========================================

}
?>