<?php
/**
 * Project:     CT_RaidTrackerImport [EQdkp Plugin]
 * License:     http://opensource.org/licenses/gpl-license.php
 * -----------------------------------------------------------------------
 * File:        import.php
 * Description	import own raids
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
 * This class imports own raids
 * @subpackage ManageCTRT
 */
class CTRT_ImportOwnRaids extends CTRT_ManageOwnRaids {

    var $_sampleXML = "
&lt;OwnRaids&gt;<br />
&nbsp;&nbsp;&lt;OwnRaid&gt;<br />
&nbsp;&nbsp;&nbsp;&nbsp;&lt;name&gt;<span class=\"positive\">Random Drop</span>&lt;/name&gt;<br />
&nbsp;&nbsp;&lt;/OwnRaid&gt;<br />
&lt;/OwnRaids&gt;";

    function CTRT_ImportOwnRaids()
    {
        parent::eqdkp_admin();

        $this->loadGlobals();
        $this->setMyMode('import');
        $this->daoOwnRaids = new CTRT_OwnRaids();

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
            'page_title'    => page_title($this->getUserLang('ctrt_own_raids_pagetitle')),
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
     * Import the own raids
     */
    function process_import () {
        $message = "";
        $ownRaids = $this->parser->xml_import(stripslashes($this->_in->get('xml')));
        if (isset($ownRaids["OWNRAIDS"]["OWNRAID"][0])) {
            $raids = $ownRaids["OWNRAIDS"]["OWNRAID"];
        } else {
            $raids = array(0 => array("NAME" => $ownRaids["OWNRAIDS"]["OWNRAID"]["NAME"]
                                ));
        }
        foreach ($raids as $raid) {
            if (!$this->daoOwnRaids->insert(array(
                                              'own_raid_name'=>$raid['NAME']
                                              ))) {
                /**
                 * This raid exists
                 */
                $message .= sprintf($this->getUserLang('ctrt_own_raids_duplicate')."<br />",$raid['NAME']);
            } else {
            /* @TODO
               IMPORT SUCCESS - LOG RECORD HERE */
                $message .= sprintf($this->getUserLang('ctrt_own_raids_success_add')."<br />",$raid['NAME']);
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
