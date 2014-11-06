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
 * @category   Magentocz
 * @package    Magentocz_Kombinator
 * @author     Jaromir Muller, jaromir.muller@getready.cz
 */
class Magentocz_Kombinator_Helper_Data extends Mage_Core_Helper_Abstract
{	
	/**
	 * Prepares 2 arrays,First given by parameter $curArray is array of combination,Second is array of combination actuali set by user
	 * 
	 * @param array of combinations $sourceArr
	 * @param unknown_type $curArray
	 * @return unknown
	 */
	public static function prepareCurrentCombiArray($sourceArr)
	{
		$result = array();
		foreach($sourceArr as $data)
    	{
    		$shippMethod = '';
    		foreach($data as $type => $method)
    		{
    			if($type == 'shipping')
    				$shippMethod = $method;
    			else if($type == 'payment')
    			{
    				$result[$shippMethod.$method] = array('shipping' => $shippMethod,'payment' => $method);
    				$shippMethod = '';
    			}
    		}
    	}
    	return  $result;
	}
	
	
	/**
	 * Prepare 2 arays, First given by parameter $dbArray is array of combination,Second is array of combination from table,index is combination name
	 *
	 * @param unknown_type $dbArray
	 * @return array
	 */
	public static function prepareDbCombiArray()
	{
		$combiCollection = Mage::getModel('magentocz_kombinator/kombinator')->getCollection();
		$resultArr = array();
		foreach($combiCollection as $combiRaw)
		{
			$combination = $combiRaw->getShippingCode().$combiRaw->getPaymentCode();
			$resultArr[$combination] = $combiRaw;			
		}
		return $resultArr;
	}
	
  /**
   * Retrieve payment methods for store
   *
   * @param   mixed $store
   * @return  array
   */
  public static function getPaymentMethods($store=null)
  {
      $methods = Mage::getStoreConfig('payment',$store);
      $res = array();
      $names = array();
      foreach ($methods as $code => $methodConfig) 
      {
          $prefix = 'payment/'.$code.'/';

       	if (!$model = Mage::getStoreConfig($prefix.'model', $store)) {
              continue;
          }
          
          $methodInstance = Mage::getModel($model);
          $sortOrder = (int)Mage::getStoreConfig($prefix.'sort_order', $store);
          $methodInstance->setSortOrder($sortOrder);
          $methodInstance->setStore($store);
          $res[] = $methodInstance;
          $names[] = $methodInstance->getTitle();
      }
      array_multisort($names,$res);

      return $res;
  }

	
	/**
	 * Returns enabled shipping methods
	 *
	 */
	public static function getShippingMethods($onlyEnabled)
	{
		$methods = Mage::getStoreConfig('carriers',Mage::app()->getStore());
		$result = array();
		foreach ($methods as $key => $data)
		{						
			if(!$onlyEnabled || $data['active']== 1)
			{
				$data['code'] = $key;
				$result[] = new Varien_Object($data);
			}
		}	
		return $result;
	}

  protected function _getRuleHash( $shippingCode, $paymentCode ) {
    return '_R/'.$shippingCode . $paymentCode;
  }

  protected function _getShippingHash( $shippingCode ) {
    return '_S/'.$shippingCode;
  }

  protected function _receiveRulesCollection() {

    $rulesCollection = Mage::registry('magentocz_kombinator/rulesCollection');
    
    if ( !$rulesCollection  ) {
      $collection = Mage::getModel('magentocz_kombinator/kombinator')->getCollection();
      $rulesCollection = array();

      foreach ($collection as $rule) {
          $hash = $this->_getRuleHash( $rule->getShippingCode(), $rule->getPaymentCode() );
          $shippingHash = $this->_getShippingHash( $rule->getShippingCode() );
          $rulesCollection[ $hash ] = true;
          $rulesCollection[ $shippingHash ] = true;
      }

      Mage::register('magentocz_kombinator/rulesCollection', $rulesCollection);
    }

    return $rulesCollection;
  }
	
	/**
	 * Recognise if current combination of payment and shipping method is allowed
	 *
	 * @param String $shippingCode
	 * @param String $paymentCode
	 * 
	 * @return True or false if combination is allowed
	 */
	public function combinationAllowed($shippingCode,$paymentCode)
	{

    // if $shippingCode contains more underscores then we're in trouble but still may work.
		$shippingCode = substr($shippingCode, 0, strpos($shippingCode,'_'));
		
    $rulesCollection = $this->_receiveRulesCollection();
    $hash = $this->_getRuleHash( $shippingCode, $paymentCode );

    return isset($rulesCollection[$hash]) && $rulesCollection[$hash];
	}
	
  /**
	 * Returns true if current shipping method is allowed.
	 *
	 * @param String $shippingCode
	 * 
	 * @return True or false if method is allowed
	 */
	public function shippingMethodAllowed($shippingCode)
	{

    $rulesCollection = $this->_receiveRulesCollection();
    $shippingHash = $this->_getShippingHash( $shippingCode );

    return isset($rulesCollection[$shippingHash]) && $rulesCollection[$shippingHash];
	}
	
	protected function getConfig($configPath) {
		return Mage::getConfig ()->getNode ($configPath);
	}

	public function getOSCdefaultShippingMethod()
	{
		$sm = $this->getConfig('default/onestepcheckout/general/default_shipping_method');
		if(isset($sm))
			return $sm;
		else
			return '';
	}
}