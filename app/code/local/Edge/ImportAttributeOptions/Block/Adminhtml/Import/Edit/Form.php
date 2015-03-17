<?php

/**
 * Import attributes form block
 *
 * @category    Edge
 * @package     Edge_ImportAttributeOptions
 * @author      outer/edge <hello@outeredgeuk.com>
 */
class Edge_ImportAttributeOptions_Block_Adminhtml_Import_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Add fieldset
     *
     * @return Edge_ImportAttributeOptions_Block_Adminhtml_Import_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
            'id'      => 'edit_form',
            'action'  => $this->getUrl('*/*/import'),
            'method'  => 'post',
            'enctype' => 'multipart/form-data'
        ));
        $fieldset = $form->addFieldset('base_fieldset', array('legend' => Mage::helper('importattributeoptions')->__('Import Settings')));
        $fieldset->addField('behaviour', 'select', array(
            'name'     => 'behaviour',
            'title'    => Mage::helper('importattributeoptions')->__('Import Behaviour'),
            'label'    => Mage::helper('importattributeoptions')->__('Import Behaviour'),
            'required' => true,
            'values'   => array(
                array(
                    'value' => Edge_ImportAttributeOptions_Model_Import::BEHAVIOR_APPEND,
                    'label' => Mage::helper('importattributeoptions')->__('Append Data')
                ),
                array(
                    'value' => Edge_ImportAttributeOptions_Model_Import::BEHAVIOR_DELETE,
                    'label' => Mage::helper('importattributeoptions')->__('Delete Existing Data')
                )
            )
        ));
        $fieldset->addField(Edge_ImportAttributeOptions_Model_Import::FIELD_NAME_SOURCE_FILE, 'file', array(
            'name'     => 'import_file',
            'label'    => Mage::helper('importattributeoptions')->__('Select File to Import'),
            'title'    => Mage::helper('importattributeoptions')->__('Select File to Import'),
            'required' => true
        ));

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
