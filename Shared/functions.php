<?php
include_once dirname(__FILE__) . "/database.php";
include_once dirname(__FILE__) . "/courses.php";
include_once dirname(__FILE__) . "/sessions.php";

//---------------------------------------------------------------------------------------------------------------
// endsWith - Tests if a string ends with another string - defaults to being non-case sensitive
//---------------------------------------------------------------------------------------------------------------
function endsWith($haystack,$needle,$case=true)
{
	if($case){return (strcmp(substr($haystack, strlen($haystack) - strlen($needle)),$needle)===0);}
	return (strcasecmp(substr($haystack, strlen($haystack) - strlen($needle)),$needle)===0);
}
//---------------------------------------------------------------------------------------------------------------
// bodywarning - prints a nicely formatted warning
//---------------------------------------------------------------------------------------------------------------
function bodywarning($msg)
{
		echo "<body>";
		echo "<span style='text-align:center;'><div class='warning'>";
		echo $msg."<hr/>";
		echo "Do not forget to use a recent browser and to enable Javascript.<br/>";
		echo "</div></span>";
		echo "</body>";		
}

//---------------------------------------------------------------------------------------------------------------
// jsvarget - Code to translate get variable to javascript through PHP
//---------------------------------------------------------------------------------------------------------------

function jsvarget($getname,$varname){
	if(isset($_GET[$getname])){
		echo 'var '.$varname.'="'.$_GET[$getname].'";';
	}else{
		echo 'var '.$varname.'="NONE!";';												
	}
}		

//---------------------------------------------------------------------------------------------------------------
// jsvarget - Code to translate session variable to javascript through PHP
//---------------------------------------------------------------------------------------------------------------

function jsvarsession($getname,$varname){
	if(isset($_SESSION[$getname])){
		echo 'var '.$varname.'="'.$_SESSION[$getname].'";';
	}else{
		echo 'var '.$varname.'="NONE!";';												
	}
}	
?>
