<?php
$dbname;

    //first time setup
  function connectToMySQL($h,$u,$p,$php,$httpd)
    {
     mysql_connect($h,$u,$p) or die('Unable to connect to the database server. Please check MySQL credentials:'.mysql_error());
     //write mysql credentials for use by other scripts
       $file = "config/mysql_credentials.txt";
       $handle = fopen($file, 'w');
       fwrite($handle, $h."\n");
       fwrite($handle, $u."\n");
       fwrite($handle, $p."\n");
       fwrite($handle, $php."\n");
       fwrite($handle, $httpd);
       fclose($handle);
       return 1;
    }

    // ordinary connection to db
    function connecter($h,$u,$p)
    {
            mysql_connect($h,$u,$p) or die('Unable to connect to the database server. Please check MySQL credentials:'.mysql_error());
            mysql_select_db("scaamp") or die('Unable to locate the scaamp databse:'.  mysql_error());

    }

    function CreateDB()
    {
       //drop the database if it is there 
        mysql_query("DROP DATABASE  IF EXISTS scaamp") or die("Trying to drop non-existent database:".mysql_error());
        //create the database
        $db =mysql_query("CREATE DATABASE scaamp") or die ('Unable to create SCAAMP database:'.mysql_error());
        $dbname='scaamp';
        //use the database
        mysql_select_db($dbname);
        return 1;
    }

    //create the required tables for php, mysql, and apache.
    function createTables()
    {
       //create the phpdirectives table
        $phpdirectives=mysql_query("CREATE TABLE phpdirectives(directivename VARCHAR(200) NOT NULL,recomendedvalue VARCHAR(200) NOT NULL,description VARCHAR(10000), remark VARCHAR(10000), PRIMARY KEY(directivename))") or die ('Unable to create table phpdirectives:'.mysql_error());

        //create the phpdirectivevalues table
        $phpdirectivevalues=mysql_query("CREATE TABLE phpdirectivevalues(directivename VARCHAR(200) NOT NULL,value VARCHAR(200),recomendedfor VARCHAR(50),safetyscore INT(11))") or die ('Unable to create table phpdirectives:'.mysql_error());
	   //create the apacheldirectives table        
	    $apacheDirectives = mysql_query("CREATE TABLE apacheDirectives(directivename VARCHAR(200) NOT NULL,recomendedvalue VARCHAR(200) NOT NULL,description VARCHAR(10000), remark VARCHAR(10000), PRIMARY KEY(directivename))") or die ('Unable to create table apacheDirectives :'.mysql_error());

       //create the apachedirectivevalues table
        $apacheDirectiveValues = mysql_query("CREATE TABLE apacheDirectiveValues (directivename VARCHAR(200) NOT NULL,value VARCHAR(200),description VARCHAR(10000),recomendedfor VARCHAR(50),remark VARCHAR(10000),safetyscore INT(11))") or die ('Unable to create table apacheDirectiveValues:'.mysql_error());

        //create the mysqldirectives table        
	    $mysqlDirectives = mysql_query("CREATE TABLE mysqlDirectives(directivename VARCHAR(200) NOT NULL,recomendedvalue VARCHAR(200) NOT NULL,description VARCHAR(10000), remark VARCHAR(10000), PRIMARY KEY(directivename))") or die ('Unable to create table mysqlDirectives:'.mysql_error());

        //create the mysqldirectivevalues table
        $mysqlDirectiveValues = mysql_query("CREATE TABLE mysqlDirectivevalues(directivename VARCHAR(200) NOT NULL,value VARCHAR(200),description VARCHAR(10000),recomendedfor VARCHAR(50),remark VARCHAR(10000),safetyscore INT(11))") or die ('Unable to create table mysqlDirectivevalues:'.mysql_error());
        
        return 1;
    }

       
    //populate directive details from file
    function populateValues ()
    {
       //******** for php case ********
    	// populate php directive file
        $phpdirectives = "data/php_directives.txt";
        $handle = fopen($phpdirectives, 'r');
        
        while (!feof($handle)) {
          $oneLine = fgets($handle);
          //echo $oneLine."<br />";
          //tokenize eachline separated by ^
          $token = strtok($oneLine, "^");
          $directive_record =array();
          $index =0;
          while ($token != false)
              {

                // insert to table
                $directive_record[$index]=$token;
                $index++;
                $token = strtok("^");
              }
             mysql_query("INSERT INTO phpdirectives(directivename,recomendedvalue,description,remark) VALUES('$directive_record[0]','$directive_record[1]','$directive_record[2]','$directive_record[3]')")or die("Error in populating PHP directive table:".mysql_error());
     
        }
        fclose($handle);
   

     //******** for MySQL case ********
     // populate mysql directive file
        $mysqlDirectives = "data/mysql_directives.txt";
        $mysqlHandle = fopen($mysqlDirectives, 'r');
        
        while (!feof($mysqlHandle)) {        
        	$oneLine = fgets($mysqlHandle);
        	$token = strtok($oneLine, "^");
          	$directive_record = array();
          	$index = 0;
          	while ($token !== false) { 
          		// insert to table
                    $directive_record[$index] = $token;
        	    $index++;
                    $token = strtok("^");
          	}
          	mysql_query("INSERT INTO mysqlDirectives(directiveName,recomendedvalue,description,remark) VALUES ('$directive_record[0]','$directive_record[1]','$directive_record[2]','$directive_record[3]')")or die("Error in populating MySQL directive table:".mysql_error());
        }
        fclose($mysqlHandle);
        
	 //******** for Apache Case ********
	  // populate Apache directive file
        $apacheDirectives = "data/apache_directives.txt";
        $apacheHandle = fopen($apacheDirectives, 'r');        
        
        while (!feof($apacheHandle)) {        
        	$oneLine = fgets($apacheHandle);
        	$token = strtok($oneLine, "^");
          	$directive_record = array();
          	$index = 0;
          	while ($token !== false) { 
          		// insert to table
	            $directive_record[$index] = $token;
        	    $index++;
                    $token = strtok("^");
          	}
          	mysql_query("INSERT INTO apacheDirectives(directiveName,recomendedvalue,description,remark) VALUES('$directive_record[0]','$directive_record[1]','$directive_record[2]','$directive_record[3]')")or die("Error in populating Apache directive table:".mysql_error());
        }
        fclose($apacheHandle);

        return 1;
        
 }

    function closeConnection()
    {
        mysql_close();
    }


?>
