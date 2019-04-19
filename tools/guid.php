<?php
   class Guid {
       var $guidText;

       function initialize()
       {
           $this->guidText = md5(uniqid(rand(),true));
           return $this->guidText;
       }
       function toString($separator = '', $case = false)
       {
           $str =& $this->guidText;
           if ($case)
           {
               switch ($case)
               {
               case 'uc':
                 $str = strtoupper($str);
               break;
               case 'lc':
                 $str = strtolower($str);
               break;
               default:
                 $str = $str;
               }
           }

           $str = substr($str,0,8) . $separator .
               substr($str,8,4) . $separator .
               substr($str,12,4). $separator .
               substr($str,16,4). $separator .
               substr($str,20);
           return htmlspecialchars($str);
       }

       // do some other stuff
   }

   $obj = new Guid();
   for ($i=0;$i<14;$i++){
		$obj->initialize();
       echo  strtoupper($obj->toString('-'))."<br/>";
	}
   

?>