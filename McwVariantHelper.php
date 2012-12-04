<?php if(!defined('TL_ROOT')) {die('You cannot access this file directly!');}

/**
 * MultiColumnWizard variant attribute for Isotope eCommerce
 *
 * @copyright 4ward.media 2012 <http://www.4wardmedia.de>
 * @author Christoph Wiechert <wio@psitrax.de>
 * @licence LGPL
 */


class McwVariantHelper extends System
{

	/**
	 * Generate the backend DCA for the MultiColumnWizard
	 *
	 * @param $strField
	 * @param $arrData
	 * @param null $objProduct
	 * @return array
	 */
	public function generateDCA($strField, $arrData, $objProduct=null)
	{
		$arrColFields = json_decode('{'.$arrData['attributes']['mcwvariant_columnFields'].'}',true);

		$arrDCA = array
		(
			'label' 		=> $arrData['label'],
			'exclude'		=> $arrData['exclude'],
			'inputType'		=> 'multiColumnWizard',
			'isMcwVariant'	=> true,
			'eval' => array
			(
				'columnFields' => $arrColFields
			),
			'attributes' => array('legend'=>$arrData['attributes']['legend']),
			'FeWidgetAttribs' => array
			(
				'mandatory' => (bool)$arrData['attributes']['mcwvariant_mandatory'],
				'inputType'	=> $arrData['attributes']['mcwvariant_inputType'],
				'autochooseFirstOption' => (bool)$arrData['attributes']['mcwvariant_autochooseFirstOption'],
			)
		);
		return $arrDCA;
	}


	/**
	 * Re-calculate the product price for the choosen variants
	 *
	 * @param $fltPrice
	 * @param $objSource
	 * @param $strField
	 * @param $intTaxClass
	 * @return mixed
	 */
	public function calculatePrice($fltPrice, $objSource, $strField, $intTaxClass)
	{
		if($objSource instanceof IsotopeProduct )
		{
			$arrOptions = $objSource->getOptions(true);

			// if a option is set, do the alter price calculation for it
			foreach($arrOptions as $option => $choosenVariant)
			{
				// strip _fe from the options name
				$fld = substr($option,0,-3);

				$arrProductVariantFieldsData = $objSource->$fld;

				// if the option hastn a price field we need not to make any calculation
				if(!isset($arrProductVariantFieldsData[0]['price'])) continue;
				foreach($arrProductVariantFieldsData as $arrVariant)
				{
					if(  $arrVariant['name'] == $choosenVariant // single option
						 || is_array($choosenVariant) && in_array($arrVariant['name'],$choosenVariant)) // multiple option
					{
						$fltPrice = $fltPrice + $arrVariant['price'];
					}
				}

			}
		}
		return $fltPrice;
	}


	/**
	 * Generate the frontend variant-fields
	 * and do its submit handling
	 *
	 * @param $arrProductAttribus
	 * @param $arrProductVariantFields
	 * @param $objProduct
	 */
	public function generateVariants(&$arrProductAttribus, &$arrProductVariantFields, $objProduct)
	{
		// find all McwVariant fields
		foreach($arrProductAttribus as $fld)
		{
			// only for McwVariant fields
			if(!$GLOBALS['TL_DCA']['tl_iso_products']['fields'][$fld]['isMcwVariant'])
			{
				continue;
			}


			// if first name is empty, the mcw hasnt any valid variants
			$arrProductVariantFieldsData = $objProduct->$fld;
			if(!isset($arrProductVariantFieldsData[0]['name']) || empty($arrProductVariantFieldsData[0]['name'])) continue;


			// find mcw-columnFields with the same name as the product attribs
			// we would overwrite its value
			$arrVarientFields = array_intersect($arrProductAttribus,array_keys($GLOBALS['TL_DCA']['tl_iso_products']['fields'][$fld]['eval']['columnFields']));
			unset($arrVarientFields[array_search('name',$arrVarientFields)]);
			foreach($arrVarientFields as $variantFld)
			{
				if(!in_array($variantFld, $arrProductVariantFields))
				{
					// add to product variant fields for the ajax loading
					$arrProductVariantFields[] = $variantFld;
				}
			}


			// generate the options
			$arrSavedOptions = $objProduct->getOptions(true);

			$arrOptions = array();
			foreach($arrProductVariantFieldsData as $variant)
			{
				$arrOptions[$variant['name']] = $variant['name'];

				// set the products-data from post
				if(preg_match("~product_{$objProduct->id}$~",$this->Input->post('FORM_SUBMIT')))
				{
					$arrSavedOptions[$fld.'_fe'] = '';
					// single option
					if($this->Input->post($fld.'_fe') == $variant['name'])
					{
						$this->setVariant($variant, $objProduct, $arrVarientFields);
					}
					// multiple options (checkboxes)
					else if(is_array($this->Input->post($fld.'_fe')) && in_array($variant['name'], $this->Input->post($fld.'_fe')))
					{
						$this->setVariant($variant, $objProduct, $arrVarientFields);
					}
				}
				// set the products-data from objProduct
				else if(isset($arrSavedOptions[$fld.'_fe']))
				{
					// single option
					if($arrSavedOptions[$fld.'_fe'] == $variant['name'])
					{
						$this->setVariant($variant, $objProduct, $arrVarientFields);
					}
					// multiple options (checkboxes)
					elseif( $GLOBALS['TL_DCA']['tl_iso_products']['fields'][$fld]['FeWidgetAttribs']['inputType'] == 'checkbox'
							&& is_array($arrSavedOptions[$fld.'_fe'])
							&& in_array($variant['name'], $arrSavedOptions[$fld.'_fe'])
						)
					{
						$this->setVariant($variant, $objProduct, $arrVarientFields);
					}
				}
			}


			// generate a virtual variant isotope field
			$GLOBALS['TL_DCA']['tl_iso_products']['fields'][$fld.'_fe'] = array
			(
				'label'		=> $GLOBALS['TL_DCA']['tl_iso_products']['fields'][$fld]['label'],
				'inputType'	=> $GLOBALS['TL_DCA']['tl_iso_products']['fields'][$fld]['FeWidgetAttribs']['inputType'],
				'options'	=> $arrOptions,
				'eval'		=> array
				(
					'includeBlankOption'	=>!(bool)$GLOBALS['TL_DCA']['tl_iso_products']['fields'][$fld]['FeWidgetAttribs']['autochooseFirstOption'],
					'mandatory'				=> (bool)$GLOBALS['TL_DCA']['tl_iso_products']['fields'][$fld]['FeWidgetAttribs']['mandatory'],
					'multiple'				=> ($GLOBALS['TL_DCA']['tl_iso_products']['fields'][$fld]['FeWidgetAttribs']['inputType'] == 'checkbox'),
				),
				'attributes' => array('customer_defined'=>true, 'ajax_option'=>true)
			);

			// autochooseFirstOption
			if((!isset($arrSavedOptions[$fld.'_fe']) || empty($arrSavedOptions[$fld.'_fe'])) && $GLOBALS['TL_DCA']['tl_iso_products']['fields'][$fld]['FeWidgetAttribs']['autochooseFirstOption'])
			{
				$this->setVariant($arrProductVariantFieldsData[0], $objProduct, $arrVarientFields);
				$arrSavedOptions[$fld.'_fe'] = $arrProductVariantFieldsData[0]['name'];
				$objProduct->setOptions($arrSavedOptions);
			}

			// set predefined value
			if(!empty($arrSavedOptions[$fld.'_fe']))
			{
					$GLOBALS['TL_DCA']['tl_iso_products']['fields'][$fld.'_fe']['default'] = $arrSavedOptions[$fld.'_fe'];
			}

			// add the field to the product
			$arrProductAttribus[] = $fld.'_fe';
		}
	}


	/**
	 * Set a variant fields in the objProduct
	 *
	 * @param $variant
	 * @param $objProduct
	 * @param $arrVarientFields
	 */
	protected function setVariant($variant, $objProduct, $arrVarientFields)
	{
		foreach($arrVarientFields as $variantFld)
		{
			// price calculation is done with the calculatePrice HOOK
			if($variantFld == 'price') continue;

			if($variantFld == 'images')
			{
				$objProduct->images = array(array('src'=>$variant['images']));
				continue;
			}

			// set the value in the objProduct
			$objProduct->$variantFld = $variant[$variantFld];
		}
	}
}