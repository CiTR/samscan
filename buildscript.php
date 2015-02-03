<?php

$creation_script = "


DROP SCHEMA IF EXISTS `".$target_db_name."` ;
CREATE SCHEMA IF NOT EXISTS `".$target_db_name."` ;
USE ".$target_db_name.";


CREATE TABLE `adz` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `PROVIDERID` int(11) NOT NULL DEFAULT '0',
  `ADZID` int(11) NOT NULL DEFAULT '0',
  `CAMPAIGNID` int(11) NOT NULL DEFAULT '0',
  `CATEGORYID` int(11) NOT NULL DEFAULT '0',
  `DATE_START` date DEFAULT NULL,
  `DATE_END` date DEFAULT NULL,
  `SONGTYPE` char(1) NOT NULL DEFAULT 'A',
  `LOCALFILENAME` varchar(200) NOT NULL DEFAULT '',
  `LOCALSTATUS` varchar(10) NOT NULL DEFAULT 'download',
  `DOWNLOAD_URL` varchar(200) NOT NULL DEFAULT '',
  `LASTUPDATE` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `DESCRIPTION` varchar(100) NOT NULL DEFAULT '',
  `CAMPAIGNACTIVE` char(3) NOT NULL DEFAULT 'yes',
  `STATUS` varchar(10) NOT NULL DEFAULT 'active',
  `WEIGHT` double NOT NULL DEFAULT '1',
  `GLOBALWEIGHT` double NOT NULL DEFAULT '1',
  `DURATION` int(11) NOT NULL DEFAULT '0',
  `FILESIZE` int(11) NOT NULL DEFAULT '0',
  `DATE_PLAYED` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `DATE_CATEGORY_PLAYED` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `DATE_CAMPAIGN_PLAYED` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `DATE_VALID` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `SPINS` int(11) NOT NULL DEFAULT '0',
  `PERFORMANCES` int(11) NOT NULL DEFAULT '0',
  `SPINS_MAX` int(11) NOT NULL DEFAULT '0',
  `PERFORMANCES_MAX` int(11) NOT NULL DEFAULT '0',
  `cap_day` int(11) NOT NULL DEFAULT '0',
  `cap_day_cnt` int(11) NOT NULL DEFAULT '0',
  `DAYS` varchar(100) NOT NULL DEFAULT '',
  `HOURS` varchar(200) NOT NULL DEFAULT '',
  `BLOCKED` char(3) NOT NULL DEFAULT 'no',
  `LOCALWEIGHT` double NOT NULL DEFAULT '1',
  `LOCALBALANCE` double NOT NULL DEFAULT '0',
  `SORTID` double NOT NULL DEFAULT '0',
  `EXTERNALID` int(11) NOT NULL DEFAULT '0',
  `SYNCINFO` varchar(200) NOT NULL DEFAULT '',
  `PROGRESS` double NOT NULL DEFAULT '100',
  `timematrix` text,
  `min_separation` int(11) NOT NULL,
  `min_separation_campaign` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `category` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `parentID` int(11) NOT NULL DEFAULT '0',
  `levelindex` tinyint(4) NOT NULL DEFAULT '0',
  `itemindex` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=53 DEFAULT CHARSET=latin1;

CREATE TABLE `categorylist` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `songID` int(11) NOT NULL DEFAULT '0',
  `categoryID` int(11) NOT NULL DEFAULT '0',
  `sortID` double NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `categoryID` (`categoryID`)
) ENGINE=MyISAM AUTO_INCREMENT=2437405 DEFAULT CHARSET=latin1;

CREATE TABLE `disk` (
  `ID` int(11) NOT NULL DEFAULT '0',
  `serial` varchar(100) NOT NULL DEFAULT '',
  `name` varchar(100) NOT NULL DEFAULT '',
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `t_stamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `event` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL DEFAULT '',
  `eventaction` varchar(50) NOT NULL DEFAULT '',
  `data` text,
  `extra` text,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=44 DEFAULT CHARSET=latin1;

CREATE TABLE `eventtime` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `eventID` int(11) NOT NULL DEFAULT '0',
  `eventtime` time NOT NULL DEFAULT '00:00:00',
  `eventdate` date NOT NULL DEFAULT '0000-00-00',
  `eventday` varchar(20) NOT NULL DEFAULT 'day',
  `recurring` enum('No','Yes') NOT NULL DEFAULT 'Yes',
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=592 DEFAULT CHARSET=latin1;

CREATE TABLE `fixedlist` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `PROVIDERID` int(11) NOT NULL DEFAULT '0',
  `FIXEDLISTID` int(11) NOT NULL DEFAULT '0',
  `SORTMODE` varchar(5) NOT NULL DEFAULT 'auto',
  `DATE_MODIFIED` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `DATE_START` date NOT NULL DEFAULT '0000-00-00',
  `DATE_END` date NOT NULL DEFAULT '0000-00-00',
  `TIME_START` time NOT NULL DEFAULT '00:00:00',
  `TIME_END` time NOT NULL DEFAULT '00:00:00',
  `LOOP_MAX` smallint(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `fixedlist_item` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `PROVIDERID` int(11) NOT NULL DEFAULT '0',
  `FIXEDLISTID` int(11) NOT NULL DEFAULT '0',
  `FIXEDLIST_ITEMID` int(11) NOT NULL DEFAULT '0',
  `ADZID` int(11) NOT NULL DEFAULT '0',
  `SORTID` double NOT NULL DEFAULT '0',
  `PLAYCOUNT` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `historylist` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `songID` int(11) NOT NULL DEFAULT '0',
  `filename` varchar(255) NOT NULL DEFAULT '',
  `date_played` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `duration` int(11) NOT NULL DEFAULT '0',
  `artist` varchar(255) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `album` varchar(255) NOT NULL DEFAULT '',
  `albumyear` varchar(4) NOT NULL DEFAULT '',
  `website` varchar(255) NOT NULL DEFAULT '',
  `buycd` varchar(255) NOT NULL DEFAULT '',
  `picture` varchar(255) NOT NULL DEFAULT '',
  `listeners` mediumint(9) NOT NULL DEFAULT '0',
  `label` varchar(100) NOT NULL DEFAULT '',
  `pline` varchar(50) NOT NULL DEFAULT '',
  `trackno` int(11) NOT NULL DEFAULT '0',
  `composer` varchar(100) NOT NULL DEFAULT '',
  `ISRC` varchar(50) NOT NULL DEFAULT '',
  `catalog` varchar(50) NOT NULL DEFAULT '',
  `UPC` varchar(50) NOT NULL DEFAULT '',
  `feeagency` varchar(20) NOT NULL DEFAULT '',
  `songtype` char(1) NOT NULL DEFAULT '',
  `requestID` int(11) NOT NULL DEFAULT '0',
  `overlay` enum('yes','no') NOT NULL DEFAULT 'no',
  `songrights` set('broadcast','download','on-demand','royaltyfree') NOT NULL DEFAULT 'broadcast',
  PRIMARY KEY (`ID`),
  KEY `date_played` (`date_played`)
) ENGINE=MyISAM AUTO_INCREMENT=77947 DEFAULT CHARSET=latin1;

CREATE TABLE `queuelist` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `songID` int(11) NOT NULL DEFAULT '0',
  `sortID` double NOT NULL DEFAULT '0',
  `requests` int(11) NOT NULL DEFAULT '0',
  `requestID` int(11) NOT NULL DEFAULT '0',
  `auxdata` varchar(200) NOT NULL DEFAULT '',
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=158465 DEFAULT CHARSET=latin1 COMMENT='17';

CREATE TABLE `requestlist` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `songID` int(11) NOT NULL DEFAULT '0',
  `t_stamp` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `host` varchar(255) NOT NULL DEFAULT '',
  `msg` text,
  `name` varchar(255) NOT NULL DEFAULT '',
  `code` mediumint(9) NOT NULL DEFAULT '0',
  `ETA` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` enum('played','ignored','pending','new') NOT NULL DEFAULT 'new',
  PRIMARY KEY (`ID`),
  KEY `t_stamp` (`t_stamp`),
  KEY `requestlist_songID_i` (`songID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `songlist` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `filename` varchar(255) NOT NULL DEFAULT '',
  `diskID` int(11) NOT NULL DEFAULT '0',
  `flags` varchar(10) NOT NULL DEFAULT 'NNNNNNNNNN',
  `songtype` char(1) NOT NULL DEFAULT 'S',
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `weight` double NOT NULL DEFAULT '50',
  `balance` double NOT NULL DEFAULT '0',
  `date_added` datetime DEFAULT NULL,
  `date_played` datetime DEFAULT NULL,
  `date_artist_played` datetime DEFAULT '2002-01-01 00:00:01',
  `date_album_played` datetime DEFAULT '2002-01-01 00:00:01',
  `date_title_played` datetime DEFAULT '2002-01-01 00:00:01',
  `duration` int(11) NOT NULL DEFAULT '0',
  `artist` varchar(255) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `album` varchar(255) NOT NULL DEFAULT '',
  `label` varchar(255) NOT NULL DEFAULT '',
  `pline` varchar(50) NOT NULL DEFAULT '',
  `trackno` int(11) NOT NULL DEFAULT '0',
  `composer` varchar(100) NOT NULL DEFAULT '',
  `ISRC` varchar(50) NOT NULL DEFAULT '',
  `catalog` varchar(50) NOT NULL DEFAULT '',
  `UPC` varchar(50) NOT NULL DEFAULT '',
  `feeagency` varchar(20) NOT NULL DEFAULT '',
  `albumyear` varchar(4) NOT NULL DEFAULT '0',
  `genre` varchar(20) NOT NULL DEFAULT '',
  `website` varchar(255) NOT NULL DEFAULT '',
  `buycd` varchar(255) NOT NULL DEFAULT '',
  `info` text,
  `lyrics` text,
  `picture` varchar(255) NOT NULL DEFAULT '',
  `count_played` mediumint(9) NOT NULL DEFAULT '0',
  `count_requested` mediumint(9) NOT NULL DEFAULT '0',
  `last_requested` datetime NOT NULL DEFAULT '2002-01-01 00:00:01',
  `count_performances` int(11) NOT NULL DEFAULT '0',
  `xfade` varchar(50) NOT NULL DEFAULT '',
  `bpm` mediumint(9) NOT NULL DEFAULT '0',
  `mood` varchar(50) NOT NULL DEFAULT '',
  `rating` mediumint(9) NOT NULL DEFAULT '0',
  `overlay` enum('yes','no') NOT NULL DEFAULT 'no',
  `playlimit_count` int(11) NOT NULL DEFAULT '0',
  `playlimit_action` enum('none','remove','erase') NOT NULL DEFAULT 'none',
  `songrights` set('broadcast','download','on-demand','royaltyfree') NOT NULL DEFAULT 'broadcast',
  `adz_listID` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `filename` (`filename`),
  KEY `date_played` (`date_played`),
  KEY `date_artist_played` (`date_artist_played`),
  KEY `date_album_played` (`date_album_played`)
) ENGINE=MyISAM AUTO_INCREMENT=206474 DEFAULT CHARSET=latin1;



";


//CiTR SPECIFIC CATEGORIES


$creation_script .= "

INSERT INTO `category` (`ID`,`name`,`parentID`,`levelindex`,`itemindex`) VALUES (2,'Cancon',0,1,0);
INSERT INTO `category` (`ID`,`name`,`parentID`,`levelindex`,`itemindex`) VALUES (3,'Femcon',0,1,2);
INSERT INTO `category` (`ID`,`name`,`parentID`,`levelindex`,`itemindex`) VALUES (4,'Cancon Femcon',0,1,6);
INSERT INTO `category` (`ID`,`name`,`parentID`,`levelindex`,`itemindex`) VALUES (5,'CiTR playlist',0,1,0);
INSERT INTO `category` (`ID`,`name`,`parentID`,`levelindex`,`itemindex`) VALUES (6,'PRIORITY ADs',0,1,4);
INSERT INTO `category` (`ID`,`name`,`parentID`,`levelindex`,`itemindex`) VALUES (7,'PSAs',0,1,17);
INSERT INTO `category` (`ID`,`name`,`parentID`,`levelindex`,`itemindex`) VALUES (11,'Community',7,2,5);
INSERT INTO `category` (`ID`,`name`,`parentID`,`levelindex`,`itemindex`) VALUES (12,'UBC',7,2,1);
INSERT INTO `category` (`ID`,`name`,`parentID`,`levelindex`,`itemindex`) VALUES (13,'New Timely PSAs',7,2,4);
INSERT INTO `category` (`ID`,`name`,`parentID`,`levelindex`,`itemindex`) VALUES (18,'STATION IDz',0,1,7);
INSERT INTO `category` (`ID`,`name`,`parentID`,`levelindex`,`itemindex`) VALUES (19,'Special Programming',0,1,13);
INSERT INTO `category` (`ID`,`name`,`parentID`,`levelindex`,`itemindex`) VALUES (21,'SHOW PROMOS',0,1,8);
INSERT INTO `category` (`ID`,`name`,`parentID`,`levelindex`,`itemindex`) VALUES (22,'Shindig',0,1,14);
INSERT INTO `category` (`ID`,`name`,`parentID`,`levelindex`,`itemindex`) VALUES (23,'Cancon Category 2',2,2,0);
INSERT INTO `category` (`ID`,`name`,`parentID`,`levelindex`,`itemindex`) VALUES (24,'Cancon Category 3',2,2,1);
INSERT INTO `category` (`ID`,`name`,`parentID`,`levelindex`,`itemindex`) VALUES (25,'temp',0,1,11);
INSERT INTO `category` (`ID`,`name`,`parentID`,`levelindex`,`itemindex`) VALUES (26,'Holidays Playlist',19,2,0);
INSERT INTO `category` (`ID`,`name`,`parentID`,`levelindex`,`itemindex`) VALUES (27,'Holidays Cancon',19,2,0);
INSERT INTO `category` (`ID`,`name`,`parentID`,`levelindex`,`itemindex`) VALUES (41,'Digital Only',0,1,9);
INSERT INTO `category` (`ID`,`name`,`parentID`,`levelindex`,`itemindex`) VALUES (48,'Discorder',0,1,11);
INSERT INTO `category` (`ID`,`name`,`parentID`,`levelindex`,`itemindex`) VALUES (49,'October issue',48,2,0);
INSERT INTO `category` (`ID`,`name`,`parentID`,`levelindex`,`itemindex`) VALUES (50,'November issue',48,2,1);
INSERT INTO `category` (`ID`,`name`,`parentID`,`levelindex`,`itemindex`) VALUES (51,'Jancember',48,2,2);
INSERT INTO `category` (`ID`,`name`,`parentID`,`levelindex`,`itemindex`) VALUES (52,'CiTR 2014 Top 100',0,1,11);
";