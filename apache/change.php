<?php

/* 
 * This is the script to change apache diretive values.
 * The script should consider the changeable access level of each directive and
 * treat each group of directives accordingly.
 */
include('../inc/header.php');
include ('../inc/databasehandler.php');
include ('../inc/utils.php');
include ('../inc/ApacheDirective_Class.php');
?>
		<div id="navigation">
			<ul>
			    <li><a href="index.php"><span>home</span></a></li>
			    <li><a href="notes.php"><span>notes</span></a></li>
			    <li><a href="credits.php"><span>credits</span></a></li>
			    <li><a href="contact.php"><span>contact</span></a></li>
			</ul>
			<div class="cl">&nbsp;</div>
		</div>
	</div>
	<!-- END Logo + Description + Navigation -->
	<!-- Header -->
	<div id="main">
            <h2 >Apache Configuration Change Summary</h2>
     <?php
       $key_values = $_POST;  //get all the key values submitted 
       getCredentials();
       $apacheDirectiveObjects = new ApacheDirective();
       //copy the whole php.ini file to an array, one line at a time
       $handle = fopen($httpdconf_path, 'r'); //read each line on an array
       $original = array();
       $index = 0;
       while (!feof($handle))
                {
                 $original[$index] = fgets($handle);
                 //echo $original[$index]."<br />";
                 $index++;
                }
            fclose($handle);
			
            //strcasecmp(substr($oneLine,0,2),"</") == 0
            // search the array and make replacement of value submitted per each directive
              $length =  count($key_values);
              $counter = 0;
              foreach($key_values as $key => $newValue){
              	$counter = $counter + 1;
              	if ($counter < $length) {
              		$key2 = explode("_", $key);
              		if (strlen($newValue) != 0 ){   
              			$name = trim($key2[0]);
              			$value = $apacheDirectiveObjects->processHttpd(trim($httpdconf_path),$name);
              			$curValue = $value["value"];
              			$lineNumber = $value["line"];  
              			
              			for ($i = 0;$i<count($original);$i++){   
              				if ($lineNumber == ($i+1)) {
              					if (strcmp($curValue,$newValue) != 0 && strlen($newValue) != 0) { 
              						if (strcmp($newValue, "Select") != 0) {
              							$original[$i] = $name." ".$newValue."\n";
              						}
              						break;
              					}//inner if
              				}//outer if
              			}//inner for 
              		}//if
              	}else {
              		break;
              	} 
              }//foreach

            //rewrite the php.ini file with the modified array

            $handle = fopen($httpdconf_path, 'w'); // open httpd.conf for writitng
            for($i=0;$i<count($original)-1;$i++){
                fwrite ($handle, $original[$i]);
            }
            fclose($handle);
            echo "<br><h3>"."Your configuration changes are successfully saved. Please reset the server before you "
            ?>
            
            <a href="index.php">Redo Audit to Verify Changes </h3></a>
	</div>
        <!-- END Main -->

<div id="description" class="separator"></div>

	
<!-- Footer -->
<?php
include ('../inc/footer.php');
?>
