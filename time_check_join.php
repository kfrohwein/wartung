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

$data = $database->select("schedules", [
	"[>]mailstatus" => ["schedules.ID" => "ScheduleID"],
	"[>]mails" => ["mailstatus.MailID" => "ID"]
],[
	"schedules.ID",
	"schedules.Name",
	"schedules.ClientID",
	"schedules.SoftwareID",
	"mailstatus.MailID",
	"mailstatus.Error",
	"mails.Subject",
	"mails.ReceivedDateTime"
],
[
	"ReceivedDateTime[>]" => date("Y-m-d", strtotime("-1 days")),
	"ORDER" => ["schedules.ClientID" => "ASC"]
]);

echo "Mails: " . count($data);
foreach($data as $d){
	if($d["Error"]){
		echo "<div style='color: red;'>{$d["Subject"]}</div>";;
	} else {
	// echo "<div>{$d["ID"]}{$d["Subject"]}</div>";
}
}
