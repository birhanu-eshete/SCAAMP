<?php

/* 
 * This is the script to change mysql diretive values.
 * The script should consider the changeable access level of each directive and
 * treat each group of directives accordingly.
 */
include('../inc/header.php');
include ('../inc/databasehandler.php');
include ('../inc/utils.php');
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
            <h2 >MySQL Configuration Change Summary</h2>

            <?php
            $key_values = $_POST;  //get all the key values submitted 
            getCredentials();
            
            //copy the whole php.ini file to an array, one line at a time
            $handle = fopen($phpini_path, 'r'); //read each line on an array
            $original = array();
            $index = 0;
            while (!feof($handle))
                {
                 $original[$index] = fgets($handle);
                 $index++;
                }
            fclose($handle);

            // search the array and make replacement of value submitted per each directive
            foreach($key_values as $key => $value){
               $firstword = strtok($key, "_");
               $key = str_replace($firstword."_", $firstword.".",$key);
               
                for ($i=0;$i<count($original);$i++){
	   
                	if ( strcmp(substr($original[$i], 0, strlen($key)+1),$key." ") == 0 && strlen($value) != 0){  //if the line starts with the directive name and the value is not empty
                        $original[$i]=$key." = ".$value."\n";  //replace directive value by submitted value
                    }//if
                } //for
            } //foreach

            //rewrite the php.ini file with the modified array

            $handle = fopen($phpini_path, 'w'); // open php.ini for writitng
            for($i=0;$i<count($original);$i++){
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
