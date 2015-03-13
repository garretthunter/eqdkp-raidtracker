<?php
/**
 * Project:     CT_RaidTrackerImport [EQdkp Plugin]
 * License:     http://opensource.org/licenses/gpl-license.php
 * -----------------------------------------------------------------------
 * File:        import.php
 * Description	import event triggers
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
 * This class imports event triggers
 * @subpackage ManageCTRT
 */
class CTRT_ImportEventTriggers extends CTRT_ManageEventTriggers {

    var $_sampleXML = "
&lt;EventTriggers&gt;<br />
&nbsp;&nbsp;&lt;EventTrigger&gt;<br />
&nbsp;&nbsp;&nbsp;&nbsp;&lt;trigger&gt;<span class=\"positive\">Kazzak</span>&lt;/trigger&gt;<br />
&nbsp;&nbsp;&nbsp;&nbsp;&lt;result&gt;<span class=\"positive\">World Bosses</span>&lt;/result&gt;<br />
&nbsp;&nbsp;&lt;/EventTrigger&gt;<br />
&nbsp;&nbsp;&lt;EventTrigger&gt;<br />
&nbsp;&nbsp;&nbsp;&nbsp;&lt;trigger&gt;<span class=\"positive\">Onyxia</span>&lt;/trigger&gt;<br />
&nbsp;&nbsp;&nbsp;&nbsp;&lt;result&gt;<span class=\"positive\">Onyxia's Lair</span>&lt;/result&gt;<br />
&nbsp;&nbsp;&lt;/EventTrigger&gt;<br />
&nbsp;&nbsp;&lt;EventTrigger&gt;<br />
&nbsp;&nbsp;&nbsp;&nbsp;&lt;trigger&gt;<span class=\"positive\">Ahn'Qiraj Ruins</span>&lt;/trigger&gt;<br />
&nbsp;&nbsp;&nbsp;&nbsp;&lt;result&gt;<span class=\"positive\">Ruins of Ahn'Qiraj</span>&lt;/result&gt;<br />
&nbsp;&nbsp;&lt;/EventTrigger&gt;<br />
&lt;/EventTriggers&gt;";

    function CTRT_ImportEventTriggers()
    {
        parent::eqdkp_admin();

		$this->loadGlobals();
		$this->setMyMode('import');
        $this->daoEventTrigger = new CTRT_EventTrigger();

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
            'page_title'    => page_title($this->getUserLang('ctrt_event_triggers_pagetitle')),
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
     * Import the event triggers
     */
    function process_import () {
        $message = "";
        $eventTriggers = $this->parser->xml_import(stripslashes($this->_in->get('xml')));
        if (isset($eventTriggers['EVENTTRIGGERS']['EVENTTRIGGER'][0])) {
            $triggers = $eventTriggers['EVENTTRIGGERS']['EVENTTRIGGER'];
        } else {
            $triggers = array(0 => array("TRIGGER" => $eventTriggers['EVENTTRIGGERS']['EVENTTRIGGER']['TRIGGER'],
                                         "RESULT"  => $eventTriggers['EVENTTRIGGERS']['EVENTTRIGGER']['RESULT']
                                ));
        }

        foreach ($triggers as $trigger) {

            if (!$this->daoEventTrigger->insert(array(
                                              'event_trigger_name'=>$trigger['TRIGGER'],
                                              'event_trigger_result'=>$trigger['RESULT'],
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
