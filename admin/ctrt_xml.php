<?php
/**
 * XML helper classes for CT_RaidTracker
 *
 * @category Plugins
 * @package CT_RaidTrackerImport
 * @copyright (c) 2006, EQdkp <http://www.eqdkp.com>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 *
 * @author Garrett Hunter <loganfive@blacktower.com>
 * $Rev: 310 $ $Date: 2009-10-18 10:12:45 +0000 (Sun, 18 Oct 2009) $
 */

if ( !defined('EQDKP_INC') )
{
    header('HTTP/1.0 404 Not Found');
    exit;
}

require_once('admin/xml.php');

/**
 * Class for parsing CTRT XML strings
 */
class CTRT_XML {

	function CTRT_XML () {}

	function validateXML ($xml) {
    $xml = html_entity_decode($xml);
		if (!stristr($xml,'<?xml')) {
				$xml = '<?xml version="1.0" encoding="ISO-8859-1" ?>'.$xml;
		}
		$data = XML_unserialize($xml);
		if ($data === NULL) {
			return 0;
		} else {
			return 1;
		}
	}
	
	/**
	 * Parses an XML string and returns a PHP array representation. All strings are lowercased on import for consistency
     * @var string xml
	 */
	function xml_import ($xml) {
    $xml = html_entity_decode($xml);
		if (!stristr($xml,'<?xml')) {
				$xml = '<?xml version="1.0" encoding="ISO-8859-1" ?>'.$xml;
		}
    $data = XML_unserialize($xml);
		if ($data === NULL) {
			return 0;
		} else {
		    return $data;
		}
	}

	/**
	 * Converts a PHP array into an XML string
     * @var array data
	 */
	function xml_export ($data) {
		$xml = XML_serialize($data);
		if ($xml === NULL) {
			return 0;
		} else {
		    return $xml;
		}
	}
}
?>