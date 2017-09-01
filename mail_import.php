<?php
$rustart = getrusage();

ini_set('display_errors', 'on');
require_once 'vendor/autoload.php';
include 'database.php';
include 'config.php';

header('Content-Type: text/html; charset=utf-8');

// Create a new IMAP transport with an SSL connection (default port is 993,
// you can specify a different one using the second parameter of the constructor).
$options = new ezcMailImapTransportOptions();
$options->ssl = true;
$options->uidReferencing = true;

// Create a new IMAP transport object by specifying the server name
$imap = new ezcMailImapTransport("imap.gmx.net", null, $options);

$imap->authenticate( $config["Email"], $config["Password"] );

// Select the Inbox mailbox
$imap->selectMailbox( 'Inbox' );

// Get the latest mail IDs
// Database
$LAST_MAIL_ID = $database->max("mails", "ID");
$NEXT_MAIL_ID = $LAST_MAIL_ID + 1;

// mailbox
$INBOX_MAX_ID = max($imap->listUniqueIdentifiers(););
$INBOX_MIN_ID = min($imap->listUniqueIdentifiers());

if ($NEXT_MAIL_ID < $INBOX_MIN_ID) {
  $LAST_MAIL_ID = $INBOX_MIN_ID;
}

echo "Next mail ID: {$NEXT_MAIL_ID} - max ID in inbox: {$INBOX_MAX_ID}\n";

if ($NEXT_MAIL_ID <= $INBOX_MAX_ID)
{
  // Fetch from Last Mail ID to not accidentally use a non existend id
  // next mail id might not exist
  $set = $imap->fetchFromOffset( $LAST_MAIL_ID );

  // get unique IDs for the mails of the set
  $uniqueIDs = $set->getMessageNumbers();

  $parser = new ezcMailParser();

  // Get the mail an insert it into the database
  $mail = $parser->parseMail( $set );

  // skip first mail because it is already in database
  for ( $i = 1; $i < count( $mail ); $i++ ) {
      $parts = $mail[$i]->fetchParts();

      foreach ( $parts as $part ) {
      if ( $part instanceof ezcMailText ) {
            if ( $part->subType == 'plain' ) {
                $database->insert("mails",[
                  "ID"=>$uniqueIDs[$i],
                  "ReceivedDateTime"=>date("Y-m-d H:i:s", strtotime($mail[$i]->getHeader('Date', true))),
                  "Email"=>$mail[$i]->from->email,
                  "Name"=>$mail[$i]->from->name,
                  "Subject"=>$mail[$i]->subject,
                  "Text"=>$part->text
                ]);
                echo "New: " . ($uniqueIDs[$i]) . "\n";
            }
        }
      }
  }
} else {
  echo "No new messages!\n";
}


// Script end
function rutime($ru, $rus, $index) {
    return ($ru["ru_$index.tv_sec"]*1000 + intval($ru["ru_$index.tv_usec"]/1000))
     -  ($rus["ru_$index.tv_sec"]*1000 + intval($rus["ru_$index.tv_usec"]/1000));
}

$ru = getrusage();
echo "\nThis process used " . rutime($ru, $rustart, "utime") .
    " ms for its computations\n";
echo "It spent " . rutime($ru, $rustart, "stime") .
    " ms in system calls\n";
