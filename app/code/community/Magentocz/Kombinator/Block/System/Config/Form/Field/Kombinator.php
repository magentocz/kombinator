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
 * @copyright Copyright (c) 2014 Magento CZ (http://www.magento.cz)
 * 
 */ 
/** 
 * 
 * @category    Magentocz 
 * @package     Magentocz_Kombinator
 * @author      Jaromir Muller, jaromir.muller@getready.cz
 */
 
class Magentocz_Kombinator_Block_System_Config_Form_Field_Kombinator extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{	
	protected $_paymentMethods;
	protected $_shippingEnabledMethods;
	protected $_selected = ' selected="selected" ';
	
    public function __construct()
    {
        $this->addColumn('shipping', array(
            'label' => Mage::helper('adminhtml')->__('Shipping method'),
            'style' => 'width:150px',
        	'class' => 'option-control',
        ));
        $this->addColumn('payment', array(
            'label' => Mage::helper('adminhtml')->__('Payment method'),
            'style' => 'width:150px',
        	'class' => 'option-control',
        ));
        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('adminhtml')->__('Add combination');
        parent::__construct();
    }
    
    /**
     * Returns payment methods options for select
     *
     * @return unknown
     */
    private function getPaymentOptionsHtml()
    {
    	if($this->_paymentMethods == null)
    		$this->_paymentMethods = $this->helper('payment')->getStoreMethods();
    	$result = "";
    	
    	foreach ($this->_paymentMethods as $paymentMethod)
    	{    		
    		$result .= '<option value="'.$paymentMethod->getCode().'" >'.$paymentMethod->getTitle().'</option>';
    	}
    	
    	return $result;
    }
    
	/**
     * Returns payment/shipping methods options for select
     *
     * @return unknown
     */
    private function getMethodsOptionsHtml($methodsType)
    {
    	$methods = null;
    	$result = array('<option value="" ></option>');
    	if($methodsType == 'payment')
    	{
    		if($this->_paymentMethods == null)
    		{
    			$this->_paymentMethods = Magentocz_Kombinator_Helper_Data::getPaymentMethods();//$this->helper('payment')->getStoreMethods(); 
    		}
    		$methods = $this->_paymentMethods;
    	}
    	if($methodsType == 'shipping')
    	{
    		if($this->_shippingMethods == null)
    			$this->_shippingMethods = Magentocz_Kombinator_Helper_Data::getShippingMethods(false);
    		$methods = $this->_shippingMethods;
    	}
    	
    	
    	foreach ($methods as $method)
    	{    		
    		if($method->getTitle() == "")
    			continue;
    		$result[] = '<option value="'.$method->getCode().'" #{'.$method->getCode().'} >'.$method->getTitle().'</option>';
    	}
    	
    	sort($result);    	
    	return implode($result);
    }
    
	/**
     * Obtain existing data from form element
     *
     * Each row will be instance of Varien_Object
     *
     * @return array
     */
    public function getArrayRows()
    {
    	if (null !== $this->_arrayRowsCache) 
    	{
    	     return $this->_arrayRowsCache;
    	}        
    		
    	if($this->_paymentMethods == null)
    		$this->_paymentMethods = $this->helper('payment')->getStoreMethods(); //Magentocz_Kombinator_Helper_Data::getPaymentMethods();
    	if($this->_shippingMethods == null)
    			$this->_shippingMethods = Magentocz_Kombinator_Helper_Data::getShippingMethods(false);
    	
    	$result = array();
    			
    	$combiCollection = Mage::getModel('magentocz_kombinator/kombinator')->getCollection();
    	foreach($combiCollection as $combiRow)
    	{
    		$row = array('_id' => $combiRow->getId(),'shipping' => $combiRow->getShippingCode(),'payment' => $combiRow->getPaymentCode());
    		//add variable to insert text to selected item for shipping method
            foreach ($this->_shippingMethods as $shippMethod) 
            {
               	if($shippMethod->getCode() == $row['shipping']) 
                	$row[$shippMethod->getCode()] = $this->_selected;
            }
                
            //add variable to insert text to selected item for payment methods
           	foreach ($this->_paymentMethods as $payMethod) 
            {
                if($payMethod->getCode() == $row['payment'])
                	$row[$payMethod->getCode()] = $this->_selected;
            }                
                
            $result[$combiRow->getId()] = new Varien_Object($row);
    	}
    	$this->_arrayRowsCache = $result;
        return $this->_arrayRowsCache;    	       
    }
    
	/**
     * Render array cell for prototypeJS template
     *
     * @param string $columnName
     * @return string
     */
    protected function _renderCellTemplate($columnName)
    {    	
        if (empty($this->_columns[$columnName])) {
            throw new Exception('Wrong column name specified.');
        }
        $column     = $this->_columns[$columnName];
        $inputName  = $this->getElement()->getName() . '[#{_id}][' . $columnName . ']';

        if ($column['renderer']) {
            return $column['renderer']->setInputName($inputName)->setColumnName($columnName)->setColumn($column)
                ->toHtml();
        }
 
        $result = '<select id="kombinator_'.$columnName.'" name="' . $inputName . '" value="#{' . $columnName . '}" ' .
            ($column['size'] ? 'size="' . $column['size'] . '"' : '') . ' class="' .
            (isset($column['class']) ? $column['class'] : 'input-text') . '"'.
            (isset($column['style']) ? ' style="'.$column['style'] . '"' : '') . '>';
            
        $result .= $this->getMethodsOptionsHtml($columnName).'</select>';
        
        return $result;        
    }
}