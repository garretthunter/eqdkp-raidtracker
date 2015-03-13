<?php
/**
 * Project:     CT_RaidTrackerImport [EQdkp Plugin]
 * License:     http://opensource.org/licenses/gpl-license.php
 * -----------------------------------------------------------------------
 * File:        list.php
 * Description  Lists own raids
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
 * Display a list of own raids. Provides mass delete.
 * @subpackage ManageCTRT
 */
class CTRT_ListOwnRaids extends CTRT_ManageOwnRaids
{
    function CTRT_ListOwnRaids()
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
         * Generate the list of own raids
         */
        $this->daoOwnRaids = new CTRT_OwnRaids;

        $ownRaids = $this->daoOwnRaids->getAll();
        foreach ($ownRaids as $row) {
            $this->_tpl->assign_block_vars('row', array(
                'ID'        => $row['own_raid_id'],
                'ROW_CLASS' => $this->_eqdkp->switch_row_class(),
                'COL1'      => $row['own_raid_name'],
                'U_ADD'     => $this->getControllerLink($this->getMyParam(),"add") . "&amp;" . URI_ID . "=".$row['own_raid_id'])
            );
        }

        $this->_tpl->assign_vars(array(
            // Form vars
            'F_CONFIG' => $this->getControllerLink($this->getMyParam(),"add"),

            // Labels
            'L_PLUGIN_TITLE'    => $this->getUserLang('ctrt'),

            // Column headings
            'L_COL1'            => $this->getUserLang('ctrt_own_raid'),
            'L_COL2'            => '',

            // Help text
            'L_HELP'            => $this->getUserLang('ctrt_own_raids_help'),
            'L_DELETE'          => $this->getUserLang('delete'),

        ));

        $this->_eqdkp->set_vars(array(
            'page_title'    => page_title($this->getUserLang('ctrt_own_raids_pagetitle')),
            'template_path' => $this->_pm->get_data('ctrt', 'template_path'),
            'template_file' => 'admin/list.html',
            'display'       => true)
        );
    }
}
