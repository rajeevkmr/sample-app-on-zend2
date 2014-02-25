<?php
namespace Companies\Model;

use Zend\Validator\AbstractValidator;

/* Validator for Employee::startdate field. */
class EmployeeStartdateValidator extends AbstractValidator 
{
    const BAD = 'f';
    
    protected $messageTemplates = array(
        self::BAD => "Date is incorrect: please use format year-month-day, i.e., 2014-02-19 and check for Feb 30th, leap years, etc."
    );
    
    // Code from http://lt1.php.net/checkdate
    function validateDate($date, $format = 'Y-m-d')
    {
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }
    
    public function isValid($value)
    {
        $this->setValue($value);
               
        if ( ! $this->validateDate($value) )
        {
            $this->error(self::BAD);
            return false;
        }
        
        return true;
    }
}