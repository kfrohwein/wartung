<?php
namespace MailManagement;

class Mail
{
  // ID, email, name, subject, text, schedule, software, error
  protected $properties = array();
  private $database;

  public function __construct($data = null)
  {
    global $database;
    $this->database = &$database;

    // @TODO Check data?
    
    if ($data != null){
    // $this->ID = $data["ID"];
    // if (isset($data["ReceivedDateTime"])){
    //   $this->ReceivedDateTime = $data["ReceivedDateTime"];
    // }
    // $this->Email = $data["Email"];
    // $this->Name = $data["Name"];
    // $this->Subject = $data["Subject"];
    // $this->Text = $data["Text"];
      foreach($data as $key => $value){
        $this->{$key} = $value;
      }
    }
  }

  public function setByID($ID)
  {

    // @TODO Merge the SQL requests (time_check_join.php)
    $data = $this->database->select(
    "mails",
    [
      "ID",
      "ReceivedDateTime",
      "Email",
      "Name",
      "Subject",
      "Text"
    ],
    [
      "ID" => $ID]
    );

    $this->ID = $data[0]["ID"];
    $this->ReceivedDateTime = $data[0]["ReceivedDateTime"];
    $this->Email = $data[0]["Email"];
    $this->Name = $data[0]["Name"];
    $this->Subject = $data[0]["Subject"];
    $this->Text = $data[0]["Text"];

    $data = $this->database->select(
    "mailstatus",
    [
      "ScheduleID",
      "Error"
    ],
    [
      "MailID" => $ID]
    );

    $this->SubjectID = $data[0]["ScheduleID"];
    $this->Error = $data[0]["Error"];
  }

  public function __set($name, $value)
  {
    switch( strtolower($name) ) {
      case 'ID':
      case 'id':
        $this->properties["ID"] = $value;
        break;
      case 'email':
        $this->properties["Email"] = $value;
        break;
      case 'name':
        $this->properties["Name"] = $value;
        break;
      case 'subject':
        $this->properties["Subject"] = $value;
        break;
      case 'text':
        $this->properties["Text"] = $value;
        break;
      case 'schedule':
        $this->properties["Schedule"] = $value;
        break;
      case 'software':
        $this->properties["Software"] = $value;
        break;
      case 'error':
        $this->properties["Error"] = $value;
        break;
      case 'receiveddatetime':
        $this->properties["ReceivedDateTime"] = $value;
      case 'scheduleid':
        $this->properties["ScheduleID"] = $value;
        break;
      default:
         $this->properties[$name] = $value;
    }
  }

  public function __get($name)
  {
    switch( strtolower($name) ) {
      case 'ID':
      case 'Id':
        return $this->properties["ID"];
      case 'email':
        return $this->properties["Email"];
      case 'name':
        return $this->properties["Name"];
      case 'subject':
        return $this->properties["Subject"];
      case 'text':
        return $this->properties["Text"];
      case 'schedule':
        return $this->properties["Schedule"];
      case 'software':
        return $this->properties["Software"];
      case 'error':
        return $this->properties["Error"];
      case 'receiveddatetime':
        return $this->properties["ReceivedDateTime"];
      case 'scheduleid':
        return $this->properties["ScheduleID"];
      default:
        return $this->properties[$name];
    }
  }
}
