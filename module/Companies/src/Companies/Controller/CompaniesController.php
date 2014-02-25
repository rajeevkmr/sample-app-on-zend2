<?php
namespace Companies\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Companies\Forms\CompanyForm;
use Zend\Mvc\InjectApplicationEventInterface;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Companies\Model\Company;

class CompaniesController extends AbstractActionController
implements InjectApplicationEventInterface
{
    /* List all companies. This is the default action. */
    public function listAction()
    {
        $companies = $this->getServiceLocator()->get('get_companies');
        
        $companies->limit = 100;
        $res = $companies->fetch();
        
        $this->layout()->addChild($this->subnav(), 'subnav');
        return new ViewModel(array('companies' => $res,
            'limit' => $companies->limit));
    }
    
    /* Add a new company. */
    public function addAction()
    {
        $form = new CompanyForm();
        
        if ( $this->getRequest()->isPost() )
        {
            $company = $this->getServiceLocator()->get('company');
            $form->setInputFilter($company->getInputFilter());
            
            $form->setData($this->getRequest()->getPost());
            if ( $form->isValid() )
            {
                $company->addData($form->getData());
                $companies = $this->getServiceLocator()->get('get_companies');
                $companies->addNew($company);
                
                $this->flashMessenger()->addMessage('Company created.');
                $this->redirect()->toRoute('companies_segment', array('action' => 'list'));
            }
        }

        $this->layout()->addChild($this->subnav(), 'subnav');
        return new ViewModel(array('form' => $form));
    }
    
    /**
     * View company.
     */
    public function viewAction()
    {
        $id = $this->params()->fromRoute('id', null);
        if ( ! $id ) {
            throw new \Exception('No company id given.');
        }
        $company = $this->getServiceLocator()->get('company')->init($id);
        $employees = $company->getEmployees()->fetch();
        
        $this->layout()->addChild(
                $this->subnav()->setVariable('company', $company),
                'subnav'
        );
        return new ViewModel(array('company' => $company,
            'employees' => $employees));
    }
    
    /**
     * For JS AJAX validation - validates a property of Company object.
     * @return JSON status=ok|error,message={string}
     */
    public function validateJSONAction()
    {
        try
        {
            $propName = $this->params()->fromPost('propName');
            if ( ! in_array($propName, Company::propertyNames()) )
                    throw new \Exception('Wrong property provided.');
                    
            $value = $this->params()->fromPost('value');
            if ( ! $propName || ! $value )
                throw new \Exception('No property and/or value given.');
         
            $form = new CompanyForm();
            $form->setInputFilter(
                    $this->getServiceLocator()->get('company')->getInputFilter());
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
     * Info page
     */
    public function infoAction()
    {
        return array();
    }
    
    /**
     * Returns an action specific navigation ViewModel
     * @param array $assocArray
     * @return ViewModel
     */
    public static function subnav()
    {
        $subnav = new ViewModel();
        return $subnav->setTemplate('companies_sub_nav');
    }
    
    protected function myJsonModel($obj)
    {
        $view = new JsonModel($obj);
        $view->setTerminal(true);
        return $view;
    }
    
    private $subnav;
}