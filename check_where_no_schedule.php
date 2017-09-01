<?php

require_once 'vendor/autoload.php';
include 'database.php';


// SELECT
// schedules.ID as ScheduleID,
// schedules.Name as ScheduleName,
// schedules.ClientID as ClientID,
// schedules.SoftwareID as SoftwareID,
// mailstatus.MailID as MailID,
// mailstatus.Error,
// mails.Subject,
// mails.ReceivedDateTime
// FROM schedules
// LEFT JOIN mailstatus ON schedules.ID = mailstatus.ScheduleID
// LEFT JOIN mails ON mails.ID = mailstatus.MailID
// AND DATE_FORMAT(mails.ReceivedDateTime, "%Y%m%d") = "20170828"
// ORDER BY `schedules`.`ClientID` ASC

$data = $database->select("mailstatus", [
	"[>]mails" => ["mailstatus.MailID" => "ID"]
],[
	"mailstatus.MailID",
	"mailstatus.Error",
	"mails.Subject",
	"mails.Name",
	"mails.Email",
	"mails.ReceivedDateTime"
],
[
	"mailstatus.ScheduleID" => -1,
]);

echo "Mails: " . count($data);
foreach($data as $d){
	echo "<div>{$d["Email"]}({$d["Name"]}): <br>{$d["Subject"]}<br><br><div>";
}
