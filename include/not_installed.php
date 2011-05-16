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
 * Not Installed Component
 *
 * @author Evelthon Prodromou <eprodromou@upnet.gr>
 * @version $Id$
 *
 * @abstract Outputs a message to the user's browser to inform him/her that eclass
 * is not installed.
 *
 */

$tool_content = "
<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
<html xmlns=\"http://www.w3.org/1999/xhtml\">
  <head>
    <title>Πλατφόρμα Ασύγχρονης Τηλεκπαίδευσης Open eClass</title>
    <meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
    <link href='./install/install.css' rel='stylesheet' type='text/css' />

  </head>
  <body>
  <div class='install_container'>
  <p><img src='./template/classic/img/logo_openeclass.png' alt='logo' /></p>
  <div class='alert' align='center'>Η πλατφόρμα ασύγχρονης τηλεκπαίδευσης Open eClass δεν λειτουργεί!</div>
      <table width='600' align='center' cellpadding='5' cellspacing='5' class='tbl_alt'>
    <tr>
      <th width='300'>Πιθανοί λόγοι</th>
      <th>Αντιμετώπιση</th>
    </tr>
    <tr>
      <td>Υπάρχει πρόβλημα με την <b>MySQL</b>:</td>
      <td>Eπικοινωνήστε με το διαχειριστή του συστήματος.</td>
    </tr>
    <tr>
      <td>Πρόβλημα στο αρχείο \"<b>config.php</b>\":</td>
      <td>Το αρχείο δεν υπάρχει ή δεν μπορεί να διαβαστεί.</td>
    </tr>
    <tr>
      <td>Xρησιμοποιείτε την πλατφόρμα για <b>πρώτη</b> φορά:</td>
      <td>Επιλέξτε τον <a href=\"./install/\" class=\"installer\"><b>Οδηγό Εγκατάστασης</b></a><br /> για να ξεκινήσετε το πρόγραμμα εγκατάστασης</td>
    </tr>
    </table>
	</div>
  </body>
</html>
";
echo $tool_content;
exit();
?>
