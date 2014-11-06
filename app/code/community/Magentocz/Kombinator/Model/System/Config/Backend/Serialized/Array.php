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
 * Backend for serialized array data
 * 
 * @category Magentocz 
 * @package Magentocz_Kombinator
 * @author Jaromir Muller, jaromir.muller@getready.cz
 */
class Magentocz_Kombinator_Model_System_Config_Backend_Serialized_Array extends Mage_Adminhtml_Model_System_Config_Backend_Serialized
{
    /**
     * Unset array element with '__empty' key and save data to proper table
     *
     */
    protected function _beforeSave()
    {    	
    	
        $value = $this->getValue();
        if (is_array($value)) {
            unset($value['__empty']);
        }
        $this->setValue($value);
        
        $combiArrayDb = Magentocz_Kombinator_Helper_Data::prepareDbCombiArray();
        $combiArrayDbKeys = array_keys($combiArrayDb);
        $combiArrayUser = Magentocz_Kombinator_Helper_Data::prepareCurrentCombiArray($this->getValue());
        $combiArrayUserKeys = array_keys($combiArrayUser);
        
        //remove items from database
        foreach(array_diff($combiArrayDbKeys,$combiArrayUserKeys) as $item)
        {
        	$combiArrayDb[$item]->delete();
        }        
    		
    	//add items to database
        foreach(array_diff($combiArrayUserKeys,$combiArrayDbKeys) as $item)
        {
        	Mage::getModel('magentocz_kombinator/kombinator')->addRow($combiArrayUser[$item]);
        }   		
        
    	$this->setValue(NULL);
        parent::_beforeSave();
    }
}
