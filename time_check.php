<?php

$rustart = getrusage();

ini_set('display_errors', 'on');
require_once 'vendor/autoload.php';
include 'database.php';

$DAY_TO_GO_BACK = -7;
$FETCH_RANGE = [-7,0]; // get mailstati for [0] to [1] days

// The Manager helps to order the mails and to find stuff within it
$manager = new \MailManagement\Manager();

//SELECT * FROM `mails` WHERE ReceivedDateTime LIKE '2017-08-24%' ORDER BY `ID`  ASC

// Do it for last 7 days
$mailIDs = $database->select("mails", "ID", [
	"ReceivedDateTime[<>]" => [date("Y-m-d", strtotime("$FETCH_RANGE[0] days")), date("Y-m-d", strtotime($FETCH_RANGE[1]+1 . " days"))]
]);



// foreach ($mailIDs as $mailID) {
//   $mail = new \MailManagement\Mail();
//   $mail->setByID($mailID);
//
//   // echo $mail->Email . "\t" . $mail->Name . "\n";
//   echo $mail->Subject . "\n\n";
//   // echo $mail->Text . "\n\n";
// }

print(min($mailIDs) . " " . max($mailIDs));

$mailstati = $database->select(
"mailstatus",
[
  "MailID",
  "ScheduleID",
  "Error",
  "Description"
],
[
  "AND" => [
    // "MailID[>=]" => min($mailIDs),
    // "MailID[<=]" => max($mailIDs),
    "MailID[<>]" => [min($mailIDs), max($mailIDs)],
    "ScheduleID[!]" => -1
  ]
]
);
$scheduleList = [];
$mailsBySchedule = [];
$mailList = [];
$clientList = [];
$caretakerList = [];



$caretakerData = $database->select("Users", ["ID", "Name"]);
foreach ( $caretakerData as $data ) {
  $caretakerList[$data["ID"]] = $data;
}

$scheduleData = $database->select("schedules", ["ID", "Name", "ClientID", "SearchFor", "SoftwareID", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"]);
foreach ( $scheduleData as $data ) {
  $scheduleList[$data["ID"]] = $data;
}

$clientData = $database->select("clients", ["ID", "Name", "Caretaker"]);
foreach ( $clientData as $data ) {
  $clientList[$data["ID"]] = $data;
}

foreach ($mailstati as $mailstatus) {
  $mail = new \MailManagement\Mail();
  $mail->setByID($mailstatus["MailID"]);
  $mailList[$mailstatus["MailID"]] = $mail;
  $mailsBySchedule[$mailstatus["ScheduleID"]][substr($mail->ReceivedDateTime,8,2)] = $mail;
  // echo $mail->Email . "\t" . $mail->Name . "\n";
  // echo $mail->Subject . "\n\n";
  // echo $mail->Text . "\n\n";
}



// Shadow Protect: Date in Title  "dd.mm.yyyy" or "mm/dd/yyyy"
// Backuo Assist: Startzeit in Text "Startzeit: dd.mm.yyyy"


echo "<table>";
echo "<thead><tr>";
echo "<td>Schedule</td>";

// -8 to -1 to get last week from yesterday on
for ($i = $FETCH_RANGE[0]; $i <= $FETCH_RANGE[1]; $i++) {
			echo "<td>" . date("D d.m", strtotime($i . " days")) . "</td>";
}
echo "</tr></thead>";


$backupCounter = 0;
$backupsTotal = 0;
$totalBackupCounter = 0;
$totalBackupsTotal = 0;

$currentClient = 0;

foreach ( $scheduleList as $scheduleID => $schedule){
	if ( $schedule["ClientID"] != $currentClient){
		$currentClient =  $schedule["ClientID"];
		echo "<tr><td><b>{$clientList[$currentClient]["Name"]} ({$caretakerList[$clientList[$currentClient]["Caretaker"]]["Name"]})</b></td></tr>";
	}
  echo "<tr>";
  // foreach ($schedule as $mail) {
  //     echo "<td>" . substr($mail->ReceivedDateTime,8,2) . "</td>";
  // }
    echo "<td>" . $schedule["ID"] . ": " . $schedule["Name"] . ":</td>";
		// "add" the numbers from -8 to -1 to get last week from yesterday on
		$backupCounter = 0;
		$totalBackups = 0;
  for ($i = $FETCH_RANGE[0]; $i <= $FETCH_RANGE[1]; $i++) {
		$totalBackups += $schedule[date("l", strtotime($i . " days"))];

    $key = date("d", strtotime($i . " days"));
      // if (array_key_exists($key, $mailsBySchedule[$scheduleID])){
      if (array_key_exists($scheduleID, $mailsBySchedule)){
				if (array_key_exists($key, $mailsBySchedule[$scheduleID])){
        // echo "<td>" . substr($schedule[$key]->ReceivedDateTime,8,2) . "</td>";
				$backupCounter += 1;
				if ($mailsBySchedule[$scheduleID][$key]->Error) {
					// " . substr($mail[$key]->ReceivedDateTime,8,2) . "
					// Red if error + face
					echo "<td style='color: red;'>({$schedule[date("l", strtotime($i . " days"))]}). &#9888;</td>";
				} else {
					// if no error green and check mark
        	echo "<td style='color: green;'>({$schedule[date("l", strtotime($i . " days"))]}). &#10003;</td>";
				}
      } else {
				// If schedule has no entry for this day: show whether report was expected
				// (1) = There should habe been a mail
				// (0) = There was no mail and there shouldn't havve benn one => Everything is fine
        echo "<td style='color:orange'>({$schedule[date("l", strtotime($i . " days"))]})</td>";
      }
		} else {
			// there isn't even one entry -> even for backups once per week this shouldn't be the case
			echo "<td style='color:red'>({$schedule[date("l", strtotime($i . " days"))]})</td>";
		}
  }
	if ( $backupCounter == 0 ) {
		echo "<td style='color:red'>&#9888; {$backupCounter}/{$totalBackups} &#9888;</td>";
	} else {
		echo "<td>{$backupCounter}/{$totalBackups}</td>";
	}
	$totalBackupCounter += $backupCounter;
	$totalBackupsTotal += $totalBackups;
  echo "</tr>";
}

echo "</table>";
echo "<p>Backups: {$totalBackupCounter}/{$totalBackupsTotal}</p>";

// Script end
function rutime($ru, $rus, $index) {
    return ($ru["ru_$index.tv_sec"]*1000 + intval($ru["ru_$index.tv_usec"]/1000))
     -  ($rus["ru_$index.tv_sec"]*1000 + intval($rus["ru_$index.tv_usec"]/1000));
}

$ru = getrusage();
echo "This process used " . rutime($ru, $rustart, "utime") .
    " ms for its computations\n";
echo "It spent " . rutime($ru, $rustart, "stime") .
    " ms in system calls\n";
