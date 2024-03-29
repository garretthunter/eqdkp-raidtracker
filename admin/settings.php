<?php
/**
 * Project:     CT_RaidTrackerImport [EQdkp Plugin]
 * License:     http://opensource.org/licenses/gpl-license.php
 * -----------------------------------------------------------------------
 * File:        settigns.php
 * Description  Update CT_RaidTracker Import configuration settings
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
 * CTRT_Settings manages the plugin configuration settings
 * @subpackage ManageCTRT
 */
class CTRT_Settings extends CTRT_ManageSettings
{
    function CTRT_Settings()
    {
        parent::eqdkp_admin();
        $this->loadGlobals();

        $this->assoc_buttons(array(
                    'submit' => array(
                'name'    => 'submit',
                          'process' => 'process_submit',
                          'check'   => 'a_members_man'),
                    'form' => array(
                            'name'    => '',
                            'process' => 'display_form',
                            'check'   => 'a_members_man'))
                );
    }

    function error_check()
    {
        global $user;

        $this->fv->is_number(array(
            'starting_dkp'      => $user->lang['fv_number'],
            'default_dkp'       => $user->lang['fv_number'],
            'start_raid_dkp'    => $user->lang['fv_number']
        ));

        return $this->fv->is_error();
    }

    /**
     * Process Submit
     */
    function process_submit()
    {
        // Update each config setting
        $this->config_set(array(
            'MinItemQuality'            => $this->getInput('min_item_quality'),
            'IgnoredLooter'             => ucwords($this->getInput('ignored_looter')),
            'AddLootDkpValuesCheckbox'  => ( $this->getInput('add_loot_dkp') == "Y" ) ? "1" : "0",
            'ConvertNames'              => ( $this->getInput('convert_names') == "Y" ) ? "1" : "0",
            'LootNoteEventTriggerCheck' => ( $this->getInput('event_trigger') == "Y" ) ? "1" : "0",
            'NewMemberDefaultRank'      => $this->getInput('default_rank_id','int'),
            'AttendanceFilter'          => $this->getInput('attendance_filter'),
            'SkipRaidsWithEmptyNote'    => ( $this->getInput('skip_empty_note_raids') == "Y" ) ? "1" : "0",
            'DefaultDKPCost'            => $this->getInput('default_dkp','int'),
            'StartingDKP'               => $this->getInput('starting_dkp','int'),
            'CreateStartRaid'           => ( $this->getInput('create_start_raid') == "Y" ) ? "1" : "0",
            'StartRaidDKP'              => $this->getInput('start_raid_dkp','int'),
            'UpdateMembers'             => ( $this->getInput('update_members') == "Y" ) ? "1" : "0",
            'OnlySimulate'              => ( $this->getInput('simulate') == "Y" ) ? "1" : "0"
        ));

        $log_action = array(
            'header'                        => '{L_ACTION_CTRT_CONFIG_UPDATED}',
            '{L_UPDATED_BY}'                => $this->admin_user);

        $this->log_insert(array(
            'log_type'   => $log_action['header'],
            'log_action' => $log_action)
        );

        $this->_tpl->assign_vars(array(
            // Form vars
            'L_UPDATE_SUCCESS' => $this->getUserLang("ctrt_settings_update_success")
            ));

        $this->display_form();
    }

    /**
     * Display form
     */
    function display_form()
    {
        $this->createMenus();

        /**
         * Get any sets associated with this category
         */
        $sql = "SELECT config_name, config_value
                  FROM __ctrt_config;";
        $results = $this->_db->query($sql);

        $ctrt_config = array();
        while ($config = $this->_db->fetch_record($results)) {
            $ctrt_config[$config['config_name']] = $config['config_value'];
        }
        $this->_db->free_result($results);

        /**
         * Create the Minimum Quality menu
         */
        $quality = array (
                CTRT_IQ_POOR       => $this->_user->lang['ctrt_iq_poor'],
                CTRT_IQ_COMMON     => $this->_user->lang['ctrt_iq_common'],
                CTRT_IQ_UNCOMMON   => $this->_user->lang['ctrt_iq_uncommon'],
                CTRT_IQ_RARE       => $this->_user->lang['ctrt_iq_rare'],
                CTRT_IQ_EPIC       => $this->_user->lang['ctrt_iq_epic'],
                CTRT_IQ_LEGENDARY  => $this->_user->lang['ctrt_iq_ledgendary']
            );

        foreach ($quality as $key => $value) {
            $this->_tpl->assign_block_vars('quality_row', array(
                'VALUE' => $key,
                'SELECTED' => ( $key == $ctrt_config['MinItemQuality']) ? ' selected="selected"' : '',
                'OPTION'   => $value)
                );
        }

        /**
         * Create the Rank menu
         */
        $sql = "SELECT rank_id, rank_name
                  FROM __member_ranks;";
        $results = $this->_db->query($sql);

        while ($rank = $this->_db->fetch_record($results)) {
            $this->_tpl->assign_block_vars('rank_row', array(
                'VALUE' => $rank['rank_id'],
                'SELECTED' => ( $rank['rank_id'] == $ctrt_config['NewMemberDefaultRank']) ? ' selected="selected"' : '',
                'OPTION'   => $rank['rank_name'])
                );
        }
        $this->_db->free_result($results);

        /**
         * Create the Attenance Filter menu
         */
        $attendance = array (
                CTRT_AF_NONE        => $this->_user->lang['ctrt_af_none'],
                CTRT_AF_LOOT_TIME   => $this->_user->lang['ctrt_af_loot_time'],
                CTRT_AF_BOSS_KILL   => $this->_user->lang['ctrt_af_boss_kill']
            );
        foreach ($attendance as $key => $value) {
            $this->_tpl->assign_block_vars('attendance_row', array(
                'VALUE' => $key,
                'SELECTED' => ( $key == $ctrt_config['AttendanceFilter']) ? ' selected="selected"' : '',
                'OPTION'   => $value)
                );
        }

        $this->_tpl->assign_vars(array(
            // Form vars
            'F_CONFIG' => $this->getControllerLink("settings","settings"),

            // Form values
            'IGNORED_LOOTER'        => $ctrt_config['IgnoredLooter'],
            'ADD_LOOT_DKP'          => ( $ctrt_config['AddLootDkpValuesCheckbox'] == "1") ? 'checked="checked"' : "",
            'CONVERT_NAMES'         => ( $ctrt_config['ConvertNames'] == "1") ? 'checked="checked"' : "",
            'EVENT_TRIGGER'         => ( $ctrt_config['LootNoteEventTriggerCheck'] == "1") ? 'checked="checked"' : "",
            'SKIP_EMPTY_NOTE_RAIDS' => ( $ctrt_config['SkipRaidsWithEmptyNote'] == "1") ? 'checked="checked"' : "",
            'DEFAULT_DKP'           => $ctrt_config['DefaultDKPCost'],
            'STARTING_DKP'          => $ctrt_config['StartingDKP'],
            'CREATE_START_RAID'     => ( $ctrt_config['CreateStartRaid'] == "1") ? 'checked="checked"' : "",
            'START_RAID_DKP'        => $ctrt_config['StartRaidDKP'],
            'UPDATE_MEMBERS'        => ( $ctrt_config['UpdateMembers'] == "1") ? 'checked="checked"' : "",
            'SIMULATE'              => ( $ctrt_config['OnlySimulate'] == "1") ? 'checked="checked"' : "",

            // Language
            'L_ALIASES'         => $this->getUserLang('ctrt_aliases'),
            'L_PLUGIN_TITLE'    => $this->getUserLang('ctrt'),

            'L_MIN_QUALITY'         => $this->getUserLang('ctrt_settings_min_quality'),
            'L_ADD_LOOT_DKP'        => $this->getUserLang('ctrt_settings_add_loot_dkp'),
            'L_IGNORED_LOOTER'      => $this->getUserLang('ctrt_settings_ignored_looter'),
            'L_CONVERT_NAMES'       => $this->getUserLang('ctrt_settings_convert_names'),
            'L_EVENT_TRIGGER'       => $this->getUserLang('ctrt_settings_loot_note_event_trigger'),
            'L_DEFAULT_RANK'        => $this->getUserLang('ctrt_settings_default_rank'),
            'L_ATTENDANCE_FILTER'   => $this->getUserLang('ctrt_settings_attendance_filter'),
            'L_SKIP_RAIDS'          => $this->getUserLang('ctrt_settings_skip_raids'),
            'L_DEFAULT_DKP'         => $this->getUserLang('ctrt_settings_default_dkp'),
            'L_STARTING_DKP'        => $this->getUserLang('ctrt_settings_starting_dkp'),
            'L_CREATE_START_RAID'   => $this->getUserLang('ctrt_settings_create_start_raid'),
            'L_START_RAID_DKP'      => $this->getUserLang('ctrt_settings_start_raid_dkp'),
            'L_UPDATE_MEMBERS'      => $this->getUserLang('ctrt_settings_update_members'),
            'L_SIMULATE'            => $this->getUserLang('ctrt_settings_simulate'),

            'L_MIN_QUALITY_HELP'        => $this->getUserLang('ctrt_settings_help_min_quality'),
            'L_ADD_LOOT_DKP_HELP'       => $this->getUserLang('ctrt_settings_help_add_loot_dkp'),
            'L_IGNORED_LOOTER_HELP'     => $this->getUserLang('ctrt_settings_help_ignored_looter'),
            'L_CONVERT_NAMES_HELP'      => $this->getUserLang('ctrt_settings_help_convert_names'),
            'L_EVENT_TRIGGER_HELP'      => $this->getUserLang('ctrt_settings_help_loot_note_event_trigger'),
            'L_DEFAULT_RANK_HELP'       => $this->getUserLang('ctrt_settings_help_default_rank'),
            'L_ATTENDANCE_FILTER_HELP'  => $this->getUserLang('ctrt_settings_help_attendance_filter'),
            'L_SKIP_RAIDS_HELP'         => $this->getUserLang('ctrt_settings_help_skip_raids'),
            'L_DEFAULT_DKP_HELP'        => $this->getUserLang('ctrt_settings_help_default_dkp'),
            'L_STARTING_DKP_HELP'       => $this->getUserLang('ctrt_settings_help_starting_dkp'),
            'L_CREATE_START_RAID_HELP'  => $this->getUserLang('ctrt_settings_help_create_start_raid'),
            'L_START_RAID_DKP_HELP'     => $this->getUserLang('ctrt_settings_help_start_raid_dkp'),
            'L_UPDATE_MEMBERS_HELP'     => $this->getUserLang('ctrt_settings_help_update_members'),
            'L_SIMULATE_HELP'           => $this->getUserLang('ctrt_settings_help_simulate'),

            'L_SETTINGS'                => $this->getUserLang('ctrt_settings_adminmenu'),
            'L_ADD_EVENT_TRIGGER'       => $this->getUserLang('ctrt_event_triggers_adminmenu'),
            'L_ADD_RAID_NOTE_TRIGGER'   => $this->getUserLang('ctrt_raid_note_triggers_adminmenu'),
            'L_ADD_CUSTOM_RAID'         => $this->getUserLang('ctrt_own_raids_adminmenu'),
            'L_ADD_IGNORED_ITEM'        => $this->getUserLang('ctrt_add_items_adminmenu'),
            'L_ADD_ADD_ITEM'            => $this->getUserLang('ctrt_ignore_items_adminmenu'),

            'L_RESET'           => $this->getUserLang('reset'),
            'L_UPDATE'          => $this->getUserLang('update'),

            // Form validation
            'FV_DEFAULT_DKP'        => $this->fv->generate_error('default_dkp'),
            'FV_STARTING_DKP'       => $this->fv->generate_error('starting_dkp'),
            'FV_START_RAID_DKP'     => $this->fv->generate_error('start_raid_dkp'),

        ));

        $this->_eqdkp->set_vars(array(
            'page_title'    => page_title($this->getUserLang('ctrt_settings_pagetitle')),
            'template_path' => $this->_pm->get_data('ctrt', 'template_path'),
            'template_file' => 'admin/settings.html',
            'display'       => true)
        );
    }

    /**
     * config_set
     * @param $config_name mixed
     * @param $config_value
     */
    function config_set($config_name, $config_value='')
    {
        if ( is_object($this->_db) )
        {
            if ( is_array($config_name) )
            {
                foreach ( $config_name as $d_name => $d_value )
                {
                    $this->config_set($d_name, $d_value);
                }
            }
            else
            {
                $sql = "UPDATE __ctrt_config
                        SET config_value='".$this->_db->escape($config_value)."'
                        WHERE (`config_name` = '".$config_name."')";
                $this->_db->query($sql);

                return true;
            }
        }

        return false;
    }
}
?>
