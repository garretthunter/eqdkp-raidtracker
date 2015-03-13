<?php
/**
 * English language file for CT_RaidTracker Import
 *
 * @category Plugins
 * @package CT_RaidTrackerImport
 * @copyright (c) 2006, EQdkp <http://www.eqdkp.com>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 *
 * @author freddy <CTRaidTrackerImport@freddy.eu.org>
 * @author loganfive <loganfive@blacktower.com>
 * $Rev: 310 $ $Date: 2009-10-18 10:12:45 +0000 (Sun, 18 Oct 2009) $
 */

if ( !defined('EQDKP_INC') )
{
    header('HTTP/1.0 404 Not Found');
    exit;
}

// Initialize the language array if it isn't already
if (empty($lang) || !is_array($lang))
{
    $lang = array();
}

// %1\$<type> prevents a possible error in strings caused
//      by another language re-ordering the variables
// $s is a string, $d is an integer, $f is a float

$lang = array_merge($lang, array(

    'ctrt'                           => "CT_RaidTracker Import",
    'import_ctrt_data'               => "Import DKP String",
    'ctrt_step1_pagetitle'           => "CTRT Import",
    'ctrt_step1_th'                  => "Paste the Log Below",
    'ctrt_step1_invalidstring_title' => "Invalid DKP String",
    'ctrt_step1_invalidstring_msg'   => "The DKP String is not valid.",
    'ctrt_step1_button_parselog'     => "Parse Log",

    'ctrt_step2_pagetitle'           => "CTRT Import",
    'ctrt_step2_foundraids'          => "Found Raids",
    'ctrt_step2_event'               => "Event:",
    'ctrt_step2_raidnote'            => "Raid Note:",
    'ctrt_step2_dkpvalue'            => "DKP Value:",
    'ctrt_step2_raidtime'            => "Raid Time:",
    'ctrt_step2_attendees'           => "Attendees",

    'ctrt_step2_itemname'            => "Item Name:",
    'ctrt_step2_itemid'              => "Item ID:",
    'ctrt_step2_looter'              => "Looter:",
    'ctrt_step2_itemdkpvalue'        => "DKP Value:",
    'ctrt_step2_dkpvaluetip'         => "Add Item value/attendees (Zero DKP)",
    'ctrt_step2_insertraids'         => "Insert Raid(s)",
    'ctrt_step2_raidsdropsdetails'   => "Raid/Drop Details",

    'ctrt_step3_pagetitle'           => "CTRT Import",
    'ctrt_step3_title'               => "Action log<br>\n",
    'ctrt_step3_alreadyexist'        => "%s (%s, %s DKP) was already added, skipping<br>\n",
    'ctrt_step3_emptyraidnote'       => "%s (%s, %s DKP) has no raid note, skipping<br>\n",
    'ctrt_step3_raidadded'           => "%s (%s, %s DKP) was added<br>\n",
    'ctrt_step3_adjadded'            => "An adjustment of %s DKP was added to %s<br>\n",
    'ctrt_step3_memberadded'         => "%s (race: %s, class: %s, level: %s, rank: %s) was added to the Members<br>\n",
    'ctrt_step3_memberupdated'       => "%s (race: %s, class: %s, level: %s, rank: %s) was updated to (race: %s, class: %s, level: %s, rank: %s)<br>\n",
    'ctrt_step3_memberlevelupdated'  => "Looks like %s gained some levels. Updated from %s to %s<br>\n",
    'ctrt_step3_attendeesadded'      => "%s attendees were added<br>\n",
    'ctrt_step3_lootadded'           => "%s (%s DKP) was added to %s<br>\n",

/**
 * added by Garrett Hunter / loganfive@blacktower.com
 */
    'ctrt_adminmenu_title'   => "CTRT Import",

/**
 * Settings
 */
    'ctrt_settings_pagetitle'           => "CTRT Import Settings",
    'ctrt_settings_adminmenu'           => "Settings",

    'ctrt_settings_min_quality'               => "Minimum Quality",
    'ctrt_settings_add_loot_dkp'              => "Add Loot DKP",
    'ctrt_settings_ignored_looter'            => "Ignored Looter",
    'ctrt_settings_convert_names'             => "Convert Names",
    'ctrt_settings_loot_note_event_trigger'   => "Loot Note Event Trigger",
    'ctrt_settings_default_rank'              => "Default Rank",
    'ctrt_settings_attendance_filter'         => "Attendance Filter",
    'ctrt_settings_skip_raids'                => "Skip Raids With Empty Note",
    'ctrt_settings_default_dkp'               => "Default DKP Cost",
    'ctrt_settings_starting_dkp'              => "Starting DKP",
    'ctrt_settings_create_start_raid'         => "Create Start Raid",
    'ctrt_settings_start_raid_dkp'            => "Start Raid DKP",
    'ctrt_settings_update_members'            => "Update Members",
    'ctrt_settings_simulate'                  => "Simulate",
    'ctrt_settings_simulate_warning'          => "Simulation ON - This is what would be done:<br><br>",

    'ctrt_settings_help_min_quality'              => "Minimum Item Quality of Items to be parsed.",
    'ctrt_settings_help_add_loot_dkp'             => "Check to set the default status of the \"Add Item value/attendees\" Checkbox.",
    'ctrt_settings_help_ignored_looter'           => "The name of the Looter to be ignored.",
    'ctrt_settings_help_convert_names'            => "Check to convert names (e.g. \"Âvâtâr\" to \"Avatar\".)",
    'ctrt_settings_help_loot_note_event_trigger'  => "Check for Event Triggers in the Loot Notes (e.g. if you have events called \"MC (Lucifron), MC (Magmadar), ...\" &amp; only want to log one raid.)",
    'ctrt_settings_help_default_rank'             => "Default Rank for new Members that are added when importing a raid.",
    'ctrt_settings_help_attendance_filter'        => "Sets the type of attendance filtering.",
    'ctrt_settings_help_skip_raids'               => "Check to ignore raids with an empty Raid Note.",
    'ctrt_settings_help_default_dkp'              => "The cost of items with no dkp value.",
    'ctrt_settings_help_starting_dkp'             => "When creating a new member, if this is &gt; 0, it will add an adjustment to the member as starting dkp.",
    'ctrt_settings_help_create_start_raid'        => "Check to create a Starting Raid when the Attendance Filter is set to Boss Kill.",
    'ctrt_settings_help_start_raid_dkp'           => "Default starting Raid dkp.",
    'ctrt_settings_help_update_members'           => "Update member details when uploading a raid.",
    'ctrt_settings_help_simulate'                 => "Check to not insert/alter any information in the database.",

/**
 * Event Triggers
 */
    'ctrt_event_triggers_pagetitle'     => "CTRT Import Event Triggers",
    'ctrt_event_triggers_adminmenu'     => "Event Triggers",

    'ctrt_event_triggers_help'        => "Check for Event Triggers in the Loot Notes (e.g. if you have events called \"MC (Lucifron), MC (Magmadar), ...\" &amp; only want to log one raid.)",
    'ctrt_event_triggers_help_name'   => "Enter the name of the event that will be replaced with the result.",
    'ctrt_event_triggers_help_result' => "Enter the text you want to see in place of the trigger",


/**
 * Aliases
 */
    'ctrt_aliases_pagetitle'             => "CTRT Import Aliases",
    'ctrt_aliases_adminmenu'             => "Aliases",

    'ctrt_aliases_help'                => "Use an alias when a player brings a different character to a raid and you want to track DKP for only one character (e.g. if a Twink of the Mainchar helps out, but the Mainchar should get the DKP Points)",
    'ctrt_aliases_help_name'           => "Whenever this name is encountered, it will be replaced by the Member name below.",
    'ctrt_aliases_help_member'         => "The member who will receive the DKP from the alias.",
    'ctrt_aliases_help_export'         => "Copy the text below and paste into your favorite text editor to save.",
    'ctrt_aliases_help_import'         => "Paste the text below to import your aliases.",
    'ctrt_aliases_help_import_format'  => "The import is case insensitive and should be in the form",

/**
 * Raid Note Triggers
 */
    'ctrt_raid_note_triggers_pagetitle'  => "CTRT Import Raid Note Triggers",
    'ctrt_raid_note_triggers_adminmenu'  => "Raid Note Triggers",

    'ctrt_raid_note_triggers_help'        => "Here you can set the triggers for the eqDKP Raid Note (CT_RaidRracker Raid note and the Loots Notes will be parsed (Loot Notes will override the Raid Note))",
    'ctrt_raid_note_triggers_help_name'   => "Enter the name of the raid note that will be replaced with the result.",
    'ctrt_raid_note_triggers_help_result' => "Enter the text you want to see in place of the trigger",

/**
 * Own Raids
 */
    'ctrt_own_raids_pagetitle'           => "CTRT Import Own Raids",
    'ctrt_own_raids_adminmenu'           => "Own Raids",

    'ctrt_own_raids_help'        => "Raid notes which should be handled as a \"own raid\" everytime",
    'ctrt_own_raids_help_name'   => "Enter the name of the raid that will be used.",

/**
 * Always Add Items
 */
    'ctrt_add_items_pagetitle'           => "CTRT Import Add Items",
    'ctrt_add_items_adminmenu'           => "Add Items",

    'ctrt_add_items_help'     => "These items will always be added regardless of the loot threshold set in CT_RaidtrackerImport.",
    'ctrt_add_items_help_wow' => "Enter the WoW item ID or name to be added.",

/**
 * Always Ignore Items
 */
    'ctrt_ignore_items_pagetitle'        => "CTRT Import Ignore Items",
    'ctrt_ignore_items_adminmenu'        => "Ignore Items",

    'ctrt_ignore_items_help'     => "These items will be ignored regardless of the loot threshold set in CT_RaidtrackerImport.",
    'ctrt_ignore_items_help_wow' => "Enter the WoW item ID or name to be ignored.",

/**
 * Common Translations
 */
    'ctrt_adminmenu_add'     => "Add",
    'ctrt_adminmenu_list'    => "List",
    'ctrt_adminmenu_export'  => "Export",
    'ctrt_adminmenu_import'  => "Import",

    'ctrt_export_help'         => "Copy the text below and paste into your favorite text editor to save.",
    'ctrt_import_help'         => "Paste the text below to import your data.",
    'ctrt_import_format_help'  => "The import is case insensitive and should be in the form",


/**
 * Item Quality
 */
    'ctrt_iq_poor'           => "Poor",
    'ctrt_iq_common'         => "Common",
    'ctrt_iq_uncommon'       => "Uncommon",
    'ctrt_iq_rare'           => "Rare",
    'ctrt_iq_epic'           => "Epic",
    'ctrt_iq_ledgendary'     => "Legendary",

/**
 * Attendance Filter
 */
    'ctrt_af_none'           => "None",
    'ctrt_af_loot_time'      => "Loot Time",
    'ctrt_af_boss_kill'      => "Boss Kill",

/**
 * Buttons
 */

/**
 * Labels
 */
    'ctrt_alias'         => "Alias",
    'ctrt_aliases'       => "Aliases",
    'ctrt_result'        => "Result",
    'ctrt_trigger'       => "Trigger",
    'ctrt_import'        => "Import",
    'ctrt_raid_note'     => "Raid Note",
    'ctrt_own_raid'      => "Own Raid",
    'ctrt_item_wow'      => "WoW Item",
    'ctrt_item_wow_id'   => "WoW Item ID",

/*
 * User messages
 */
    'ctrt_settings_update_success'   => "Settings updated!!",

    'ctrt_aliases_confirm_delete'      		=> "Are you sure you want to delete the following aliases?",
    'ctrt_aliases_not_selected'         	=> "No alias selected.",
    'ctrt_aliases_duplicate'           		=> "Cannot add <span class=\"negative\">%s</span>; member <span class=\"negative\">%s</span> is assigned that alias.",
    'ctrt_aliases_success_add'         		=> "Alias <span class=\"positive\">%s</span> added for member <span class=\"positive\">%s</span>.",
    'ctrt_aliases_success_update'      		=> "Alias <span class=\"positive\">%s</span> updated to <span class=\"positive\">%s</span> for member <span class=\"positive\">%s</span>.",
    'ctrt_aliases_success_delete'           => "Alias <span class=\"positive\">%s</span> deleted from member <span class=\"positive\">%s</span>.",
    'ctrt_aliases_import_missing_member' 	=> "Cannot create alias <span class=\"negative\">%s</span>. Member <span class=\"negative\">%s</span> does not exist",

    'ctrt_trigger_confirm_delete'    => "Are you sure you want to delete the following triggers?",
    'ctrt_trigger_not_selected'      => "No triggers selected.",
    'ctrt_trigger_duplicate'         => "Trigger <span class=\"negative\">%s</span> for <span class=\"negative\">%s</span> already exists.",
    'ctrt_trigger_success_add'       => "Trigger <span class=\"positive\">%s</span> added for <span class=\"positive\">%s</span>.",
    'ctrt_trigger_success_update'    => "Trigger updated.",
    'ctrt_trigger_success_delete'    => "Trigger <span class=\"positive\">%s</span> deleted for <span class=\"positive\">%s</span>.",

    'ctrt_own_raids_confirm_delete'   => "Are you sure you want to delete the following custom raids?",
    'ctrt_own_raids_not_selected'     => "No custom raids selected.",
    'ctrt_own_raids_duplicate'        => "Custom raid <span class=\"negative\">%s</span> already exists.",
    'ctrt_own_raids_success_add'      => "Custom raid <span class=\"positive\">%s</span> added.",
    'ctrt_own_raids_success_update'   => "Custom raid updated.",
    'ctrt_own_raids_success_delete'   => "Custom raid <span class=\"positive\">%s</span> deleted.",

    'ctrt_item_confirm_delete'   => "Are you sure you want to delete the following items?",
    'ctrt_item_not_selected'     => "No items selected.",
    'ctrt_item_duplicate'        => "Item <span class=\"negative\">%s</span> already exists.",
    'ctrt_item_not_found'        => "Item <span class=\"negative\">%s</span> not found!.",
    'ctrt_item_success_add'      => "Item <span class=\"positive\">%s</span> added.",
    'ctrt_item_success_update'   => "Item updated.",
    'ctrt_item_success_delete'   => "Item <span class=\"positive\">%s</span> deleted.",

/**
 * Form validation messages
 */
    'ctrt_fv_invalid_xml'        => "Cannont import XML, invalid string. Compare with sample provided.",

/**
 * Log Actions
 */
    'action_ctrt_config_updated' => "CT_RaidTracker Import Updated",
    'action_ctrt_alias_added'    => "CT_RaidTracker Import Alias Added",
    'action_ctrt_alias_updated'  => "CT_RaidTracker Import Alias Updated",
    'action_ctrt_alias_deleted'  => "CT_RaidTracker Import Alias Deleted",
    'ctrt_alias_name_before'     => "Alias Before",
    'ctrt_alias_name_after'      => "Alias After",
    'ctrt_member_name_before'    => "Member Before",
    'ctrt_member_name_after'     => "Member After",

/**
 * Verbose Log Messages
 */
    'vlog_ctrt_config_updated'   => "%s updated CTRT Import config.",
    'vlog_ctrt_alias_added'      => "%s added CTRT Import alias '%s' for member '%s'.",
    'vlog_ctrt_alias_updated'    => "%s updated CTRT Import alias '%s' for member '%s'.",
    'vlog_ctrt_alias_deleted'    => "%s deleted CTRT Import alias '%s' from member '%s'.",

/**
 * Log labels
 */
    'ctrt_label_alias_name'  => "Alias Name",
    'ctrt_label_member_name' => "Member Name",

/*
 * Race Names
 */
    'blood_elf'  => 'Blood Elf',
    'draenei'    => 'Draenei',
    'dwarf'      => 'Dwarf',
    'gnome'      => 'Gnome',
    'human'      => 'Human',
    'night_elf'  => 'Night Elf',
    'orc'        => 'Orc',
    'undead'     => 'Undead',
    'tauren'     => 'Tauren',
    'troll'      => 'Troll',

/*
 * Class Names
 */
    'warrior'      => 'Warrior',
    'rogue'        => 'Rogue',
    'hunter'       => 'Hunter',
    'paladin'      => 'Paladin',
    'priest'       => 'Priest',
    'druid'        => 'Druid',
    'shaman'       => 'Shaman',
    'warlock'      => 'Warlock',
    'mage'         => 'Mage',
    'death_knight' => 'Death Knight',

));
