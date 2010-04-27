##########################################
##########################################
####                                  ####
####   THIS FILE IS NOT UP TO DATE!   ####
####                                  ####
##########################################
##########################################

# Datenbank: `arbs_geraete`
# 

# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `mrbs_area`
#

CREATE TABLE `mrbs_area` (
  `id` int(11) NOT NULL auto_increment,
  `area_name` varchar(30) default NULL,
  PRIMARY KEY  (`id`),
  KEY `id` (`id`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `mrbs_category`
#

CREATE TABLE `mrbs_category` (
  `id` int(11) NOT NULL auto_increment,
  `area_id` int(11) NOT NULL default '0',
  `name` varchar(30) NOT NULL default '',
  KEY `id` (`id`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `mrbs_entry`
#

CREATE TABLE `mrbs_entry` (
  `id` int(11) NOT NULL auto_increment,
  `start_time` int(11) NOT NULL default '0',
  `end_time` int(11) NOT NULL default '0',
  `entry_type` int(11) NOT NULL default '0',
  `type` char(1) NOT NULL default '',
  `repeat_id` int(11) NOT NULL default '0',
  `room_id` int(11) NOT NULL default '1',
  `timestamp` timestamp(14) NOT NULL,
  `created` timestamp(14) NOT NULL default '00000000000000',
  `create_by` varchar(25) NOT NULL default '',
  `printed` tinyint(1) NOT NULL default '0',
  `confirmed` tinyint(1) NOT NULL default '0',
  `institute` varchar(6) NOT NULL default '',
  `title` varchar(32) NOT NULL default '',
  `advisor_email` varchar(64) NOT NULL default '',
  `description` varchar(255) NOT NULL default '',
  `name` varchar(32) NOT NULL default '',
  `advisor_name` varchar(32) NOT NULL default '',
  `advisor_phone` varchar(16) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `idxStartTime` (`start_time`),
  KEY `idxEndTime` (`end_time`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `mrbs_multicat`
#

CREATE TABLE `mrbs_multicat` (
  `CID` int(11) NOT NULL default '0',
  `RID` int(11) NOT NULL default '0',
  `uorder` tinyint(4) NOT NULL default '0',
  `extra` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`CID`,`RID`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `mrbs_periods`
#

CREATE TABLE `mrbs_periods` (
  `id` int(128) NOT NULL auto_increment,
  `startdate` int(128) NOT NULL default '0',
  `enddate` int(128) NOT NULL default '0',
  `title` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `mrbs_repeat`
#

CREATE TABLE `mrbs_repeat` (
  `id` int(11) NOT NULL auto_increment,
  `start_time` int(11) NOT NULL default '0',
  `end_time` int(11) NOT NULL default '0',
  `rep_type` int(11) NOT NULL default '0',
  `end_date` int(11) NOT NULL default '0',
  `rep_opt` varchar(32) NOT NULL default '',
  `room_id` int(11) NOT NULL default '1',
  `timestamp` timestamp(14) NOT NULL,
  `type` char(1) NOT NULL default 'E',
  `rep_num_weeks` tinyint(4) default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `mrbs_room`
#

CREATE TABLE `mrbs_room` (
  `id` int(11) NOT NULL auto_increment,
  `area_id` int(11) NOT NULL default '0',
  `room_name` varchar(25) NOT NULL default '',
  `description` varchar(60) default NULL,
  `capacity` int(11) NOT NULL default '0',
  `limit_hour` tinyint(4) NOT NULL default '0',
  `limit_day` tinyint(4) NOT NULL default '0',
  `limit_week` tinyint(4) NOT NULL default '0',
  `comment` mediumtext NOT NULL,
  `adminmail` varchar(50) NOT NULL default '',
  `infourl` varchar(255) NOT NULL default '',
  `infotext` varchar(128) NOT NULL default '',
  `hidden` enum('false','true') NOT NULL default 'false',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;