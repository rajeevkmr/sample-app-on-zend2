<?php
namespace Companies\Code;

/**
 * Provides a common interface to set and check a boolean value
 * for whether or not the object is initialized.
 */
interface CheckInitialized
{
    /**
     * Set object as initialized or not.
     * @param bool $bool
     * @return $this
     */
    public function set_init($bool);
    
    /**
     * Is object initiated?
     * @return bool 
     */
    public function is_init();
}
