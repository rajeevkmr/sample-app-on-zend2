<?php
namespace Companies\Model;

use Zend\Validator\AbstractValidator;
use Zend\Db\TableGateway\TableGateway;

/* Companies have a restriction for unique names, so we need
 * this validator. */
class CompanyNameValidator extends AbstractValidator 
{
    const DUPLICATE = 'd';
    
    protected $messageTemplates = array(
        self::DUPLICATE => "Company name '%value%' duplicates with another one in the database."
    );
    
    private $serviceLocator;
    
    public function __construct($options = array())
    {
        parent::__construct($options);
        
        $this->serviceLocator = isset($options['serviceLocator']) ?
                $options['serviceLocator'] : null;
    }
    
    public function isValid($value)
    {
        $this->setValue($value);
        
        // search for this name.
        $table = new TableGateway('company',
                $this->serviceLocator->get('Zend\Db\Adapter\Adapter'));
        
        if ( $table->select(array('name' => $value))->count() > 0 )
        {
            $this->error(self::DUPLICATE);
            return false;
        }
        
        return true;
    }
}