<?php
namespace Companies\Forms;

use Zend\Form\Form;

/**
 * Form for Company.
 */
class CompanyForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('company');
        
        $this->add(array(
            'name' => 'name',
            'type' => 'Text',
            'options' => array(
                'label' => 'Name: '
            ),
            /* for js validations. */
            'attributes' => array(
                'valid_required' => 'true',
                'valid_minlength' => '3',
                'valid_maxlength' => '50',
            ),
        ));
        
        $this->add(array(
            'name' => 'addr',
            'type' => 'Text',
            'options' => array(
                'label' => 'Address: '
            ),
            // for js validations
            'attributes' => array(
                'valid_required' => 'true',
                'valid_minlength' => '10',
                'valid_maxlength' => '100',
            ),
        ));
        
        $this->add(array(
            'name' => 'tel',
            'type' => 'Text',
            'options' => array(
                'label' => 'Telephone: '
            ),
            'attributes' => array(
                'size' => '9',
                'valid_required' => 'true',
                'valid_regexp' => '\d{9}',
            ),
        ));
        
        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Add'
            ),
        ));
    }
}
