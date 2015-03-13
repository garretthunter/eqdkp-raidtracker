<?php
/**
 * Project:     CT_RaidTrackerImport [EQdkp Plugin]
 * License:     http://opensource.org/licenses/gpl-license.php
 * -----------------------------------------------------------------------
 * File:        import.php
 * Description	import raid note triggers
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
 * This class imports raid note triggers
 * @subpackage ManageCTRT
 */
class CTRT_ImportRaidNoteTriggers extends CTRT_ManageRaidNoteTriggers {

    var $_sampleXML = "
&lt;RaidNoteTriggers&gt;<br />
&nbsp;&nbsp;&lt;RaidNoteTrigger&gt;<br />
&nbsp;&nbsp;&nbsp;&nbsp;&lt;trigger&gt;<span class=\"positive\">Bloodlord</span>&lt;/trigger&gt;<br />
&nbsp;&nbsp;&nbsp;&nbsp;&lt;result&gt;<span class=\"positive\">Bloodlord Mandokir</span>&lt;/result&gt;<br />
&nbsp;&nbsp;&lt;/RaidNoteTrigger&gt;<br />
&nbsp;&nbsp;&lt;RaidNoteTrigger&gt;<br />
&nbsp;&nbsp;&nbsp;&nbsp;&lt;trigger&gt;<span class=\"positive\">Fankriss</span>&lt;/trigger&gt;<br />
&nbsp;&nbsp;&nbsp;&nbsp;&lt;result&gt;<span class=\"positive\">Fankriss the Unyielding</span>&lt;/result&gt;<br />
&nbsp;&nbsp;&lt;/RaidNoteTrigger&gt;<br />
&nbsp;&nbsp;&lt;RaidNoteTrigger&gt;<br />
&nbsp;&nbsp;&nbsp;&nbsp;&lt;trigger&gt;<span class=\"positive\">Gruul</span>&lt;/trigger&gt;<br />
&nbsp;&nbsp;&nbsp;&nbsp;&lt;result&gt;<span class=\"positive\">Gruul the Dragonkiller</span>&lt;/result&gt;<br />
&nbsp;&nbsp;&lt;/RaidNoteTrigger&gt;<br />
&lt;/RaidNoteTriggers&gt;";

    function CTRT_ImportRaidNoteTriggers()
    {
        parent::eqdkp_admin();

		$this->loadGlobals();
		$this->setMyMode('import');
        $this->daoRaidNoteTrigger = new CTRT_RaidNoteTrigger();

		require_once('admin/ctrt_xml.php');
        $this->parser = new CTRT_XML();

        $this->assoc_buttons(array(
            'import' => array(
                'name'    => 'import',
                'process' => 'process_import',
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

        $this->_tpl->assign_vars(array(
            // Form vars
            'XML'               => $this->_in->get('xml'),
            'SAMPLE_XML'        => $this->_sampleXML,

            // Language
            'L_PLUGIN_TITLE'    => $this->getUserLang('ctrt'),

            // Buttons
            'L_IMPORT'          => $this->getUserLang('ctrt_import'),

            // Help text
            'L_HELP'            => $this->getUserLang('ctrt_import_help'),
            'L_HELP_FORMAT'     => $this->getUserLang('ctrt_import_format_help'),

            // Form validation
            'FV_INVALID_XML'        => $this->fv->generate_error('xmlError'),
        ));

        $this->_eqdkp->set_vars(array(
            'page_title'    => page_title($this->getUserLang('ctrt_raid_note_triggers_pagetitle')),
            'template_path' => $this->_pm->get_data('ctrt', 'template_path'),
            'template_file' => 'admin/import.html',
            'display'       => true)
        );
    }

    /**
     * Validate the xml string
     */
    function error_check()
    {
        $xml = $this->parser->validateXML(stripslashes($this->_in->get('xml')));
        if (!$xml) {
            $this->fv->is_filled('xmlError', $this->getUserLang('ctrt_fv_invalid_xml'));
        }
        return $this->fv->is_error();
    }

    /**
     * Import the raid note triggers
     */
    function process_import () {
        $message = "";

        $raidNoteTriggers = $this->parser->xml_import(stripslashes($this->_in->get('xml')));
        if (isset($raidNoteTriggers["RAIDNOTETRIGGERS"]["RAIDNOTETRIGGER"][0])) {
            $triggers = $raidNoteTriggers["RAIDNOTETRIGGERS"]["RAIDNOTETRIGGER"];
        } else {
            $triggers = array(0 => array("TRIGGER" => $raidNoteTriggers["RAIDNOTETRIGGERS"]["RAIDNOTETRIGGER"]["TRIGGER"],
                                         "RESULT"  => $raidNoteTriggers["RAIDNOTETRIGGERS"]["RAIDNOTETRIGGER"]["RESULT"]
                                ));
        }
				
        foreach ($triggers as $key=>$trigger) {

            if (!$this->daoRaidNoteTrigger->insert(array(
                                              'raid_note_trigger_name'=>$trigger['TRIGGER'],
                                              'raid_note_trigger_result'=>$trigger['RESULT'],
                                              ))) {
                /**
                 * This trigger exists
                 */
                $message .= sprintf($this->getUserLang('ctrt_trigger_duplicate')."<br />",
                                    $trigger['TRIGGER'],
                                    $trigger['RESULT']);
            } else {
            /* @TODO
               IMPORT SUCCESS - LOG RECORD HERE */
                $message .= sprintf($this->getUserLang('ctrt_trigger_success_add')."<br />",
                                    $trigger['TRIGGER'],
                                    $trigger['RESULT']);
            }
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
        $this->admin_die($message, $link_list);
    }
}
