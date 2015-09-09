<?php
include ('../inc/header.php');
include ('../inc/databasehandler.php');
include ('../inc/utils.php');
include ('../inc/PHPDirective_Class.php');
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
    <h2>Select Security Configuration Mode:</h2>
    <br />
    <form action="<?php $_SERVER['PHP_SELF']?>" method="POST">
        Audit Mode:<select name="mode">
            <option value="Deployment">Deployment</option>
            <option value="Development">Development</option>

        </select>
        <input type="submit" name="audit" value="Run Configuration Audit">
    </form>
    <br />

<?php
$num_directives =0;
   if (isset($_POST['audit']))
    {
        $mode =$_POST['mode'];
        getCredentials();
        connecter($host, $user, $passwd);
        $directives = mysql_query("SELECT * FROM phpdirectives") or die ("Unable to fetch PHP directives:".  mysql_error());
            
        if ($directives)
            {
            $PHP_directive_objects = array();
            $num_directives=mysql_num_rows($directives);
            for ($i=0;$i<$num_directives;$i++)
                {
                    $PHP_directive_Objects[$i] = new PHPDirective();
                    $PHP_directive_Objects[$i]->setDirectiveName(mysql_result($directives, $i, 'directivename'));
                    $PHP_directive_Objects[$i]->setRecommendedValue(mysql_result($directives, $i, 'recomendedvalue'));
                    $PHP_directive_Objects[$i]->setCurrentValue($PHP_directive_Objects[$i]->getCurrentValue($PHP_directive_Objects[$i]->getDirectiveName(),$phpini_path));
                    $PHP_directive_Objects[$i]->setDescription(mysql_result($directives, $i, 'description'));
                    $PHP_directive_Objects[$i]->setRemark(mysql_result($directives, $i, 'remark'));
                    $PHP_directive_Objects[$i]->setPossibleValues("this is an array that comes from values table per each directive");
                }
            }
?>
    
    <table cellpadding="0" cellspacing="0">
        <tr> <td align="right"><b>Mode Selected:</b></td> <td><?php echo $mode; ?></td> </tr>
        <tr> <td align="right"><b>Operating System:</b></td> <td><?php echo getOS(); ?></td> </tr>
        <tr> <td align="right"><b>PHP Version:</b></td> <td><?php echo getPHPVersion(); ?></td> </tr>

    </table>
    <br />
     <form name="changeForm" method="POST" action="change.php">
     <table border="1" cellspacing="0" cellpadding="0" >
         <th bgcolor="white" colspan="7"><h1>PHP Security Configuration Directives for <?php echo $mode;?></h1></th>

         <tr bgcolor="white">

             <td align="center"><b>No.</b></td><td align="center"><b>Name</b></td><td align="center"><b>Recommended</b></td>
             <td align="center"><b>Current</b></td><td align="center"><b>Remarks</b></td><td><b>Values</b></td>
         </tr>
         <?php
         $safe=0;
         $unsafe=0;
         $notUsed=0;
         for ($i=0;$i<$num_directives;$i++)
         {

         ?>
         <tr>
             <td><?php echo ($i+1); ?></td>
             <td><?php echo $PHP_directive_Objects[$i]->getDirectiveName();?></td>
             <td><?php echo $PHP_directive_Objects[$i]->getRecomendedValue();?></td>
             <td>
                <?php
                //if the directive has boolean value ini_get returns either 0 or empty string for Off. we need to do conversion in this case
                   $current_Value="";

                   $current_value_returned =$PHP_directive_Objects[$i]->getCurrentValue($PHP_directive_Objects[$i]->getDirectiveName(),$phpini_path);
                   
                    if((strcmp($PHP_directive_Objects[$i]->getRecomendedValue(),"On")==0 || strcmp($PHP_directive_Objects[$i]->getRecomendedValue(),"Off")==0) && strcmp($current_value_returned,"")==0){
                       $PHP_directive_Objects[$i]->_currentValue="Off";
                       
                    }
                    if($current_value_returned!=-1){
                    echo $PHP_directive_Objects[$i]->_currentValue;
                    }
                    else{
                        echo "<b>Not In Use</b>";
                    }

                ?>
             </td>
             <td><?php echo $PHP_directive_Objects[$i]->getRemark();?></td>

             <?php
             $description =$PHP_directive_Objects[$i]->getDescription();
             //set the values list box or text box accordingly
             // names of directives are used as names of varaibles for POST
             if ($PHP_directive_Objects[$i]->getRecomendedValue()=="On" || $PHP_directive_Objects[$i]->getRecomendedValue()=="Off")
             {
             ?>
             <td>
                 Pick Value:<select name="<?php echo $PHP_directive_Objects[$i]->getDirectiveName();?>"><option value="">Select</option><option value="On">On</option><option value="Off">Off</option> </select>
                 <a href="javascript: void(0)" onclick="return showPopup('<?php echo $description; ?>','<?php echo $PHP_directive_Objects[$i]->getDirectiveName();?>')">[Help]</a>
             </td>
             <?php
             
             }
             else
             {
             ?>
             <td>
                 Set Value: <input type="text" name="<?php echo $PHP_directive_Objects[$i]->getDirectiveName();?>"/>
                 <a href="javascript: void(0)" onclick="return showPopup('<?php echo $description; ?>','<?php echo $PHP_directive_Objects[$i]->getDirectiveName();?>')">[Help]</a>
             </td>
             <?php
             }
             //disable making changes if recommended value is same as current value
             if ($PHP_directive_Objects[$i]->_currentValue==$PHP_directive_Objects[$i]->_recommendedValue)
                     {
                     ?>

                <script language="javascript" type="text/javascript">
                    var directiveName =<?php echo $PHP_directive_Objects[$i]->_directiveName;?>;
                    document.changeForm.directiveName.disabled=true;

               </script>
                <?php
                     }
             if($PHP_directive_Objects[$i]->_currentValue!=-1){
             $score=$PHP_directive_Objects[$i]->computeSafetyScore($PHP_directive_Objects[$i]->getRecomendedValue(),$PHP_directive_Objects[$i]->_currentValue);
                    if ($score==1)
                        $safe++;
                    elseif($score==0)
                        $unsafe++;
             }
             else if ($PHP_directive_Objects[$i]->_currentValue==-1){
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
              <th bgcolor="white" colspan="5"><h1>PHP Security Configuration Safety Report for <?php echo $mode;?></h1></th>

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
