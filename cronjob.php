<?php
//echo "start debuging<br/>";


$wpdirectory=substr($_SERVER['SCRIPT_NAME'],0,strpos($_SERVER['SCRIPT_NAME'],'wp-content')-1);
	include ("$_SERVER[DOCUMENT_ROOT]$wpdirectory/wp-config.php");
	//bedzie ustwiona opcja "sprawdzaj ile zostalo postow" - zalaczana kiedy ladowane sa nowe posty
	//a wylaczana kiedy wysle sie maila ze koncza sie posty

$posttable=$table_prefix.'posts';
$opttable=$table_prefix.'options';

$connection=mysql_connect(DB_HOST,DB_USER,DB_PASSWORD)or die ("Couldn't connect to database");  
mysql_select_db(DB_NAME)or die ("Couldn't choose the database");

//post entries scheduled as drafts
//$result=mysql_query("select ID, post_content from $posttable where post_date < NOW() and post_status='draft'");
//while ($publish=mysql_fetch_assoc($result))
//{
//	mysql_query("update $posttable set post_status='publish' where ID='$publish[ID]'");
//	pingback($publish[content],$publish[ID]);
//	$wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE post_id = '$publish[ID]'");
//}

$result=mysql_query("select option_value from $opttable where option_name = 'kev_cron_job'");

$opt=mysql_fetch_assoc($result);
$cron=$opt['option_value'];
if ($cron) {//only if cron job is enabled
//echo "Cron is enable";
$result=mysql_query("select option_value from $opttable where option_name = 'kev_postsleft'");
$opt=mysql_fetch_assoc($result);
$pleft=$opt['option_value'];
if (!is_numeric($pleft))
	exit;
//echo "pleft is valid number";
$result=mysql_query("select option_value from $opttable where option_name = 'kev_mail'");
$opt=mysql_fetch_assoc($result);
$mail=$opt['option_value'];
$result=mysql_query("select ID from $posttable where  post_date > NOW()  and post_status='future' ");
if (mysql_num_rows($result) <= $pleft)
{
    
    //echo "mailing";
    	$result=mysql_query("select post_date from  $posttable where post_status='future' order by post_date desc limit 1");
	$date=mysql_fetch_assoc($result);
	if (strstr($_SERVER[SERVER_NAME],'www.'))
	{
		$server=substr($_SERVER[SERVER_NAME],4);
	}
	else
	{
		$server=$_SERVER[SERVER_NAME];
	}
//	echo $_SERVER[SERVER_NAME];
	$content="There are $pleft (or less now) posts to publish on $_SERVER[SERVER_NAME]. Last post will be published on $date[post_date]";
	mail($mail,'Posts finishing',$content,"From: webmaster@$server", "-fwebmaster@$server");
	mysql_query("update $opttable set option_value='0' where option_name = 'kev_cron_job'");//disable cron job until articles uploaded
}
}
mysql_close($connection);
?>
