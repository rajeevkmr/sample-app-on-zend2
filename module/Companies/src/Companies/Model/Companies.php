<?php
namespace Companies\Model;

use Zend\Db\Sql\Sql;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Stdlib\Hydrator\ObjectProperty as hydrator;

use Companies\Model\Company;

/**
 * This class is used when working with company collections (e.g. retrieval)
 * and for CRUD ops (adding).
 */
class Companies implements ServiceLocatorAwareInterface
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
     * Fetches companies.
     * @return Zend\Db\ResultSet\HydratingResultSet collection of Company obj's
     */
    public function fetch()
    {
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        
        $sql = new Sql($adapter);
        
        $select = $sql->select('company')->offset($this->offset)->limit($this->limit);
        $statement = $sql->prepareStatementForSqlObject($select);
        $res = $statement->execute();
        
        $resultSet = new HydratingResultSet(new hydrator, new Company);
        if ( $res instanceof ResultInterface && $res->isQueryResult() )
        {
            $resultSet->initialize($res);
        }
        
        return $resultSet;
    }
    
    /**
     * Adds a new record of the Company object in the database.
     * @param Company $company - a populated object to add.
     */
    public function addNew(Company $company)
    {
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
              
        $sql = new Sql($adapter);
        $insert = $sql->insert('company')
            ->columns(array('name', 'addr', 'tel'))
            ->values(array(
                'name' => $company->name,
                'addr' => $company->addr, 
                'tel' => $company->tel));
                
        $sql->prepareStatementForSqlObject($insert)->execute();
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
}

