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
     * Populates the Company object with data given.
     * @param mixed $data an indexed key-value collection.
     * @return $this
     */
    public function addData($data)
    {
        
        foreach ( array('id','name','addr','tel') as $field )
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
            throw new \Exception("Company search yelded zero results during initiation.");
        
        $this->addData($rowset->current());
        
        return $this;
    }
    
    /**
     * Returns the number of employees belonging to this company.
     * @return int
     */
    public function getEmployeeCount()
    {
        if ( ! $this->id )
        {
            throw new \Exception('Company is not initialized: id not set.');
        }
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
     */
    public function getEmployees()
    {
        if ( ! $this->employees )
        {
            $this->employees = new Employees($this);
        }
        return $this->employees;
    }
    
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception('Not used');
    }

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
                ),
            ));
        }
        return $this->inputFilter;
    }
    
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->services = $serviceLocator;
    }

    public function getServiceLocator()
    {
        if ( ! $this->services )
            throw new Exception("Service locator not set");
        return $this->services;
    }
    
    /**
     * Employees.
     * @var Employees
     */
    private $employees;
    
    private $services;
    
    private $employeeCount;
    
    private $inputFilter;
}
