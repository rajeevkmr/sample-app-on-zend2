<?php
namespace Companies\Forms;

use Zend\Form\Form;
use Zend\Form\Element;

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
            )
        ));
        
        $this->add(array(
            'name' => 'addr',
            'type' => 'Text',
            'options' => array(
                'label' => 'Address: '
            )
        ));
        
        $this->add(array(
            'name' => 'tel',
            'type' => 'Text',
            'options' => array(
                'label' => 'Telephone: '
            ),
            'attributes' => array(
                'size' => '10',
            )
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
