<?php

class DatabaseConnect
{
  public function __construct($Db_Host, $Db_UserName, $Db_Password)
   {
    if(!@$this->Connect($Db_Host, $Db_UserName, $Db_Password))
	 {
      echo"connection Failed";
     }
    else if($this->Connect($Db_Host, $Db_UserName, $Db_Password))
	 {
 
	      mysql_select_db("buzzbe6_dominee");
     	 }
  }


 public function connect($Db_Host, $Db_UserName, $Db_Password)
 {
  if(!mysql_connect($Db_Host, $Db_UserName, $Db_Password))
   {
    return false;
   }
 else
  {
   return true;
  }
 }
}
 $con_onject=new DatabaseConnect('localhost','buzzbtfx_buzzbee','buzzbee123');
 //$con_onject=new DatabaseConnect('localhost','root','');
  //$mysqli=$con_object->connect('localhost','root','');

?>