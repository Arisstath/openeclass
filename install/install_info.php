<?php header('Content-Type: text/html; charset=UTF-8');
?><!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<META http-equiv=Content-Type content="text/html; charset=UTF-8">
<title>Οδηγίες Εγκατάστασης Πλατφόρμας Open eClass 2.3</title>
<style type="text/css">

body {
 font-family: Verdana, Times New Roman;
  font-size: 12px;
  font-weight: normal;
 margin-right: 2em;
 margin-left: 2em;
}

p {
 line-height: 110%;
}
 
ol, ul {
 line-height: 140%;
}


h1, h2, h3, h4, h5 {
 font-weight: bold;
}

h2 { 
  font-size: 19px;
} 

h3 { 
  font-size: 16px;
} 

h4 { 
  font-size: 13px;
} 

pre {
 margin-left: 3em;
 padding: .5em;
}

.note {
 background-color: #E6E6E6;
}

</style>
<style type="text/css">
 li.c2 {list-style: none}
 div.c1 {text-align: center}
</style>
  </head>
  <body>
    <h2>Οδηγίες Εγκατάστασης Πλατφόρμας Open eClass 2.3</h2>
<p>Η πλατφόρμα <b>Open eClass</b> είναι ένα ολοκληρωμένο Σύστημα Διαχείρισης Ηλεκτρονικών Μαθημάτων και αποτελεί την πρόταση του Ακαδημαϊκού Διαδικτύου GUnet για την υποστήριξη της Υπηρεσίας Ασύγχρονης Τηλεκπαίδευσης. Αναπτύχθηκε από την Ομάδα Ασύγχρονης Τηλεκπαίδευσης του Ακαδημαϊκού Διαδικτύου, υποστηρίζεται κεντρικά από το GUnet και διανέμεται ελεύθερα ως Λογισμικό Ανοικτού Κώδικα.</p>
    <p>
      Η Πλατφόρμα Ασύγχρονης Τηλεκπαίδευσης <b>Open eClass
      2.3</b> έχει δοκιμαστεί και λειτουργεί κανονικά
    </p>
    <ul>
      <li>Σε περιβάλλον MsWindows (<b>Windows NT</b>, <b>Windows
      2000</b>, <b>Windows XP</b>, <b>Windows 2003</b>, <b>Windows >Vista</b>, <b>Windows 7</b>)
      </li>
      <li>Σε διάφορες διανομές Linux (π.χ. <b>RedHat</b>, <b>CentOS</b>,
      <b>Debian</b>, <b>Ubuntu</b>, <b>Suse</b> κ.λπ.)
      </li>
      <li>Σε περιβάλλον UNIX (π.χ. <b>Solaris</b>).
      </li>
    </ul>Στη συνέχεια παρατίθενται αναλυτικά οι οδηγίες
    εγκατάστασης της πλατφόρμας:<br>
    <ul>
      <li><a href="#before">Ενέργειες πριν την εγκατάσταση - Προαπαιτούμενα</a></li>
      <li><a href="#unix">Για περιβάλλοντα Unix / Linux </a> </li>
      <li><a href="#win">Για περιβάλλοντα MsWindows</a></li>
      <li><a href="#after">Ενέργειες μετά την εγκατάσταση - Άλλες ρυθμίσεις</a></li>
    </ul>
    <hr>
    <h3>
      <a name="before" id="before">Ενέργειες πριν την εγκατάσταση -
      Προαπαιτούμενα:</a>
    </h3>
    <p>
	Για την εγκατάσταση της πλατφόρμας Open eClass 2.3, απαιτείται η ύπαρξη και η καλή λειτουργία μιας σειράς συστημάτων και εφαρμογών. Αυτές είναι οι εξής:
    </p>
    <h4>
      1. Web Server (<a href="http://httpd.apache.org/" target="_blank">Apache</a> 1.3.x ή 2.x)
    </h4>
    <p>
      Ο Apache πρέπει να μπορεί να χειριστεί σελίδες τύπου
      <em>.php .inc</em> Αν δεν τον έχετε ρυθμίσει,
      τότε αρκεί στο αρχείο <code>httpd.conf</code> να προσθέσετε
      την ακόλουθη γραμμή:
    </p>

    <pre>AddType application/x-httpd-php .php .inc</pre>

    
<p> Επίσης, θα πρέπει να ορίσετε ότι η προκαθορισμένη κωδικοποίηση των σελίδων 
  που στέλνει ο Web Server είναι <em>UTF-8</em>. Στον Apache, 
  αυτό μπορείτε να το κάνετε βάζοντας στο αρχείο <code>httpd.conf</code> την δήλωση: 
</p>
    <pre>AddDefaultCharset UTF-8</pre>
    
<p> Αν χρησιμοποιείτε apache 1.3.x, για λόγους ασφαλείας, απενεργοποιήστε το directory 
  indexing για τους υποκαταλόγους που θα εγκατασταθεί το eClass. Στο αρχείο <code>httpd.conf</code> 
  προσθέστε στα Options την επιλογή <em>-Indexes</em>. Αν για παράδειγμα το eClass 
  θα εγκατασταθεί στο /var/www/ τότε προσθέστε στο <code>httpd.conf</code> την 
  παρακάτω δήλωση: </p>
    <pre>
&lt;Directory /var/www/&gt;
................
Options -Indexes
................
&lt;/Directory&gt;
</pre>
    <div class="note">
      <p>
        <b>Μόνο για Windows</b>. Αν στον υπολογιστή σας τρέχει o
        WebServer της Microsoft (<em>IIS</em>) πρέπει να τον
        απενεργοποιήσετε. Πηγαίνετε στο
        <em>Start-&gt;Programs-&gt;Administrative
        Tools-&gt;Services</em> και σταματήστε την υπηρεσία
        <em>«World Wide Web Publishing Service»</em>, κάνοντας δεξί
        κλικ και επιλέγοντας <em>«stop»</em>. Για να
        απενεργοποιηθεί ο <em>IIS</em> μόνιμα, κάντε δεξί κλικ στην
        παραπάνω υπηρεσία και επιλέξτε <em>«Disabled»</em> από τον
        κατάλογο επιλογών <em>«Startup type»</em>.
	Να σημειωθεί ότι η πλατφόρμα λειτουργεί κανονικά και σε περιβάλλον IIS αλλά δεν έχει δοκιμαστεί εκτενώς. 
      </p>

    </div>
<h4>2. <a href="http://www.php.net" target="_blank">PHP</a> (εκδόσεις &gt;= 4.3.0) 
</h4>
<p> Η πλατφόρμα λειτουργεί χωρίς προβλήματα με εκδόσεις &gt;= <i>4.3.0 </i>και 
  με εκδόσεις <em>5.x.</em> </p>
    
<p> Ταυτόχρονα με την εγκατάσταση της PHP, απαιτείται και ενεργοποίηση της υποστήριξης 
  του Apache για PHP. Σημειώστε ότι στη PHP, θα πρέπει να ενεργοποιήσετε την υποστήριξη 
  για τα modules <em>mysql, zlib, pcre</em> και <em>gd</em>. Αν επιθυμείτε να χρησιμοποιήσετε 
  κάποιον εξυπηρέτη LDAP για την πιστοποίηση των χρηστών, τότε θα πρέπει να ενεργοποιήσετε 
  και το module για την υποστήριξη <em>ldap</em>. Είναι πιθανόν η διανομή της PHP που χρησιμοποιείτε
	να έχει ενσωματωμένη υποστήριξη για κάποια από τα παραπάνω modules. </p>
    <p>
      Κατά την εγκατάσταση του eClass ελέγχεται αν πληρούνται τα
      παραπάνω.
    </p>
<p> Θα πρέπει επίσης να ορίσετε στο αρχείο <code>php.ini</code> τις παραμέτρους: 
</p>
<pre>register_globals = on
short_open_tag = on
magic_quotes_gpc = on</pre>
    <p>
      Όσον αφορά το μέγιστο μέγεθος των αρχείων που θα επιτρέπεται
      να γίνονται upload στην πλατφόρμα, μπορείτε να το ρυθμίσετε
      με τις παρακάτω γραμμές στο αρχείο <code>php.ini</code>.
      Προτείνεται:
    </p>

    <ul>
      <li>
        <code>upload_max_filesize = 40M</code> (η προκαθορισμένη
        τιμή είναι 2M)
      </li>
      <li>
        <code>memory_limit = 25M</code> (η προκαθορισμένη είναι 8M)
      </li>
      <li>
        <code>post_max_size = 45M</code> (η προκαθορισμένη είναι
        8M)
      </li>
      <li>
        <code>max_execution_time = 100</code> (η προκαθορισμένη
        είναι 30 sec)
      </li>
    </ul>
<p> Επίσης, αν τυχόν, εμφανιστεί κάποιο notice της PHP κατά την διάρκεια της εφαρμογής, 
  αναζητήστε στο αρχείο <code>php.ini</code> την μεταβλητή <em>display_errors</em> και 
  αλλάξτε την τιμή της σε: </p>

    <pre>display_errors = Off</pre>
    <div class="note">
      <ul>
        <li class="c2">
          <b>Μόνο για Windows</b>.
        </li>
        <li>Στα windows extensions βγάζετε τα σχόλια (;) από τη
        γραμμή  <code>extension = php_ldap.dll</code>
        </li>
        <li>Αλλάξτε την μεταβλητή <em>session.save_path</em> σε ένα
        path το οποίο υπάρχει <em>(π.χ.
        session.save_path=c:\winnt\temp\)</em>. Βεβαιωθείτε επίσης,
        ότι ο apache έχει δικαιώματα πρόσβασης σε αυτό.
        </li>
        <li>Συμπληρώστε τον SMTP server που σας εξυπηρετεί για
        αποστολή e-mail, π.χ. <code>SMTP = mail.gunet.gr</code>
        </li>
        <li>Συμπληρώστε μια έγκυρη email διεύθυνση αποστολέα στο πεδίο <code>sendmail_from</code></li> 
      </ul>
    </div>
    <p>
      Τέλος, καλό είναι να ορίσετε κι εδώ την προκαθορισμένη
      κωδικοποίηση με τη γραμμή: <code>default_charset =
      "UTF-8"</code>
    </p>
    <p>
      Μόλις ολοκληρώσετε τις αλλαγές σας, επανεκκινήστε τον apache.
    </p>
<h4> 3. <a href="http://www.mysql.com" target="_blank">MySQL</a> (εκδόσεις 4.1.x 
  ή 5.x) </h4>
    <p>
      Παράλληλα με την εγκατάσταση της MySQL θα πρέπει να
      δημιουργηθεί ένας λογαριασμός χρήστη (user account), με
      δικαιώματα δημιουργίας βάσης δεδομένων.
    </p>
	<p>Προσέξτε η MySQL να μην τρέχει σε strict mode.
	Για να το διαπιστώσετε ελέγξτε αν έχει κάποια τιμή η παράμετρος 
	<pre>--sql-mode</pre> η οποία βρίσκεται στο αρχείο ρυθμίσεων <em>my.cnf</em> ή <em>my.ini</em> 
	για τους χρήστες UNIX και Windows αντίστοιχα. Αν έχει (π.χ. <code>--sql-mode=STRICT_TRANS_TABLES</code>
	ή <code>--sql-mode=STRICT_ALL_TABLES</code>) τότε αλλάξτε την σε κενή (<code>--sql-mode=""</code>). 
	</p>
    <h4>
      4. <a href="http://www.sendmail.org" target=
      "_blank">sendmail</a> ή <a href="http://www.postfix.org"
      target="_blank">postfix</a> (προαιρετικά)
    </h4>

    <p>
      Σε μερικές λειτουργίες της πλατφόρμας (π.χ. κατά την εγγραφή
      των χρηστών), γίνεται αποστολή mail. Αν δεν λειτουργεί κάποια
      εφαρμογή αποστολής mail, τα mail της πλατφόρμας δεν θα
      αποστέλλονται πουθενά.
    </p>
    <div class="note">
      <p>
        <b>Μόνο για Windows:</b> Εναλλακτικά, για να εγκαταστήσετε
        τα παραπάνω, μπορείτε να χρησιμοποιήσετε το πακέτο
       <a href="http://www.easyphp.org" target="_blank">EasyPHP</a> ή το πακέτο <a href="http://www.apachefriends.org/en/xampp-windows.html" target="_blank">XAMPP</a>.
      </p>
    </div>
    <hr>
    <h3>
      <a name="unix" id="unix">Για περιβάλλοντα Unix / Linux</a>
    </h3>
    <h3>
      Διαδικασία εγκατάστασης:
    </h3>
    <p>
      Μπορείτε να αποσυμπιέσετε το αρχείο <b>openeclass-2.3.tar.gz</b> με
      την εντολή <code>tar xzvf openeclass-2.3.tar.gz</code>. O
      υποκατάλογος που δημιουργείται κατά την αποσυμπίεση του
      πακέτου, περιέχει όλα τα αρχεία της εφαρμογής. Αυτός ο
      υποκατάλογος πρέπει να τοποθετηθεί σε σημείο προσβάσιμο από
      τον web server του υπολογιστή.
    </p>
    <p>
	Για να ρυθμίσετε τα  δικαιώματα πρόσβασης μπορείτε να δώσετε τις παρακάτω εντολές
	(αν ο web server τρέχει σαν χρήστης www-data)
    </p><pre>
		cd (path του eclass) (π.χ. cd /opt/openeclass)
		chown -R www-data *
		find ./ -type f -exec chmod 664 {} \;
		find ./ -type d -exec chmod 775 {} \;
		</pre>
    <p>
      Για να δώσετε τις παραπάνω εντολές, συνήθως χρειάζεται
      δικαιώματα διαχειριστή (root).
    </p>
    <p>
      Για να ξεκινήσετε την εγκατάσταση, επισκεφτείτε με κάποιον
      web browser την διεύθυνση που βρίσκεται ο υποκατάλογος
      /install/. Αν για παράδειγμα ο κυρίως κατάλογος της
      εφαρμογής (ο openeclass/) βρίσκεται στη διεύθυνση
      http://www.example.gr/openeclass/, η διεύθυνση που πρέπει να
      πληκτρολογήσετε είναι η
    </p><code>http://www.example.gr/openeclass/install/</code>
    Στη συνέχεια ακολουθείστε τα βήματα του οδηγού εγκατάστασης της
    πλατφόρμας όπως αυτά παρουσιάζονται στην οθόνη σας. Σημειώστε
    ότι κατά την διαδικασίας εγκατάστασης θα σας ζητηθούν τα
    παρακάτω:
    
<ul>
  <li>Το όνομα του υπολογιστή όπου είναι εγκατεστημένη η MySQL (π.χ. openeclass.org, 
    localhost - αν είναι στον ίδιο υπολογιστή) </li>
  <li>Ένα "Όνομα Χρήστη" και "Συνθηματικό" για τη MySQL με δικαιώματα δημιουργίας 
    νέων βάσεων </li>
  <li>Όνομα για την κύρια βάση δεδομένων του eClass (το default είναι eclass). 
    Αλλάξτε το οπωσδήποτε, αν υπάρχει ήδη μια βάση δεδομένων με το ίδιο όνομα. 
  </li>
  <li>URL της πλατφόρμας (όπως αυτό θα εμφανίζεται στον browser μετά την εγκατάσταση 
    π.χ. http://www.openeclass.org/eclass/) </li>
  <li>Το path των αρχείων στον server. Βεβαιωθείτε ότι το path είναι σωστό (π.χ. 
    /var/www/html/). </li>
  <li>Όνομα / Επώνυμο και email Διαχειριστή. </li>
  <li>Όνομα Χρήστη και Συνθηματικό Διαχειριστή.</li>
  <li>Το όνομα που θέλετε να δώσετε στην πλατφόρμα (π.χ. Open eClass) </li>
  <li>Tηλέφωνο και email helpdesk (σε αυτό το email στέλνονται οι διάφορες αιτήσεις, 
    θα μπορούσε να είναι το ίδιο με του διαχειριστή). </li>
  <li>Όνομα και διεύθυνση του ιδρύματος σας.</li>
</ul>
    <hr>
    <h3>
      <a name="win" id="win">Για περιβάλλοντα MsWindows</a>
    </h3>
    <h3>
      Διαδικασία εγκατάστασης:
    </h3>
    <p>
      Αποσυμπιέστε το αρχείο openeclass-2.2.zip στο root directory του
      Apache. O υποκατάλογος που δημιουργείται κατά την αποσυμπίεση
      του πακέτου, περιέχει όλα τα αρχεία της εφαρμογής. Αυτός ο
      υποκατάλογος πρέπει να τοποθετηθεί σε σημείο προσβάσιμο από
      τον web server του υπολογιστή.
    </p>
    <p>
      Για να ξεκινήσετε την εγκατάσταση, επισκεφτείτε με κάποιον
      web browser την διεύθυνση που βρίσκεται ο υποκατάλογος
      /install/. Αν για παράδειγμα ο κυρίως κατάλογος της
      εφαρμογής (ο openeclass/) βρίσκεται στη διεύθυνση
      http://www.example.gr/eclass/, η διεύθυνση που πρέπει να
      πληκτρολογήσετε είναι η
    </p><code>http://www.example.gr/openeclass/install/</code>
    <p>
      Στη συνέχεια ακολουθείστε τα βήματα του οδηγού εγκατάστασης
      της πλατφόρμας όπως αυτά παρουσιάζονται στην οθόνη σας.
    </p>
    <p>
      Σημειώστε ότι κατά την διαδικασίας εγκατάστασης θα σας
      ζητηθούν τα παρακάτω:
    </p>
    
<ul>
  <li>Το όνομα του υπολογιστή όπου είναι εγκατεστημένη η MySQL (π.χ. openeclass.org, 
    localhost - αν είναι στον ίδιο υπολογιστή) </li>
  <li>Ένα "Όνομα Χρήστη" και "Συνθηματικό" για τη MySQL με δικαιώματα δημιουργίας 
    νέων βάσεων </li>
  <li>Όνομα για την κύρια βάση δεδομένων του eClass (το default είναι eclass). 
    Αλλάξτε το οπωσδήποτε, αν υπάρχει ήδη μια βάση δεδομένων με το ίδιο όνομα. 
  </li>
  <li>URL της πλατφόρμας (όπως αυτό θα εμφανίζεται στον browser μετά την εγκατάσταση 
    π.χ. http://www.openeclass.org/eclass/) </li>
  <li>Το path των αρχείων στον server. Βεβαιωθείτε ότι το path είναι σωστό (π.χ. 
    C:\Program Files\Apache\htdocs\). </li>
  <li>Όνομα / Επώνυμο και email Διαχειριστή. </li>
  <li>Όνομα Χρήστη και Συνθηματικό Διαχειριστή </li>
  <li>Το όνομα που θέλετε να δώσετε στην πλατφόρμα (π.χ. Open eClass) </li>
  <li>Tηλέφωνο και email helpdesk (σε αυτό το email έρχονται οι διάφορες αιτήσεις 
    θα μπορούσε να είναι το ίδιο με του διαχειριστή). </li>
  <li>Όνομα και διεύθυνση του ιδρύματος σας.</li>
</ul>
    <hr>
    <h3>
      <a name="after" id="after">Ενέργειες μετά την εγκατάσταση -
      Άλλες ρυθμίσεις:</a>
    </h3>
<ul>
  <li> 
    <p> Η πλατφόρμα διανέμεται με το διαχειριστικό εργαλείο phpMyAdmin. Για λόγους 
      ασφαλείας, η πρόσβαση στο phpMyAdmin γίνεται μέσω cookies του browser. Αν 
      θέλετε να το αλλάξετε, ανατρέξτε στο αρχείο ρυθμίσεων <em>config.inc.php</em> 
      του phpMyAdmin.</p>
  </li>
  <li>To OpeneClass κατά την εγκατάσταση εισάγει 3 δοκιμαστικές / γενικές Σχολές 
    /Τμήματα. (Τμήμα 1 με κωδικό TMA, Τμήμα 2 με κωδικό TMB κ.λπ.). Εσείς θα πρέπει 
    να τις αλλάξετε και να τις προσαρμόσετε στις Σχολές-Τμήματα του Ιδρύματός 
    σας. Αυτό μπορείτε να το κάνετε μέσα από το διαχειριστικό εργαλείο. Περισσότερες 
    και αναλυτικότερες οδηγίες για τις ενέργειες αυτές, μπορείτε να βρείτε στο 
    εγχειρίδιο του Διαχειριστή (βρίσκεται μέσα στο διαχειριστικό εργαλείο). </li>
  <li> 
    <p> Aν κάποιο ίδρυμα θέλει να αντικαταστήσει το αρχικό λογότυπο του OpeneClass 
      με το δικό, αρκεί να αντικαταστήσει την εικόνα </p>
    <code>(path του eClass)/template/classic/img/logo_bg_50.gif</code> 
    <p> με την δικιά του. </p>
  </li>
  <li>  
    <p> Στο αρχείο <em>config.php</em> ορίζεται η μεταβλητή <em>close_user_registration</em> 
      η οποία εξ'ορισμού έχει τιμή <em>FALSE</em>. Αλλάζοντας την σε τιμή <em>TRUE</em> 
      η εγγραφή χρηστών με δικαιώματα "φοιτητή" δεν θα είναι πλέον ελεύθερη. Οι 
      χρήστες για να αποκτήσουν λογαριασμό στην πλατφόρμα θα ακολουθούν πλέον 
      διαδικασία παρόμοια με τη δημιουργία λογαριασμού "καθηγητή" δηλαδή θα συμπληρώνουν 
      μια φόρμα-αίτηση δημουργίας λογαριασμού φοιτητή. Η αίτηση εξετάζεται από 
      τον διαχειριστή ο οποίος εγκρίνει την αίτηση, οπότε δημιουργεί τον λογαριασμό, 
      ή την απορρίπτει. Αν δεν επιθυμείτε να αλλάξει ο τρόπος εγγραφής φοιτητών 
      αφήστε την όπως είναι. (δηλαδή στην τιμή <em>FALSE</em>). </p>
  </li>
<li>
<p> Αν θέλετε να χρησιμοποιήσετε την πλατφόρμα με Web server που έχει ενεργοποιημένη την υποστήριξη SSL 
(π.χ. https://eclass.gunet.gr) μπορείτε να το κάνετε δηλώνοντας στο <em>config.php</em> την μεταβλητή
<em>urlSecure</em>. π.χ. <code>$urlSecure = "https://eclass.gunet.gr"</code>. Περισσότερες και αναλυτικότερες 
οδηγίες για τις ενέργειες αυτές, μπορείτε να βρείτε στο εγχειρίδιο του Διαχειριστή (βρίσκεται μέσα στο διαχειριστικό εργαλείο).
</p>
</li>

<li>
<p>Αν θέλετε να αλλάξετε οποιοδήποτε μήνυμα της πλατφόρμας συνίσταται να το κάνετε ως εξής:
Δημιουργήστε ένα αρχείο τύπου .php με όνομα <em>greek.inc.php</em> (ή <em>english.inc.php</em> αν πρόκειται για αγγλικά μηνύματα) και τοποθετήστε το στον κατάλογο <em>(path του eclass)/config/</em>. Αναζητήστε το όνομα της μεταβλητής που περιέχει το μήνυμα που θέλετε να αλλάξετε και απλά αναθέστε της το καινούριο μήνυμα. Π.χ.
Αν θέλουμε να αλλάξουμε το μήνυμα <pre>$langAboutText = "Η έκδοση της πλατφόρμας είναι";</pre> απλά δημιουργούμε το <em>greek.inc.php</em> στον κατάλογo (path του eclass)/config/ ως εξής:
<pre>
&lt;?
$langAboutText = "Τρέχουσα έκδοση της πλατφόρμας";
?&gt;
</pre>
Με τον παραπάνω τρόπο εξασφαλίζεται η διατήρηση των τροποποιημένων μηνυμάτων από μελλοντικές αναβαθμίσεις της πλατφόρμας.
<p>
Μπορείτε να αλλάξετε τα ονόματα των βασικών ρόλων των χρηστών της πλατφόρμας αλλάζοντας το αρχείο μηνυμάτων <em>(path του eClass)/modules/lang/greek/common.inc.php</em>
</p>
<p>Επίσης σημειώστε ότι μπορείτε να προσθέσετε κείμενο (π.χ. ενημερωτικού περιεχομένου) στα αριστερά και δεξιά της αρχικής σελίδας της πλατφόρμας. Για το σκοπό αυτό, αναθέστε την τιμή - μήνυμα στις μεταβλητές <em>$langExtrasLeft</em> και <em>$langExtrasRight</em> αντίστοιχα, που βρίσκονται στο <em>(path του eClass)/modules/lang/greek/common.inc.php</em>
</p>
</li>
<li>Η πλατφόρμα υποστηρίζει την συγγραφή μαθηματικών συμβόλων στα υποσύστηματα "Ασκήσεις", "Περιοχές συζητήσεων" και "Ανακοινώσεις". Συγκεκριμένα στο υποσύστημα "Ασκήσεις" μπορείτε να βάλετε μαθηματικά σύμβολα στα πεδία "Περιγραφή Άσκησης" όταν δημιουργείτε μια καινούρια άσκηση (ή όταν την διορθώνετε), στο πεδίο "Προαιρετικό Σχόλιο" όταν δημιουργείτε μια καινούρια ερώτηση σε μια άσκηση (ή όταν την διορθώνετε). Στο υποσύστημα "Περιοχές συζητήσεων" όταν συντάσσετε ένα καινούριο μήνυμα ή όταν απαντάτε σε αυτό και στο υποσύστημα "Ανακοινώσεις" όταν δημιουργείτε μια ανακοίνωση. Τα μαθηματικά σύμβολα πρέπει απαραίτητα να περικλείονται με τα tags <em>&lt;m&gt;</em> και <em>&lt;/m&gt;</em>.
Π.χ. πληκτρολογώντας
<pre>
&lt;m&gt;sqrt{x-1}&lt;/m&gt; 
</pre>
θα σχηματιστεί η τετραγωνική ρίζα του x-1. Για την σύνταξη των υπόλοιπων μαθηματικών συμβόλων ανατρέξτε στο <em>http://(url της εγκατάστασης)/manuals/PhpMathPublisherHelp.pdf</em> 
</li>
  <li>Τέλος, στους χρήστες της πλατφόρμας, θα πρέπει να επισημανθεί να έχουν την 
    javascript ενεργοποιημένη στον browser τους. </li>
  <li class="c2"> 
    <ul>
      <li>Για τους χρήστες του Internet Explorer, από τα μενού επιλέξτε διαδοχικά 
        <em>Internet Options/Security/Custom Level/Security Options</em> και μαρκάρετε 
        την επιλογή <em>"Scripting of java applets"</em>. </li>
      <li>Για τους χρήστες του <em>Firefox</em>, επιλέξτε διαδοχικά <em>Edit / 
        Preferences / Web features</em> και μαρκάρετε την επιλογή <em>"Enable 
        JavaScript".</em> </li>
    </ul>
    <div class="note"> 
      <ul>
        <li> <b>Μόνο για συστήματα Unix/Linux:</b> Αφού ολοκληρωθεί η εγκατάσταση, 
          συνίσταται για λόγους ασφαλείας, να αλλάξετε τα δικαιώματα πρόσβασης 
          των αρχείων <code>/config/config.php</code> και <code>/install/index.php</code> 
          <p> και να επιτρέψτε μόνο ανάγνωση (τα permissions των παραπάνω θα πρέπει 
            να είναι 444) </p>
          <pre>chmod 444 /config/config.php /install/index.php</pre>
        </li>
      </ul>
    </div>
  </li>
</ul>
  </body>
</html>
