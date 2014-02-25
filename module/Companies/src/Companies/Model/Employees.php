<?php
namespace Companies\Model;

use Companies\Model\Company;
use Companies\Model\Employee;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Stdlib\Hydrator\ObjectProperty as Hydrator;

Use Zend\Db\Sql\Sql;

use Companies\Code\CheckInitialized;

/**
 * Employees collection. This class contains all the functionality needed
 * to work with employee lists.
 */
class Employees implements ServiceLocatorAwareInterface, CheckInitialized
{
    /**
     * Max num. of records to return in fetch().
     * @var int 
     */
    public $limit = 10;
    
    /**
     * Set offset (starting record) for fetch().
     * @var int
     */
    public $offset = 0;
    
    /**
     * This app only cares about employees as belonging to a certain company,
     * not for example, all of them globally, so Employees instances are tied
     * to one certain Company.
     * @param \Companies\Model\Company $company
     * @return $this
     */
    public function init(Company $company)
    {
        $this->company = $company;
        $this->setServiceLocator($company->getServiceLocator());
        $this->set_init();
        return $this;
    }
    
    /**
     * Fetches employees of the company.
     * @see init()
     * @return \Zend\Db\ResultSet\HydratingResultSet of Employee objects
     */
    public function fetch()
    {
        if ( ! $this->is_init() )
            throw new \Exception("Employees collection uninitiated.");
        
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        
        $sql = new Sql($adapter);
        
        $select = $sql->select('employee')->where(array('cid' => $this->company->id))
                ->offset($this->offset)->limit($this->limit);
        $statement = $sql->prepareStatementForSqlObject($select);
        $res = $statement->execute();
        
        $hydratedRes = new HydratingResultSet(new Hydrator, new Employee);
        if ($res instanceof ResultInterface && $res->isQueryResult()) {
            $hydratedRes->initialize($res);
        }
        return $hydratedRes;
    }
    
    /**
     * Adds a new record of the Employee object in the database.
     * @param Employee $employee - a populated object to add.
     */
    public function addNew(Employee $employee)
    {
        if ( ! $this->is_init() )
            throw new \Exception("Employees collection uninitiated.");
                
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        
        $sql = new Sql($adapter);
        $insert = $sql->insert('employee')
            ->columns(array('cid','name', 'lastname', 'email', 'startdate'))
            ->values(array(
                'cid' => $employee->cid,
                'name' => $employee->name,
                'lastname' => $employee->lastname, 
                'email' => $employee->email,
                'startdate' => $employee->startdate));
                
        $sql->prepareStatementForSqlObject($insert)->execute();
    }
    
    /**
     * Deletes a record.
     * @param $id
     * @throws Exception if no records are deleted
     * @return $this
     */
    public function delete($id)
    {
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $sql = new Sql($adapter);
        $delete = $sql->delete('employee')->where(array('id'=>$id));
        $res = $sql->prepareStatementForSqlObject($delete)->execute();
        if ( $res->getAffectedRows() < 1 )
            throw new \Exeption('Employee not deleted: DB affected rows count=0.');
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
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->services;
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
     * Company.
     * @var Companies\Model\Company 
     */
    private $company;
    
    
    /**
     * @var bool 
     */
    private $initialized = false;
}
