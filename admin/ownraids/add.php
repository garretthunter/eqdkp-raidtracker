<?php
/**
 * Project:     CT_RaidTrackerImport [EQdkp Plugin]
 * License:     http://opensource.org/licenses/gpl-license.php
 * -----------------------------------------------------------------------
 * File:        add.php
 * Description  add, update, and delete own raids
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
 * This class handles own raid add & update
 * @subpackage ManageCTRT
 */
class CTRT_AddOwnRaids extends CTRT_ManageOwnRaids
{
    var $data = array();           // Holds own_raid data if URI_ID is set         @var alias

    function CTRT_AddOwnRaids()
    {
        parent::eqdkp_admin();

        $this->loadGlobals();
        $this->setMyMode('add');
        $this->daoOwnRaids = new CTRT_OwnRaids;

        $this->data = array(
            'own_raid_id'       => 0,
            'own_raid_name'     => $this->_in->get('own_raid_name')
            );

        // Vars used to confirm deletion
        $confirm_text = $this->getUserLang('ctrt_own_raids_confirm_delete');
        $own_raid_ids = array();
        if ( $this->_in->exists('delete') )
        {
            if ( $this->_in->exists('compare_ids') )
            {
                foreach ( $this->_in->getArray('compare_ids','int') as $keyID )
                {
                    $own_raid = $this->daoOwnRaids->getByPrimaryKey($keyID);
                    $own_raid_ids[] = $own_raid[0]['own_raid_id'];
                    $own_raid_names[] = $own_raid[0]['own_raid_name'];
                }

                $names = implode(', ', $own_raid_names);
                $ids = implode(', ', $own_raid_ids);

                $confirm_text .= '<br />' . $names;
            }
            else
            {
                $failure_message = sprintf($this->getUserLang("ctrt_own_raids_not_selected"));
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
            'url_id'        => ( sizeof($own_raid_ids) > 0 ) ? $ids : (( $this->_in->exists(URI_ID) ) ? $this->_in->get(URI_ID) : ''),
            'script_name'   => $this->getControllerLink($this->getMyParam(),$this->getMyMode()))
        );

        $this->assoc_buttons(array(
            'add' => array(
                'name'    => 'add',
                'process' => 'process_add',
                'check'   => 'a_raid_add'),
            'update' => array(
                'name'    => 'update',
                'process' => 'process_update',
                'check'   => 'a_raid_add'),
            'delete' => array(
                'name'    => 'delete',
                'process' => 'process_delete',
                'check'   => 'a_raid_add'),
            'form' => array(
                'name'    => '',
                'process' => 'display_form',
                'check'   => 'a_members_man'))
        );

        // Build the own raids array
        // ---------------------------------------------------------
        if ( !empty($this->url_id) )
        {
            $row = $this->daoOwnRaids->getByPrimaryKey($this->url_id);
            $this->data = array(
                'own_raid_id'     => $row[0]['own_raid_id'],
                'own_raid_name'   => $this->_in->get('own_raid_name', $row[0]['own_raid_name']),
            );
        }
    }

    function error_check()
    {
        if ( ($this->_in->exists('add')) || ($this->_in->exists('update'))) {
            $this->fv->is_filled('own_raid_name', $this->_user->lang['fv_required_name']);
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
            $old_data = $this->daoOwnRaids->getById($id);

            // Remove the record
            $this->daoOwnRaids->deleteById($id);

            // Append success message
            $success_message .= sprintf($this->_user->lang["ctrt_own_raids_success_delete"],$old_data[0]['own_raid_name']) . '<br />';
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
     * Update an own raid record
     */
    function process_update()
    {
        // Make a copy of the data prior to updating
        $old_data = $this->daoOwnRaids->getById($this->url_id);

        if(!$this->daoOwnRaids->update($this->data)) {
            // Error out if own raid exists

            $failure_message = sprintf($this->getUserLang("ctrt_own_raids_duplicate"),$this->_in->get('own_raid_name'));
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
            $success_message = sprintf($this->getUserLang('ctrt_own_raids_success_update'), $old_data[0]['own_raid_name']);
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
     * Add an own raid record
     */
    function process_add()
    {
        $name = $this->_in->get('own_raid_name');

        if (!$this->daoOwnRaids->insert(array('own_raid_name' => $name
                                                  ))) {
            // Error out if the own raid exists
            $failure_message = sprintf($this->getUserLang("ctrt_own_raids_duplicate"),$name);
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
                'header'                        => '{L_ACTION_CTRT_OWN_RAID_ADDED}',
                '{L_CTRT_LABEL_OWN_RAID_NAME}'  => $name,

                '{L_ADDED_BY}'                  => $this->admin_user);

            $this->log_insert(array(
                'log_type'   => $log_action['header'],
                'log_action' => $log_action)
            );

            /**
             * Success message
             */
            $success_message = sprintf($this->getUserLang('ctrt_own_raids_success_add'), $name);
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

        $this->_tpl->assign_vars(array(
            // Form vars
            'F_CONFIG' => $this->getControllerLink($this->getMyParam(),$this->getMyMode()),

            // Form values
            'V_ID'          => ( $this->_in->exists('add') ) ? '' : $this->data['own_raid_id'],
            'ID'            => $this->data['own_raid_id'],
            'NAME'          => $this->data['own_raid_name'],

            // Language
            'L_PLUGIN_TITLE'    => $this->getUserLang('ctrt'),
            'L_OWN_RAID'        => $this->getUserLang('ctrt_own_raid'),

            // Buttons
            'S_ADD'     => ( !empty($this->url_id) ) ? false : true,
            'L_ADD'     => $this->getUserLang('add'),
            'L_UPDATE'  => $this->getUserLang('update'),
            'L_RESET'   => $this->getUserLang('reset'),

            /**
             * Help text
             */
            'L_HELP'        => $this->getUserLang('ctrt_own_raids_help'),
            'L_HELP_NAME'   => $this->getUserLang('ctrt_own_raids_help_name'),

            // Form validation
            'FV_NAME'        => $this->fv->generate_error('own_raid_name'),
        ));

        $this->_eqdkp->set_vars(array(
            'page_title'    => page_title($this->getUserLang('ctrt_own_raids_pagetitle')),
            'template_path' => $this->_pm->get_data('ctrt', 'template_path'),
            'template_file' => 'admin/addownraid.html',
            'display'       => true)
        );
    }
}
