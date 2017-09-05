<?PHP
//http://sourceforge.net/projects/phpmysqlezedit
//
//published by williamconley of poundteam via sourceforge.net
//
//
//license: http://creativecommons.org/licenses/by-sa/3.0/
//
//credit: developed from a code sample published by Sergey Skudaev at http://www.configure-all.com/display_any_table.php
//        Sergey's code sample was in mysql, not mysqli, and was only for viewing based on a given query hard-coded into the file.
//        But it was a great start. I couldn't find anything published that would do the rest, so here it is.
//
/* Purpose: php/MySQL easy data editor. A single php page or with a single config file for security
 * to make it easy and safe for non programmers/web designers/mysql gurus to add, edit and delete records.
 * No need to create a new database interface for every table. Just add user/pwd/host! */

/* NOTES:
 * 1) Will not allow editing or deleting records if there is no primary key.
 * 2) Does not "page through" records or allow sorting or filtering of records.
*/

/* REQUIREMENTS FOR INSTALLATION / OPERATION
 *
 * 1) Place this file where it can be accessed by a web browser on an apache/php/mysql server
 * 2) In "Preferences" Below fill in the details to connect to the table (or see readme.txt to use an external file for credentials)
 *
 */

### PREFERENCES ###
//Get preferences before we begin
$script_name=$_SERVER["SCRIPT_NAME"];
$raw_file=pathinfo($script_name);
$base_file=$raw_file['filename'];
session_start();
if ($test1=file_exists("/etc/$script_name")) include("/etc/$script_name");
if (!$preferences_loaded) {
    //Preferences and configuration settings can be loaded from a file with the same name as this one (including the .php) in /etc/$script_name
    //OR you can change the preferences below, but that will leave a security hole if this file is exposed.
    //Sample content for that file is included in the readme.txt file

    //Access code to use page:
    //* Modify XXXX to an access code and include in URL, phpmysqlezedit.php?access=XXXX or you will "bounce" to www.google.com!
    $access='1234';
    $preferences_loaded='1';
    // Database Access Credentials
    $hostname = "localhost";
    $dbuser = "root";
    $dbpassword = "Las1";
    $dbname = "las";
    $table = "employee";
//	$starting = "1";
    $limit = "50";
    $title = "Php-MySql";
    $greeting = "Welcome to PHP-MYSQL Interface";
    $can_add = '1';
    $can_mod = '1';
    $can_del = '1';
    $clean_add=TRUE;
    $clean_mod=TRUE;
    // This can be set to your company home page or a security page.
    $url = 'http://www.google.com';
    ////Options: Uncomment to activate:
    ////lock to a specific table:
    //$tablelock = true;
    ////run a script after specific activities:
    //$addscript='/usr/share/poundteam/goodguysstartup.php';
    //$editscript='/usr/share/poundteam/goodguysstartup.php';
    //$deletescript='/usr/share/poundteam/goodguysstartup.php';
}
//Debug modes (handy for troubleshooting)
if ( isset($_REQUEST['debug'])) { $debug=$_REQUEST['debug']; }
if ( isset($_REQUEST['debugphpmysqlezedit'])) { $debug=$_REQUEST['debugphpmysqlezedit']; }

// if($_GET['access']==$access) {
    $_SESSION['has_access']=true;
// }
// if($_SESSION['has_access']==true) {
    $has_access = '1'; 
// }
?>
<html>
    <head>
        <title>
            <?PHP echo $title; ?>
        </title>
        <link rel="stylesheet" type="text/css" href="<?PHP echo $base_file; ?>.css">
    </head><body><?PHP
echo "<h2>$greeting</h2>";
if($has_access == '1') {
    $DB1 = new mysqli($hostname,  $dbuser, $dbpassword, $dbname);
    if ($DB1->connect_errno) {
        printf("Connect failed: %s\n", $DB1->connect_error);
        exit();
    }
    // Allows URL or POST entries to override the stated defaults above or in the configuration file:
    $ignore_starting==FALSE;
    if(isset($_REQUEST['table'])) {
        if($_SESSION['table']<> $_REQUEST['table']){
            $ignore_starting=TRUE;
        }
        $_SESSION['table']=$_REQUEST['table'];
    } else {
        $_SESSION['table']=$table;
    }
    if(isset($_REQUEST['limit'])) {
        $_SESSION['limit']=$_REQUEST['limit'];
    } else {
        $_SESSION['limit']=$limit;
    }
    if(isset($_SESSION['starting']) && $_SESSION['starting']>0){
        $starting=$_SESSION['starting'];
    }
    if(isset($_REQUEST['starting']) && $_REQUEST['starting']>0) {
        $_SESSION['starting']=$_REQUEST['starting'];
        $starting=$_REQUEST['starting'];
    }
    if( (isset($_REQUEST['starting']) && $_REQUEST['starting']<=0) || $ignore_starting) {
        unset($_SESSION['starting']);
        unset($starting);
    }
    if(!$tablelock==TRUE) {
        $Query = "SELECT distinct table_name FROM information_schema.columns WHERE table_schema = '$dbname'";
        $table_options='';
        if ($Result1 = $DB1->query($Query)) {
            while($Record1 = $Result1->fetch_assoc()) {
                    $table_options .= "<option>{$Record1['table_name']}</option>\n";
            }
        }
    }
    $Query = "SELECT distinct column_name, column_key FROM information_schema.columns WHERE table_schema = '$dbname' and table_name='{$_SESSION['table']}'";
    $column_options='';
    $Result1 = $DB1->query($Query);
    if ($Result1->num_rows > 0) {
        $i=0;
        while($Record1 = $Result1->fetch_assoc()) {
            if($i==0) {
                $i=1;
                //Used for "order by"
                $column=$Record1['column_name'];
                //Used for "Delete X and Modify Y" URL Generators
                $first_column=$Record1['column_name'];
                //Used for permission to delete or modify (only if 1st column is primary key or unique)
                if($Record1['column_key']=='PRI') $can_delete=true;
            }
            $column_options .= "<option>{$Record1['column_name']}</option>\n";
        }
    } else {
        echo "Query: $Query<br/>No Such Table!";
        exit();
    }
//Delete a record action set:
    if($_GET['action']=='del' && $_GET['conf']=='true' ) {
        $Query = "delete from {$_SESSION['table']} where {$_GET['col']}='{$_GET['rec']}' limit 1";
        $Result1 = $DB1->query($Query);
        if(strlen($deletescript)>'4'){
            include($deletescript);
        }
    }
    if($_GET['action']=='del') {
        if($_GET['conf']<>'true') {
            ?>
<script type="text/javascript">
    var conf=confirm("Are you sure you want to delete this record?")
    if (conf)
        window.location="<?PHP echo curPageURL(); ?>&conf=true"
</script>
            <?PHP
        }
    }


    $DB1=mysqli_connect($hostname, $dbuser, $dbpassword)
            or die("Unable to connect to the server!");

    mysqli_select_db($DB1,$dbname)
            or die("Unable to connect to the database");

    $fields_array=array();
    $num_fields=0;
    $num_row=0;

    // Build Search String
    if (isset($_POST['search']) || !empty($_POST['search'])) {
        $search = searchString($_POST['search']);
    }    else {
        $search = '';
    }
    // Basic construct to "pull" the data using supplied variables (parsed below to "extract" the table name from an older version!
    if(isset($campaign)){
        if(strlen($search)>0){
            $campaign_sql_string="and campaign_id='$campaign'";
        } else {
            $campaign_sql_string="where campaign_id='$campaign'";
        }
    } else {
        $campaign_sql_string="";
    }
    if($starting > 0 ){
        $starting2=$starting-1;
        $starting_string="$starting2,";
    }
    $sql_count="select count(*) as count from {$_SESSION['table']} $search $campaign_sql_string";
    $sql="select * from {$_SESSION['table']} $search $campaign_sql_string order by $column desc limit $starting_string {$_SESSION['limit']}";

    // find position of "FROM" in query
    $fpos=strpos($sql, 'from');

    // get string starting from the first word after "FROM"
    $strfrom=substr($sql, $fpos+5, 50);

    // Find position of the first space after the first word in the string
    $Opos=strpos($strfrom,' ');

    // Get table name. If query pull data from more then one table only first table name will be read.
    $table=substr($strfrom, 0,$Opos);
// echo $sql_count;
    if($result=mysqli_query($DB1,$sql_count)) {
        if($row=mysqli_fetch_row($result)) {
            $total_records=$row[0];
        } else {
            $total_records=0;
        }
    } else {
        $total_records=0;
    }
// echo "total records=" . $total_records;
    print('<html>');
    print('<head><title>');
    print('View Table: '.$table.'</title>');
    print('<link rel="stylesheet" href="style.css">');

    print("</head>");
    print("<body>\n");
    if(!$clean_add || !$_REQUEST['action']=='add'){
    ?>
<br>
<FORM method=post action="<?PHP echo $_SERVER['PHP_SELF']; ?>" >

<?PHP if (!$tablelock==TRUE) {
    //Only show the following if $tablelock is NOT set
    ?>
    Table to View: <select name="table"><option selected><?PHP echo $_SESSION['table']; ?></option>
            <?PHP echo $table_options; ?></select>
<?PHP }
    //Then show the rest whether $tablelock is set or not
?>
    <INPUT type=submit name=submitphpmysqlezedit value="Show" />
    <br>
    Starting Record: <INPUT type=text name='starting' size="3" value="<?PHP echo $starting; ?>" /> 
    Records per page: <INPUT type=text name='limit' size="3" value="<?PHP echo $_SESSION['limit']; ?>" /> (<?PHP echo $total_records; ?> Total Records)
    <br>
    Search for Text or Number: <INPUT type=text name='search' size="30" value="<?PHP echo $_POST['search']; ?>" />
</FORM>
<div style="text-align: center"><FORM method=post action="<?php echo $_SERVER['PHP_SELF']; ?>" style='display: inline;'>
    <INPUT type=submit name=submitphpmysqlezedit value="First" />
    <INPUT type='hidden' name='starting'  value="0" /> 
    <INPUT type='hidden' name='limit' value="<?PHP echo $_SESSION['limit']; ?>" />
    <INPUT type='hidden' name='table' value="<?PHP echo $_SESSION['table']; ?>" />
    <INPUT type='hidden' name='search' value="<?PHP echo $_POST['search']; ?>" />
</FORM>
<FORM method=post action="<?php echo $_SERVER['PHP_SELF']; ?>" style='display: inline;'>
    <INPUT type=submit name=submitphpmysqlezedit value="Previous" />
    <INPUT type='hidden' name='starting'  value="<?PHP echo $starting-$_SESSION['limit']; ?>" /> 
    <INPUT type='hidden' name='limit' value="<?PHP echo $_SESSION['limit']; ?>" />
    <INPUT type='hidden' name='table' value="<?PHP echo $_SESSION['table']; ?>" />
    <INPUT type='hidden' name='search' value="<?PHP echo $_POST['search']; ?>" />
</FORM>
<FORM method=post action="<?php echo $_SERVER['PHP_SELF']; ?>" style='display: inline;'>
    <INPUT type=submit name=submitphpmysqlezedit value="Next" />
    <INPUT type='hidden' name='starting'  value="<?PHP echo $starting+$_SESSION['limit']; ?>" /> 
    <INPUT type='hidden' name='limit' value="<?PHP echo $_SESSION['limit']; ?>" />
    <INPUT type='hidden' name='table' value="<?PHP echo $_SESSION['table']; ?>" />
    <INPUT type='hidden' name='search' value="<?PHP echo $_POST['search']; ?>" />
</FORM>
<FORM method=post action="<?php echo $_SERVER['PHP_SELF']; ?>" style='display: inline;'>
    <INPUT type=submit name=submitphpmysqlezedit value="Last" />
    <INPUT type='hidden' name='starting'  value="<?PHP echo 1+$total_records-$_SESSION['limit']; ?>" /> 
    <INPUT type='hidden' name='limit' value="<?PHP echo $_SESSION['limit']; ?>" />
    <INPUT type='hidden' name='table' value="<?PHP echo $_SESSION['table']; ?>" />
    <INPUT type='hidden' name='search' value="<?PHP echo $_POST['search']; ?>" />
</FORM>
</div>
<br>
    <?PHP
    }
    if($_GET['action']=='add') {
        //Gather data to show Add Record section
        $Query = "SELECT distinct column_name, column_key, extra, column_default, column_comment, column_type FROM information_schema.columns WHERE table_schema = '$dbname' and table_name='{$_GET['table']}'";
        if ($Result1 = $DB1->query($Query)) {
            print("<FORM method=post action='{$_SERVER['PHP_SELF']}?action=savenew&table={$_GET['table']}&limit={$_GET['limit']}' ><table>");
            while($Record1 = $Result1->fetch_array()) {
                if(strpos($Record1['column_comment'], '{top=') !== FALSE){
                    $top_comment = array();
                    $t = preg_match('/{top=(.*?)}/s', $Record1['column_comment'], $top_comment);
                    $top_comment_text = "<tr><td colspan='2'>{$top_comment[1]}</td></tr>\n";
                } else {
                    $top_comment_text = '';
                }
                if(strpos($Record1['column_comment'], '{display=') !== FALSE){
                    $display = array();
                    $t = preg_match('/{display=(.*?)}/s', $Record1['column_comment'], $display);
                    $field_display_name = $display[1];
                } else {
                    $field_display_name = $Record1['column_name'];
                }
                if(strpos($Record1['column_comment'], '[use_request_value]') !== FALSE) {
                    if(isset($_REQUEST["{$Record1['column_name']}"])){
                        $column_default=$_REQUEST["{$Record1['column_name']}"];
                    } else {
                        $column_default=$Record1['column_default'];
                    }
                } else {
                    $column_default=$Record1['column_default'];
                }
                if($Record1['extra']=='auto_increment'||$column_default=='CURRENT_TIMESTAMP') {
                    $fielddata='AUTO';
                } elseif(strpos($Record1['column_comment'], '[disable]') !== FALSE) {
                    $fielddata="<INPUT type=hidden name={$Record1['column_name']} value='$column_default' />$column_default";
                } else {
                    if(substr($Record1['column_type'], 0,4)=='enum'){
                        $dropdown_string=enum_to_dropdown($Record1['column_type'],$column_default);
                        $fielddata="\n<select name={$Record1['column_name']}>$dropdown_string</select>\n";
                    } elseif(strpos($Record1['column_comment'], '{checkboxes=') !== FALSE){
                        $checkboxes_from_comment=checkboxes_from_comment($Record1['column_name'],$Record1['column_comment'],$column_default);
                        $fielddata="\n$checkboxes_from_comment";
                    } elseif(substr($Record1['column_type'], 0,4)=='text'){
                        $column_default=htmlentities($column_default,ENT_QUOTES);
                        $fielddata="\n<textarea name={$Record1['column_name']} rows='3' >$column_default</textarea>\n";
                    } else {
                        $fielddata="<INPUT type=text name={$Record1['column_name']} value='$column_default' />";
                    }
                }
                $column_comment = preg_replace('/\[.*?\]/', '', $Record1['column_comment']); // remove [disable] from comment
                $column_comment = preg_replace('/{.*?}/', '', $column_comment); // remove {top=...} from side comment
                if(strlen($column_comment)>0){
                    $column_comment="<td>$column_comment</td>";
                } else {
                    $column_comment="";
                }
                print("$top_comment_text<tr><td><b>$field_display_name</b></td><td>$fielddata</td>$column_comment</tr>\n");
            }
            print("</table>\n<INPUT type=submit name=submitphpmysqlezedit value='Save New Record' />\n</FORM>");
        }
    }

    if($_GET['action']=='mod') {
        //Begin Modify Record action
        //First Gather column comments to use as hints on fields
        $Query = "SELECT distinct column_name, column_key, extra, column_default, column_comment FROM information_schema.columns WHERE table_schema = '$dbname' and table_name='{$_GET['table']}'";
        if ($Result1 = $DB1->query($Query)) {
            $i=0;
            while($Record1 = $Result1->fetch_array()) {
                $field_comments[$i]=$Record1['column_comment'];
                $i++;
            }
        }
        //Gather data to show Modify Record section
        $Query = "SELECT * FROM {$_GET['table']} WHERE {$_GET['col']} = '{$_GET['rec']}' limit 1";
        if ($Result1 = $DB1->query($Query)) {
            print("<FORM method=post action='{$_SERVER['PHP_SELF']}?action=savechanges&table={$_GET['table']}&col={$_GET['col']}&rec={$_GET['rec']}&limit={$_GET['limit']}' ><table>");
            $Record1 = $Result1->fetch_array();
            $i=0;
            while($i<$Result1->field_count) {
                $finfo = $Result1->fetch_field_direct($i);
                $clean_data=htmlentities($Record1[$i],ENT_QUOTES);
                if($finfo->flags=='49667') {
                    $fielddata=$clean_data;
                } else {
                    if(strlen($clean_data)>100){
                        $fielddata="\n<textarea name=$finfo->name rows=3>$clean_data</textarea>\n";
                    } else {
                        $fielddata="<INPUT type=text name=$finfo->name size=$finfo->max_length value='$clean_data' />";
                    }
                }
                print("<tr><td><b>$finfo->name</b></td><td>$fielddata</td><td>$field_comments[$i]</td></tr>");
                $i++;
            }
           if($debug=='database'){
               print("\n<INPUT type=hidden name=debugphpmysqlezedit value='database' />\n");
           }
            print("<tr><td><b>Editing Record:</b></td><td>{$_GET['rec']}</td></tr></table>\n<INPUT type=submit name=submitphpmysqlezedit value='Save Changes' />\n</FORM>");
        }
    }

    if($_GET['action']=='savechanges') {
        //Announce Saved (although if there is an error later, this will not be true, we want to give an indication to the user that their prior button press had an effect ...) ?>
        <h3 style=" color: green">Record Changed</h3><p style=" color: green">If you have changed your mind, you may press REVERT below to revert to the prior version of this record.<br/>The PRIOR data is displayed <i>above</i> the Revert button.<br/>The NEW data is displayed <i>below</i> the Revert button (with the rest of the records).</p>
<?PHP   //Begin Save Changes Section
        //BUT FIRST duplicate Modify Record action for all except name of button (we'll turn this into a recyclable function later)
        //This allows for "REVERT" in case there has been an Error.
        //First Gather column comments to use as hints on fields
        $Query = "SELECT distinct column_name, column_key, extra, column_default, column_comment FROM information_schema.columns WHERE table_schema = '$dbname' and table_name='{$_GET['table']}'";
        if ($Result1 = $DB1->query($Query)) {
            $i=0;
            while($Record1 = $Result1->fetch_array()) {
                $field_comments[$i]=$Record1['column_comment'];
                $i++;
            }
        }
        //Gather data to show Modify Record section
        $Query = "SELECT * FROM {$_GET['table']} WHERE {$_GET['col']} = '{$_GET['rec']}' limit 1";
        if ($Result1 = $DB1->query($Query)) {
            print("<FORM method=post action='{$_SERVER['PHP_SELF']}?action=savechanges&table={$_GET['table']}&col={$_GET['col']}&rec={$_GET['rec']}&limit={$_GET['limit']}' >\n<table>\n");
            $Record1 = $Result1->fetch_array();
            $i=0;
            while($i<$Result1->field_count) {
                $finfo = $Result1->fetch_field_direct($i);
                $clean_data=htmlentities($Record1[$i],ENT_QUOTES);
                if($finfo->flags=='49667') {
                    $fielddata=$clean_data;
                } else {
                    if(strlen($clean_data)>100){
                        $fielddata="\n<textarea name=$finfo->name rows=3>$clean_data</textarea>\n";
                    } else {
                        $fielddata="\n<INPUT type=text name=$finfo->name size=$finfo->max_length value='$clean_data' />";
                    }
                }
                print("\n<tr><td><b>$finfo->name</b></td><td>$fielddata</td><td>$field_comments[$i]</td></tr>");
                $i++;
            }
            print("\n<tr><td><b>Editing Record:</b></td><td>{$_GET['rec']}</td></tr></table>\n<INPUT type=submit name=submitphpmysqlezedit value='Revert' />\n</FORM>");
        }
        $updateString='';
        $i=0;
        foreach ($_POST as $next=>$value) {
            if ( ($i<>0) && ($next<>"submitphpmysqlezedit") && ($next <> "debugphpmysqlezedit") ) {
                $updateString.=", ";
            }

            if ( ($next <> "submitphpmysqlezedit") && ($next <> "debugphpmysqlezedit") ) $updateString.=" $next='".mysqli_real_escape_string($DB1,$value)."'";
            $i++;
        }
        $Query = "update {$_GET['table']} set $updateString WHERE {$_GET['col']} = '{$_GET['rec']}' limit 1";
        if($debug=='database'){
            echo "<br/>\nLine".__LINE__." Query: $Query<br/>\n";
        }
        $Result1 = $DB1->query($Query);
        if($debug=='database'){
            echo "<br/>\nLine".__LINE__."DBerror=$DB1->error\nDBerrno=$DB1->errno\n";
        }
        if(strlen($editscript)>'4'){
            include($editscript);
        }
    }
    if($_GET['action']=='savenew') {
        $insertfields='';
        $insertvalues="";
        $i=0;
        foreach ($_POST as $next=>$value) {
            if(is_array($value)){
                $value=implode(' | ',$value);
            }
            if ($i<>0 && $next <>"submitphpmysqlezedit") {
                $insertfields.=",";
                $insertvalues.=",";
            }
            if ($next <>"submitphpmysqlezedit") {
                $insertfields.="$next";

                $insertvalues.="'".mysqli_real_escape_string($DB1,$value)."'";
            }
            $i++;
        }
        $Query = "insert into {$_GET['table']} ($insertfields) VALUES ($insertvalues)";
        $Result1 = $DB1->query($Query);
        $InsertID=$DB1->insert_id;
        if(isset($addscript)) {
            echo"<div class=\"addscript\">";
            include($addscript);
            echo "</div>";
        } else {
            echo "<div class=\"recordadd\">Record $InsertID Added.</div><br>\n";
        }
    }
    if(!$clean_add || !$_REQUEST['action']=='add'){
        // Get result from query

        if($result=mysqli_query($DB1,$sql)) {
            //Get number of fields in query
            $num_fields=mysqli_num_fields($result);

            # get column metadata
            $i = 0;

            //Set table width 15% for each column
            $width=15 * $num_fields;
            if ($can_add=='1') {
                $addstring="<a href='{$_SERVER['PHP_SELF']}?action=add&table=$table&limit=$limit'>Add</a>";
            }
            print('<br>'."\n".'<table width='.$width.'% align="center">'."\n");
            print("   <tr><th colspan=$num_fields>View Table $table&nbsp;$addstring</th></tr>\n   <tr align=left><th><b>Del</b></th><th><b>Mod</b></th>\n");

            while ($i < $num_fields) {

                //Get fields (columns) names
                $meta = mysqli_fetch_field($result);

                    $fields_array[]=$meta->name;

                //Display column headers in upper case
                //print('      <th><b>'.strtoupper($fields_array[$i]).'</b></th>'."\n");
				print('      <th><b>'.$fields_array[$i].'</b></th>'."\n");
                $i=$i+1;
            }

            print('   </tr>');


            //Get values for each row and column
            while($row=mysqli_fetch_row($result)) {
                if ($can_delete) {
                    if ($can_del == '1') {
                        $delstring="<a href='{$_SERVER['PHP_SELF']}?action=del&table=$table&col=$first_column&rec=$row[0]&limit=$limit'>X</a>";
                    }
                    if ($can_mod == '1') {
                        $modstring="<a href='{$_SERVER['PHP_SELF']}?action=mod&table=$table&col=$first_column&rec=$row[0]&limit=$limit'>O</a>";
                    }
                    print("   <tr><td>$delstring</td><td>$modstring</td>");
                } else {
                    print("   <tr><td></td><td></td>");
                }
                for($i=0; $i<$num_fields; $i++) {
                    //Display values for each row and column
                    if(strlen($row[$i])>100) { $display_string=substr($row[$i],0,100).' ...'; } else { $display_string=$row[$i]; }
                    print('      <td>'.htmlentities($display_string,ENT_QUOTES).'</td>'."\n");

                }

                print('   </tr>'."\n");
            }
            print('</table>');
        }
        if($debug=='phpinfo'){ phpinfo(); }
        ?>
        <div style="text-align: center">
            <FORM method=post action="<?php echo $_SERVER['PHP_SELF']; ?>" style='display: inline;'>
                <INPUT type=submit name=submitphpmysqlezedit value="First" />
                <INPUT type='hidden' name='starting'  value="0" /> 
                <INPUT type='hidden' name='table' value="<?PHP echo $_SESSION['table']; ?>" />
                <INPUT type='hidden' name='limit' value="<?PHP echo $_SESSION['limit']; ?>" />
                <INPUT type='hidden' name='search' value="<?PHP echo $_POST['search']; ?>" />
            </FORM>
            <FORM method=post action="<?php echo $_SERVER['PHP_SELF']; ?>" style='display: inline;'>
                <INPUT type=submit name=submitphpmysqlezedit value="Previous" />
                <INPUT type='hidden' name='starting'  value="<?PHP echo $starting-$_SESSION['limit']; ?>" /> 
                <INPUT type='hidden' name='table' value="<?PHP echo $_SESSION['table']; ?>" />
                <INPUT type='hidden' name='limit' value="<?PHP echo $_SESSION['limit']; ?>" />
                <INPUT type='hidden' name='search' value="<?PHP echo $_POST['search']; ?>" />
            </FORM>
            <FORM method=post action="<?php echo $_SERVER['PHP_SELF']; ?>" style='display: inline;'>
                <INPUT type=submit name=submitphpmysqlezedit value="Next" />
                <INPUT type='hidden' name='starting'  value="<?PHP echo $starting+$_SESSION['limit']; ?>" /> 
                <INPUT type='hidden' name='table' value="<?PHP echo $_SESSION['table']; ?>" />
                <INPUT type='hidden' name='limit' value="<?PHP echo $_SESSION['limit']; ?>" />
                <INPUT type='hidden' name='search' value="<?PHP echo $_POST['search']; ?>" />
            </FORM>
            <FORM method=post action="<?php echo $_SERVER['PHP_SELF']; ?>" style='display: inline;'>
                <INPUT type=submit name=submitphpmysqlezedit value="Last" />
                <INPUT type='hidden' name='starting'  value="<?PHP echo 1+$total_records-$_SESSION['limit']; ?>" /> 
                <INPUT type='hidden' name='table' value="<?PHP echo $_SESSION['table']; ?>" />
                <INPUT type='hidden' name='limit' value="<?PHP echo $_SESSION['limit']; ?>" />
                <INPUT type='hidden' name='search' value="<?PHP echo $_POST['search']; ?>" />
            </FORM>
        </div>
        <?PHP
    }
    ?>
    </body>
</html>

    <?PHP
    } else {
    ?>
<html>
    <head><title>Bouncing for Login ...</title>
        <meta http-equiv="refresh" content="0; URL=<?php echo $url; ?>">
    </head>
    <body>You are bouncing to
        <a href="<?php echo $url; ?>"><?php echo $url; ?></a> for login, then try again.
    </body>
</html><?PHP
}
function curPageURL() {
    $pageURL = 'http';
    if ($_SERVER["HTTPS"] == "on") {
        $pageURL .= "s";
    }
    $pageURL .= "://";
    if ($_SERVER["SERVER_PORT"] != "80") {
        $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
    } else {
        $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
    }
    return $pageURL;
}
function searchString($search) {
    global $dbname;
    global $DB1;
    $table=$_SESSION['table'];
    $Query = "SELECT column_name FROM information_schema.columns WHERE table_schema='$dbname' AND table_name = '$table'";
    $Result1 = $DB1->query($Query);
    $string='';
    while($Record1 = $Result1->fetch_assoc()) {
        if( !empty( $string )) $string .= " OR "; else $string .= " WHERE (";
        $string .= $Record1['column_name']." LIKE '%$search%'";
    }
    if(!empty($string)){ $string.=")"; }
    return $string;
}
function enum_to_dropdown($column_type,$present_value){
    $column_type=  substr($column_type, 5,-1);
    $column_type=explode(',',$column_type);
    $return_string="$selected\n";
    foreach($column_type as $data){
        $data=substr($data,1,-1);
        if("$data"=="$present_value"){
            $selected='selected';
        } else {
            $selected='';
        }
        $return_string.="   <option $selected>$data</option>\n";
    }
    return $return_string;
}
function checkboxes_from_comment($column_name,$column_comment,$column_default){
    if(!is_array($column_default)){
        $column_default=array($column_default);
    }
    $checkbox_temp = array();
    $t = preg_match('/{checkboxes=(.*?)}/s', $column_comment, $checkbox_temp);
    $checkbox_text = $checkbox_temp[1];
    $checkbox_text=  substr($checkbox_text, 0,-1);
    $checkbox_array=explode(',',$checkbox_text);
    foreach($checkbox_array as $data){
        $data=substr($data,1,-1);
        if(in_array($data, $column_default)){
            $checked='checked';
        } else {
            $checked='';
        }
        $return_string.="   <label><input type='checkbox' name='{$column_name}[]' value='$data' $checked>$data</label> \n";
    }
    return $return_string;
}
if($debug){
    echo "<pre>";
    echo "POST ";
    print_r($_POST);
    echo "GET ";
    print_r($_GET);
    echo "REQUEST ";
    print_r($_REQUEST);
    echo "SESSION ";
    print_r($_SESSION);
    echo "Query: ";
    print_r($sql);
}
?>
