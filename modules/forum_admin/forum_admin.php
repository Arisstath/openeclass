<?
/*========================================================================
*   Open eClass 2.3
*   E-learning and Course Management System
* ========================================================================
*  Copyright(c) 2003-2010  Greek Universities Network - GUnet
*  A full copyright notice can be read in "/info/copyright.txt".
*
*  Developers Group:	Costas Tsibanis <k.tsibanis@noc.uoa.gr>
*			Yannis Exidaridis <jexi@noc.uoa.gr>
*			Alexandros Diamantidis <adia@noc.uoa.gr>
*			Tilemachos Raptis <traptis@noc.uoa.gr>
*
*  For a full list of contributors, see "credits.txt".
*
*  Open eClass is an open platform distributed in the hope that it will
*  be useful (without any warranty), under the terms of the GNU (General
*  Public License) as published by the Free Software Foundation.
*  The full license can be read in "/info/license/license_gpl.txt".
*
*  Contact address: 	GUnet Asynchronous eLearning Group,
*  			Network Operations Center, University of Athens,
*  			Panepistimiopolis Ilissia, 15784, Athens, Greece
*  			eMail: info@openeclass.org
* =========================================================================*/

/* @version $Id$
*/

$require_current_course = TRUE;
$require_help = TRUE;
$helpTopic = 'For';
$require_prof = true;

include '../../include/baseTheme.php';
include '../../include/sendMail.inc.php';
include '../phpbb/functions.php';

$nameTools = $langCatForumAdmin;
$navigation[]= array ("url"=>"../phpbb/index.php?course=$code_cours", "name"=> $langForums);

$forum_id = isset($_GET['forum_id'])?intval($_GET['forum_id']):'';
$cat_id = isset($_GET['cat_id'])?intval($_GET['cat_id']):'';

if($is_adminOfCourse) {

$head_content .= '
<script type="text/javascript">
function confirmation ()
{
    if (confirm("'.$langConfirmDelete.'"))
        {return true;}
    else
        {return false;}
}
</script>
';

$head_content .= <<<hContent
<script type="text/javascript">
function checkrequired(which, entry) {
	var pass=true;
	if (document.images) {
		for (i=0;i<which.length;i++) {
			var tempobj=which.elements[i];
			if (tempobj.name == entry) {
				if (tempobj.type=="text"&&tempobj.value=='') {
					pass=false;
					break;
		  		}
	  		}
		}
	}
	if (!pass) {
		alert("$langEmptyCat");
		return false;
	} else {
		return true;
	}
}

</script>
hContent;
	// forum go
if(isset($_GET['forumgo'])) {
	$nameTools = $langAdd;
	$navigation[]= array ("url"=>"../forum_admin/forum_admin.php?course=$code_cours", "name"=> $langCatForumAdmin);
	$result = db_query("SELECT forum_id, forum_name, forum_desc, forum_access, forum_moderator, forum_type 
			FROM forums WHERE cat_id='$cat_id'", $currentCourseID);
	$ctg = category_name($cat_id);
	if ($result and mysql_num_rows($result) > 0) {
		$tool_content .= "
              <form action=\"$_SERVER[PHP_SELF]?course=$code_cours&amp;forumgoadd=yes&amp;cat_id=$cat_id\" method=post>
		<table width=99% class=\"tbl_alt\">
		<tr>
		  <th colspan=\"4\">$langForCat: <b>$ctg</b></th>
		</tr>
		<tr>
		  <th width='10'>$langID</th>
		  <th><div align=\"left\">$langForName</div></th>
		  <th><div align=\"left\">$langDescription</div></th>
		  <th width='75'>$langActions</th>
		</tr>";
		$i=1;
		while(list($forum_id, $forum_name, $forum_desc, $forum_access,
			$forum_moderator, $forum_type) = mysql_fetch_row($result)) {
				if ($i%2==1) {
					$tool_content .= "\n<tr class=\"even\">";
				} else {
					$tool_content .= "\n<tr class=\"odd\">";
				}
				$tool_content .= "
				<td class='right'>$i.</td>
				<td>$forum_name</td>
				<td>$forum_desc&nbsp;</td>
				<td class='center'>
				 <a href='$_SERVER[PHP_SELF]?course=$code_cours&amp;forumgoedit=yes&amp;forum_id=$forum_id&amp;cat_id=$cat_id'>
				 <img src='../../template/classic/img/edit.png' title='$langModify' /></a>&nbsp;
				 <a href='$_SERVER[PHP_SELF]?course=$code_cours&amp;forumgodel=yes&amp;forum_id=$forum_id&amp;cat_id=$cat_id' onClick='return confirmation();'>
				 <img src='../../template/classic/img/delete.png' title='$langDelete' /></a></td>
			      </tr>";
				$i++;
			}
			$tool_content .= "
			</table>
			</form>
			<br/>";
		} else {
			$tool_content .= "\n  <p class=\"alert1\">$langNoForumsCat</p>";
		}

		$tool_content .= "
	      <form action=\"$_SERVER[PHP_SELF]?course=$code_cours&amp;forumgoadd=yes&amp;cat_id=$cat_id\" method=post onsubmit=\"return checkrequired(this,'forum_name');\">
              <fieldset>
                <legend>$langAddForCat</legend>    
		<table class=\"tbl\">
		<tr>
		  <th>$langCat:</th>
		  <td>$ctg</td>
		</tr>
		<tr>
		  <th>$langForName:</th>
		  <td><input type=text name=forum_name size=40></td>
		</tr>
		<tr>
		  <th valign='top'>$langDescription:</th>
		  <td><textarea name=forum_desc cols=37 rows=3></textarea></td>
		</tr>
		<tr>
                  <th>&nbsp;</th>
		  <td><input type=submit value=$langAdd></td>
		</tr>
                </table>
              </fieldset>
              </form>
              <p>&laquo; <a href='$_SERVER[PHP_SELF]?course=$code_cours'>$langBackCat</a></p>";
	}
	// forum go edit
	elseif(isset($_GET['forumgoedit'])) {
		$nameTools = $langEditForum;
		$navigation[]= array ("url"=>"../forum_admin/forum_admin.php?course=$code_cours", "name"=> $langCatForumAdmin);
		$result = db_query("SELECT forum_id, forum_name, forum_desc, forum_access, forum_moderator, cat_id, forum_type
			FROM forums WHERE forum_id='$forum_id'", $currentCourseID);
		list($forum_id, $forum_name, $forum_desc, $forum_access, $forum_moderator, $cat_id_1,
		$forum_type) = mysql_fetch_row($result);
		$tool_content .= "
		<form action=\"$_SERVER[PHP_SELF]?course=$code_cours&amp;forumgosave=yes&amp;cat_id=".@$cat_id."\" method=post onsubmit=\"return checkrequired(this,'forum_name');\">
		<input type=hidden name=forum_id value=$forum_id>
                <fieldset>
                  <legend>$langChangeForum</legend>
		  <table class='tbl'>
		  <tr>
		    <th>$langForName</th>
		    <td><input type=text name=forum_name size=50 value='$forum_name'></td>
		  </tr>
		  <tr>
		    <th valign='top'>$langDescription</th>
		    <td><textarea name=forum_desc cols=47 rows=3>$forum_desc</textarea></td>
		  </tr>
		  <tr>
		    <th>$langChangeCat</th>
		  <td>
		    <select name=cat_id>";
		$result = db_query("SELECT cat_id, cat_title FROM catagories", $currentCourseID);
		while(list($cat_id, $cat_title) = mysql_fetch_row($result)) {
			if ($cat_id == $cat_id_1) {
					$tool_content .= "\n<option value='$cat_id' selected>$cat_title</option>"; 
				} else {
					$tool_content .= "\n<option value='$cat_id'>$cat_title</option>";
				}
			}
		$tool_content .= "\n</select>
                    </td>
                  </tr>
		  <tr>
                    <th>&nbsp;</th>
                    <td><input type=submit value='$langModify'></td>
		  </tr>
                  </table>
                </fieldset>
                </form>";
	}

	// edit forum category
	elseif(isset($_GET['forumcatedit'])) {
		$result = db_query("select cat_id, cat_title from catagories where cat_id='$cat_id'", $currentCourseID);
		list($cat_id, $cat_title) = mysql_fetch_row($result);
		$tool_content .= "
  		<form action='$_SERVER[PHP_SELF]?course=$code_cours&amp;forumcatsave=yes' method=post onsubmit=\"return checkrequired(this,'cat_title');\">
    		<input type=hidden name=cat_id value=$cat_id>
                <fieldset>
                <legend>$langModCatName</legend>
    		<table class=\"tbl\">
    		<tr>
      		  <th>$langCat</th>
      		  <td><input type=text name=cat_title size=55 value='$cat_title' class=\"FormData_InputText\"></td>
    		</tr>
    		<tr>
      		  <th>&nbsp;</th>
      		  <td><input type=submit value='$langModify'></td>
    		</tr>
    		</table>
                </fieldset>
  		</form>";
	}

	// save forum category
	elseif (isset($_GET['forumcatsave'])) {
		db_query("UPDATE catagories SET cat_title='$_POST[cat_title]' WHERE cat_id='$_POST[cat_id]'", $currentCourseID);
		$tool_content .= "\n   <p class=\"success\">$langNameCatMod</p>
		\n <p>&laquo; <a href='$_SERVER[PHP_SELF]?course=$code_cours'>$langBack</a></p>";
	}

	// forum go save
	elseif(isset($_GET['forumgosave'])) {
		$nameTools = $langDelete;
		$navigation[]= array ("url"=>"../forum_admin/forum_admin.php?course=$code_cours", "name"=> $langCatForumAdmin);
		$result = @db_query("SELECT user_id FROM users WHERE username='$forum_moderator'", $currentCourseID);
		list($forum_moderator) = mysql_fetch_row($result);
		db_query("UPDATE forums SET forum_name='$_POST[forum_name]', forum_desc='$_POST[forum_desc]',
			forum_access='2', forum_moderator='1', cat_id='$_POST[cat_id]'
			WHERE forum_id='$_POST[forum_id]'", $currentCourseID);
		$tool_content .= "\n<p class='success'>$langForumDataChanged</p>
		<p>&laquo; <a href=\"$_SERVER[PHP_SELF]?course=$code_cours&amp;forumgo=yes&amp;cat_id=$cat_id\">$langBack</a></p>";
	}

	// forum add category
	elseif(isset($_GET['forumcatadd'])) {
		db_query("INSERT INTO catagories VALUES (NULL, '$_POST[catagories]', NULL)", $currentCourseID);
		$tool_content .= "\n<p class='success'>$langCatAdded</p>
		\n  <p>&laquo; <a href='$_SERVER[PHP_SELF]?course=$code_cours'>$langBack</a></p>";
		}

	// forum go add
	elseif(isset($_GET['forumgoadd'])) {
		$nameTools = $langAdd;
		$navigation[]= array ("url"=>"../forum_admin/forum_admin.php?course=$code_cours", "name"=> $langCatForumAdmin);
		$ctg = category_name($_GET['cat_id']);
		$result = @db_query("SELECT user_id FROM users WHERE username='$forum_moderator'", $currentCourseID);
		list($forum_moderator) = mysql_fetch_row($result);
		db_query("UPDATE users SET user_level='2' WHERE user_id='$forum_moderator'", $currentCourseID);
		@db_query("INSERT INTO forums (forum_name, forum_desc, forum_access, forum_moderator, cat_id)
        	VALUES ('$_POST[forum_name]', '$_POST[forum_desc]', '2', '1', '$_GET[cat_id]')", $currentCourseID);
		$idforum=db_query("SELECT forum_id FROM forums WHERE forum_name='$_POST[forum_name]'", $currentCourseID);
		while ($my_forum_id = mysql_fetch_array($idforum)) {
			$forid = $my_forum_id[0];
		}
		// --------------------------------
		// notify users 
		// --------------------------------
		$subject_notify = "$logo - $langCatNotify";
		$sql = db_query("SELECT DISTINCT user_id FROM forum_notify 
				WHERE (cat_id = $cat_id) 
				AND notify_sent = 1 AND course_id = $cours_id AND user_id != $uid", $mysqlMainDb);
		$body_topic_notify = "$langBodyCatNotify $langInCat '$ctg' \n\n$gunet";
		while ($r = mysql_fetch_array($sql)) {
			$emailaddr = uid_to_email($r['user_id']);
			send_mail('', '', '', $emailaddr, $subject_notify, $body_topic_notify, $charset);
		}
		// end of notification
		
		$tool_content .= "\n  <p class='success'>$langForumCategoryAdded</p>
		<p>&laquo; <a href='$_SERVER[PHP_SELF]?course=$code_cours&amp;forumgo=yes&amp;cat_id=$_GET[cat_id]'>$langBack</a></p>";
	}

	// forum delete category
	elseif(isset($_GET['forumcatdel'])) {
		$result = db_query("SELECT forum_id FROM forums WHERE cat_id='$cat_id'", $currentCourseID);
		while(list($forum_id) = mysql_fetch_row($result)) {
			db_query("DELETE from topics where forum_id=$forum_id", $currentCourseID);
		}
		db_query("DELETE FROM forums where cat_id=$cat_id", $currentCourseID);
		db_query("DELETE FROM catagories where cat_id=$cat_id", $currentCourseID);
		$tool_content .= "\n  <p class=\"success\">$langCatForumDelete</p>
		\n  <p>&laquo; <a href='$_SERVER[PHP_SELF]?course=$code_cours'>$langBack</a></p>";
	}

	// forum delete
	elseif(isset($_GET['forumgodel'])) {
		$nameTools = $langDelete;
		$navigation[]= array ("url"=>"../forum_admin/forum_admin.php?course=$code_cours", "name"=> $langCatForumAdmin);
		db_query("DELETE FROM topics WHERE forum_id = $forum_id", $currentCourseID);
		db_query("DELETE FROM forums WHERE forum_id = $forum_id", $currentCourseID);
		db_query("UPDATE `group` SET forum_id=0 WHERE forum_id = $forum_id", $mysqlMainDb);
		$tool_content .= "\n<p class=\"success\">$langForumDelete</p>
		\n<p>&laquo; <a href=\"$_SERVER[PHP_SELF]?course=$code_cours&amp;forumgo=yes&amp;cat_id=$cat_id\">$langBack</a></p>";
	} else {
		if(isset($_GET['forumcatnotify'])) { // modify forum category notification
			$rows = mysql_num_rows(db_query("SELECT * FROM forum_notify 
				WHERE user_id = $uid AND cat_id = $cat_id AND course_id = $cours_id"));
			if ($rows > 0) {
				db_query("UPDATE forum_notify SET notify_sent = '$_GET[forumcatnotify]' 
					WHERE user_id = $uid AND cat_id = $cat_id AND course_id = $cours_id");
			} else {
				db_query("INSERT INTO forum_notify SET user_id = $uid,
				cat_id = $cat_id, notify_sent = 1, course_id = $cours_id");
			}
		}
		$tool_content .= "<form action='$_SERVER[PHP_SELF]?course=$code_cours' method=post>";
		$tool_content .= "
                <table width=100% class=\"tbl_alt\">
    		<tr>
                  <th width='2'>$langID</th>
      		  <th><div align='left'>$langCategories</div></th>
      		  <th width='25'>$langNbFor</th>
      		  <th width='85'>$langActions</th>
    		</tr>";
		$result = db_query("SELECT cat_id, cat_title FROM catagories ORDER BY cat_id", $currentCourseID);
		$i=1;
		while(list($cat_id, $cat_title) = mysql_fetch_row($result)) {
			$gets = db_query("SELECT COUNT(*) AS total FROM forums WHERE cat_id=$cat_id", $currentCourseID);
			$numbers = mysql_fetch_array($gets);
			list($forum_cat_action_notify) = mysql_fetch_row(db_query("SELECT notify_sent FROM forum_notify 
				WHERE user_id = $uid AND cat_id = $cat_id AND course_id = $cours_id", $mysqlMainDb));
			if (!isset($forum_cat_action_notify)) {
				$link_notify = FALSE;
				$icon = '_off';
			} else {
				$link_notify = toggle_link($forum_cat_action_notify);
				$icon = toggle_icon($forum_cat_action_notify);
			}
                        if ($i%2) {
                                $tool_content .= "\n<tr class='odd'>";
                        } else {
                                $tool_content .= "\n<tr class='even'>";
                        }

		$tool_content .= "\n
                  <td><div align='right'>$i.</div></td>
                  <td><div align='left'>$cat_title &nbsp;</div></td>
                  <td><div align='center'>$numbers[total]</div></td>
                  <td><a href='forum_admin.php?course=$code_cours&amp;forumgo=yes&amp;cat_id=$cat_id'>
			<img src='../../template/classic/img/forum_on.png' title='$langForumAdmin' /></a>&nbsp;
			<a href='$_SERVER[PHP_SELF]?course=$code_cours&amp;forumcatedit=yes&amp;cat_id=$cat_id'>
			<img src='../../template/classic/img/edit.png' title='$langModify' /></a>&nbsp;
			<a href='$_SERVER[PHP_SELF]?course=$code_cours&amp;forumcatdel=yes&amp;cat_id=$cat_id' onClick='return confirmation();'>
			<img src='../../template/classic/img/delete.png' title='$langDelete' /></a>
			<a href='$_SERVER[PHP_SELF]?course=$code_cours&amp;forumcatnotify=$link_notify&amp;cat_id=$cat_id'>	
			<img src='../../template/classic/img/announcements$icon.gif' title='$langNotify' /></a>
                  </td>
                </tr>";
			$i++;
		}
		$tool_content .= "</table></form>
                <br/>
		<form action=\"$_SERVER[PHP_SELF]?course=$code_cours&amp;forumcatadd=yes\" method=post onsubmit=\"return checkrequired(this,'catagories');\">
                <fieldset>
                <legend>$langAddCategory</legend>
		<table class=\"tbl\">
		<tr>
		  <th class='left'>$langCat</th>
		  <td><input type=text name=catagories size=50></td>
		</tr>
	        <tr>
                  <th>&nbsp;</th>
		  <td><input type=submit value='$langAdd'></td>
		</tr>
		</table>
                </fieldset>
                </form>
		<p><b><u>$langNote</u>:</b><br/>
		<em>$langAddForums</em></p>";
	}
} else {
	$tool_content .= "$langNotAllowed<br>";
}
if($is_adminOfCourse) {
	draw($tool_content, 2, '', $head_content);
} else {
	draw($tool_content, 2);
}
?>