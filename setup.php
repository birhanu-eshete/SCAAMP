<?php
include ('inc/header.php');
include ('inc/databasehandler.php');
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
            <h2 >Database Setup</h2>
            <br /><br />

    <form action="<?php $_SERVER['PHP_SELF']?>" method="POST">
        <table>
            <tr><td align="right">Database Host:</td> <td align="left"><input type="text" name ="host"></td> </tr>
            <tr><td align="right">Database User:</td> <td align="left"><input type="text" name ="user"> </td> </tr>
            <tr><td align="right">Password:</td> <td align="left"><input type="password" name ="passwd"> </td> </tr>
            <tr><td align="right">php.ini path:</td> <td align="left"><input type="text" name ="phpini"> </td> </tr>
            <tr><td align="right">httpd.conf Path:</td> <td align="left"><input type="text" name ="httpdconf"> </td> </tr>
            <tr><td align="right"><input type="submit" name="connect" value="Connect"></td></tr>
        </table>
    </form>
    <br />

    <?php
    if (isset($_POST['connect'])){
    $host =$_POST['host'];
    $user =$_POST['user'];
    $passwd =$_POST['passwd'];
    $phpini=$_POST['phpini'];
    $httpdconf=$_POST['httpdconf'];
    $connected=connectToMySQL($host, $user, $passwd,$phpini,$httpdconf);
    $db_created=CreateDB();
    $tables_created=createTables();
    $directives_populated=populateValues();
    if ($connected && $db_created && $tables_created && $directives_populated)
        {

            echo "Database setup successfully completed. Please ";
        }
    
    ?>
    <a href="index.php"> Continue... </a>
    <?php
       
    }
    ?>

         <br />
		<!-- END Slider -->
	</div>
<div id="description" class="separator"></div>
 
	<!-- END Main -->
	<!-- Footer -->
<?php
include ('inc/footer.php');
?>