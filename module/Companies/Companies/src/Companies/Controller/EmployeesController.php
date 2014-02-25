<?php
namespace Companies\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Form\Annotation\AnnotationBuilder;
use Companies\Model\Employee;
use Companies\Controller\CompaniesController;
use \Zend\View\Model\JsonModel;

class EmployeesController extends AbstractActionController
{
    /**
     * Add a new employee.
     */
    public function addAction()
    {
        $company_id = $this->params()->fromRoute('company_id');
        if ( ! $company_id )
            return $this->notAvailableAction();
        
        $company = $this->getServiceLocator()->get('company')
                ->init($company_id);
        
        $employee = new Employee();
        
        $builder = new AnnotationBuilder();
        $form = $builder->createForm($employee);
        
        if ( $this->getRequest()->isPost() )
        {
            $form->setData($this->getRequest()->getPost());
            if ( $form->isValid() )
            {
                $employee->addData($form->getData());
                $this->getServiceLocator()->get('employees')
                ->init($company)->addNew($employee);
                
                $this->flashMessenger()->addMessage('Employee created.'); 
                $this->redirect()->toRoute('companies_segment',
                    array('action' => 'view', 'id' => $company->id));
            }
        }
        
        // Use CompaniesController's subnav because we're still pretty
        // much inside of a company.
        $this->layout()->addChild(
                CompaniesController::subnav()->setVariable('company', $company),
                'subnav');
        return array('form' => $form, 'company' => $company);
    }
    
    /**
     * Deletes an employee. 
     * @return JSON
     */
    public function deleteAction()
    {
        try
        {
            $id = $this->params()->fromPost('id');
            if ( ! $id )
                throw new \Exception('No employee id.');
            
            $this->getServiceLocator()->get('employees')
                    ->delete($id);

            return $this->myJsonModel(array('status'=>'ok'));
        }
        catch ( \Exception $e )
        {
            return $this->myJsonModel(array('status'=>'error',
                'message' => $e->getMessage()));
        }
    }
    
    /**
     * Returns 404 response.
     **/
    public function notAvailableAction()
    {
        return $this->notFoundAction();
    }
   
    protected function myJsonModel($obj)
    {
        $view = new JsonModel($obj);
        $view->setTerminal(true);
        return $view;
    }
}