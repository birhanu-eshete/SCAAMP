
<?php
include ('Directive_Class.php');

/**
 *
 * @author birhanum
 */
class PHPDirective extends Directive{

public function  getCurrentValue($name,$path) {
        $file = fopen($path, 'r') or exit("Unable to open file:". $path);
  	$found = 0;
	//while end of file
	while (!feof($file)) {
		$oneLine = fgets($file);
               // echo $oneLine."<br>";
		if (strcmp(substr($oneLine, 0,1),";") == 0 || strcmp($oneLine,"\n") == 0){
		//ignore this line and continue.
			continue;
		}
                else{
                    //echo $oneLine."<br>";
                    $firstword = strtok($oneLine, "=");
                    if (strcmp(trim($firstword),$name) == 0) {
				$found = 1;
				break;
		    }

               }
        }
        fclose($file);

	if ($found == 1) {
		$curValue = strtok($oneLine,"=");
                //echo $curValue."<br>";
                $curValue = strtok("=");
		$curValueNew = $curValue;
		$curValue = strtok($curValueNew,";");
                //echo $curValue."<br>";
		if (strlen(trim($curValue )) != 0 ) {
                    return trim($curValue);
		}

               else{
                   return NULL;
                }
        }
                else {
                    return -1;
                }

	
    }

}

?>
