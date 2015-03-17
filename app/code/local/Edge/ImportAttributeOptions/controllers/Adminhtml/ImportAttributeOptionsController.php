<?php

class Edge_ImportAttributeOptions_Adminhtml_ImportAttributeOptionsController extends Mage_Adminhtml_Controller_Action
{
    
    protected $existingOptions = array();

    /**
     * Initialize layout.
     */
    protected function _initAction()
    {
        $this->_title($this->__('Import Attribute Values'))
            ->loadLayout()
            ->_setActiveMenu('system');

        return $this;
    }

    /**
     * Check access (in the ACL) for current user.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/convert/import');
    }

    /**
     * Index action.
     *
     * @return void
     */
    public function indexAction()
    {
        $this->_initAction()
            ->_addBreadcrumb($this->__('Import Attribute Values'), $this->__('Import Attribute Values'));

        $this->renderLayout();
    }

    /**
     * Start import for uploaded CSV.
     *
     * @return void
     */
    public function importAction()
    {
        $importModel = Mage::getModel('importattributeoptions/import');
        
        $file = $importModel::FIELD_NAME_SOURCE_FILE;
        $data = $this->getRequest()->getPost();
        
        if ($data && isset($_FILES[$file]) && is_uploaded_file($_FILES[$file]['tmp_name'])){
            
            $handle = fopen($_FILES[$file]['tmp_name'], 'r');
            $headers = fgetcsv($handle, 1000, ",");
            
            if ($data['behaviour'] == $importModel::BEHAVIOR_DELETE) {
                $this->deleteExistingData($headers);
            }
            
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                foreach ($data as $key => $value) {
                    if(!empty($value)) {
                        $this->addAttributeOption($headers[$key], $value);
                    }
                }
            }
            fclose($handle);
            
            Mage::getSingleton('adminhtml/session')->addSuccess('Attribute options created for ' . implode(', ',$headers));
        }
        
        $importModel->invalidateIndex();
        
        $this->_redirect('*/*/');
        return;
    }
    
    /**
     * Add new option to attribute
     * 
     * @param string $code
     * @param string $value
     * @param int $sort
     */
    private function addAttributeOption($code, $value, $sort = 0)
    {
        $attribute = Mage::getModel('catalog/resource_eav_attribute')->loadByCode(Mage_Catalog_Model_Product::ENTITY, $code);
        
        if (!$this->attributeOptionExists($code, $value)) {
            $result = array();
            $result['value']['option'] = array(trim($value));
            $result['order']['option'] = $sort;
            
            $attribute->setData('option', $result);
            $attribute->save();
        }
    }
    
    /**
     * Check if option already exists on attribute
     * 
     * @param string $code
     * @param string $value
     * @return boolean
     */
    private function attributeOptionExists($code, $value)
    {
        if(!isset($this->existingOptions[$code])) {
            $this->setExistingOptions($code);
        }
        
        if (isset($this->existingOptions[$code]) && in_array(trim($value), $this->existingOptions[$code])) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Populate existing options array
     * 
     * @param string $code
     */
    private function setExistingOptions($code)
    {
        $attribute = Mage::getModel('catalog/resource_eav_attribute')->loadByCode(Mage_Catalog_Model_Product::ENTITY, $code);
        
        $model = Mage::getModel('eav/entity_attribute_source_table')->setAttribute($attribute);
        $options = $model->getAllOptions(false);
        
        foreach($options as $option) {
            $this->existingOptions[$code][] = trim($option['label']);
        }
    }
    
    /**
     * Delete all options for given attribute
     * 
     * @param string $attributes attribute code
     */
    private function deleteExistingData($attributes)
    {
        foreach ($attributes as $attributeCode) {
            $attribute = Mage::getModel('catalog/resource_eav_attribute')->loadByCode(Mage_Catalog_Model_Product::ENTITY, $attributeCode);

            $model = Mage::getModel('eav/entity_attribute_source_table')->setAttribute($attribute);
            $options = $model->getAllOptions(false);

            $optionsDelete = array();
            foreach($options as $option) {
                if ($option['value'] != "") {
                    $optionsDelete['delete'][$option['value']] = true;
                    $optionsDelete['value'][$option['value']] = true;
                }
            }

            $attribute->setData('option', $optionsDelete);
            $attribute->save();
            
            Mage::getSingleton('adminhtml/session')->addError($attributeCode . ' options deleted.');
        }
    }
}