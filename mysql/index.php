<?php
include ('../inc/header.php');
include ('../inc/databasehandler.php');
include ('../inc/utils.php');
include ('../inc/MySQLDirective_Class.php');
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
    <h2>MySQL Server Security Configuration Audit</h2>
    <br />
    <form action="<?php $_SERVER['PHP_SELF']?>" method="POST">
        <input type="submit" name="audit" value="Run Configuration Audit">
    </form>
    <br />

<?php
$num_directives =0;
   if (isset($_POST['audit']))
    {
        getCredentials();
        connecter($host, $user, $passwd);
        $directives = mysql_query("SELECT * FROM mysqlDirectives") or die ("Unable to fetch MySQL directives:".  mysql_error());
            
        if ($directives)
            {
            $PHP_directive_objects = array();
            $num_directives = mysql_num_rows($directives);
             $num_directives = mysql_num_rows($directives);
            for ($i=0;$i<$num_directives;$i++){
            	    $MySQL_directive_objects[$i] = new MySQLDirective();
                    $MySQL_directive_objects[$i]->setDirectiveName(mysql_result($directives, $i, 'directivename'));
                    $MySQL_directive_objects[$i]->setRecommendedValue(mysql_result($directives, $i, 'recomendedvalue'));
                    $MySQL_directive_objects[$i]->setCurrentValue($MySQL_directive_objects[$i]->getCurrentValue($MySQL_directive_objects[$i]->getDirectiveName(),$phpini_path));
                    $MySQL_directive_objects[$i]->setDescription(mysql_result($directives, $i, 'description'));
                    $MySQL_directive_objects[$i]->setRemark(mysql_result($directives, $i, 'remark'));
                    $MySQL_directive_objects[$i]->setPossibleValues("this is an array that comes from values table per each directive");
            }
            }
?>
    
    <table cellpadding="0" cellspacing="0">
        <tr> <td align="right"><b>Operating System:</b></td> <td><?php echo " ".getOS(); ?></td> </tr>
        <tr> <td align="right"><b>MySQL Version:</b></td> <td><?php echo " MySQL ".getMySQLversion(); ?></td> </tr>

    </table>
    <br />
     <form name="changeForm" method="POST" action="change.php">
     <table border="1" cellspacing="0" cellpadding="0" >
         <th bgcolor="white" colspan="7"><h1>MySQL Server Security Configuration Details</h1></th>

         <tr bgcolor="white">

             <td align="center"><b>No.</b></td><td align="center"><b>Name</b></td><td align="center"><b>Recommended</b></td>
             <td align="center"><b>Current</b></td><td align="center"><b>Remarks</b></td><td><b>Values</b></td>
         </tr>
         <?php
         $safe = 0;
         $unsafe = 0;
         $notUsed=0;
         for ($i= 0;$i<$num_directives;$i++)
         {

         ?>
         <tr>
             <td align="left"><?php echo ($i+1); ?></td>
             <td align="left"><?php echo $MySQL_directive_objects[$i]->getDirectiveName();?></td>
             <td align="left"><?php echo $MySQL_directive_objects[$i]->getRecomendedValue();?></td>
             <td align="left">
                <?php
                //if the directive has boolean value ini_get returns either 0 or empty string for Off. we need to do conversion in this case
                   $current_Value = "";
                   $current_value_returned = $MySQL_directive_objects[$i]->getCurrentValue($MySQL_directive_objects[$i]->getDirectiveName(),$phpini_path);
                    if((strcmp($MySQL_directive_objects[$i]->getRecomendedValue(),"On") == 0 || strcmp($MySQL_directive_objects[$i]->getRecomendedValue(),"Off") == 0) && strcmp($current_value_returned,"") == 0){
                       $MySQL_directive_objects[$i]->_currentValue = "Off";

                    }
                    if($current_value_returned!=-1){
                    	echo $MySQL_directive_objects[$i]->_currentValue;
                    }else {
                    	echo "<b>Not In Use </b>";
             }
             

                ?>
             </td>
             <td><?php echo $MySQL_directive_objects[$i]->getRemark();?></td>

             <?php
             $description = $MySQL_directive_objects[$i]->getDescription();
             //set the values list box or text box accordingly
             // names of directives are used as names of varaibles for POST
             if ($MySQL_directive_objects[$i]->getRecomendedValue() == "On" || $MySQL_directive_objects[$i]->getRecomendedValue() == "Off")
             {
             ?>
             <td>
                 Pick Value:<select name="<?php echo $MySQL_directive_objects[$i]->getDirectiveName();?>"><option value="">Select</option><option value="On">On</option><option value="Off">Off</option> </select>
                 <a href="javascript: void(0)" onclick="return showPopup('<?php echo $description; ?>','<?php echo $MySQL_directive_objects[$i]->getDirectiveName();?>')">[Help]</a>
             </td>
             <?php
             
             }
             else
             {
             ?>
             <td>
                 Set Value: <input type="text" name="<?php echo $MySQL_directive_objects[$i]->getDirectiveName();?>"/>
                 <a href="javascript: void(0)" onclick="return showPopup('<?php echo $description; ?>','<?php echo $MySQL_directive_objects[$i]->getDirectiveName();?>')">[Help]</a>
             </td>
             <?php
             }
             //disable making changes if recommended value is same as current value
             if ($MySQL_directive_objects[$i]->_currentValue == $MySQL_directive_objects[$i]->_recommendedValue)
                     {
                     ?>

                <script language="javascript" type="text/javascript">
                    var directiveName =<?php echo $MySQL_directive_objects[$i]->_directiveName;?>;
                    document.changeForm.directiveName.disabled=true;

               </script>
                <?php
                     }
             if($MySQL_directive_objects[$i]->_currentValue!=-1){
             $score = $MySQL_directive_objects[$i]->computeSafetyScore($MySQL_directive_objects[$i]->getRecomendedValue(),$MySQL_directive_objects[$i]->_currentValue);
                    if ($score == 1)
                        $safe++;
                    elseif($score == 0)
                        $unsafe++;
             }

             else if ($MySQL_directive_objects[$i]->_currentValue==-1){
                 $notUsed++;
             }
             ?>
        </tr>
        <?php
         }

         ?>

        <tr bgcolor="white">

            <td colspan="7" align="right"><h2>Click this button to save configuration changes</h2><br /><input type="submit" name="set" value="[CHANGE CONFIGURATION]"><br /><br /></td>
        
        </tr>
     </table>
         <br> <br>
          <table border ="1" cellpadding="0" cellspacing="0">
              <th bgcolor="white" colspan="5"><h1>MySQL Security Configuration Safety Report</h1></th>

         <tr bgcolor="white">

             <td align="center"><b>Total # of Directives(100%)</b></td><td align="center"><b>Safely Set</b></td>
             <td align="center"><b>Unsafely Set</b></td><td align="center"><b>Not Used/No Value</b></td><td align="center"><b>Safely Set(out of 100%)</b></td>
         </tr>
        <tr>
             <td align="center"><b><?php echo $num_directives; ?></b></td>
             <td align="center"><b><?php echo $safe." out of ".($safe+$unsafe)." directives" ;?></b></td>
             <td align="center"><b><?php echo $unsafe." out of ".$num_directives." directives" ;?></b></td>
              <td align="center"><b><?php echo $notUsed." out of ".$num_directives." directives" ;?></b></td>
             <td align="center"><b><?php echo number_format(($safe/($safe+$unsafe))*100,2,'.','') ;?></b></td>
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
