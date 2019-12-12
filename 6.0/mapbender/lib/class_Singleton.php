<?php
class Singleton
{
   // Hold an instance of the class
   private static $instance;
 
   // A private constructor; prevents direct creation of object
   private function __construct()
   {
       echo 'I am constructed';
   }

   // The singleton method
   public static function singleton($classname)
   {
       if (!isset(self::$instance)) {
           self::$instance = new $classname;
       }

       return self::$instance;
   }
}
?>