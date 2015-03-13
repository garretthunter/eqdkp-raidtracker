<?php
/**
 * Project:     CT_RaidTrackerImport [EQdkp Plugin]
 * License:     http://opensource.org/licenses/gpl-license.php
 * -----------------------------------------------------------------------
 * File:        add.php
 * Description	add and delete ignored items
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
 * This class handles ignore items add & update
 * @subpackage ManageCTRT
 */
class CTRT_AddIgnoreItem extends CTRT_ManageIgnoreItems
{
    var $data = array();           // Holds own_raid data if URI_ID is set         @var data

    function CTRT_AddIgnoreItem()
    {
        parent::eqdkp_admin();

        $this->loadGlobals();
        $this->setMyMode('add');
        $this->daoIgnoreItem = new CTRT_Items;

		$this->data = array(
			'items_id'	=> 0,
			'item'		=> $this->_in->get('item')
		);

        // Vars used to confirm deletion
        $confirm_text = $this->getUserLang('ctrt_item_confirm_delete');
        $items_ids = array();
        $items_names = array();
        if ( $this->_in->exists('delete') )
        {
            if ( $this->_in->exists('compare_ids') )
            {
                foreach ( $this->_in->getArray('compare_ids','int') as $keyID )
                {
                    $item = $this->daoIgnoreItem->getByPrimaryKey($keyID);
                    $items_ids[] = $item[0]['items_id'];
                    $items_names[] = $item[0]['items_name'];
                }

                $names = implode(', ', $items_names);
                $ids = implode(', ', $items_ids);

                $confirm_text .= '<br />' . $names;
            }
            else
            {
                $failure_message = sprintf($this->getUserLang("ctrt_item_not_selected"));
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
            'url_id'        => ( sizeof($items_ids) > 0 ) ? $ids : (( $this->_in->exists(URI_ID) ) ? $this->_in->get(URI_ID) : ''),
            'script_name'   => $this->getControllerLink($this->getMyParam(),$this->getMyMode()))
        );

        $this->assoc_buttons(array(
            'add' => array(
                'name'    => 'add',
                'process' => 'process_add',
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

        // Build the item array
        // ---------------------------------------------------------
        if ( !empty($this->url_id) )
        {
            $row = $this->daoIgnoredItem->getByPrimaryKey($this->url_id);
            $this->data = array(
                'items_id'  => $row[0]['items_id'],
                'item'   	=> $this->_in->get('item', $row[0]['items_name']),
            );
        }
    }

    function error_check()
    {
        require_once('admin/ctrt_common.php');
        if ( ($this->_in->exists('add')) ) {
            // required field
            $this->fv->is_filled('item', $this->getUserLang('fv_required_name'));

            if (!isset($this->fv->errors['item'])) {
                // must be a valid item (hopefully wowhead is current)
                $ignore_item = getWoWHeadItem ($this->_in->get('item'));
                if (isset($ignore_item['error'])) {
                    $this->fv->errors['item'] = sprintf($this->getUserLang('ctrt_item_not_found'), $this->_in->get('item'));
                } else {
                    // cannot already exist in DB
                    if ($this->daoIgnoreItem->isDuplicate(array('items_wowid' => $ignore_item['wowid']))) {
                        $this->fv->errors['item'] = sprintf($this->getUserLang('ctrt_item_duplicate'), $_POST['item']);
                    }
                }
            }
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
            $old_data = $this->daoIgnoreItem->getByPrimaryKey($id);

            // Remove the record
            $this->daoIgnoreItem->deleteById($id);

            // Append success message
            $success_message .= sprintf($this->getUserLang("ctrt_item_success_delete"),$old_data[0]['items_name']) . '<br />';
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
     * Add an own raid record
     */
    function process_add()
    {
        include_once('admin/ctrt_common.php');

        $ignore_item = getWoWHeadItem ($this->_in->get('item'));

        if (!$this->daoIgnoreItem->insert(array('items_wowid' => $ignore_item['wowid'],
                                                'items_name' => $ignore_item['name'],
                                                'items_quality' => $ignore_item['quality'],
                                                'items_ctrt_type' => CTRT_IGNORE_ITEM
                                                  ))) {
            // Error out if the item is a duplicate
            $failure_message = sprintf($this->getUserLang("ctrt_item_duplicate"),$ignore_item['name'].' (#'.$ignore_item['wowid'].')');
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
                'header'                      => '{L_ACTION_CTRT_IGNORE_ITEM_ADDED}',
                '{L_CTRT_LABEL_IGNORE_ITEM}'  => $ignore_item['name'].' (#'.$ignore_item['wowid'].')',

                '{L_ADDED_BY}'                => $this->admin_user);

            $this->log_insert(array(
                'log_type'   => $log_action['header'],
                'log_action' => $log_action)
            );

            /**
             * Success message
             */
            $success_message = sprintf($this->getUserLang('ctrt_item_success_add'), $ignore_item['name'].' (#'.$ignore_item['wowid'].')');
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
            'ITEM'              => $this->data['item'],

            // Labels
            'L_PLUGIN_TITLE' => $this->getUserLang('ctrt'),
            'L_ITEM_WOW'     => $this->getUserLang('ctrt_item_wow'),

            // Buttons
            'L_ADD'     => $this->getUserLang('add'),

            /**
             * Help text
             */
            'L_HELP'     => $this->getUserLang('ctrt_ignore_items_help'),
            'L_HELP_WOW' => $this->getUserLang('ctrt_ignore_items_help_wow'),

            // Form validation
            'FV_NAME'        => $this->fv->generate_error('item'),
        ));

        $this->_eqdkp->set_vars(array(
            'page_title'    => page_title($this->getUserLang('ctrt_ignore_items_pagetitle')),
            'template_path' => $this->_pm->get_data('ctrt', 'template_path'),
            'template_file' => 'admin/additem.html',
            'display'       => true)
        );
    }
}
