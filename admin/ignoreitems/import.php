<?php
/**
 * Project:     CT_RaidTrackerImport [EQdkp Plugin]
 * License:     http://opensource.org/licenses/gpl-license.php
 * -----------------------------------------------------------------------
 * File:        import.php
 * Description	import items to always ignore
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
 * This class imports ignore items
 * @subpackage ManageCTRT
 */
class CTRT_ImportIgnoreItems extends CTRT_ManageIgnoreItems {

    var $_sampleXML = "
&lt;IgnoreItems&gt;<br />
&nbsp;&nbsp;&lt;IgnoreItem&gt;<br />
&nbsp;&nbsp;&nbsp;&nbsp;&lt;item&gt;<span class=\"positive\">Shadowsong Amethyst</span>&lt;/item&gt;<br />
&nbsp;&nbsp;&lt;/IgnoreItem&gt;<br />
&nbsp;&nbsp;&lt;IgnoreItem&gt;<br />
&nbsp;&nbsp;&nbsp;&nbsp;&lt;item&gt;<span class=\"positive\">20725</span>&lt;/item&gt;<br />
&nbsp;&nbsp;&lt;/IgnoreItem&gt;<br />
&lt;/IgnoreItems&gt;";


    function CTRT_ImportIgnoreItems()
    {
        parent::eqdkp_admin();

        $this->loadGlobals();
        $this->setMyMode('import');
        $this->daoIgnoreItem = new CTRT_Items();

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
            'FV_INVALID_XML'    => $this->fv->generate_error('xmlError'),
        ));

        $this->_eqdkp->set_vars(array(
            'page_title'    => page_title($this->getUserLang('ctrt_ignore_items_pagetitle')),
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
     * Import the items
     */
    function process_import () {
        $message = "";
        $ignoreItems = $this->parser->xml_import(stripslashes($this->_in->get('xml')));

        if (isset($ignoreItems["IGNOREITEMS"]["IGNOREITEM"][0])) {
            $items = $ignoreItems["IGNOREITEMS"]["IGNOREITEM"];
        } else {
            $items = array(0 => array("ITEM" => $addItems["IGNOREITEMS"]["IGNOREITEM"]["ITEM"]
                                ));
            }
            foreach ($items as $item) {
                // Validate each item
	        	require_once('admin/ctrt_common.php');
                $WHItem = getWoWHeadItem (trim($item['ITEM']));
                if (!isset($WHItem['error'])) {
                    if (!$this->daoIgnoreItem->insert(array('items_wowid' => $WHItem['wowid'],
                                                            'items_name' => $WHItem['name'],
                                                            'items_quality' => $WHItem['quality'],
                                                            'items_ctrt_type' => CTRT_IGNORE_ITEM
                                                              ))) {
                        // This item exists
                    $message .= sprintf($this->getUserLang('ctrt_item_duplicate').'<br />',$WHItem['name'].' (#'.$WHItem['wowid'].')');

                    } else {
                    /* @TODO
                       IMPORT SUCCESS - LOG RECORD HERE */
                        $message .= sprintf($this->getUserLang('ctrt_item_success_add').'<br />',$WHItem['name'].' (#'.$WHItem['wowid'].')');
                    }

                } else {
                    // Invalid item
                $message .= sprintf($this->_user->lang['ctrt_item_not_found']."<br />", $WHItem['name'].' (#'.$WHItem['wowid'].')');
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