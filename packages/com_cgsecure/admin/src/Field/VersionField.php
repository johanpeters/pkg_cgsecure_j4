<?php
/**
 * CG Secure Component for Joomla 4.x
 *
 * @author     ConseilgGouz
 * @copyright (C) 2023 www.conseilgouz.com. All Rights Reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace ConseilGouz\Component\CGSecure\Administrator\Field;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\Language\Text;
use Joomla\String\StringHelper;

// Prevent direct access
defined('_JEXEC') || die;

class VersionField extends FormField
{
	/**
	 * Element name
	 *
	 * @var   string
	 */
	protected $_name = 'Version';

	function getInput()
	{
		$return = '';
		// Load language
		$extension = $this->def('extension');

		$version = '';

		$jinput = Factory::getApplication()->input;
		$db = Factory::getDBO();
		$query = $db->getQuery(true);
		$query
			->select($db->quoteName('manifest_cache'))
			->from($db->quoteName('#__extensions'))
			->where($db->quoteName('element') . '= :ext')
			->bind(':ext',$extension,\Joomla\Database\ParameterType::STRING);
		$db->setQuery($query, 0, 1);
		$row = $db->loadAssoc();
		$tmp = json_decode($row['manifest_cache']);
		$version = $tmp->version;
		
		$document = Factory::getDocument();
		$css = '';
		$css .= ".version {display:block;text-align:right;color:brown;font-size:12px;}";
		$css .= ".readonly.plg-desc {font-weight:normal;}";
		$css .= "fieldset.radio label {width:auto;}";
		$document->addStyleDeclaration($css);
		$margintop = $this->def('margintop');
		if (StringHelper::strlen($margintop)) {
			$js = "document.addEventListener('DOMContentLoaded', function() {
			vers = document.querySelector('.version');
			parent = vers.parentElement.parentElement;
			parent.style.marginTop = '".$margintop."';
			})";
			$document->addScriptDeclaration($js);
		}
		$return .= '<span class="version">' . Text::_('JVERSION') . ' ' . $version . "</span>";

		return $return;
	}
	public function def($val, $default = '')
	{
	    return ( isset( $this->element[$val] ) && (string) $this->element[$val] != '' ) ? (string) $this->element[$val] : $default;
	}
	
}
