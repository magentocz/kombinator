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
 * @category    Magentocz 
 * @package     Magentocz_Kombinator
 * @author      Jaromir Muller, jaromir.muller@getready.cz
 */
class Magentocz_Kombinator_Block_Onepage_Payment_Methods extends Mage_Checkout_Block_Onepage_Payment_Methods
{
    public function getMethods()
    {


    	$paymetMethods = parent::getMethods(); 
   	    	    	
    	if($this->getQuote()->getStore()->getConfig('checkout/magentocz_kombinator/enabled') == 0)
    		return $paymetMethods; 
    	
    	$shippingMethod = $this->getQuote()->getShippingAddress()->getShippingMethod();
    	if(!isset($shippingMethod) || $shippingMethod == '' || $shippingMethod == false) {
    		$shippingMethod = Mage::helper('magentocz_kombinator')->getOSCdefaultShippingMethod();
    	}

    	//filter allowed methods by kombinator settings
    	$filteredMethods = array();
    	foreach($paymetMethods as $method)
    	{

            // REFACTOR
    		if(Mage::helper('magentocz_kombinator')->combinationAllowed($shippingMethod,$method->getCode()))
    		{
    			$filteredMethods[] = $method;
    		}
    	}    	
    	return $filteredMethods;    	        
    }

}
