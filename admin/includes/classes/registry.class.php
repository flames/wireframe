<?php
 
class registry
{
   /**
    * @var object Instance of registry class
    */
    protected static $instance = NULL;
 
 
    /**
     * Get an instance of registry class
     *
     * @return object Instance of registry class
     */
    public static function getInstance()
    {
        if (self::$instance === NULL)
        {
            self::$instance = new self();
        }
        return self::$instance;
    }
 
 
    /**
     * Constructor - start session
     */
    protected function __construct()
    {
        session_start();
        if (isset( $_SESSION['_registry'] ) === FALSE)
        {
            $_SESSION['_registry'] = array();
        }
    }
 
 
    /**
     * Clone - prevent additional instances of the class
     */
    private function __clone() {}
 
 
    /**
     * Magic Method to set a registry variable
     *
     * @param  string  $key   Registry array key
     * @param  string  $value Value of registry key
     * @return mixed   TRUE on success otherwise FALSE
     */
    public function __set( $key, $value )
    {
        if (isset( $_SESSION['_registry'][$key] ) === FALSE)
        {
            $_SESSION['_registry'][$key] = $value;
            return TRUE;
        }
        return FALSE;
    }
 
 
    /**
     * Magic Method to get a registry variable
     *
     * @param  string  $key   Registry array key
     * @return bool    TRUE on success otherwise NULL
     */
    public function &__get( $key )
    {
        if (isset( $_SESSION['_registry'][$key] ))
        {
            return $_SESSION['_registry'][$key];
        }
        return NULL;
    }
 
 
    /**
     * Unset a registry variable
     *
     * @param  string  $key   Registry array key
     * @return bool    TRUE on success otherwise FALSE
     */
    public function __unset( $key )
    {
        if (isset( $_SESSION['_registry'][$key] ))
        {
            unset( $_SESSION['_registry'][$key] );
            return TRUE;
        }
        return FALSE;
    }
 
 
    /**
     * Reset registry
     *
     * @return bool    TRUE
     */
    public function reset()
    {
        $_SESSION['_registry'] = array();
        return FALSE;
    }
}
 
?>