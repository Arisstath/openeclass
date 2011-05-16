<?php
/* ========================================================================
 * Open eClass 2.4
 * E-learning and Course Management System
 * ========================================================================
 * Copyright 2003-2011  Greek Universities Network - GUnet
 * A full copyright notice can be read in "/info/copyright.txt".
 * For a full list of contributors, see "credits.txt".
 *
 * Open eClass is an open platform distributed in the hope that it will
 * be useful (without any warranty), under the terms of the GNU (General
 * Public License) as published by the Free Software Foundation.
 * The full license can be read in "/info/license/license_gpl.txt".
 *
 * Contact address: GUnet Asynchronous eLearning Group,
 *                  Network Operations Center, University of Athens,
 *                  Panepistimiopolis Ilissia, 15784, Athens, Greece
 *                  e-mail: info@openeclass.org
 * ======================================================================== */

/*
 * Groups Component
 *
 * @author Evelthon Prodromou <eprodromou@upnet.gr>
 * @version $Id$
 *
 * @abstract This module is responsible for the user groups of each lesson
 *
 */

$require_current_course = TRUE;
$require_help = TRUE;
$helpTopic = 'Group';

include '../../include/baseTheme.php';
include '../../include/sendMail.inc.php';

$group_id = intval($_REQUEST['group_id']);

$nameTools = $langEmailGroup;
$navigation[]= array ("url"=>"group.php?course=$code_cours", "name"=> $langGroupSpace,
"url"=>"group_space.php?group_id=$group_id", "name"=>$langGroupSpace);

list($tutor_id) = mysql_fetch_row(db_query("SELECT is_tutor FROM group_members WHERE group_id='$group_id'", $mysqlMainDb));
$is_tutor = ($tutor_id == 1)?TRUE:FALSE;

if (!$is_adminOfCourse and !$is_tutor) {
        header('Location: group_space.php?course='.$code_cours.'&group_id=' . $group_id);
        exit;
}

if ($is_adminOfCourse or $is_tutor)  {
	if (isset($_POST['submit'])) {
                $sender = mysql_fetch_array(db_query("SELECT email, nom, prenom FROM user
						WHERE user_id = $uid", $mysqlMainDb));
                $sender_name = $sender['prenom'] . ' ' . $sender['nom'];
                $sender_email = $sender['email'];
                $emailsubject = $intitule." - ".$_POST['subject'];
                $emailbody = "$_POST[body_mail]\n\n$langSender: $sender[nom] $sender[prenom] <$sender[email]>\n$langProfLesson\n";
		$req = db_query("SELECT user_id FROM group_members WHERE group_id = '$group_id'", $mysqlMainDb);
		while ($userid = mysql_fetch_array($req)) {
                        $r = db_query("SELECT email FROM user where user_id='$userid[user_id]'", $mysqlMainDb);
			list($email) = mysql_fetch_array($r);
			if (email_seems_valid($email) and
                            !send_mail($sender_name, $sender_email,
                                       '', $email,
                                       $emailsubject, $emailbody, $charset)) {
                                $tool_content .= "<h4>$langMailError</h4>";
			}
		}
		// aldo send email to professor 
		send_mail($sender_name, $sender_email,'', $sender_email, $emailsubject, $emailbody, $charset);
		$tool_content .= "<p class='success_small'>$langEmailSuccess<br />";
		$tool_content .= "<a href='group.php?course=$code_cours'>$langBack</a></p>";
	} else {
		$tool_content .= "
		<form action='$_SERVER[PHP_SELF]?course=$code_cours' method='post'>
		<fieldset>
		<legend>$langTypeMessage</legend>
		<input type='hidden' name='group_id' value='$group_id'>
		<table width='99%' class='FormData'>
		<thead>
		<tr>
		  <td class='left'>$langMailSubject</td></tr>
		</tr>
		<tr>
		    <td><input type='text' name='subject' size='58' class='FormData_InputText'></input></td>
		</tr>
		<tr>
		  <td class='left'>$langMailBody</td>
		</tr>
		<tr>
		  <td><textarea name='body_mail' rows='10' cols='73' class='FormData_InputText'></textarea></td>
		</tr>
		<tr>
		  <td><input type='submit' name='submit' value='$langSend'></input></td>
		</tr>
		</thead>
		</table>
		</fieldset>
		 </form>";
	}
}
draw($tool_content, 2);