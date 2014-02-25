<?php
namespace Companies\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Companies\Forms\CompanyForm;
use Zend\Mvc\InjectApplicationEventInterface;
use Zend\View\Model\ViewModel;

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
                
                // for some reason breaks here - no time to fix right now.
                // $this->flashMessenger()->addMessage('Company record created.'); 
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
        $employees = $company->getEmployees()->init($company)->fetch();
        
        $this->layout()->addChild(
                $this->subnav()->setVariable('company', $company),
                'subnav'
        );
        return new ViewModel(array('company' => $company,
            'employees' => $employees));
    }
    
    /**
     * Info page
     */
    public function infoAction()
    {
        return array();
    }
    
    /**
     * Returns a ViewModel for company related links.
     * @param array $assocArray
     * @return ViewModel
     */
    public static function subnav()
    {
        $subnav = new ViewModel();
        return $subnav->setTemplate('companies_sub_nav');
    }
    
    private $subnav;
}