<?php 

/* 
    Function chaining method written by: Augustinas Malinauskas 
    amirascareless@gmail.com v.0.2 
    Allows to chain a sequence of functions that are going to  
    be performed on a specific variable. 
     
    Changelog: 
    v.0.2 
    -Modified apply method to get rid of eval function which is definitely wrong way to code. 
    -Added protection against non existing functions for 'add' method. In case non existing  
    function is being used, it is going to be skipped and the warning message will be issued. 
    However the code is still going to be executed. 
     
    Example: 
    $chain = new Chaining; 
    $chain->setObject("<b>this></b> is a crazy string ?>>"); 
     
    $chain->add("htmlspecialchars") 
          ->add("ucwords") 
          ->add("str_replace", array(' ', '_', $chain->me()) ); 
           
    to return the result use: 
    $chain->getObject(); 
     
    above is being transformed to: 
    str_replace(' ', '_', (ucwords(htmlspecialchars("<b>this></b> is a crazy string ?>>")))); 
     
    A constructor could also be used to define an object: 
    $chain = new Chaining("<b>this></b> is a crazy string ?>>"); 
*/ 

interface ChainingInterface { 

    /* 
        Name    : __construct 
        Required: optional 
        Purpose : constructor that allows to specify working variable 
        Params  : $inobj[optional] - specifying working variable 
        automatically add desirable variable. 
        Usage: $chain = new Chaining("my Lovely string"); 
    */ 
    public function __construct($inobj = NULL); 

     
    /* 
        Name    : setObject 
        Required: required if constructor is not used. 
        Purpose : to specify a working variable 
        Params  : $inobj - working variable 
        Returns : nothing             
        Usage   : $chain = new Chaining(); 
                  $chain->setObject("my Lovely string");  
                  //$chain->setObject($myVariable); 
    */ 
    public function setObject($inobj); 

     
    /* 
        Name    : getObject 
        Required: optional 
        Purpose : get the working variable set up using setObject($inobj)  
                  or using constructor. 
        Params  : none 
        Returns : working variable 
        Usage   : $chain = new Chaining("my Lovely string"); 
                  echo $chain->getObject(); 
    */ 
    public function getObject(); 

     
    /* 
        Same as getObject() only shorter 
    */ 
    public function me(); 

     
    /* 
        Name    : add 
        Required: optional (heart of the class) 
        Purpose : to apply a function to a variable 
        Params  : $function - name of the function 
                  $param[optional] - array containing arguments for that function 
        Returns : A pointer to the instance of the class 
        Usage   : $chain = new Chaining("my Lovely string"); 
                  $chain->add("strtoupper") 
                        ->add("str_replace", array("MY", "OUR", $chain->me())); 
                  echo $chain->getObject(); 
    */ 
    public function add($function, $param = 0); 
     
     
    /* 
        Name    : apply 
        Required: optional 
        Purpose : to apply a function to a variable, function must not return anything! 
        Params  : $function - name of the function 
                  $param[optional] - array containing arguments for that function 
        Returns : nothing 
        Usage   : function myfunc($var) 
                   { 
                     echo $var.$var; 
                  } 
                  $chain = new Chaining("my Lovely string"); 
                  $chain->add("strtoupper") 
                        ->apply("myfunc"); 
        Note: This is useful if you want to use this variable somewhere. 
              It should be the last chaining function because it does not return anything 
              $chain->add()->apply()->add() is INVALID, because apply() does not return a pointer. 
    */     
    public function apply($function, $param = 0); 
     
     
    /* 
        Name    : getType 
        Required: optional 
        Purpose : get type of the variable 
        Params  : none 
        Returns : a type of a variable 
        Usage   : $chain = new Chaining("my Lovely string"); 
                  echo "Type: ".$chain->getType(); 
                   
                  // $chain2 = new Chaining(array(1,2,3)); 
                  // echo "Type: ".$chain2->getType(); 
    */     
    public function getType(); 
     
     
    public function stringAppend($str); 

     
    public function stringPrepend($str); 
     
} 

class Chaining implements ChainingInterface{ 
     
    private $me; 
     
    public function __construct($inobj = NULL) 
    { 
        $this->me = $inobj; 
    } 
     
    public function setObject($inobj) 
    { 
        $this->me = $inobj; 
    } 
         
    public function getObject() 
    { 
        return $this->me; 
    } 

    public function me() 
    { 
        return $this->me; 
    } 
     
    public function add($function, $param = 0) 
    { 
        if (!function_exists($function)) { 
            echo "The function '".$function."' does not exists. Function skipped."; 
            return $this; 
        } 
         
        if ($param == 0) 
            $this->me = call_user_func($function, $this->me); 
        else 
            $this->me = call_user_func_array($function, $param); 
             
        return $this; 
    } 
     
    public function apply($function, $param = 0) 
    { 
        if ($param == 0) 
            call_user_func($function, $this->me); 
        else 
            call_user_func_array($function, $param); 
             
        return $this; 
    } 
     
    public function getType() 
    { 
        return gettype($this->me); 
    } 
     
    public function stringAppend($str) 
    { 
        $this->me = $this->me.$str; 
        return $this; 
    } 
     
    public function stringPrepend($str) 
    { 
        $this->me = $str.$this->me; 
        return $this; 
    } 
     
} 

     
?>
