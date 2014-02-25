<?php
namespace Companies\Model;

use Zend\Form\Annotation;

/**
 * Employee entity: this class represents data of a single employee.
 * @Annotation\Name("employee")
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 */
class Employee
{
    /**
     * Employee ID
     * @var int
     * @Annotation\Exclude()
     */
    public $id;
    
    /**
     * Company ID to which it belongs.
     * @var int
     */
    public $cid;
    
    /**
     * First name
     * @var string
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":2, "max":20}})
     * @Annotation\Validator({"name":"Regex", "options":{"pattern":"/^[a-zA-Z]+$/"}})
     * @Annotation\Options({"label":"First name:"})
     */
    public $name;
    
    /**
     * Last name
     * @var string
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":2, "max":20}})
     * @Annotation\Validator({"name":"Regex", "options":{"pattern":"/^[a-zA-Z]+$/"}})
     * @Annotation\Options({"label":"First name:"})
     */
    public $lastname;
    
    /**
     * First name
     * @var string
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"max":30}})
     * @Annotation\Validator({"name":"EmailAddress"})
     * @Annotation\Options({"label":"E-mail address:"})
     */
    public $email;
    
    /**
     * Date of employment.
     * @var string format Y-m-d
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"Date of employment:"})
     * @Annotation\Validator({"name":"Companies\Model\EmployeeStartdateValidator"})
     */
    public $startdate;
    
    // end of employee database fields.
    
    /**
     * This field is form the form builder.
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Submit"})
     */
    public $submit;
    
    /**
     * Populates the Employee object with data given.
     * @param mixed $data an indexed key-value collection.
     * @return $this
     */
    public function addData($data)
    {
        foreach ( array('id', 'cid', 'name','lastname','email','startdate') as $field )
        {
            $this->$field = !empty($data[$field]) ? $data[$field] : null;
        }
        return $this;
    }
}
