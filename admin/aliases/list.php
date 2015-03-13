<?php
/**
 * Project:     CT_RaidTrackerImport [EQdkp Plugin]
 * License:     http://opensource.org/licenses/gpl-license.php
 * -----------------------------------------------------------------------
 * File:        list.php
 * Description  Lists aliases
 * Date:        $Date: 2009-10-18 10:12:45 +0000 (Sun, 18 Oct 2009) $
 * -----------------------------------------------------------------------
 * @author      Garrett Hunter <loganfive@blacktower.com>
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

/**
 * Display a list of member aliases. Provides mass delete and single alias add.
 * @subpackage ManageCTRT
 */
class CTRT_ListAliases extends CTRT_ManageAlias
{
     function CTRT_ListAliases()
    {
		$this->loadGlobals();

        $this->setMyMode('list');
        $this->assoc_buttons(array(
            'delete' => array(
                'name'    => 'delete',
                'process' => 'process_delete',
                'check'   => 'a_raid_add'),
            'form' => array(
                'name'    => '',
                'process' => 'display_form',
                'check'   => 'a_members_man'))
        );
    }

    /**
     * Display form
     */
    function display_form()
    {
        $this->createMenus();

        /**
         * Generate the list of aliases
         */
        $this->daoPlayerAlias = new CTRT_PlayerAlias;

        $playerAliases = $this->daoPlayerAlias->getAll();

		/**
		 * Generate the list of members
		 * TO DO this is very very sloppy & slow. make a new DAO gehTODO
		 */
		require_once('admin/dao/members.php');
		$this->daoMembers = new EQDKP_Members;

        foreach ($playerAliases as $row) {

			$member = $this->daoMembers->getByPrimaryKey($row['alias_member_id']);
            $this->_tpl->assign_block_vars('row', array(
                'ID'    	=> $row['alias_id'],
                'ROW_CLASS' => $this->_eqdkp->switch_row_class(),
                'COL1' 		=> $row['alias_name'],
                'COL2'  	=> $member[0]['member_name'],
                'U_ADD'   	=> $this->getControllerLink($this->getMyParam(),"add") . "&amp;" . URI_ID . "=".$row['alias_id'])
            );
        }

        $this->_tpl->assign_vars(array(
            // Form vars
            'F_CONFIG' 			=> $this->getControllerLink($this->getMyParam(),"add"),

            // Labels
            'L_PLUGIN_TITLE'    => $this->getUserLang('ctrt'),

			// Column headings
			'L_COL1'			=> $this->getUserLang('ctrt_alias'),
			'L_COL2'			=> $this->getUserLang('member'),

            // Help text
            'L_HELP'      		=> $this->getUserLang('ctrt_aliases_help'),
            'L_DELETE'          => $this->getUserLang('delete'),

        ));

        $this->_eqdkp->set_vars(array(
            'page_title'    => page_title($this->getUserLang('ctrt_aliases_pagetitle')),
            'template_path' => $this->_pm->get_data('ctrt', 'template_path'),
            'template_file' => 'admin/list.html',
            'display'       => true)
        );
    }
}
