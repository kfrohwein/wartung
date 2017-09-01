
<a href="mail_import.php">Import</a>
<a href="mail_check.php">Check</a>
<a href="time_check.php">Time Check</a>
<a href="time_check_join.php">Time Check Day</a>
<a href="check_where_no_schedule.php">No Schedule</a>

<hr>

Show content of mailstatus.

All mails with schedule ID not -1 are assigned to one schedule
Errors are currently:
  -1: Not checked because no schedule
   0: Everything is fine. The error condition was not fulfilled
   1: An Error was found based on the software's error criteria defined by a schedule

1) Show all mails without connection (-1). They went through the detection and
some rules might have to be modified. Maybe create rule to ignore specific mails.

2) Create view of backup history per schedule.


@todo:
Errors:
time_check: Mulitple mails per day possible. Currently only one will be shown
Mails might find multiple schedules but for now they will be assigned the first one found
Shadow Protect Report with 0 backups executed currently counts as successful. Should be own error level.
What will happen if all mails are deleted from the mailbox?
Schedule for "Festplattenintegritätsbericht"?:
  [Monitoring][nas-a-001] Monatlicher Festplattenintegritätsbericht zu NAS-A-002
