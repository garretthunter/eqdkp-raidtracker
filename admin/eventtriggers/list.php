<?php
/**
 * Project:     CT_RaidTrackerImport [EQdkp Plugin]
 * License:     http://opensource.org/licenses/gpl-license.php
 * -----------------------------------------------------------------------
 * File:        list.php
 * Description	Lists event triggers
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
 * Display a list of event triggers. Provides mass delete.
 * @subpackage ManageCTRT
 */
class CTRT_ListEventTriggers extends CTRT_ManageEventTriggers
{
    function CTRT_ListEventTriggers()
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
                'check'   => 'a_raid_add'))
        );
    }

    /**
     * Display form
     */
    function display_form()
    {
        $this->createMenus();

        /**
         * Generate the list of event triggers
         */
        $this->daoEventTrigger = new CTRT_EventTrigger;
        $eventTriggers = $this->daoEventTrigger->getAll();

        foreach ($eventTriggers as $row) {
            $this->_tpl->assign_block_vars('row', array(
                'ID'    	=> $row['event_trigger_id'],
                'ROW_CLASS' => $this->_eqdkp->switch_row_class(),
                'COL1' 		=> $row['event_trigger_name'],
                'COL2'  	=> $row['event_trigger_result'],
                'U_ADD'   	=> $this->getControllerLink($this->getMyParam(),"add") . path_params(URI_ID,$row['event_trigger_id'])
            ));
        }

        $this->_tpl->assign_vars(array(
            // Form vars
            'F_CONFIG' => $this->getControllerLink($this->getMyParam(),"add"),

            // Language
            'L_PLUGIN_TITLE'    => $this->getUserLang('ctrt'),
			
			// Column headings
			'L_COL1'			=> $this->getUserLang('ctrt_trigger'),
			'L_COL2'			=> $this->getUserLang('event'),

            /**
             * Help text
             */
            'L_HELP'            => $this->getUserLang('ctrt_event_triggers_help'),
            'L_DELETE'  		=> $this->getUserLang('delete'),

        ));

        $this->_eqdkp->set_vars(array(
            'page_title'    => page_title($this->getUserLang('ctrt_event_triggers_pagetitle')),
            'template_path' => $this->_pm->get_data('ctrt', 'template_path'),
            'template_file' => 'admin/list.html',
            'display'       => true)
        );
    }
}