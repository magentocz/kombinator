<?php
/** 
 * Magento CZ Module
 * 
 * NOTICE OF LICENSE 
 * 
 * This source file is subject to the Open Software License (OSL 3.0) 
 * that is bundled with this package in the file LICENSE.txt. 
 * It is also available through the world-wide-web at this URL: 
 * http://opensource.org/licenses/osl-3.0.php 
 * If you did of the license and are unable to 
 * obtain it through the world-wide-web, please send an email 
 * to magentocz@gmail.com so we can send you a copy immediately. 
 * 
 * @copyright Copyright (c) 2014 GetReady s.r.o. (https://getready.cz)
 * 
 */ 
/** 
 * 
 * @category  Magentocz 
 * @package   Magentocz_Kombinator
 * @author    Jaromir Muller, jaromir.muller@getready.cz
 */
class Magentocz_Kombinator_Block_Onepage_Shipping_Method_Available extends Mage_Checkout_Block_Onepage_Shipping_Method_Available 
{

  public function getShippingRates() {

		$shippingRateGroups = parent::getShippingRates();

    // REFACTOR
		if($this->getQuote()->getStore()->getConfig('checkout/magentocz_kombinator/enabled') == 0)
    		return $shippingRateGroups;   
		
		//filter groups by module settings
		$filteredRateGroups = array();
		foreach ($shippingRateGroups as $code => $_rates) {
			if( Mage::helper('magentocz_kombinator')->shippingMethodAllowed($code) ) {
				$filteredRateGroups[$code] = $_rates;		
			}

		}

		return $filteredRateGroups;  
  }

}