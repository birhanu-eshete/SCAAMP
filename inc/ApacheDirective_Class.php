<?php
include ('Directive_Class.php');

/**
 * This is a class for Apache specific directives
 *
 * @author birhanum
 */
class ApacheDirective extends Directive{

public function fetchValueSet ($name) {

	$map = array("ServerSignature" => array('Select','On','Off','EMail'
													), 
				 "ServerTokens" => array('Select','Full','OS','Minor','Minimal','Major','Prod'
													),
				 "LogLevel"		=> array('Select','debug', 'info', 'notice', 'warn', 'error', 
				 						'crit','alert', 'emerg'
     												),
     			 "TraceEnable"  => array('Select','On','Off','Extended'
     												),
     			 "AllowOverride"=> array('Select','All','None','Directive-type'
     												),
     			 "KeepAlive"    => array('Select','On','Off'
     												),
     			 "AcceptMutex"  => array('Select','Default','Method'
     												),
     			 "Options"	    => array('Select','None', 'All', 'ExecCGI','FollowSymLinks','Includes',
     			 					'IncludesNOEXEC','Indexes','MultiViews','SymLinksIfOwnerMatch' 			
     												)
     												);
     																																																				
     foreach ($map as $j => $i) {     	
     	if ($j == $name) {
     	//	testing
     		//foreach ($i as $val){
     			//echo "$val\n";
     		//}
     		//echo count($i);
     		return ($i);
     		break;
     	}
     	
     }
}
 
public  function processHttpd ($path,$name) {
	
	$file = fopen($path, 'r') or exit("Unable to open file: $path!"); 
   //Local variables;
	$tagCounter = 0; 
	$found = 0;
	$curPosition  = 0;
   //echo "path ... ".$path." and directive ... ".$name."<br />";
	//while end of file                 
	while (!feof($file)) {    	    
		$oneLine = fgets($file);
		$curPosition = $curPosition + 1;		
		
		if (strcmp(substr($oneLine, 0,1),"#") == 0 || strcmp($oneLine,"\n") == 0) {
			//ignore this line and continue.
			continue;
		}
		
		if (strcmp(substr($oneLine,0,1),"<") == 0 && strcmp(substr($oneLine,1,1),"/") != 0) {
			//ignore this line but increament the 
			$tagCounter = $tagCounter + 1;
		}elseif (strcmp(substr($oneLine,0,1),"<") == 0 && strcmp(substr($oneLine,1,1),"/") == 0) {
					//ignore this line.
			$tagCounter = $tagCounter - 1;
		}

		//No more tags
		if ($tagCounter == 0) {
			$firstword = strtok($oneLine, " ");  
			
			if (strcmp($firstword,$name) == 0) {
				$found = 1; 
				break;
			}	
		}
	}
	
	if ($found === 1) {			
		$curValue = str_replace($name, " ",$oneLine);
		if (strlen(trim($curValue )) != 0 ) {
			$valueWithLine = array("line"=>$curPosition,"value" => trim($curValue) );
		}
		else {
			$valueWithLine= array("line"=>$curPosition,"value" => "no value");
		}
		return $valueWithLine;
	}else { 
		return (array("line"=>$curPosition,"value" => null));
	}
	//We're done with the file. Thus, we must close it!
	fclose($file);
		
	}
       

public function  computeSafetyScore($recomended, $current) {
    //unsafe by default unless checked 
    $safetyScore =0;
   $recomended = trim($recomended);
   $current = trim($current);
   
   if (strlen($current) != 0 ) {
   	
   	if (strcasecmp($recomended,$current) == 0) {
   		$safetyScore = 1;
   	}
    else {
    	$safetyScore = 0;
    	
   }
   return $safetyScore;
}
}
}

?>
