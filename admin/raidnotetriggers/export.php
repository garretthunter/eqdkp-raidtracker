<?php
/**
 * Project:     CT_RaidTrackerImport [EQdkp Plugin]
 * License:     http://opensource.org/licenses/gpl-license.php
 * -----------------------------------------------------------------------
 * File:        export.php
 * Description	export raid note triggers
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
 * Export Raid Note Triggers
 * @subpackage ManageCTRT
 */
class CTRT_ExportRaidNoteTriggers extends CTRT_ManageRaidNoteTriggers {

    function CTRT_ExportRaidNoteTriggers()
    {
		$this->loadGlobals();
        $this->setMyMode('export');
        $this->assoc_buttons(array(
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
		require_once('admin/ctrt_xml.php');
		$parser = new CTRT_XML();

		$daoRaidNoteTrigger = new CTRT_RaidNoteTrigger();
		$eventTriggers = $daoRaidNoteTrigger->getAll();
		
		$eventTrigger = array();
        foreach ( $eventTriggers as $trigger)
        {
			$eventTrigger[] = array("trigger"=>$trigger["raid_note_trigger_name"],"result"=>$trigger["raid_note_trigger_result"]);
        }
		
		$exportXML = $parser->xml_export(array("RaidNoteTriggers"=>array("RaidNoteTrigger"=>$eventTrigger)));

		$this->createMenus();
		
        $this->_tpl->assign_vars(array(
            // Form vars
            'EXPORT'          	=> $exportXML,

            // Language
			'L_PLUGIN_TITLE'	=> $this->getUserLang('ctrt'),

            // Help text
            'L_HELP'            => $this->getUserLang('ctrt_export_help'),

        ));

        $this->_eqdkp->set_vars(array(
            'page_title'    => page_title($this->getUserLang('ctrt_raid_note_triggers_pagetitle')),
            'template_path' => $this->_pm->get_data('ctrt', 'template_path'),
            'template_file' => 'admin/export.html',
            'display'       => true)
        );
    }
}
