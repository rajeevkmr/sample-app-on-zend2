<?php

return array(
    'controllers' => array(
        'invokables' => array(
            'Companies\Controller\Companies' => 'Companies\Controller\CompaniesController',
            'Companies\Controller\Employees' => 'Companies\Controller\EmployeesController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'add_employee' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/add_employee/[:company_id]',
                    'constraints' => array(
                        'company_id' => '\d+',
                    ),
                    'defaults' => array(
                        'controller' => 'Companies\Controller\Employees',
                        'action' => 'add'
                    )
                ),
            ),
            'delete_employee' => array(
                'type' => 'literal',
                'options' => array(
                    'route' => '/delete_employee',
                    'defaults' => array(
                        'controller' => 'Companies\Controller\Employees',
                        'action' => 'delete'
                    )
                ),
            ),
            'companies_segment' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/companies[/][:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z]+',
                        'id' => '\d+',
                    ),
                    'defaults' => array(
                        'controller' => 'Companies\Controller\Companies',
                        'action' => 'list',
                    ),
                ),
            ),
            'employee_segment' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/employees[/][:action]',
                    'constraints' => array(
                        'action' => '[a-zA-Z]+'
                    ),
                    'defaults' => array(
                        'controller' => 'Companies\Controller\Employees',
                        'action' => 'notAvailable',
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_map' => array(
            'employee_partial' => __DIR__ . '/../view/companies/employees/employeePartial.phtml',
            'companies_sub_nav' => __DIR__ . '/../view/companies/companies_sub_nav.phtml',
        ),
        'template_path_stack' => array(
            'companies' => __DIR__ . '/../view',
        ),
        'strategies' => array(
           'ViewJsonStrategy',
        ),
    ),
);
