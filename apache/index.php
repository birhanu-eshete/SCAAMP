<?php
include ('../inc/header.php');
include ('../inc/databasehandler.php');
include ('../inc/utils.php');
include ('../inc/ApacheDirective_Class.php');
?>

<div id="navigation">
        <ul>
            <li><a href="../index.php"><span>home</span></a></li>
            <li><a href="../notes.php"><span>notes</span></a></li>
            <li><a href="../credits.php"><span>credits</span></a></li>
            <li><a href="../contact.php"><span>contact</span></a></li>
        </ul>
        <div class="cl">&nbsp;</div>
</div>
</div>
	<!-- Main -->
 <div id="main">
    <br />
    <h2>Apache HTTP Server Configuration Audit</h2>
    <br />
    <form action="<?php $_SERVER['PHP_SELF']?>" method="POST">
        <input type="submit" name="audit" value="Run Configuration Audit">
    </form>
    <br />

<?php
   if (isset($_POST['audit']))
    {
        getCredentials();
        connecter($host, $user, $passwd);
        //replace with ..
        $path = $httpdconf_path;
        
        $directives = mysql_query("SELECT * FROM apacheDirectives") or die ("Unable to fetch Apache directives:".  mysql_error());        
        
        if ($directives ){    
        	$Apache_directive_objects = array();
            $num_directives = mysql_num_rows($directives);
            for ($i=0;$i<$num_directives;$i++)
                {
                    $Apache_directive_Objects[$i] = new ApacheDirective();
                    $value = $Apache_directive_Objects[$i]->processHttpd($path,mysql_result($directives, $i, 'directivename'));
                    if (strlen($value["value"]) !=0 ) {                 
                    	$Apache_directive_Objects[$i]->setDirectiveName(mysql_result($directives, $i, 'directivename'));
                    	$Apache_directive_Objects[$i]->setRecommendedValue(mysql_result($directives, $i, 'recomendedvalue'));           	
                    	//echo "Setting current value ".$value["value"]."<br />";
                    	$Apache_directive_Objects[$i]->setCurrentValue($value["value"]);
                    	$Apache_directive_Objects[$i]->setDescription(mysql_result($directives, $i, 'description'));
                    	$Apache_directive_Objects[$i]->setRemark(mysql_result($directives, $i, 'remark'));
                    	$Apache_directive_Objects[$i]->setPossibleValues("this is an array that comes from values table per each directive");
                    }
                }
        }?>
    
    <table cellpadding="0" cellspacing="0">
        <tr> <td align="right"><b>Operating System:</b></td> <td><?php echo " "; echo getOS(); ?></td> </tr>
        <tr> <td align="right"><b>Apache Version:</b></td> <td><?php echo " "; echo getApacheVersion(); ?></td> </tr>

    </table>
    <br />
     <form method="POST" action="change.php">
     <table border="1" cellspacing="0" cellpadding="0" >
         <th bgcolor="white" colspan="7"><h1>Apache Security Configuration Details </h1></th>

         <tr bgcolor="white">
             <td align="center"><b>No.</b></td><td align="center"><b>Name</b></td><td align="center"><b>Recommended</b></td>
             <td align="center"><b>Current</b></td><td align="center"><b>Remarks</b></td><td align="center"><b>Values</b></td>
         </tr>
         <?php
         $safe = 0;
         $unsafe = 0;
          $NumOfUnspecifiedDirective = 0;
          
         for ($i=0;$i<$num_directives;$i++) {?>
         <tr>       
         <?php 
         	$directiveName = $Apache_directive_Objects[$i]->getDirectiveName();
         	$value = $Apache_directive_Objects[$i]->processHttpd($path,$directiveName);
         	$possValue = $Apache_directive_Objects[$i]->fetchValueSet($directiveName);
         
          	if (strlen($Apache_directive_Objects[$i]->getCurrentValue($directiveName,$path)) != 0 ){?>
          	<td align ="center"><?php echo ($i+1 - $NumOfUnspecifiedDirective); ?></td>
            	<td align ="left"><?php echo $directiveName;?></td>
            	<td align ="left"><?php echo $Apache_directive_Objects[$i]->getRecomendedValue();?></td>
                <?php
                if ($value["value"]!=null){

                ?>
                <td align ="left"><?php echo  $value["value"];//." (at line ". $value["line"].")";?></td>
                <?php
                }

                else
                     echo "<b>Not In Use</b>";
                ?>
             	<td align = "left"><?php echo $Apache_directive_Objects[$i]->getRemark();?></td>
          	<?php
             $description = $Apache_directive_Objects[$i]->getDescription();
             //set the values list box or text box accordingly
             // names of directives are used as names of varaibles for POST
           	if (count($possValue) > 0) { ?>
             	<td align="left">
                 Pick Value:<select name="<?php echo $directiveName;
                  foreach ($possValue as $val) {?>
                  	"><option value="<?php echo $val;?>"><?php echo $val?></option> <?php }?>
                	  </select> <br />                
             	    <a href="javascript: void(0)" onclick="return showPopup('<?php echo $description; ?>','<?php echo $directiveName;?>')">[Help]</a>
             	</td>
             	<?php
             }
             else
             {
             ?>
             <td align="left">
                 Set Value: <input type="text" name="<?php echo $directiveName;?>"/>
                 <a href="javascript: void(0)" onclick="return showPopup('<?php echo $description; ?>','<?php echo $directiveName;?>')">[Help]</a>
             </td>
             <?php
             }
         }
         else  {
         	$NumOfUnspecifiedDirective =  $NumOfUnspecifiedDirective + 1;
         	
         } 
         
        	if (strlen($Apache_directive_Objects[$i]->getCurrentValue($directiveName,$path)) != 0) {
		$score = $Apache_directive_Objects[$i]->computeSafetyScore($Apache_directive_Objects[$i]->getRecomendedValue(),$Apache_directive_Objects[$i]->getCurrentValue($directiveName,$path));
            if ($score == 1) {                        
            	$safe  = $safe + 1;
            } elseif($score == 0) {                        
            	$unsafe  = $unsafe + 1;
            }
        	}
             ?>
        </tr>
        <?php
         }

         
         $num_directives = $num_directives - $NumOfUnspecifiedDirective;
         ?>

        <tr bgcolor="white">

            <td colspan="7" align="right"><h2>Click this button to save configuration changes</h2><br /><input type="submit" name="set" value="[CHANGE CONFIGURATION]"><br /><br /></td>
        
        </tr>
     </table>
         <br> <br>
          <table border ="1" cellpadding="0" cellspacing="0">
              <th bgcolor="white" colspan="5"><h1>Apache Security Configuration Safety Report</h1></th>

         <tr bgcolor="white">

             <td align="center"><b>Total # of Directives (100%)</b></td><td align="center"><b>Safely Set</b></td>
             <td align="center"><b>Unsafely Set </b></td><td align="center"><b>Not Used/No Value </b></td><td align="center"><b>Safety (out of 100%)</b></td>
         </tr>
        <tr>
             <td align ="center"><b><?php echo $num_directives; ?></b></td>
             <td align ="center"><b><?php echo $safe." out of ".$num_directives." directives" ;?></b></td>
             <td align ="center"><b><?php echo $unsafe." out of ".$num_directives." directives" ;?></b></td>
             <td align ="center"><b><?php echo $NumOfUnspecifiedDirective." out of ".($num_directives+$NumOfUnspecifiedDirective)." directives" ;?></b></td>
             <td align ="center"><b><?php echo number_format (($safe/$num_directives)*100,2,'.','') ;?></b></td>
         </tr>
        
    </table>

   </form>
         <br /><br />


   <?php
   
    }
   
    ?>
   
</div>
            	<!-- END Main -->
                 <div id="description" class="separator"></div>
	<!-- Footer -->
<?php include ('../inc/footer.php'); ?>