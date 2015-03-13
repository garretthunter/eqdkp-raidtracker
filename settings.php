<?php
/**
 * Project:     CT_RaidTrackerImport [EQdkp Plugin]
 * License:     http://opensource.org/licenses/gpl-license.php
 * -----------------------------------------------------------------------
 * File:        settings.php
 * Description	Constants and runtime variables
 * Date:        $Date: 2009-10-18 10:12:45 +0000 (Sun, 18 Oct 2009) $
 * -----------------------------------------------------------------------
 * @author 		Garrett Hunter <loganfive@blacktower.com>
 * @copyright   (c) 2008 Garrett Hunter
 * @link        http://code.google.com/p/eqdkp-raidtracker/
 * @package     CT_RaidTrackerImport
 * @version     $id$
 */

/**
 * Table identifiers
 */
define('CTRT_IGNORE_ITEM',  0);
define('CTRT_ADD_ITEM',     1);

/**
 * URI Parameters
 */
define('URI_ID',    'id');

/**
 * Item Quality
 */
define('CTRT_IQ_POOR',          0);
define('CTRT_IQ_COMMON',        1);
define('CTRT_IQ_UNCOMMON',      2);
define('CTRT_IQ_RARE',          3);
define('CTRT_IQ_EPIC',          4);
define('CTRT_IQ_LEGENDARY',     5);

/**
 * Attendance Filter
 */
define('CTRT_AF_NONE',      0);         // 0 = None ( if a person was in the raid they are added to all events )
define('CTRT_AF_LOOT_TIME', 1);         // 1 = Loot Time ( if a person was in the raid when the loot attached to an event was picked up they are added to the event )
define('CTRT_AF_BOSS_KILL', 2);         // 2 = Boss Kill Time ( if a person was in the raid when a boss attached to an event was killed they are addedto the event )

/**
 * Race Names
 */
define ('RACE_NAME_BLOOD_ELF',  'BloodElf');
define ('RACE_NAME_DRAENEI',    'Draenei');
define ('RACE_NAME_DWARF',      'Dwarf');
define ('RACE_NAME_GNOME',      'Gnome');
define ('RACE_NAME_HUMAN',      'Human');
define ('RACE_NAME_NIGHT_ELF',  'NightElf');
define ('RACE_NAME_UNDEAD',     'Undead');
define ('RACE_NAME_SCOURGE',    'Scourge');
define ('RACE_NAME_TAUREN',     'Tauren');
define ('RACE_NAME_TROLL',      'Troll');
define ('RACE_NAME_ORC',        'Orc');

/**
 * Class Names
 */
define ('CLASS_NAME_WARRIOR',    'Warrior');
define ('CLASS_NAME_ROGUE',      'Rogue');
define ('CLASS_NAME_HUNTER',     'Hunter');
define ('CLASS_NAME_PALADIN',    'Paladin');
define ('CLASS_NAME_PRIEST',     'Priest');
define ('CLASS_NAME_DRUID',      'Druid');
define ('CLASS_NAME_SHAMAN',     'Shaman');
define ('CLASS_NAME_WARLOCK',    'Warlock');
define ('CLASS_NAME_MAGE',       'Mage');
define ('CLASS_NAME_DEATHKNIGHT','DeathKnight');

?>