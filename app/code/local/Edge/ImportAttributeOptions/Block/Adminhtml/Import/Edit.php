<?php

class Edge_ImportAttributeOptions_Block_Adminhtml_Import_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Constructor
     *
     * @return void
     */
    public function __construct()
    {
        $this->_objectId   = 'importattributeoptions_id';
        $this->_controller = 'adminhtml_import';
        $this->_blockGroup = 'importattributeoptions';
        
        parent::__construct();

        $this->removeButton('back')
            ->removeButton('reset')
            ->_updateButton('save', 'label', $this->__('Import'))
            ->_updateButton('save', 'id', 'upload_button');
    }
}
