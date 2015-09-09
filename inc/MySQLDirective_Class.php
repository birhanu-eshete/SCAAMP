
<?php
include ('Directive_Class.php');

/**
 * This is a class for MySQL specific directives
 *
 * @author birhanum
 */
class MySQLDirective extends Directive{

public function  getCurrentValue($name,$path) {
	 $file = fopen($path, 'r') or exit("Unable to open file:". $path);
         $found = 0;
	//while end of file
	 while (!feof($file)) {
		$oneLine = fgets($file);
		if (strcmp(substr($oneLine, 0,1),";") == 0 || strcmp($oneLine,"\n") == 0){
		//ignore this line and continue.
			continue;
		}
                else{
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
                $curValue = strtok("=");
		$curValueNew = $curValue;
                $curValue = strtok($curValueNew,";");
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
