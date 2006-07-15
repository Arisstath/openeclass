<?php
/**=============================================================================
       	GUnet e-Class 2.0 
        E-learning and Course Management Program  
================================================================================
       	Copyright(c) 2003-2006  Greek Universities Network - GUnet
        A full copyright notice can be read in "/info/copyright.txt".
        
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
        phpbb/viewtopic.php
        @last update: 2006-07-15 by Artemios G. Voyiatzis
        @authors list: Artemios G. Voyiatzis <bogart@upnet.gr>

        based on Claroline version 1.7 licensed under GPL
              copyright (c) 2001, 2006 Universite catholique de Louvain (UCL)

        Claroline authors: Piraux Sébastien <pir@cerdecam.be>
                      Lederer Guillaume <led@cerdecam.be>

	based on phpBB version 1.4.1 licensed under GPL
		copyright (c) 2001, The phpBB Group
==============================================================================
    @Description: This module implements a per course forum for supporting
	discussions between teachers and students or group of students.
	It is a heavily modified adaptation of phpBB for (initially) Claroline
	and (later) eclass. In the future, a new forum should be developed.
	Currently we use only a fraction of phpBB tables and functionality
	(viewforum, viewtopic, post_reply, newtopic); the time cost is
	enormous for both core phpBB code upgrades and migration from an
	existing (phpBB-based) to a new eclass forum :-(

    @Comments:

    @todo:
==============================================================================
*/

error_reporting(E_ALL);
/*
 * GUNET eclass 2.0 standard stuff
 */
$require_current_course = TRUE;
$require_login = TRUE;
$langFiles = 'phpbb';
$require_help = FALSE;
include '../../include/baseTheme.php';
$nameTools = $l_forums;
$tool_content = "";

/*
 * Tool-specific includes
 */
include_once("./config.php");
include("functions.php"); // application logic for phpBB

/******************************************************************************
 * Actual code starts here
 *****************************************************************************/

$sql = "SELECT f.forum_type, f.forum_name
	FROM forums f, topics t 
	WHERE (f.forum_id = '$forum') AND (t.topic_id = $topic) AND (t.forum_id = f.forum_id)";
if (!$result = db_query($sql, $currentCourseID)) {
	//XXX: Error message in specified language.
	$tool_content .= "An Error Occured. Could not connect to the forums database.";
	draw($tool_content, 2);
	exit;
}
if (!$myrow = mysql_fetch_array($result)) {
	//XXX: Error message in specified language.
	$tool_content .= "Error - The forum/topic you selected does not exist. Please go back and try again.";
	draw($tool_content, 2);
	exit;
}
$forum_name = own_stripslashes($myrow["forum_name"]);

$sql = "SELECT topic_title, topic_status
	FROM topics 
	WHERE topic_id = '$topic'";

$total = get_total_posts($topic, $currentCourseID, "topic");
if ($total > $posts_per_page) {
	$times = 0;
	for ($x = 0; $x < $total; $x += $posts_per_page) {
	     $times++;
	}
	$pages = $times;
}

if (!$result = db_query($sql, $currentCourseID)) {
	$tool_content .= "An Error Occured. Could not connect to the forums database.";
	draw($tool_content, 2);
	exit;
}
$myrow = mysql_fetch_array($result);
$topic_subject = own_stripslashes($myrow["topic_title"]);
$lock_state = $myrow["topic_status"];

if ( $total > $posts_per_page ) {
	$times = 1;
	$tool_content .= <<<cData
		<TABLE BORDER="0" WIDTH="99%" ALIGN="CENTER">
		<TR><TD>$l_gotopage (
cData;
	$last_page = $start - $posts_per_page;
	if ( $start > 0 ) {
		$tool_content .= "<a href=\"$PHP_SELF?topic=$topic&forum=$forum&start=$last_page\">$l_prevpage</a> ";
	}
	for($x = 0; $x < $total; $x += $posts_per_page) {
		if($times != 1) {
			$tool_content .= " | ";
		}
		if($start && ($start == $x)) {
			$tool_content .= "" .  $times;
		} else if($start == 0 && $x == 0) {
			$tool_content .= "1";
		} else {
			$tool_content .= "<a href=\"$PHP_SELF?mode=viewtopic&topic=$topic&forum=$forum&start=$x\">$times</a>";
		}
		$times++;
	}
	if (($start + $posts_per_page) < $total) {
		$next_page = $start + $posts_per_page;
		$tool_content .= " <a href=\"$PHP_SELF?topic=$topic&forum=$forum&start=$next_page\">$l_nextpage</a>";
	}
	$tool_content .= " ) </TD></TR></TABLE>\n";
}

$tool_content .= <<<cData
	<TABLE BORDER="0" CELLPADDING="1" CELLSPACING="0" ALIGN="CENTER" VALIGN="TOP" WIDTH="99%\">
	<TR><TD>
		<TABLE BORDER="0" CELLPADDING="3" CELLSPACING="1" WIDTH="99%">
		<TR><TD WIDTH="20%">$l_author</TD><TD>$topic_subject</TD></TR>
cData;

if (isset($start)) {
	$sql = "SELECT p.*, pt.post_text FROM posts p, posts_text pt 
		WHERE topic_id = '$topic' 
		AND p.post_id = pt.post_id
		ORDER BY post_id LIMIT $start, $posts_per_page";
} else {
	$sql = "SELECT p.*, pt.post_text FROM posts p, posts_text pt
		WHERE topic_id = '$topic'
		AND p.post_id = pt.post_id
		ORDER BY post_id LIMIT $posts_per_page";
}
if (!$result = db_query($sql, $currentCourseID)) {
	$tool_content .= "An Error Occured. Could not connect to the Posts database. $sql";
	draw($tool_content, 2);
	exit;
}
$myrow = mysql_fetch_array($result);
$row_color = $color2;
$count = 0;
do {
	if(!($count % 2))
		$row_color = $color2;
	else 
		$row_color = $color1;
	$tool_content .= "<TR BGCOLOR=\"$row_color\" ALIGN=\"LEFT\">\n";
	$tool_content .= "<TD>" . $myrow["prenom"] . " " . $myrow["nom"] . "</TD>";
	$tool_content .= "<TD><img src=\"$posticon\">$l_posted: " . $myrow["post_time"] . "&nbsp;&nbsp;&nbsp";
	$tool_content .= "<HR>\n";
	$message = own_stripslashes($myrow["post_text"]);
	$tool_content .= "$message<BR><HR>";
	if ($status[$dbname]==1 OR $status[$dbname]==2) { // course admin
		$tool_content .= "<a href=\"editpost.php?post_id=" . 
					$myrow["post_id"] . 
					"&topic=$topic&forum=$forum\">$langEditDel</a>";
	}
	$tool_content .= "</TD></TR>";
	$count++;
} while($myrow = mysql_fetch_array($result));

$sql = "UPDATE topics SET topic_views = topic_views + 1 WHERE topic_id = '$topic'";
db_query($sql, $currentCourseID);

$tool_content .= "</TABLE></TD></TR></TABLE>";
$tool_content .= "<TABLE ALIGN=\"CENTER\" BORDER=\"0\" WIDTH=\"99%\">";

if ($total > $posts_per_page) {
	$times = 1;
	$tool_content .= "<TR ALIGN=\"RIGHT\"><TD colspan=2>$l_gotopage ( ";
	$last_page = $start - $posts_per_page;
	if($start > 0) {
		$tool_content .= "<a href=\"$PHP_SELF?topic=$topic&forum=$forum&start=$last_page\">$l_prevpage</a> ";
	}
	for($x = 0; $x < $total; $x += $posts_per_page) {
		if ($times != 1) {
			$tool_content .= " | ";
		}
		if ($start && ($start == $x)) {
			$tool_content .= $times;
		} else if ($start == 0 && $x == 0) {
			$tool_content .= "1";
		} else {
			$tool_content .= "<a href=\"$PHP_SELF?mode=viewtopic&topic=$topic&forum=$forum&start=$x\">$times</a>";
		}
		$times++;
	}
	if (($start + $posts_per_page) < $total) {
		$next_page = $start + $posts_per_page;
		$tool_content .= "<a href=\"".$PHP_SELF."?topic=".$topic."&forum=".$forum."&start=".$next_page."\">".$l_nextpage."</a>";
	}
	$tool_content .= "
			</FONT>
		</TD>
	</TR>";
}

$tool_content .= "<TR><TD colspan=\"2\"><a href=\"newtopic.php?forum=$forum\">";
$tool_content .= "$langNewTopic</a>&nbsp;&nbsp;";
if($lock_state != 1) {
	$tool_content .= "<a href=\"reply.php?topic=$topic&forum=$forum\">$langAnswer</a></TD></TR>";
} else {
	$tool_content .= "<IMG SRC=\"$reply_locked_image\" BORDER=\"0\"></TD></TR>";
}
$tool_content .= "</TD><TD ALIGN=\"RIGHT\" colspan=2><hr noshade size=1></TR></TABLE><CENTER>";

draw($tool_content,2);
?>
