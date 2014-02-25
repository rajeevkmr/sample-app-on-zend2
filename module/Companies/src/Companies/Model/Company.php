<?php
namespace Companies\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

use Zend\Db\TableGateway\TableGateway;
Use Zend\Db\Sql;

/**
 * Company entity: this class represents data of a single company.
 */
class Company implements InputFilterAwareInterface, ServiceLocatorAwareInterface
{
    /**
     * @var int
     */
    public $id;
    
    /**
     * @var string 
     */
    public $name;
    
    /**
     * Address
     * @var string 
     */
    public $addr;
    
    /**
     * Telephone number
     * @var string
     */
    public $tel;
    
    /**
     * Returns all valid properties of Company.
     * @return array
     */
    public static function propertyNames()
    {
        return array('id','name','addr','tel');
    }
    
    /**
     * Populates the Company object with data given.
     * @param mixed $data an indexed key-value collection.
     * @return $this
     */
    public function addData($data)
    {
        foreach ( $this::propertyNames() as $field )
        {
            $this->$field = !empty($data[$field]) ? $data[$field] : null;
        }
        return $this;
    }
   
    /**
     * Initialize Company object by id.
     * This initializes the direct properties of the companies table,
     * without any foreign relations like the num. of employees. The latter
     * one is loaded on request.
     * @param int $id
     * @return $this
     * @throws Exception when no company found in DB
     */
    public function init($id)
    {
        $table = new TableGateway('company',
            $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
        $rowset = $table->select(array('id' => $id));
        
        if ( $rowset->count() < 1 )
            throw new \Exception("Company with id $id not found during initiation.");
        
        $this->addData($rowset->current());
        $this->set_init();
        
        return $this;
    }
    
    /**
     * Returns the number of employees belonging to this company.
     * @return int
     * @throws Exception if company not initialized
     */
    public function getEmployeeCount()
    {
        if ( ! $this->is_init() )
            throw new \Exception('Company is not initialized.');

        if ( ! $this->employeeCount )
        {
            $employeeTable = new TableGateway('employee',
                $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
            
            $select = new Sql\Select('employee');
            $select->columns(
                    array('n' => new Sql\Expression('COUNT(*)')
            ));
            $where = new Sql\Where();
            $where->equalTo('cid', $this->id);
            $select->where($where);
            
            $row = $employeeTable->selectWith($select)->current();
            $this->employeeCount = $row->n;
        }
        return $this->employeeCount;
    }
    
    /**
     * Gets company employees
     * @return Companies\Model\Employees
     * @throws Exception if company not initialized
     */
    public function getEmployees()
    {
        if ( ! $this->is_init() )
            throw new \Exception('Company is not initialized.');
        
        if ( ! $this->employees )
        {
            $this->employees = new Employees();
            $this->employees->init($this);
        }
        return $this->employees;
    }
    
    /**
     * Not used - out input filter is lazily loaded in call to getInputFilter.
     * @param \Zend\InputFilter\InputFilterInterface $inputFilter
     * @throws \Exception
     */
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception('Not used');
    }

    /**
     * @return Zend\InputFilter\InputFilter
     */
    public function getInputFilter()
    {
        if ( ! $this->inputFilter )
        {
            $this->inputFilter = new InputFilter();
            $this->inputFilter->add(array(
                'name'     => 'name',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'min' => 3,
                            'max' => 50,
                        ),
                    ),
                    array(
                        'name' => 'Companies\Model\CompanyNameValidator',
                        'options' => array(
                            'serviceLocator' => $this->getServiceLocator(),
                        )
                    )
                ),
            ));
            $this->inputFilter->add(array(
                'name'     => 'addr',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'min' => 10,
                            'max' => 100,
                        ),
                    ),
                ),
            ));
            $this->inputFilter->add(array(
                'name'     => 'tel',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'min' => 9, // format: 869821191
                            'max' => 9,
                        ),
                    ),
                    array(
                        'name' => 'Regex',
                        'options' => array(
                            'pattern' => '/^\d{9}$/'
                        )
                    ),
                ),
            ));
        }
        return $this->inputFilter;
    }    
    
    /**
     * Set object as initialized or not.
     * @param bool $bool
     * @return $this
     */
    public function set_init($bool = true)
    {
        $this->initialized = (bool) $bool;
        return $this;
    }
    
    /**
     * Is object initiated?
     * @return bool 
     */
    public function is_init()
    {
        return $this->initialized;
    }
    
    /**
     * Set service locator
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->services = $serviceLocator;
    }

    /**
     * Get service locator
     * @return ServiceLocator
     */
    public function getServiceLocator()
    {
        return $this->services;
    }
    
    /**
     * @var ServiceLocator
     */
    private $services;
    
    /**
     * @var Employees Employees object.
     */
    private $employees;
    
    /**
     * @var int 
     */
    private $employeeCount;
    
    /**
     * @var Zend\InputFilter\InputFilter
     */
    private $inputFilter;
    
    /**
     * @var bool 
     */
    private $initialized = false;
}
