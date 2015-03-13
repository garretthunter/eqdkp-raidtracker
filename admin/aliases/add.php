<?php
/**
 * Project:     CT_RaidTrackerImport [EQdkp Plugin]
 * License:     http://opensource.org/licenses/gpl-license.php
 * -----------------------------------------------------------------------
 * File:        add.php
 * Description  add, update, and delete an alias
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
 * CTRT_AddAlias process the CRUD events from the web form
 * @subpackage ManageCTRT
 */
class CTRT_AddAlias extends CTRT_ManageAlias
{
    var $data     = array();           // Holds alias data if URI_ID is set         @var alias

    function CTRT_AddAlias()
    {
        parent::eqdkp_admin();

        $this->loadGlobals();
        $this->setMyMode('add');

		require_once('admin/dao/members.php');
        $this->daoPlayerAlias = new CTRT_PlayerAlias;
        $this->daoMembers     = new EQDKP_Members;

        $this->data = array(
            'alias_id'          => 0,
            'member_name'       => '',
            'alias_member_id'   => $this->_in->get('alias_member_id'),
            'alias_name'        => $this->daoPlayerAlias->getProperName($this->_in->get('alias_name'))
        );

        // Vars used to confirm deletion
        $confirm_text = $this->getUserLang('ctrt_aliases_confirm_delete');
        $alias_ids = array();
        if ( $this->_in->exists('delete') )
        {
            if ( $this->_in->exists('compare_ids') )
            {
                foreach ( $this->_in->getArray('compare_ids','int') as $keyID )
                {
                    $alias_name = $this->daoPlayerAlias->getByPrimaryKey($keyID);
                    $alias_names[] = $alias_name[0]['alias_name'];
                    $alias_ids[] = $alias_name[0]['alias_id'];
                }

                $names = implode(', ', $alias_names);
                $ids = implode(', ', $alias_ids);

                $confirm_text .= '<br />' . $names;
            }
            else
            {
                $failure_message = sprintf($this->getUserLang("ctrt_aliases_not_selected"));
                $link_list = array(
                    $this->getUserLang('ctrt_adminmenu_list')  => $this->getControllerLink($this->getMyParam(),'list'),
                    $this->getUserLang('ctrt_adminmenu_add')  => $this->getControllerLink($this->getMyParam(),'add'),
                    $this->getUserLang('ctrt_adminmenu_export')  => $this->getControllerLink($this->getMyParam(),'export'),
                    $this->getUserLang('ctrt_adminmenu_import') => $this->getControllerLink($this->getMyParam(),'import')
                    );

                $this->admin_die($failure_message, $link_list);
            }
        }

        $this->set_vars(array(
            'confirm_text'  => $confirm_text,
            'uri_parameter' => URI_ID,
            'url_id'        => ( sizeof($alias_ids) > 0 ) ? $ids : (( $this->_in->exists(URI_ID) ) ? $this->_in->get(URI_ID) : ''),
            'script_name'   => $this->getControllerLink($this->getMyParam(),$this->getMyMode()))
        );

        $this->assoc_buttons(array(
            'add' => array(
                'name'    => 'add',
                'process' => 'process_add',
                'check'   => 'a_members_man'),
            'update' => array(
                'name'    => 'update',
                'process' => 'process_update',
                'check'   => 'a_members_man'),
            'delete' => array(
                'name'    => 'delete',
                'process' => 'process_delete',
                'check'   => 'a_members_man'),
            'form' => array(
                'name'    => '',
                'process' => 'display_form',
                'check'   => 'a_members_man'))
        );

        // Build the alias array
        // ---------------------------------------------------------
        if ( !empty($this->url_id) )
        {
            $row = $this->daoPlayerAlias->getByPrimaryKey($this->url_id);
			$member = $this->daoMembers->getbyPrimaryKey($row[0]['alias_member_id']);
            $this->data = array(
                'alias_id'          => $row[0]['alias_id'],
                'alias_member_id'   => $this->_in->get('alias_member_id', $row[0]['alias_member_id']),
                'alias_name'        => $this->daoPlayerAlias->getProperName($this->_in->get('alias_name', $row[0]['alias_name'])),
				'member_name'		=> $member[0]['member_name'],
            );
        }
    }

    function error_check()
    {
        if ( ($this->_in->exists('add')) || ($this->_in->exists('update'))) {
            $this->fv->is_filled('alias_name', $this->_user->lang['fv_required_name']);
        }

        return $this->fv->is_error();
    }

    /**
     * Process Delete (confirmed)
     */
    function process_confirm()
    {
        $success_message = "";
        $ids = explode(", ", $this->url_id);

        foreach ($ids as $id) {
            // Save the record that will be deleted for later logging
            $old_data = $this->daoPlayerAlias->getByPrimaryKey($id);
			$member = $this->daoMembers->getbyPrimaryKey($old_data[0]['alias_member_id']);

            // Remove the record
            $this->daoPlayerAlias->deleteById($id);

            // Append success message
            $success_message .= sprintf($this->getUserLang('ctrt_aliases_success_delete'),$old_data[0]['alias_name'], $member[0]['member_name']) . '<br />';
        }

        $link_list = array(
                    $this->getUserLang('ctrt_adminmenu_list')  => $this->getControllerLink($this->getMyParam(),'list'),
                    $this->getUserLang('ctrt_adminmenu_add')  => $this->getControllerLink($this->getMyParam(),'add'),
                    $this->getUserLang('ctrt_adminmenu_export')  => $this->getControllerLink($this->getMyParam(),'export'),
                    $this->getUserLang('ctrt_adminmenu_import') => $this->getControllerLink($this->getMyParam(),'import')
                    );

        //
        // Success message
        //
        $this->admin_die($success_message, $link_list);
    }

    /**
     * Update a record
     */
    function process_update()
    {
        // Make a copy of the data prior to updating
        $old_data = $this->daoPlayerAlias->getByPrimaryKey($this->url_id);

		/**
		 * Get the member name for this alias
		 */
		$member = $this->daoMembers->getbyPrimaryKey($old_data[0]['alias_member_id']);
		$alias_name = $this->daoPlayerAlias->getProperName($this->_in->get('alias_name'));
		
        if(!$this->daoPlayerAlias->update($this->data)) {
            // Error out if alias name exists

            $failure_message = sprintf($this->getUserLang('ctrt_aliases_duplicate'),$alias_name,$member[0]['member_name']);
            $link_list = array(
                    $this->getUserLang('ctrt_adminmenu_list')  => $this->getControllerLink($this->getMyParam(),'list'),
                    $this->getUserLang('ctrt_adminmenu_add')  => $this->getControllerLink($this->getMyParam(),'add'),
                    $this->getUserLang('ctrt_adminmenu_export')  => $this->getControllerLink($this->getMyParam(),'export'),
                    $this->getUserLang('ctrt_adminmenu_import') => $this->getControllerLink($this->getMyParam(),'import')
                    );

            $this->admin_die($failure_message, $link_list);
        } else {
            //
            // Success message
            //
            $success_message = sprintf($this->getUserLang('ctrt_aliases_success_update'), $old_data[0]['alias_name'], $alias_name, $member[0]['member_name']);
            $link_list = array(
                    $this->getUserLang('ctrt_adminmenu_list')  => $this->getControllerLink($this->getMyParam(),'list'),
                    $this->getUserLang('ctrt_adminmenu_add')  => $this->getControllerLink($this->getMyParam(),'add'),
                    $this->getUserLang('ctrt_adminmenu_export')  => $this->getControllerLink($this->getMyParam(),'export'),
                    $this->getUserLang('ctrt_adminmenu_import') => $this->getControllerLink($this->getMyParam(),'import')
                    );
            $this->admin_die($success_message, $link_list);
        }
    }

    /**
     * Add a new record
     */
    function process_add()
    {
		$alias_name = $this->daoPlayerAlias->getProperName($this->_in->get('alias_name'));
		$alias_member_id = $this->_in->get('alias_member_id');

		/**
		 * Get the member name for this alias
		 */
		require_once('admin/dao/members.php');
		$this->daoMembers = new EQDKP_Members;
		$member = $this->daoMembers->getbyPrimaryKey($alias_member_id);
		$member_name = $member[0]['member_name'];

        if (!$this->daoPlayerAlias->insert(array('alias_name' => $alias_name,
                                                 'alias_member_id' => $alias_member_id
                                                 ))) {
            // Error out if alias name exists

            $failure_message = sprintf($this->getUserLang('ctrt_aliases_duplicate'),$alias_name,$member_name);
            $link_list = array(
                    $this->getUserLang('ctrt_adminmenu_list')  => $this->getControllerLink($this->getMyParam(),'list'),
                    $this->getUserLang('ctrt_adminmenu_add')  => $this->getControllerLink($this->getMyParam(),'add'),
                    $this->getUserLang('ctrt_adminmenu_export')  => $this->getControllerLink($this->getMyParam(),'export'),
                    $this->getUserLang('ctrt_adminmenu_import') => $this->getControllerLink($this->getMyParam(),'import')
                    );
            $this->admin_die($failure_message, $link_list);
        } else {

            /**
             * Logging
             */
            $log_action = array(
                'header'                        => '{L_ACTION_CTRT_ALIASES_ADDED}',
                '{L_CTRT_LABEL_ALIAS_NAME}'     => $alias_name,
                '{L_CTRT_LABEL_MEMBER_NAME}'    => $member_name,

                '{L_ADDED_BY}'                  => $this->admin_user);

            $this->log_insert(array(
                'log_type'   => $log_action['header'],
                'log_action' => $log_action)
            );

            /**
             * Success message
             */
            $success_message = sprintf($this->getUserLang('ctrt_aliases_success_add'), $alias_name, $member_name);
            $link_list = array(
                    $this->getUserLang('ctrt_adminmenu_list')  => $this->getControllerLink($this->getMyParam(),'list'),
                    $this->getUserLang('ctrt_adminmenu_add')  => $this->getControllerLink($this->getMyParam(),'add'),
                    $this->getUserLang('ctrt_adminmenu_export')  => $this->getControllerLink($this->getMyParam(),'export'),
                    $this->getUserLang('ctrt_adminmenu_import') => $this->getControllerLink($this->getMyParam(),'import')
                    );
            $this->admin_die($success_message, $link_list);
        }

        return;
    }

    /**
     * Display form
     */
    function display_form()
    {
        $this->createMenus();

        /**
         * Generate the list of members
         */
		require_once('admin/dao/members.php');
        $this->daoMembers = new EQDKP_Members;
        $members = $this->daoMembers->getAll();
		
        foreach ($members as $member) {
            $this->_tpl->assign_block_vars('members_row', array(
                'VALUE'    => $member['member_id'],
                'SELECTED' => ( $this->data['alias_member_id'] == $member['member_id'] ) ? ' selected="selected"' : '',
                'OPTION'   => $member['member_name'])
            );
        }

        $this->_tpl->assign_vars(array(
            // Form vars
            'F_CONFIG' => $this->getControllerLink($this->getMyParam(),$this->getMyMode()),

            // Form values
            'V_ID'        => ( $this->_in->exists('add') ) ? '' : $this->data['alias_id'],
            'ID'          => $this->data['alias_id'],
            'ALIAS_NAME'  => $this->data['alias_name'],
            'MEMBER_NAME' => $this->data['member_name'],

            // Language
            'L_PLUGIN_TITLE'    => $this->getUserLang('ctrt'),
            'L_MEMBER'      => $this->getUserLang('member'),
            'L_NAME'        => $this->getUserLang('name'),

            // Buttons
            'S_ADD'         => ( !empty($this->url_id) ) ? false : true,
            'L_ADD'     => $this->getUserLang('add'),
            'L_UPDATE'  => $this->getUserLang('update'),
            'L_RESET'   => $this->getUserLang('reset'),

            /**
             * Help text
             */
            'L_HELP'              => $this->getUserLang('ctrt_aliases_help'),
            'L_HELP_NAME'         => $this->getUserLang('ctrt_aliases_help_name'),
            'L_HELP_RESULT'       => $this->getUserLang('ctrt_aliases_help_member'),


            // Form validation
            'FV_NAME'        		=> $this->fv->generate_error('alias_name'),
        ));

        $this->_eqdkp->set_vars(array(
            'page_title'    => page_title($this->getUserLang('ctrt_aliases_pagetitle')),
            'template_path' => $this->_pm->get_data('ctrt', 'template_path'),
            'template_file' => 'admin/addalias.html',
            'display'       => true)
        );
    }
}