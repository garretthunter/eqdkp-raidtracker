<?php
/**
 * Project:     CT_RaidTrackerImport [EQdkp Plugin]
 * License:     http://opensource.org/licenses/gpl-license.php
 * -----------------------------------------------------------------------
 * File:        import.php
 * Description	import aliases
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
 * This class imports one or multiple aliases
 * @subpackage ManageCTRT
 */
class CTRT_ImportAliases extends CTRT_ManageAlias {

    var $_sampleXML = "
&lt;PlayerAliases&gt;<br />
&nbsp;&nbsp;&lt;PlayerAlias&gt;<br />
&nbsp;&nbsp;&nbsp;&nbsp;&lt;alias&gt;<span class=\"positive\">Twinkone</span>&lt;/alias&gt;<br />
&nbsp;&nbsp;&nbsp;&nbsp;&lt;member&gt;<span class=\"positive\">Maintoon</span>&lt;/member&gt;<br />
&nbsp;&nbsp;&lt;/PlayerAlias&gt;<br />
&nbsp;&nbsp;&lt;PlayerAlias&gt;<br />
&nbsp;&nbsp;&nbsp;&nbsp;&lt;alias&gt;<span class=\"positive\">Twinktwo</span>&lt;/alias&gt;<br />
&nbsp;&nbsp;&nbsp;&nbsp;&lt;member&gt;<span class=\"positive\">Maintoon</span>&lt;/member&gt;<br />
&nbsp;&nbsp;&lt;/PlayerAlias&gt;<br />
&lt;/PlayerAliases&gt;";

    function CTRT_ImportAliases()
    {
        parent::eqdkp_admin();

        $this->loadGlobals();
        $this->setMyMode('import');
        $this->daoPlayerAlias = new CTRT_PlayerAlias();

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
            'page_title'    => page_title($this->getUserLang('ctrt_aliases_pagetitle')),
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
     * Import the aliases
     */
    function process_import () {
        $message = "";
        $playerAliases = $this->parser->xml_import(stripslashes($this->_in->get('xml')));
        if (isset($playerAliases['PLAYERALIASES']['PLAYERALIAS'][0])) {
			$aliases = $playerAliases['PLAYERALIASES']['PLAYERALIAS'];
		} else {
			$aliases = array(0 => array('ALIAS'  => $playerAliases['PLAYERALIASES']['PLAYERALIAS']['ALIAS'],
										'MEMBER' => $playerAliases['PLAYERALIASES']['PLAYERALIAS']['MEMBER'],
								));
		}
        foreach ($aliases as $alias) {

            // Make sure the both the member names are properly capitalized and formed
            $member_name = $this->daoPlayerAlias->getProperName($alias['MEMBER']);

            /**
             * The member name must exist
             */
            $sql = "SELECT member_id
                      FROM __members
                     WHERE (`member_name` = '".$this->_db->escape($member_name)."')";
            $member_id = $this->_db->query_first($sql);

            if ( isset($member_id) ) {
                /**
                 * Member name exists; insert the record
                 */
                if (!$this->daoPlayerAlias->insert (array('alias_name'=>$alias['ALIAS'],
                                                          'alias_member_id'=>$member_id))) {
                    // Error out if alias name exists

                    /**
                     * Get the member name for this alias
                     */
                    $member_name = $this->daoPlayerAlias->getMemberName($alias['ALIAS']);
					
                    $message .= sprintf($this->getUserLang("ctrt_aliases_duplicate")."<br />",ucwords($alias['ALIAS']),$member_name);
                } else {
                /* @TODO
                   IMPORT SUCCESS - LOG RECORD HERE */
                    $message .= sprintf($this->getUserLang('ctrt_aliases_success_add')."<br />",ucwords($alias['ALIAS']),$member_name);
                }
            } else {
                $message .= sprintf($this->getUserLang('ctrt_aliases_import_missing_member')."<br />",ucwords($alias['ALIAS']),$member_name);
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
