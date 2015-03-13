<?php
/**
 * Project:     CT_RaidTrackerImport [EQdkp Plugin]
 * License:     http://opensource.org/licenses/gpl-license.php
 * -----------------------------------------------------------------------
 * File:        playeralias.php
 * Description	Data accessor class for Player Aliases
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
 * CRUD Class PlayerAlias table
 * @subpackage CTRTDataAccessors
 */
class CTRT_PlayerAlias extends CTRT_DataAccessor {

    var $_myTableName = '__ctrt_aliases';
    var $_mySortOrder = 'alias_name';

    /**
     * Update a record
     * @var array $data Contains all values to be updated
     */
    function update($data) {

        if (!$this->isDuplicate($data)) {

            $this->doUpdate ($data[$this->getMyPrimaryKey()],
                             array('alias_name'=>$data['alias_name'],
                                   'alias_member_id'=>$data['alias_member_id']));
        } else {
            return 0;
        }
        return 1;
    }

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

	function getAllWithMemberName() {

		$playeraliases = array();
			
        /**
         * Generate the list of aliases
         */
        $sql =   'SELECT alias_id, alias_name, alias_member_id, member_name
                    FROM ' . $this->getMyTableName() . ',
                         __members
                   WHERE member_id = alias_member_id';
        $sql .= ' ORDER BY '.$this->getMySortOrder();

        $result = $this->_db->query($sql);
        while ($row = $this->_db->fetch_record($result) )
        {
			$playeraliases[] = array("alias_id"=>$row["alias_id"],
			                         "alias_name"=>$row["alias_name"],
								     "alias_member_id"=>$row["alias_member_id"],
								     "member_name"=>$row["member_name"]
								    );
        }
        $this->_db->free_result($result);
		return $playeraliases;
	}

	function getMemberName($alias_name) {

        /**
         * Get the member name for a specific alias
         */
        $sql =   'SELECT member_name
                    FROM ' . $this->getMyTableName() . ',
                         __members
                   WHERE member_id = alias_member_id
				     AND alias_name = \''.$alias_name.'\'';

        $member_name = $this->_db->query_first($sql);
		return $member_name;
	}
	
    //========================================
    // Getter / Setter methods
    //========================================

}
?>