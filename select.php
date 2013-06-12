<?php

/**************************************************************
"Learning with Texts" (LWT) is released into the Public Domain.
This applies worldwide.
In case this is not legally possible, any entity is granted the
right to use this work for any purpose, without any conditions, 
unless such conditions are required by law.

Developed by J. P. in 2011, 2012, 2013.
***************************************************************/

/**************************************************************
Call: select.php
Analyse DB tables and offer management functions
***************************************************************/

include "settings.inc.php";
include "connect.inc.php";
include "utilities.inc.php";

$message = "";

if (isset($_REQUEST['delpref'])) {
	if($_REQUEST['delpref'] !== '-') {
		$dummy = runsql('DROP TABLE ' . $_REQUEST['delpref'] . 'archivedtexts','');
		$dummy = runsql('DROP TABLE ' . $_REQUEST['delpref'] . 'archtexttags','');
		$dummy = runsql('DROP TABLE ' . $_REQUEST['delpref'] . 'languages','');
		$dummy = runsql('DROP TABLE ' . $_REQUEST['delpref'] . 'sentences','');
		$dummy = runsql('DROP TABLE ' . $_REQUEST['delpref'] . 'tags','');
		$dummy = runsql('DROP TABLE ' . $_REQUEST['delpref'] . 'tags2','');
		$dummy = runsql('DROP TABLE ' . $_REQUEST['delpref'] . 'textitems','');
		$dummy = runsql('DROP TABLE ' . $_REQUEST['delpref'] . 'texts','');
		$dummy = runsql('DROP TABLE ' . $_REQUEST['delpref'] . 'texttags','');
		$dummy = runsql('DROP TABLE ' . $_REQUEST['delpref'] . 'words','');
		$dummy = runsql('DROP TABLE ' . $_REQUEST['delpref'] . 'wordtags', '');
		$dummy = runsql('DROP TABLE ' . $_REQUEST['delpref'] . 'settings', '');
		$message = 'Table Set "' . $_REQUEST['delpref'] . '" deleted';
		if ($_REQUEST['delpref'] == $tbpref) {
			$tbpref = "";
			LWTTableSet ("current_table_prefix", $tbpref);
		}
	}
}

elseif (isset($_REQUEST['newpref'])) {
	$tbpref = $_REQUEST['newpref'];
	LWTTableSet ("current_table_prefix", $tbpref);
	header("Location: index.php");
	exit(); 
}

elseif (isset($_REQUEST['prefix'])) {
	if($_REQUEST['prefix'] !== '-') {
		$tbpref = $_REQUEST['prefix'];
		LWTTableSet ("current_table_prefix", $tbpref);
		header("Location: index.php");
		exit(); 
	}
}

pagestart('Select, Create or Delete a Table Set',true);
echo error_message_with_hide($message,0);

if ($fixed_tbpref) {

?>

<table class="tab1" cellspacing="0" cellpadding="5">
<tr>
<td class="td1">
	<p>These features are not currently not available.<br /><br />Reason:<br /><b>$tbpref</b> is set to a fixed value in <i>connect.inc.php</i>.<br />Please remove the definition<br /><span class="red"><b>$tbpref = '<?php echo $tbpref; ?>';</b></span></br />in <i>connect.inc.php</i> to make these features available.<br /> Then try again.</p>
	<p class="right">&nbsp;<br /><input type="button" value="&lt;&lt; Back" onclick="history.back();" /></p>
</td>
</tr>
</table>

<?php	

} else {

$prefix = array();
$res = mysql_query("SHOW TABLES LIKE " . convert_string_to_sqlsyntax_nonull('%settings'));
if ($res == FALSE) die("SHOW TABLES error");
while ($row = mysql_fetch_row($res)) 
	$prefix[] = substr($row[0], 0, -8);
mysql_free_result($res);

?>

<table class="tab1" cellspacing="0" cellpadding="5">

<tr>
<th class="th1 center">Select</th>
<td class="td1">
<form name="f1" class="inline" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<p>Table Set: <select name="prefix">
<option value="-" selected="selected">[Choose...]</option>
<?php
foreach ($prefix as $value) {
?>
<option value="<?php echo tohtml($value); ?>"><?php echo ($value == '' ? '* Default Table Set *' : tohtml($value)); ?></option>
<?php
}
?>
</select> 
</p>
<p class="right">&nbsp;<br /><input type="submit" name="op" value="Start LWT with selected Table Set" />
</p>
</form>
</td>
</tr>

<tr>
<th class="th1 center">Create</th>
<td class="td1">
<form name="f2" class="inline" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" onsubmit="return check_table_prefix(document.f2.newpref.value);">
<p>New Table Set: <input type="text" name="newpref" value="" maxlength="20" size="20" />
</p>
<p class="right">&nbsp;<br /><input type="submit" name="op" value="Create New Table Set &amp; Start LWT" />
</p>
</form>
</td>
</tr>

<tr>
<th class="th1 center">Delete</th>
<td class="td1">
<form name="f3" class="inline" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" onsubmit="if (document.f3.delpref.selectedIndex > 0) { return confirm('\n\n*** DELETING TABLE SET: ' + document.f3.delpref.options[document.f3.delpref.selectedIndex].text + ' ***\n\n*** ALL DATA IN THIS TABLE SET WILL BE LOST! ***\n\n*** ARE YOU SURE ?? ***'); } else { return true; }">
<p>Table Set: <select name="delpref">
<option value="-" selected="selected">[Choose...]</option>
<?php
foreach ($prefix as $value) {
	if ( $value != '') {
?>
<option value="<?php echo tohtml($value); ?>"><?php echo tohtml($value); ?></option>
<?php
	}
}
?>
</select>
<br />
(You cannot delete the Default Table Set.)
</p> 
<p class="right">&nbsp;<br /><span class="red2">YOU MAY LOSE DATA - BE CAREFUL: &nbsp; &nbsp; &nbsp;</span><input type="submit" name="op" value="DELETE Table Set" />
</p>
</form>
</td>
</tr>

</table>

<?php

}

pageend();

?>