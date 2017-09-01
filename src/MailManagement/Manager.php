<?php
namespace MailManagement;

// Takes a string and searches for it in an array
// SearchString: String contains OR and AND - Text is surounded by ""
// Infront of the text has to be a SearchArea. This shall be defined.
//  $ALLOWED_SEARCH_AREAS = ["Subject","Text","Email","Name"];
// e.g.: Name:"Kunde1" OR Subject:"DerKunde1"

// Array haystack has a few keys with related values
// the needle will contain the key and the value to be searched for

// Needs more comments and clean up

class Manager
{
    protected $MAIL_KEYS = ["Email", "Name", "Subject", "Text"];

    private $mails;
    private $schedules;
    private $software;
    private $result;
    private $database;

    public $log = true;
    public $error;

    public function __construct(){
      global $database;
      $this->database = $database;
    }

    public function runStatusCheck()
    {
      // Set mails, schedules and software
      $this->fetchNewMails();
      $this->fetchSchedules();
      $this->fetchSoftware();

      // Check whether there are new mails
      if(!$this->mails){
        return false;
      }

      $this->results = [];

      // go through each mail
      foreach ($this->mails as $mail) {
        // @TODO

        $mail->schedule = $this->searchSchedule($mail);
        $mail->error = 0;

        // If a schedule was found search for errors
        if($mail->schedule["ID"] != -1) {
          $mail->software = $this->software[$mail->schedule["SoftwareID"]];
          if($this->searchError($mail, $mail->software)){
            $mail->error = 1;
          }
        }
        // assign the schedule to the mail
        $this->print_log("Inserted mail ({$mail->ID}) into schedule ({$mail->schedule["ID"]}) with error({$mail->error})");
        array_push($this->results, $mail);
      }

      $this->updateMailStatus();

      return $this->results;
    }

    private function updateMailStatus()
    {
      foreach ($this->results as $result){
        $this->database->insert("mailstatus",[
          "MailID"=>$result->ID,
          "ScheduleID"=>$result->schedule["ID"],
          "Error"=>$result->error
        ]);
      }
    }

    public function IDtoKey($array)
    {
      $returnArray = [];
      foreach($array as $a) {
        $returnArray[$a["ID"]] = $a;
      }
      return $returnArray;
    }

    private function fetchNewMails()
    {
      // Next mail to be checked
      $NEXT_MAIL_ID = $this->database->max("mailstatus", "MailID") + 1;
      // $NEXT_MAIL_ID = 1;

      // Fetch all new mails from database
      $mails = $this->database->select(
      "mails",
      [
        "ID",
        "Subject",
        "Email",
        "Name",
        "Text"
      ],
      [
        "ID[>=]" => $NEXT_MAIL_ID]
      );

      // If there are no new mails stop the script
      if(!$mails){
        $this->error = "No new mails in database. (Next: {$NEXT_MAIL_ID})";
        $this->mails = false;
      }

      $returnArray = [];
      foreach($mails as $mail) {
        $returnArray[$mail["ID"]] = new Mail($mail);
      }
      $this->mails = $returnArray;
    }

    private function fetchSchedules()
    {
      // Fetch schedules
      $schedules = $this->database->select("schedules", ["ID", "SearchFor", "SoftwareID"]);

      $this->schedules = $this->IDtoKey($schedules);
    }

    private function fetchSoftware()
    {
      // Fetch backup software
      $software = $this->database->select("software", ["ID", "Name", "SearchForError"]);

      $this->software = $this->IDtoKey($software);
    }

    // @TODO notify in case of multiple schedules found
    private function searchSchedule($mail)
    {
      foreach($this->schedules as $schedule) {
        if($this->searchWithJSON($schedule["SearchFor"], $mail)) {
            return $schedule;
        }
      }
      // No schedule found
      return ["ID"=> -1];
    }


    // @TODO change return values and fix search or search values for backup assist
    private function searchError($mail, $software)
    {
      if($this->searchWithJSON($software["SearchForError"], $mail)) {
        // Fehler gefunden
        // $mail->error = 1337;
        return 1;
      }
      // Kein Fehler gefunden
      // $mail->error = 0;
      return 0;
    }


    public function searchWithJSON($searchJSON, $mail)
    {
      // $searchJSON = ARRAY(ORs[ANDs["KEY","SEARCH","NOT"],ANDs[],...],ORs[],...)
      foreach (json_decode($searchJSON) as $ORs) {
        foreach ($ORs as $ANDs) {
           // verify search key
          if(in_array($ANDs[0], $this->MAIL_KEYS)) {
            // if [2] != NOT and search not succeeding return false
            if(strpos(strtolower($mail->{$ANDs[0]}), strtolower($ANDs[1])) === false && $ANDs[2] != "NOT") {
              return false;
            }
            // wenn gesuchtes gefunden aber nicht gefunden werden soll
            elseif (strpos(strtolower($mail->{$ANDs[0]}), strtolower($ANDs[1])) !== false && $ANDs[2] == "NOT") {
              return false;
            }
          }
        }
        // no false was returned, so every search criteria was successful
        return true;
      }
    }

    // private function searchDate($mail, $JSON)
    // {
    //   // $JSON = [[["WHERE","WHAT","NEGATE"]]]
    //
    //   // @TODO WORK HERE
    //   // Find a way to get date out of mails. search with placeholder
    //   // or keep an eye out for the date of yesterday and check mails where no date was found yet
    //
    //
    //   $replacedJSON = [];
    //
    //   foreach (json_decode($JSON) as $OR) {
    //     $replacedANDs = [];
    //     foreach ($OR as $AND) {
    //         $AND[1] = $this->prepareDateSearch($AND[1]);
    //         $newAND = $AND;
    //         array_push($replacedANDs, $newAND);
    //     }
    //     array_push($replacedJSON, $replacedANDs);
    //   }
    //   print_r($replacedJSON);
    //   die();
    // }

    // private function prepareDateSearch($date){
    //   // replace dd.mm.yyyy with format
    //   // Shadow Protect: Date in Title  "dd.mm.yyyy" or "mm/dd/yyyy"
    //   // Backuo Assist: Startzeit in Text "Startzeit: dd.mm.yyyy"
    //   $day = "yesterday";
    //
    //   $dd = date("d", strtotime($day)); // mm.dd.yyyy
    //   $mm = date("m", strtotime($day)); // mm.dd.yyyy
    //   $yyyy = date("Y", strtotime($day)); // mm.dd.yyyy
    //
    //   $search = ["dd", "mm", "yyyy"];
    //   $replacement = [$dd, $mm, $yyyy];
    //
    //   return str_replace($search, $replacement, $date);
    // }

    private function print_log($text)
    {
      if ($this->log) {
        echo $text ."\n";
      }
    }
}
