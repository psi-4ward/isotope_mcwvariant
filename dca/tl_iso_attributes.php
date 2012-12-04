<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * MultiColumnWizard variant attribute for Isotope eCommerce
 *
 * @copyright 4ward.media 2012 <http://www.4wardmedia.de>
 * @author Christoph Wiechert <wio@psitrax.de>
 * @licence LGPL
 */


// Palette
$GLOBALS['TL_DCA']['tl_iso_attributes']['palettes']['mcw_variant'] = '{attribute_legend},name,field_name,type,legend;'
																	.'{description_legend:hide},description;'
																	.'{config_legend},mcwvariant_columnFields,mcwvariant_mandatory,mcwvariant_autochooseFirstOption,mcwvariant_inputType';


$jsonTpl = '"images":
{
	"label":"bild",
	"inputType":"filepicker4ward"
},
"name":
{
	"label":"text",
	"inputType":"text",
	"eval":
	{
		"style":"width:150px"
	}
},
"price":
{
	"label":"price",
	"inputType":"text",
	"eval":
	{
		"style":"width:150px"
	}
},
"sku":
{
	"label":"sku",
	"inputType":"text",
	"eval":
	{
		"style":"width:150px"
	}
}';


// Fields
$GLOBALS['TL_DCA']['tl_iso_attributes']['fields']['mcwvariant_columnFields'] = array
(
	'label'		=> &$GLOBALS['TL_LANG']['tl_iso_attributes']['mcwvariant_columnFields'],
	'inputType'	=> 'textarea',
	'default'	=> $jsonTpl,
	'eval'		=> array('mandatory'=>true, 'rte'=>'codeMirror|php', 'decodeEntities'=>true, 'style'=>'height:600px', 'tl_class'=>'long')
);

$GLOBALS['TL_DCA']['tl_iso_attributes']['fields']['mcwvariant_mandatory'] = array
(
	'label'		=>  &$GLOBALS['TL_LANG']['tl_iso_attributes']['mcwvariant_mandatory'],
	'inputType'	=> 'checkbox',
	'eval'		=> array('mandatory'=>false, 'tl_class'=>'w50')
);

$GLOBALS['TL_DCA']['tl_iso_attributes']['fields']['mcwvariant_inputType'] = array
(
	'label'		=>  &$GLOBALS['TL_LANG']['tl_iso_attributes']['mcwvariant_inputType'],
	'inputType'	=> 'select',
	'options'	=> array('select','radio','checkbox'),
	'eval'		=> array('mandatory'=>true, 'tl_class'=>'w50')
);

$GLOBALS['TL_DCA']['tl_iso_attributes']['fields']['mcwvariant_autochooseFirstOption'] = array
(
	'label'		=>  &$GLOBALS['TL_LANG']['tl_iso_attributes']['mcwvariant_autochooseFirstOption'],
	'inputType'	=> 'checkbox',
	'eval'		=> array('mandatory'=>false, 'tl_class'=>'w50')
);
