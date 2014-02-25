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
     * Returns a 404 response if no company_id is given in route.
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
     * @return JSON JSON with keys status=ok|error and optional key message.
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
     * For JS AJAX validation - validates a property of Employee object.
     * @return JSON status=ok|error,message={string}
     */
    public function validateJSONAction()
    {
        try
        {
            $propName = $this->params()->fromPost('propName');
            if ( ! in_array($propName, Employee::propertyNames()) )
                throw new \Exception('Wrong property provided.');
                    
            $value = $this->params()->fromPost('value');
            if ( ! $propName || ! $value )
                throw new \Exception('No property and/or value given.');
            
            $employee = new Employee();
            $builder = new AnnotationBuilder();
            
            $form = $builder->createForm($employee);
            $form->setData(array($propName => $value))->isValid();
            
            $elements = $form->getElements();
            $element = $elements[$propName];
            
            if ( count($element->getMessages()) )
            {
                return $this->myJsonModel(array('status'=>'error',
                    'message' => $element->getMessages()));
            }
            else
            {
                return $this->myJsonModel(array('status'=>'ok',
                    'message' => ''));
            }
        }
        catch ( \Exception $e )
        {
            // @TODO: set status to syserror so JS doesn't confuse with 
            // validation error
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