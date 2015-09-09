<?php

 
 $host="";
 $user="";
 $passwd="";
 $phpini_path="";
 $httpdconf_path="";


function getOS()
{
$userAgent= $_SERVER['HTTP_USER_AGENT'];
if (strpos($userAgent,'Linux'))
        return 'Linux';
elseif (strpos($userAgent,'Windows'))
    return 'Windows';
elseif (strpos($userAgent,'Macintosh'))
        return 'Macintosh';
else //others
        return 'Other';
}

function getPHPVersion()
{
 return phpversion();
}

function getApacheVersion()
{
return substr(apache_get_version(),0,13);
}

function getMySQLversion()
{
    return mysql_get_server_info();
}

function getCredentials()
{
        // so as to be visible outside this function.
        global $host,$user,$passwd,$phpini_path,$httpdconf_path; 
        $credentials = "../config/mysql_credentials.txt";
        $handle = fopen($credentials, 'r');
        $credential_values =array();
        $index =0;
        while (!feof($handle))
            {
                $oneLine = fgets($handle);
                $credential_values[$index]=$oneLine;
                $index++;
            }
        fclose($handle);
        $host=trim($credential_values[0]);
        $user=trim($credential_values[1]);
        $passwd=trim($credential_values[2]);
        $phpini_path=trim($credential_values[3]);
        $httpdconf_path=trim($credential_values[4]);
}


?>
