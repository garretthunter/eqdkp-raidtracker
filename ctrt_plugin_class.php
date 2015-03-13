<?php
/**
 * Project:     CT_RaidTrackerImport [EQdkp Plugin]
 * License:     http://opensource.org/licenses/gpl-license.php
 * -----------------------------------------------------------------------
 * File:        ctrt_plugin_class.php
 * Began:       Sat Jan 4 2003
 * Date:        $Date: 2009-10-18 10:12:45 +0000 (Sun, 18 Oct 2009) $
 * -----------------------------------------------------------------------
 * @author 		freddy <CTRaidTrackerImport@freddy.eu.org>
 * @author 		Garrett Hunter <loganfive@blacktower.com>
 * @copyright   2002-2008 The EQdkp Project Team
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
 * ctrt_Plugin_Class to handle installation & uninstallation of the plugin
 * @subpackage Installation
 */
class ctrt_Plugin_Class extends EQdkp_Plugin
{
    /**
     * @var array plugin configuration values
     */
    var $_ctrt_config = array();
    var $_ctrt_controller;

    function ctrt_plugin_class($pm)
    {
        global $eqdkp_root_path, $user, $db;

        $this->eqdkp_plugin($pm);
        $this->pm->get_language_pack('ctrt');

        /**
         * Log Events
         */
        $this->add_log_action('{L_ACTION_CTRT_CONFIG_UPDATED}', $user->lang['action_ctrt_config_updated']); //gehTODO - Do I need to add the $user->lang[] string here? seemded redundant based on code review of logs.php
        $this->add_log_action('{L_ACTION_CTRT_ALIAS_ADDED}',    $user->lang['action_ctrt_alias_added']);
        $this->add_log_action('{L_ACTION_CTRT_ALIAS_UPDATED}',  $user->lang['action_ctrt_alias_updated']);
        $this->add_log_action('{L_ACTION_CTRT_ALIAS_DELETED}',  $user->lang['action_ctrt_alias_deleted']);

        $this->add_data(array(
            'name'          => 'CT_RaidTrackerImport',
            'code'          => 'ctrt',
            'path'          => 'ctrt',
            'contact'       => 'loganfive@blacktower.com',
            'template_path' => 'plugins/ctrt/templates/',
            'version'       => '2.0.0')
        );

        $this->add_menu('admin_menu', $this->gen_admin_menu());

        /**
         * Since EQDKP calls this page from every other page within the site, we must give this file a name that will not collide with any other.
         */
        require_once($eqdkp_root_path.'plugins/'.$this->get_data('path').'/settings.php');

        // Define installation
        // -----------------------------------------------------
        $this->add_sql(SQL_INSTALL, "CREATE TABLE IF NOT EXISTS __ctrt_config (
                                              `config_id` smallint(5) unsigned NOT NULL auto_increment,
                                              `config_name` varchar(255) NOT NULL default '',
                                              `config_value` text NOT NULL,
                                              PRIMARY KEY  (`config_id`)
                                            );");
        $this->add_sql(SQL_INSTALL, "CREATE TABLE IF NOT EXISTS __ctrt_aliases (
                                              `alias_id` smallint unsigned NOT NULL auto_increment,
                                              `alias_member_id` mediumint NOT NULL,
                                              `alias_name` varchar(50) NOT NULL,
                                              PRIMARY KEY  (`alias_id`),
                                              UNIQUE KEY `alias_name` (`alias_name`)
                                            );");
        $this->add_sql(SQL_INSTALL, "CREATE TABLE IF NOT EXISTS __ctrt_event_triggers (
                                              `event_trigger_id` smallint unsigned NOT NULL auto_increment,
                                              `event_trigger_name` varchar(50) NOT NULL,
                                              `event_trigger_result` varchar(50) NOT NULL,
                                              PRIMARY KEY  (`event_trigger_id`),
                                              UNIQUE KEY `event_trigger_name` (`event_trigger_name`)
                                            );");
        $this->add_sql(SQL_INSTALL, "CREATE TABLE IF NOT EXISTS __ctrt_raid_note_triggers (
                                              `raid_note_trigger_id` smallint unsigned NOT NULL auto_increment,
                                              `raid_note_trigger_name` varchar(50) NOT NULL,
                                              `raid_note_trigger_result` varchar(50) NOT NULL,
                                              PRIMARY KEY  (`raid_note_trigger_id`),
                                              UNIQUE KEY `raid_note_trigger_name` (`raid_note_trigger_name`)
                                            );");
        $this->add_sql(SQL_INSTALL, "CREATE TABLE IF NOT EXISTS __ctrt_own_raids (
                                              `own_raid_id` smallint unsigned NOT NULL auto_increment,
                                              `own_raid_name` varchar(50) NOT NULL,
                                              PRIMARY KEY  (`own_raid_id`),
                                              UNIQUE KEY `own_raid_name` (`own_raid_name`)
                                            );");
        $this->add_sql(SQL_INSTALL, "CREATE TABLE IF NOT EXISTS __ctrt_items (
                                              `items_id` smallint unsigned NOT NULL auto_increment,
                                              `items_name` varchar(50) NOT NULL,
                                              `items_wowid` mediumint NOT NULL,
                                              `items_quality` smallint NOT NULL,
                                              `items_ctrt_type` tinyint(1) NOT NULL,
                                              PRIMARY KEY  (`items_id`),
                                              UNIQUE KEY `items_name` (`items_name`),
                                              UNIQUE KEY `items_wowid` (`items_wowid`)
                                            );");
        $this->add_sql(SQL_INSTALL, "ALTER TABLE __items ADD `item_ctrt_wowitemid` INT(10) UNSIGNED NULL;");

        /**
         * Installation defaults
         */
        $this->_ctrt_config = array(
            'MinItemQuality'            => CTRT_IQ_RARE,        // Sets the minimum Item Quality of Items to get parsed (4 = Epic, 3 = Rare, 2 = uncommon)
            'IgnoredLooter'             => "disenchanted",      // Set a "Looter" which should be ignored
            'AddLootDkpValuesCheckbox'  => 0,                   // Here you can set the default status of the "Add Item value/attendees" Checkbox
            'ConvertNames'              => 0,                   // Setting it to true will convert e.g. "Âvâtâr" to "Avatar".
            'LootNoteEventTriggerCheck' => 0,                   // Will Check for Event Triggers in the Loot Notes (e.g. if you have events called "MC (Lucifron), MC (Magmadar), ..."
                                                                // and only want to log one raid.
            'NewMemberDefaultRank'      => "",                  // Here you can set the default Rank of new Members (e.g. Member).
            'AttendanceFilter'          => CTRT_AF_BOSS_KILL,   // Sets the type of attendance filtering
                                                                // 0 = None ( if a person was in the raid they are added to all events )
                                                                // 1 = Loot Time ( if a person was in the raid when the loot attached to an event was picked up they are added to the event )
                                                                // 2 = Boss Kill Time ( if a person was in the raid when a boss attached to an event was killed they are addedto the event )
            'SkipRaidsWithEmptyNote'    => 1,                   // This will skip any raids which have an empty Raid Note
            'DefaultDKPCost'            => 0,                   // The cost of items with no dkp value
            'StartingDKP'               => 0,                   // When creating a new member, if this is > 0, it will add an adjustment to the member as starting dkp
            'CreateStartRaid'           => 0,                   // Set this to true to create a Starting Raid when using boss kill time.
            'StartRaidDKP'              => 0,                   // Default starting Raid DKP.
            'UpdateMembers'             => 1,                   // Should the plugin update the members details
            'OnlySimulate'              => 0                    // Change this to true, to not insert/alter any information in the database
        );

        foreach ($this->_ctrt_config as $key => $value) {
            $this->add_sql(SQL_INSTALL, "INSERT INTO __ctrt_config ( `config_id` , `config_name` , `config_value` )
                                         VALUES (NULL, '".$key."', '".$value."' );");
        }

        /**
         * Define uninstallation
         */
        $this->add_sql(SQL_UNINSTALL, "DROP TABLE IF EXISTS __ctrt_config;");
        $this->add_sql(SQL_UNINSTALL, "DROP TABLE IF EXISTS __ctrt_aliases;");
        $this->add_sql(SQL_UNINSTALL, "DROP TABLE IF EXISTS __ctrt_event_triggers;");
        $this->add_sql(SQL_UNINSTALL, "DROP TABLE IF EXISTS __ctrt_raid_note_triggers;");
        $this->add_sql(SQL_UNINSTALL, "DROP TABLE IF EXISTS __ctrt_own_raids;");
        $this->add_sql(SQL_UNINSTALL, "DROP TABLE IF EXISTS __ctrt_items;");
        $this->add_sql(SQL_UNINSTALL, "ALTER TABLE __items DROP `item_ctrt_wowitemid`;");

        // Legacy cleanup required when I combined these tables
        $this->add_sql(SQL_UNINSTALL, "DROP TABLE IF EXISTS __ctrt_ignored_items;");
        $this->add_sql(SQL_UNINSTALL, "DROP TABLE IF EXISTS __ctrt_add_items;");
    }

    function gen_admin_menu()
    {
        if ( $this->pm->check(PLUGIN_INSTALLED, 'ctrt') )
        {
            global $db, $user, $eqdkp, $eqdkp_root_path;

            $admin_menu = array(
                    'ctrt' => array(
                              0 => $user->lang['ctrt_adminmenu_title'],
                              1 => array('link' => plugin_path('ctrt', 'manage_settings.php'),
                                         'text' => $user->lang['ctrt_settings_adminmenu'],
                                         'check' => 'a_raid_add'),
                              2 => array('link' => plugin_path('ctrt', 'index.php'),
                                         'text' => $user->lang['import_ctrt_data'],
                                         'check' => 'a_raid_add'),
                ),
                    'raids' => array(
                                    0 => $user->lang['raids'],
                                    1 => array('link' => path_default('admin/addraid.php'),
                                               'text' => $user->lang['add'],
                                               'check' => 'a_raid_add'),
                                    2 => array('link' => plugin_path('ctrt', 'index.php'),
                                               'text' => $user->lang['import_ctrt_data'],
                                               'check' => 'a_raid_add'),
                                    3 => array('link' => path_default('admin/listraids.php'),
                                               'text' => $user->lang['list'],
                                               'check' => 'a_raid_')
                )
             );

            return $admin_menu;
        }
        return;
    }

}
?>