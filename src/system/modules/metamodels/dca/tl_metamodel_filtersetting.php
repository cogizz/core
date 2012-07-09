<?php
/**
 * The MetaModels extension allows the creation of multiple collections of custom items,
 * each with its own unique set of selectable attributes, with attribute extendability.
 * The Front-End modules allow you to build powerful listing and filtering of the
 * data in each collection.
 *
 * PHP version 5
 * @package	   MetaModels
 * @subpackage Backend
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @copyright  CyberSpectrum
 * @license    private
 * @filesource
 */
if (!defined('TL_ROOT'))
{
	die('You cannot access this file directly!');
}

/**
 * Table tl_metamodel_attribute 
 */

$GLOBALS['TL_DCA']['tl_metamodel_filtersetting'] = array
(
	'config' => array
	(
		'dataContainer'               => 'Table',
		'switchToEdit'                => false,
		'enableVersioning'            => false,
		'oncreate_callback'           => array(array('TableMetaModelFilterSetting', 'create_callback')),
		'palettes_callback'           => array(array('TableMetaModelFilterSetting', 'preparePalettes'))
	),

	// List
	'list' => array
	(
		'sorting' => array
		(
			'mode'                    => 5,
			'fields'                  => array('attr_id'),
			'panelLayout'             => 'filter,limit', 
			'headerFields'            => array('type', 'attr_id'), 
			'flag'                    => 1,
			'icon'                    => 'system/modules/metamodels/html/filter_and.png',
			'paste_button_callback'   => array('TableMetaModelFilterSetting', 'pasteButton'),
		),

		'label' => array
		(
			'fields'                  => array('type'),
			'format'                  => '%s',
			'label_callback'          => array('TableMetaModelFilterSetting', 'drawSetting')
		),

		'global_operations' => array
		(
			'all' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
				'href'                => 'act=select',
				'class'               => 'header_edit_all',
				'attributes'          => 'onclick="Backend.getScrollOffset();"'
			),
			// unfortunately, I can not place Back at the beginning (before new), so I put it at the end.
			'back' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['MSC']['backBT'],
				// TODO: this is an evil hack, replace with something better.
				'href'                => str_replace(array('contao/main.php?do=metamodel', $this->Environment->url), '', $this->getReferer(false, 'tl_metamodel_filter')),
				'class'               => 'header_back',
				'attributes'          => 'onclick="Backend.getScrollOffset();"'
			)
		),

		'operations' => array
		(
			'edit' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_metamodel_filtersetting']['edit'],
				'href'                => 'act=edit',
				'icon'                => 'edit.gif'
			),
			'copy' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_metamodel_filtersetting']['copy'],
				'href'                => 'act=copy',
				'icon'                => 'copy.gif'
			),
			'cut' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_metamodel_filtersetting']['cut'],
				'href'                => 'act=paste&amp;mode=cut',
				'icon'                => 'cut.gif',
				'attributes'          => 'onclick="Backend.getScrollOffset()"',
			),
			'delete' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_metamodel_filtersetting']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.gif',
				'attributes'          => 'onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\')) return false; Backend.getScrollOffset();"'
			),
			'show' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_metamodel_filtersetting']['show'],
				'href'                => 'act=show',
				'icon'                => 'show.gif'
			),
		)
	),

	'palettes' => array
	(
		'__selector__' => array('type')
	),

	'metapalettes' => array
	(
		'default' => array
		(
			'title' => array('type')
		),
		'_attribute_ extends default' => array
		(
			'config' => array('attr_id')
		),

		// base rules shipped with metamodels.

		'simplelookup extends _attribute_' => array
		(
			'+config' => array('urlparam'),
		),

		'customsql extends default' => array
		(
			'+config' => array('customsql'),
		),

	),

	'metasubselectpalettes' => array
	(
		'attr_id' => array
		(
		)
	),

	// Fields
	'fields' => array
	(
		'fid' => array
		(
			// keep this empty but keep it here!
			// needed for act=copy in DC_Table, as otherwise the fid value will not be copied.
			'label'                   => &$GLOBALS['TL_LANG']['tl_metamodel_filtersetting']['fid'],
		),

		'type' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_metamodel_filtersetting']['type'],
			'exclude'                 => true,
			'inputType'               => 'select',
			'options_callback'        => array('TableMetaModelFilterSetting', 'getSettingTypes'),
			'reference'               => &$GLOBALS['TL_LANG']['tl_metamodel_filtersetting']['typenames'],
			'eval'                    => array(
				'doNotSaveEmpty'      => true,
				'alwaysSave'          => true,
				'submitOnChange'      => true,
				'includeBlankOption'  => true,
				'mandatory'           => true,
				'tl_class'            => 'w50',
				'chosen'              => true
			),
		),

		'attr_id' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_metamodel_filtersetting']['attr_id'],
			'exclude'                 => true,
			'inputType'               => 'select',
			'options_callback'        => array('TableMetaModelFilterSetting', 'getAttributeNames'),
			'eval'                    => array(
				'doNotSaveEmpty'      => true,
				'alwaysSave'          => true,
				'submitOnChange'      => true,
				'includeBlankOption'  => true,
				'mandatory'           => true,
			),
			'load_callback'           => array(array('TableMetaModelFilterSetting', 'attrIdToName')),
			'save_callback'           => array(array('TableMetaModelFilterSetting', 'nameToAttrId')),
		),

		'urlparam' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_metamodel_filtersetting']['urlparam'],
			'exclude'                 => true,
			'inputType'               => 'text',
		),

		'customsql' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_metamodel_filtersetting']['customsql'],
			'exclude'                 => true,
			'inputType'               => 'textarea',
			'eval'                    => array(
				'allowHtml'           => true,
				'rte'                 => 'codeMirror|sql',
				'class'               => 'monospace',
				'helpwizard'          => true,
			),
			'explanation'         => 'customsql'
		),
	)
);

?>