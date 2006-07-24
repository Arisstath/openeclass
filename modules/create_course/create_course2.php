<?


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

/*===========================================================================
	create_course2.php
	@last update: 18-07-2006 by Sakis Agorastos
	@authors list: Agorastos Sakis <th_agorastos@hotmail.com>
==============================================================================        
        @Description: 2nd step for the Create New Course Wizard

    The script transfers data from the 1st step of the wizard in hidden
    input tags.
        
 	The script requires some fields to be filled-in, thus it checks the
 	validity of the entries by javascripts.
==============================================================================*/

$require_login = TRUE;
$require_prof = TRUE;

$langFiles = array('create_course', 'opencours');

$local_head = "<script language=\"javascript\">
function previous_step()
{
	document.location.href = \"./create_course.php\";
}

function validate() {

		if (document.forms[0].description.value==\"\") {
				alert(\"�������� ����������� ��� ������� ��������� ��� �� ������!\");	
				return false;																																																	}

     if (document.forms[0].course_keywords.value==\"\") {
					alert(\"�������� ����������� ��� ������ ������� ��� ���������!\");
					return false;
		}
	return true;																																																}

</script>";

include '../../include/baseTheme.php';

$tool_content = "";

$titulaire_probable="$prenom $nom";
$local_style = "input { font-size: 12px; }";

// ---------------------------------------------
// ---------------------- form -----------------
// ---------------------------------------------

   @$tool_content .= "
<!-- S T E P  2   [start] -->    

<tr bgcolor=\"$color1\">
	<td>
		<table bgcolor=\"$color1\" border=\"2\">
			<tr valign=\"top\" align=\"middle\">
				<td colspan=\"3\" valign=\"middle\">
					<table width=\"100%\">
						<tr>
							<td align=\"left\">
								<font face=\"arial, helvetica\" size=\"4\" color=\"gray\">$langCreateCourse</font>
							</td>
							<td align=\"right\">
								<font face=\"arial, helvetica\" size=\"4\" color=\"gray\">$langCreateCourseStep&nbsp;2&nbsp;$langCreateCourseStep2&nbsp;3</font>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr valign=\"top\">
				<td colspan=\"5\" valign=\"middle\">
					<font face=\"arial, helvetica\" size=\"3\"><b>$langCreateCourseStep2Title</b></font>
				</td>
			</tr>
			<tr><td colspan=\"3\"><font face=\"arial, helvetica\" size=\"2\"><i>$langFieldsOptionalNote</i></font></td></tr>
			<tr><td colspan=\"3\">&nbsp;</td></tr>

<form method=\"post\" action=\"create_course3.php\" onsubmit=\"return validate();\">
	
	<input type=\"hidden\" name=\"intitule\" value=\"$intitule\">
	<input type=\"hidden\" name=\"faculte\" value=\"$faculte\">
	<input type=\"hidden\" name=\"titulaires\" value=\"$titulaires\">
	<input type=\"hidden\" name=\"type\" value=\"$type\">
	
			<tr valign=\"top\"> 
			<td width=\"100\" valign=\"top\" align=\"right\"> 
			<font face=\"arial, helvetica\" size=\"2\"><b>$langDescrInfo:</b></font>
			</td>   
			<td valign=top>
			<font face=\"arial, helvetica\" size=\"2\">
			<textarea name=\"description\" cols=\"40\" rows=\"4\"></textarea>
			$langFieldsRequAsterisk
			</font>    
			</td> 
				<td valign=\"middle\">
					<a href=\"../help/help.php?topic=CreateCourse_course_intronote\" onclick=\"window.open('../help/help.php?topic=CreateCourse_course_intronote','help','toolbar=no,location=no,directories=no,status=yes,menubar=no,scrollbars=yes,resizable=yes,width=400,height=500,left=300,top=10'); return false;\"><img src=\"../../template/classic/img/help.gif\" border=\"0\"></a>
				</td>
			</tr>
			<tr>
			<td align=\"top\" align=\"right\">
		<font face=\"arial, helvetica\" size=\"2\"><b>$langCourseKeywords</b></font>
		</td>
<td valign=\"top\">
			<font face=\"arial, helvetica\" size=\"2\">
			<textarea name=\"course_keywords\" value=\"$course_keywords\" cols=\"40\" rows=\"2\"></textarea>
			$langFieldsRequAsterisk
			</font>
				</td>
				<td valign=\"middle\">
					<a href=\"../help/help.php?topic=CreateCourse_course_intronote\" onclick=\"window.open('../help/help.php?topic=CreateCourse_course_intronote','help','toolbar=no,location=no,directories=no,status=yes,menubar=no,scrollbars=yes,resizable=yes,width=400,height=500,left=300,top=10'); return false;\"><img src=\"../../template/classic/img/help.gif\" border=\"0\"></a>
				</td>
			</tr>
			<tr>
			<td align=\"top\" align=\"right\">
		<font face=\"arial, helvetica\" size=\"2\"><b>$langCourseAddon</b></font>
		</td>
<td valign=\"top\">
			<font face=\"arial, helvetica\" size=\"2\">
			<textarea name=\"course_addon\" value=\"$course_addon\" cols=\"40\" rows=\"4\"></textarea></font>
			</td>
			<td>
			<a href=\"../help/help.php?topic=CreateCourse_course_intronote\" onclick=\"window.open('../help/help.php?topic=CreateCourse_course_intronote','help','toolbar=no,location=no,directories=no,status=yes,menubar=no,scrollbars=yes,resizable=yes,width=400,height=500,left=300,top=10'); return false;\"><img src=\"../../template/classic/img/help.gif\" border=\"0\"></a>
		</td>
			</tr>
			</table>
	</td>
	</tr>
	<tr>
		<td align=\"left\">
			<input type=\"button\" name=\"button\" value=\"$langPreviousStep\" onclick=\"previous_step();\">
		</td>
		<td align=\"right\">
			<input type=\"Submit\" name=\"submit\" value=\"$langNextStep\">
		</td>
	</tr>
	</table>
	</td>
	</tr>
	</table>
</form>
</body>
</html>";

draw($tool_content, '1', '', $local_head);

?>
