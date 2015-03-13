<?php
/**
 * Project:     CT_RaidTrackerImport [EQdkp Plugin]
 * License:     http://opensource.org/licenses/gpl-license.php
 * -----------------------------------------------------------------------
 * File:        add.php
 * Description	add, update, delete event triggers
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

/**
 * CTRT_AddEventTrigger process the CRUD events from the web form
 * @subpackage ManageCTRT
 */
class CTRT_AddEventTrigger extends CTRT_ManageEventTriggers
{
    var $data    = array();           // Holds event_trigger data if URI_ID is set         @var alias

    function CTRT_AddEventTrigger()
    {
        parent::eqdkp_admin();

        $this->loadGlobals();
        $this->setMyMode('add');
        $this->daoEventTrigger = new CTRT_EventTrigger;

        $this->data = array(
            'event_trigger_id'      => 0,
            'event_trigger_name'    => $this->_in->get('event_trigger_name'),
            'event_trigger_result'  => $this->_in->get('event_trigger_result')
        );

        // Vars used to confirm deletion
        $confirm_text = $this->getUserLang('ctrt_trigger_confirm_delete');
        $event_trigger_ids = array();
        $event_trigger_names = array();
        if ( $this->_in->exists('delete') )
        {
            if ( $this->_in->exists('compare_ids') )
            {
                foreach ( $this->_in->getArray('compare_ids','int') as $keyID )
                {
                    $event_trigger = $this->daoEventTrigger->getByPrimaryKey($keyID);
                    $event_trigger_ids[] = $event_trigger[0]['event_trigger_id'];
                    $event_trigger_names[] = $event_trigger[0]['event_trigger_name'];
                }

                $names = implode(', ', $event_trigger_names);
                $ids = implode(', ', $event_trigger_ids);

                $confirm_text .= '<br />' . $names;
            }
            else
            {
                $failure_message = sprintf($this->getUserLang("ctrt_trigger_not_selected"));
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
            'url_id'        => ( sizeof($event_trigger_ids) > 0 ) ? $ids : (( $this->_in->exists(URI_ID) ) ? $this->_in->get(URI_ID) : ''),
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

        // Build the alias array
        // ---------------------------------------------------------
        if ( !empty($this->url_id) )
        {
            $row = $this->daoEventTrigger->getByPrimaryKey($this->url_id);
            $this->data = array(
                'event_trigger_id'     => $row[0]['event_trigger_id'],
                'event_trigger_name'   => $this->_in->get('event_trigger_name', $row[0]['event_trigger_name']),
                'event_trigger_result' => $this->_in->get('event_trigger_result', $row[0]['event_trigger_result']),
            );
        }
    }

    function error_check()
    {
        if ( ($this->_in->exists('add')) || ($this->_in->exists('update')) ) {
            $this->fv->is_filled('event_trigger_name', $this->getUserLang('fv_required_name'));
            $this->fv->is_filled('event_trigger_result', $this->getUserLang('fv_required_name'));
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
            $old_data = $this->daoEventTrigger->getByPrimaryKey($id);

            // Remove the record
            $this->daoEventTrigger->deleteById($id);

            // Append success message
            $success_message .= sprintf($this->getUserLang("ctrt_trigger_success_delete"),$old_data[0]['event_trigger_name'], $old_data[0]['event_trigger_result']) . '<br />';
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
        $old_data = $this->daoEventTrigger->getByPrimaryKey($this->url_id);

        if(!$this->daoEventTrigger->update($this->data)) {
            // Error out if event trigger exists

            $failure_message = sprintf($this->getUserLang("ctrt_trigger_duplicate"),$this->_in->get('event_trigger_name'),$this->_in->get('event_trigger_result'));
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
            $success_message = sprintf($this->getUserLang('ctrt_trigger_success_update'), $old_data['event_trigger_name'], $old_data['event_trigger_result']);
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
        //Clean up the data
        $name = $this->_in->get('event_trigger_name');
        $result = $this->_in->get('event_trigger_result');

        if (!$this->daoEventTrigger->insert(array('event_trigger_name' => $name,
                                                  'event_trigger_result' => $result
                                                  ))) {
            // Error out if the trigger exists
            $failure_message = sprintf($this->getUserLang("ctrt_trigger_duplicate"),$name,$result);
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

            /**
             * Get the member name for this alias
             */
            $log_action = array(
                'header'                                => '{L_ACTION_CTRT_EVENT_TRIGGER_ADDED}',
                '{L_CTRT_LABEL_EVENT_TRIGGER_NAME}'     => $name,
                '{L_CTRT_LABEL_EVENT_TRIGGER_RESULT}'   => $result,

                '{L_ADDED_BY}'                  => $this->admin_user);

            $this->log_insert(array(
                'log_type'   => $log_action['header'],
                'log_action' => $log_action)
            );

            /**
             * Success message
             */
            $success_message = sprintf($this->getUserLang('ctrt_trigger_success_add'), $name, $result);
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
            'V_ID'          => ( $this->_in->exists('add') ) ? '' : $this->data['event_trigger_id'],
            'ID'            => $this->data['event_trigger_id'],
            'NAME'          => $this->data['event_trigger_name'],
            'RESULT'        => $this->data['event_trigger_result'],

            // Language
            'L_PLUGIN_TITLE'    => $this->getUserLang('ctrt'),
            'L_TRIGGER'         => $this->getUserLang('ctrt_trigger'),
            'L_RESULT'          => $this->getUserLang('event'),

            // Buttons
            'S_ADD'     => ( !empty($this->url_id) ) ? false : true,
            'L_ADD'     => $this->getUserLang('add'),
            'L_UPDATE'  => $this->getUserLang('update'),
            'L_RESET'  => $this->getUserLang('reset'),

            /**
             * Help text
             */
            'L_HELP'        => $this->getUserLang('ctrt_event_triggers_help'),
            'L_HELP_NAME'   => $this->getUserLang('ctrt_event_triggers_help_name'),
            'L_HELP_RESULT' => $this->getUserLang('ctrt_event_triggers_help_result'),

            // Form validation
            'FV_NAME'        => $this->fv->generate_error('event_trigger_name'),
            'FV_RESULT'      => $this->fv->generate_error('event_trigger_result'),
        ));

        $this->_eqdkp->set_vars(array(
            'page_title'    => page_title($this->getUserLang('ctrt_event_triggers_pagetitle')),
            'template_path' => $this->_pm->get_data('ctrt', 'template_path'),
            'template_file' => 'admin/addeventtrigger.html',
            'display'       => true)
        );
    }
}
