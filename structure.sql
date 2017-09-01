-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 01. Sep 2017 um 14:59
-- Server-Version: 5.7.19-0ubuntu0.16.04.1
-- PHP-Version: 7.0.22-0ubuntu0.16.04.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `buwartung`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `clients`
--

CREATE TABLE `clients` (
  `ID` int(11) NOT NULL,
  `Name` varchar(64) NOT NULL,
  `Caretaker` int(11) NOT NULL COMMENT 'UserID'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `mails`
--

CREATE TABLE `mails` (
  `ID` int(11) NOT NULL COMMENT 'ID = Mail ID',
  `ReceivedDateTime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `Email` varchar(256) CHARACTER SET latin1 NOT NULL,
  `Name` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `Subject` varchar(256) CHARACTER SET latin1 NOT NULL,
  `Text` text CHARACTER SET latin1 NOT NULL COMMENT 'plain text or html?'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `mailstatus`
--

  `MailID` int(11) NOT NULL,
  `ScheduleID` int(11) NOT NULL,
  `Error` tinyint(2) NOT NULL DEFAULT '0' COMMENT '0 = Gut; 1... = Error -1 Kein Plan gefunden',
  `Description` varchar(512) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT 'In case of Error'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `schedules`
--

CREATE TABLE `schedules` (
  `ID` int(11) NOT NULL,
  `Name` varchar(256) NOT NULL,
  `ClientID` int(11) NOT NULL,
  `SoftwareID` int(11) NOT NULL,
  `SearchFor` varchar(512) NOT NULL COMMENT 'Subject: *Ram* ODER FROM: backup24@ram...',
  `Info` text NOT NULL,
  `Monday` tinyint(1) DEFAULT '0',
  `Tuesday` tinyint(1) DEFAULT '0',
  `Wednesday` tinyint(1) DEFAULT '0',
  `Thursday` tinyint(1) DEFAULT '0',
  `Friday` tinyint(1) DEFAULT '0',
  `Saturday` tinyint(1) DEFAULT '0',
  `Sunday` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `software`
--

CREATE TABLE `software` (
  `ID` int(11) NOT NULL,
  `Name` varchar(32) NOT NULL,
  `SearchForError` text NOT NULL,
  `SearchForDate` text NOT NULL,
  `Info` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Users`
--

CREATE TABLE `Users` (
  `ID` int(11) NOT NULL,
  `Name` varchar(128) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`ID`);

--
-- Indizes für die Tabelle `mails`
--
ALTER TABLE `mails`
  ADD PRIMARY KEY (`ID`);

--
-- Indizes für die Tabelle `mailstatus`
--
ALTER TABLE `mailstatus`
  ADD PRIMARY KEY (`MailID`);

--
-- Indizes für die Tabelle `schedules`
--
ALTER TABLE `schedules`
  ADD PRIMARY KEY (`ID`);

--
-- Indizes für die Tabelle `software`
--
ALTER TABLE `software`
  ADD PRIMARY KEY (`ID`);

--
-- Indizes für die Tabelle `Users`
--
ALTER TABLE `Users`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `clients`
--
ALTER TABLE `clients`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;
--
-- AUTO_INCREMENT für Tabelle `mailstatus`
--
ALTER TABLE `mailstatus`
  MODIFY `MailID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1038;
--
-- AUTO_INCREMENT für Tabelle `schedules`
--
ALTER TABLE `schedules`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=87;
--
-- AUTO_INCREMENT für Tabelle `software`
--
ALTER TABLE `software`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT für Tabelle `Users`
--
ALTER TABLE `Users`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
