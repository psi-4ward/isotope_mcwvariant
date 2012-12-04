<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * MultiColumnWizard variant attribute for Isotope eCommerce
 *
 * @copyright 4ward.media 2012 <http://www.4wardmedia.de>
 * @author Christoph Wiechert <wio@psitrax.de>
 * @licence LGPL
 */

$GLOBALS['ISO_ATTR']['mcw_variant'] = array
(
	'sql'		=> "blob NULL",
	'callback'	=> array(array('McwVariantHelper','generateDCA')),
	'frontend'	=> 'WidgetMcwVariant'
);

$GLOBALS['ISO_HOOKS']['productAttributes'][] 	= array('McwVariantHelper','generateVariants');
$GLOBALS['ISO_HOOKS']['calculatePrice'][] 		= array('McwVariantHelper','calculatePrice');