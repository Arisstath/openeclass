<?php
/**=============================================================================
       	GUnet e-Class 2.0 
        E-learning and Course Management Program  
================================================================================
       	Copyright(c) 2003-2006  Greek Universities Network - GUnet
        � full copyright notice can be read in "/info/copyright.txt".
        
       	Authors:    Costas Tsibanis <k.tsibanis@noc.uoa.gr>
        	    Yannis Exidaridis <jexi@noc.uoa.gr> 
      		    Alexandros Diamantidis <adia@noc.uoa.gr> 

        For a full list of contributors, see "credits.txt".  
     
        This program is a free software under the terms of the GNU 
        (General Public License) as published by the Free Software 
        Foundation. See the GNU License for more details. 
        The full license can be read in "license.txt".
     
       	Contact address: GUnet Asynchronous Teleteaching Group, 
        Network Operations Center, University of Athens, 
        Panepistimiopolis Ilissia, 15784, Athens, Greece
        eMail: eclassadmin@gunet.gr
==============================================================================*/

/**===========================================================================
	auth.inc.php
	@last update: 31-05-2006 by Stratos Karatzidis
	@authors list: Karatzidis Stratos <kstratos@uom.gr>
		       Vagelis Pitsioygas <vagpits@uom.gr>
==============================================================================        
        @Description: Functions Library for authentication purposes

 	This library includes all the functions for authentication
	and their settings.

 		

==============================================================================
*/

// pop3 class
require("methods/pop3.php");

/****************************************************************
find/return the id of the default authentication method
return $auth_id (a value between 1 and 5: 1-eclass,2-pop3,3-imap,4-ldap,5-db)
****************************************************************/
function get_auth_id()
{
	global $db;
	$sql = "SELECT auth_id FROM auth WHERE auth_default=1";
  $auth_method = mysql_query($sql,$db);
  if($auth_method)
  {
		$authrow = mysql_fetch_row($auth_method);
		if(mysql_num_rows($auth_method)==1)
		{
	    $auth_id = $authrow[0];
	    return $auth_id;
		}
		else
		{
	    return 0;
		}
	}
  else
  {
		return 0;
	}
}

/****************************************************************
find/return the string, describing in words the default authentication method
return $m (string)
****************************************************************/
function get_auth_info($auth)
{
	if(!empty($auth))
	{
		switch($auth)
		{
			case '2': $m = "����������� ���� POP3";
				break;
			case '3': $m = "����������� ���� IMAP";
				break;
			case '4':	$m = "����������� ���� LDAP";
				break;
			case '5': $m = "����������� ���� External DB";
				break;
			default:	$m = 0;
				break;
		}
		return $m;
	}
	else
	{
		return 0;
	}
}

/****************************************************************
find/return the settings of the default authentication method

$auth : integer a value between 1 and 5: 1-eclass,2-pop3,3-imap,4-ldap,5-db)
return $auth_row : an associative array
****************************************************************/
function get_auth_settings($auth)
{
	$qry = "SELECT * FROM auth WHERE auth_id = ".$auth;
  $result = db_query($qry);
  $db_auth_email = array();
  if($result)
  {
		if(mysql_num_rows($result)==1)
		{
	    $auth_row = mysql_fetch_array($result,MYSQL_ASSOC);
	    return $auth_row;
		}
		else
		{
	    return 0;
		}	
	}
	else
	{
		return 0;
	}
}

/****************************************************************
Try to authenticate the user with the admin-defined auth method
true (the user is authenticated) / false (not authenticated)

$auth an integer-value for auth method(1:eclass, 2:pop3, 3:imap, 4:ldap, 5:db)
$test_username
$test_password
return $testauth (boolean: true-is authenticated, false-is not)
****************************************************************/
function auth_user_login ($auth,$test_username, $test_password) 
{
    switch($auth)
    {
	case '1':
	    // Returns true if the username and password work and false if they don't
	    $sql = "SELECT user_id FROM user WHERE username='".$test_username."' AND password='".$test_password."'";
	    $result = db_query($sql);
	    if(mysql_num_rows($result)==1)
	    {
    		$testauth = true;
	    }
	    else
	    {
		$testauth = false;
	    }
	break;	
    
    
	case '2':
	    $pop3host = $GLOBALS['pop3host'];
	    $pop3=new pop3_class;
	    $pop3->hostname = $pop3host;	/* POP 3 server host name                      */
	    $pop3->port=110;				/* POP 3 server host port                      */
	    $user = $test_username;                       	/* Authentication user name                    */
	    $password = $test_password;                   	/* Authentication password                     */
	    $pop3->realm="";                         	/* Authentication realm or domain              */
	    $pop3->workstation="";			/* Workstation for NTLM authentication         */
	    $apop = 0;			/* Use APOP authentication                     */
	    $pop3->authentication_mechanism="USER";  /* SASL authentication mechanism               */
	    $pop3->debug=0;                          /* Output debug information                    */
	    $pop3->html_debug=1;                     /* Debug information is in HTML                */
	    $pop3->join_continuation_header_lines=1; /* Concatenate headers split in multiple lines */

	    if(($error=$pop3->Open())=="")
	    {
		if(($error=$pop3->Login($user,$password,$apop))=="")
		{
		    if($error=="" && ($error=$pop3->Close())=="")
		    {
		    $testauth = true;
		    }
		    else
		    {
		    $testauth = false;
		    }
		}
		else
		{
		    $testauth = false;
		}
	    }
	    else
	    {
		$testauth = false;
	    }
	    if($error!="")
	    {
		$testauth = false;
	    }
	    break;
	
	case '3':
	    $imaphost = $GLOBALS['imaphost'];
	    $imapauth = imap_auth($imaphost, $test_username, $test_password);
	    if($imapauth)
	    {
		$testauth = true;
	    }
	    else
	    {
		$testauth = false;
	    }
	    break;
	    
	case '4':
			$ldaphost = $GLOBALS['ldaphost'];
			$basedn = $GLOBALS['ldapbind_dn'];
			$ldap_uid = $test_username;
			$ldap_passwd = $test_password;
			// anonymous account:
			$a_user = $GLOBALS['ldapbind_user'];
			$a_pass = $GLOBALS['ldapbind_pw'];
			$testauth = "false";
			
			//$ds=ldap_connect($ldaphost);  //get the ldapServer, baseDN from the db
			
			    // suppose user has provided a pair: $user, $pass
    $ldap_host = $ldaphost;
    $ldap_base_dn = $basedn;
    $ldap_user_attrib = 'uid';
    $user = $ldap_uid;
    $pass = $ldap_passwd;
    $all_ldap_base_dn     = array();
    $all_ldap_user_attrib = array();

    $all_ldap_base_dn = array($ldap_base_dn);

    // Transfer the array of user attributes to a new value. Create an array of the user attributes to match
    // the number of base dn's if a single user attribute has been passed.
    $all_ldap_user_attrib[] = $ldap_user_attrib;

    $ldap = ldap_connect($ldap_host);

    if($ldap)		// Check that connection was established
    {
        // now process all base dn's until authentication is achieved or fail
        foreach( $all_ldap_base_dn as $idx => $base_dn)
        {
            // construct dn for user
            $dn = $all_ldap_user_attrib[$idx] . "=" . $user . "," . $base_dn;

            // try an authenticated bind. use this to confirm that the user/password pair
            if(ldap_bind($ldap, $dn, $pass))
            {
		
							$testauth = true;

            } // end if
            else
            {
            	$testauth = false;
            }
        } // foreach
        @ldap_unbind($ldap);
    } // if($ldap)
    else
    {
    	$testauth = false;
    }

			break;    

	case '5':
	    
	    $dbtype = $GLOBALS['dbtype'];
	    $dbhost = $GLOBALS['dbhost'];
	    $dbname = $GLOBALS['dbname'];
	    $dbuser = $GLOBALS['dbuser'];
	    $dbpass = $GLOBALS['dbpass'];
	    $dbtable = $GLOBALS['dbtable'];
	    $dbfielduser = $GLOBALS['dbfielduser'];
	    $dbfieldpass = $GLOBALS['dbfieldpass'];
	    $newlink = true;
	    $link = mysql_connect($dbhost,$dbuser,$dbpass,$newlink);
	    if($link)
	    {
				$db_ext = mysql_select_db($dbname,$link);
				if($db_ext)
				{
		    	$qry = "SELECT * FROM ".$dbname.".".$dbtable." WHERE ".$dbfielduser."='".$test_username."' AND ".$dbfieldpass."='".$test_password."'";

		    	$res = mysql_query($qry,$link);
			    	
		    	if($res)
		    	{
						if(mysql_num_rows($res)>0)
						{
			     		$testauth = true;
			 
			    		mysql_close($link);
					mysql_select_db($mysqlMainDb,$GLOBALS['db']);
						}
		    	}
		    	else
		    	{
						$testauth = false;
		    	}
		    	
				}
				else
				{
		    	$testauth = false;
				}
	    }
	    else
	    {
				$testauth = false;
	    }
	    break;
	    
	default:
	    $testauth = $auth;
	    break;
    }
    
    return $testauth;

}

/********************************************************************
Show a selection box. Taken from main.lib.php
Difference: the return value and not just echo the select box

$entries: an array of (value => label)
$name: the name of the selection element
$default: if it matches one of the values, specifies the default entry
***********************************************************************/
function selection3($entries, $name, $default = '')
{
	$select_box = "<select name='$name'>\n";
	foreach ($entries as $value => $label) 
	{
	    if ($value == $default) 
	    {
		$select_box .= "<option selected value='" . htmlspecialchars($value) . "'>" .
				htmlspecialchars($label) . "</option>\n";
	    } 
	    else 
	    {
		$select_box .= "<option value='" . htmlspecialchars($value) . "'>" .
				htmlspecialchars($label) . "</option>\n";
	    }
	}
	$select_box .= "</select>\n";
	
	return $select_box;
}


/****************************************************************
TCheck if an account is active or not. Apart from admin, everybody has
a registration unix timestamp and an expiration unix timestamp.
By default is set to last a year

$userid : the id of the account
return $testauth (boolean: true-is authenticated, false-is not)
****************************************************************/
function check_activity($userid)
{
	global $db;
	$qry = "SELECT registered_at,expires_at FROM user WHERE user_id=".$userid;
	$res = mysql_query($qry,$db);
	if(($res) && (mysql_num_rows($res)==1))
	{
		$row = mysql_fetch_row($res);
		if($row[1]>time())
		{
			return 1;
		}
		else
		{
			return 0;
		}
	}
	else
	{
		return 0;
	}
}

?>