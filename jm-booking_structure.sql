-- phpMyAdmin SQL Dump
-- version 2.11.6
-- http://www.phpmyadmin.net
--
-- Vert: localhost
-- Generert den: 21. Mai, 2010 klokka 17:44 PM
-- Tjenerversjon: 5.0.51
-- PHP-Versjon: 5.2.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `jm-booking`
--

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `contact_firm_temp`
--

CREATE TABLE `contact_firm_temp` (
  `firm_id` int(11) NOT NULL auto_increment,
  `firm_name` varchar(255) NOT NULL,
  `firm_info` varchar(255) NOT NULL,
  PRIMARY KEY  (`firm_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `customer`
--

CREATE TABLE `customer` (
  `customer_id` int(11) NOT NULL auto_increment,
  `customer_name` varchar(255) NOT NULL,
  `customer_type` enum('person','firm') NOT NULL,
  `customer_municipal_num` varchar(10) NOT NULL,
  `customer_address_id_invoice` int(11) NOT NULL,
  `slettet` enum('0','1') NOT NULL default '0',
  PRIMARY KEY  (`customer_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `customer_address`
--

CREATE TABLE `customer_address` (
  `address_id` int(11) NOT NULL auto_increment,
  `customer_id` int(11) NOT NULL,
  `address_info` varchar(255) NOT NULL,
  `address_line_1` varchar(255) NOT NULL,
  `address_line_2` varchar(255) NOT NULL,
  `address_line_3` varchar(255) NOT NULL,
  `address_line_4` varchar(255) NOT NULL,
  `address_line_5` varchar(255) NOT NULL,
  `address_line_6` varchar(255) NOT NULL,
  `address_line_7` varchar(255) NOT NULL,
  `address_postalnum` varchar(20) NOT NULL,
  `address_full` text NOT NULL,
  PRIMARY KEY  (`address_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `customer_phone`
--

CREATE TABLE `customer_phone` (
  `phone_id` int(11) NOT NULL auto_increment,
  `customer_id` int(11) NOT NULL,
  `phone_num` varchar(25) NOT NULL,
  `phone_name` varchar(100) NOT NULL,
  PRIMARY KEY  (`phone_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `entry`
--

CREATE TABLE `entry` (
  `entry_id` int(11) NOT NULL auto_increment,
  `entry_name` varchar(255) NOT NULL,
  `entry_title` varchar(255) NOT NULL,
  `confirm_email` tinyint(1) NOT NULL,
  `entry_type_id` int(11) NOT NULL,
  `num_person_child` double NOT NULL,
  `num_person_adult` double NOT NULL,
  `num_person_count` tinyint(1) NOT NULL default '1',
  `program_id` int(11) NOT NULL,
  `program_description` text NOT NULL,
  `service_alco` enum('0','1') NOT NULL,
  `service_description` text NOT NULL,
  `comment` text NOT NULL,
  `infoscreen_txt` varchar(255) NOT NULL,
  `rev_num` int(11) NOT NULL,
  `time_start` int(11) NOT NULL,
  `time_end` int(11) NOT NULL,
  `time_day` varchar(2) NOT NULL,
  `time_month` varchar(2) NOT NULL,
  `time_year` varchar(5) NOT NULL,
  `time_hour` varchar(2) NOT NULL,
  `time_min` varchar(2) NOT NULL,
  `time_created` int(11) NOT NULL,
  `time_last_edit` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `customer_municipal_num` varchar(10) NOT NULL,
  `customer_municipal` varchar(255) NOT NULL,
  `contact_person_name` varchar(255) NOT NULL,
  `contact_person_phone` varchar(25) NOT NULL,
  `contact_person_email` varchar(255) NOT NULL,
  `room_id` varchar(255) NOT NULL,
  `area_id` int(11) NOT NULL,
  `created_by` varchar(255) NOT NULL,
  `edit_by` varchar(255) NOT NULL,
  `user_assigned` varchar(255) NOT NULL,
  `user_assigned2` varchar(255) NOT NULL,
  `user_last_edit` int(11) NOT NULL,
  `invoice` tinyint(1) NOT NULL,
  `invoice_ref_your` varchar(255) NOT NULL,
  `invoice_comment` text NOT NULL,
  `invoice_internal_comment` text NOT NULL,
  `invoice_address_id` int(11) NOT NULL,
  `invoice_content` text NOT NULL,
  `invoice_status` enum('0','1','2','3','4') NOT NULL,
  `invoice_locked` tinyint(1) NOT NULL default '0',
  `invoice_electronic` tinyint(1) NOT NULL default '0',
  `invoice_email` varchar(255) NOT NULL,
  PRIMARY KEY  (`entry_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `entry_confirm`
--

CREATE TABLE `entry_confirm` (
  `confirm_id` int(11) NOT NULL auto_increment,
  `entry_id` int(11) NOT NULL,
  `rev_num` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `confirm_time` varchar(25) NOT NULL,
  `confirm_to` text NOT NULL,
  `confirm_txt` text NOT NULL,
  `confirm_tpl` text NOT NULL,
  `confirm_pdf` enum('0','1') NOT NULL,
  `confirm_pdf_tpl` text NOT NULL,
  `confirm_pdf_txt` text NOT NULL,
  `confirm_pdffile` varchar(255) NOT NULL,
  `confirm_comment` text NOT NULL,
  PRIMARY KEY  (`confirm_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `entry_confirm_attachment`
--

CREATE TABLE `entry_confirm_attachment` (
  `att_id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `att_uploadtime` varchar(20) NOT NULL,
  `att_filename_orig` varchar(255) NOT NULL,
  `att_filename` varchar(255) NOT NULL,
  `att_filetype` varchar(100) NOT NULL,
  `att_filesize` varchar(50) NOT NULL,
  `slettet` enum('0','1') NOT NULL,
  PRIMARY KEY  (`att_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `entry_confirm_usedatt`
--

CREATE TABLE `entry_confirm_usedatt` (
  `confirm_id` int(11) NOT NULL,
  `att_id` int(11) NOT NULL,
  `timeused` varchar(20) NOT NULL,
  PRIMARY KEY  (`confirm_id`,`att_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `entry_log`
--

CREATE TABLE `entry_log` (
  `log_id` int(11) NOT NULL auto_increment,
  `entry_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `log_action` enum('add','edit') NOT NULL,
  `log_action2` varchar(255) NOT NULL,
  `log_time` int(11) NOT NULL,
  `rev_num` int(11) NOT NULL,
  `log_data` text NOT NULL,
  PRIMARY KEY  (`log_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `entry_read`
--

CREATE TABLE `entry_read` (
  `read_id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `entry_id` int(11) NOT NULL,
  `rev_num` int(11) NOT NULL,
  `time_read` int(11) NOT NULL,
  PRIMARY KEY  (`read_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `entry_type`
--

CREATE TABLE `entry_type` (
  `entry_type_id` int(11) NOT NULL auto_increment,
  `entry_type_name` varchar(255) NOT NULL,
  `entry_type_name_short` varchar(25) NOT NULL,
  `group_id` varchar(255) NOT NULL,
  `day_start` varchar(4) NOT NULL,
  `day_end` varchar(4) NOT NULL,
  PRIMARY KEY  (`entry_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `entry_type_defaultattachment`
--

CREATE TABLE `entry_type_defaultattachment` (
  `entry_type_id` int(11) NOT NULL,
  `area_id` int(11) NOT NULL,
  `att_id` int(11) NOT NULL,
  PRIMARY KEY  (`entry_type_id`,`area_id`,`att_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `groups`
--

CREATE TABLE `groups` (
  `group_id` int(11) NOT NULL auto_increment,
  `user_ids` text NOT NULL,
  `group_name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`group_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `import_dn_kategori`
--

CREATE TABLE `import_dn_kategori` (
  `kat_id` int(11) NOT NULL auto_increment,
  `area_id` int(11) NOT NULL,
  `kat_navn` varchar(255) NOT NULL,
  PRIMARY KEY  (`kat_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `import_dn_tall`
--

CREATE TABLE `import_dn_tall` (
  `vare_nr` char(75) NOT NULL,
  `area_id` int(11) NOT NULL,
  `dag` int(11) NOT NULL,
  `kat_id` int(11) NOT NULL,
  `antall_barn` int(11) NOT NULL,
  `antall_voksne` int(11) NOT NULL,
  PRIMARY KEY  (`vare_nr`,`area_id`,`dag`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `import_dn_vareregister`
--

CREATE TABLE `import_dn_vareregister` (
  `vare_nr` char(75) NOT NULL,
  `area_id` int(11) NOT NULL,
  `navn` varchar(255) NOT NULL,
  `kat_id` int(11) default NULL,
  `barn` tinyint(1) NOT NULL,
  PRIMARY KEY  (`vare_nr`,`area_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `invoice`
--

CREATE TABLE `invoice` (
  `invoice_id` int(11) NOT NULL auto_increment,
  `invoice_time_created` int(11) NOT NULL default '0',
  `invoice_time` int(11) NOT NULL default '0',
  `invoice_time_due` int(11) NOT NULL default '0',
  `invoice_time_payed` int(11) NOT NULL default '0',
  `invoice_content` text NOT NULL,
  `invoice_topay_total` double NOT NULL default '0',
  `invoice_topay_total_net` double NOT NULL,
  `invoice_topay_total_tax` double NOT NULL,
  `invoice_payed` tinyint(1) NOT NULL default '0',
  `invoice_payed_amount` double NOT NULL,
  `invoice_idlinks` varchar(255) NOT NULL default '',
  `invoice_to_customer_id` int(11) NOT NULL,
  `invoice_to_address_id` int(11) NOT NULL,
  `invoice_to_line1` varchar(255) NOT NULL,
  `invoice_to_line2` varchar(255) NOT NULL default '',
  `invoice_to_line3` varchar(255) NOT NULL default '',
  `invoice_to_line4` varchar(255) NOT NULL default '',
  `invoice_to_line5` varchar(255) NOT NULL default '',
  `invoice_to_line6` varchar(255) NOT NULL,
  `invoice_to_line7` varchar(255) NOT NULL default '',
  `invoice_to_email` varchar(255) NOT NULL default '',
  `invoice_electronic` enum('0','1') NOT NULL default '0',
  `invoice_comment` text NOT NULL,
  `invoice_internal_comment` text NOT NULL,
  `invoice_ref_your` varchar(255) NOT NULL,
  `invoice_created_by_id` int(11) NOT NULL,
  PRIMARY KEY  (`invoice_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `invoice_log`
--

CREATE TABLE `invoice_log` (
  `log_id` int(11) NOT NULL auto_increment,
  `invoice_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `log_action` enum('add','edit') NOT NULL,
  `log_action2` varchar(255) NOT NULL,
  `log_time` int(11) NOT NULL,
  `rev_num` int(11) NOT NULL,
  `log_data` text NOT NULL,
  PRIMARY KEY  (`log_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `mrbs_area`
--

CREATE TABLE `mrbs_area` (
  `id` int(11) NOT NULL auto_increment,
  `area_name` varchar(30) default NULL,
  `area_group` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `mrbs_category`
--

CREATE TABLE `mrbs_category` (
  `id` int(11) NOT NULL auto_increment,
  `area_id` int(11) NOT NULL default '0',
  `name` varchar(30) NOT NULL default '',
  KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `mrbs_entry`
--

CREATE TABLE `mrbs_entry` (
  `id` int(11) NOT NULL auto_increment,
  `start_time` int(11) NOT NULL default '0',
  `end_time` int(11) NOT NULL default '0',
  `entry_type` int(11) NOT NULL default '0',
  `type` char(1) NOT NULL default '',
  `repeat_id` int(11) NOT NULL default '0',
  `room_id` int(11) NOT NULL default '1',
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `created` timestamp NOT NULL default '0000-00-00 00:00:00',
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `mrbs_multicat`
--

CREATE TABLE `mrbs_multicat` (
  `CID` int(11) NOT NULL default '0',
  `RID` int(11) NOT NULL default '0',
  `uorder` tinyint(4) NOT NULL default '0',
  `extra` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`CID`,`RID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `mrbs_periods`
--

CREATE TABLE `mrbs_periods` (
  `id` int(128) NOT NULL auto_increment,
  `startdate` int(128) NOT NULL default '0',
  `enddate` int(128) NOT NULL default '0',
  `title` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `mrbs_repeat`
--

CREATE TABLE `mrbs_repeat` (
  `id` int(11) NOT NULL auto_increment,
  `start_time` int(11) NOT NULL default '0',
  `end_time` int(11) NOT NULL default '0',
  `rep_type` int(11) NOT NULL default '0',
  `end_date` int(11) NOT NULL default '0',
  `rep_opt` varchar(32) NOT NULL default '',
  `room_id` int(11) NOT NULL default '1',
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `type` char(1) NOT NULL default 'E',
  `rep_num_weeks` tinyint(4) default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `mrbs_room`
--

CREATE TABLE `mrbs_room` (
  `id` int(11) NOT NULL auto_increment,
  `area_id` int(11) NOT NULL default '0',
  `room_name` varchar(25) NOT NULL default '',
  `room_name_short` char(3) NOT NULL default '',
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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL auto_increment,
  `product_name` varchar(255) NOT NULL,
  `product_desc` text NOT NULL,
  `area_id` int(11) NOT NULL,
  `product_price` double NOT NULL,
  `product_tax` double NOT NULL,
  PRIMARY KEY  (`product_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `programs`
--

CREATE TABLE `programs` (
  `program_id` int(11) NOT NULL auto_increment,
  `program_name` varchar(255) NOT NULL,
  `program_desc` text NOT NULL,
  `area_id` int(11) NOT NULL,
  PRIMARY KEY  (`program_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `programs_defaultattachment`
--

CREATE TABLE `programs_defaultattachment` (
  `program_id` int(11) NOT NULL,
  `att_id` int(11) NOT NULL,
  PRIMARY KEY  (`program_id`,`att_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `template`
--

CREATE TABLE `template` (
  `template_id` int(11) NOT NULL auto_increment,
  `template` text NOT NULL,
  `template_name` varchar(255) NOT NULL,
  `template_type` enum('confirm') NOT NULL,
  `template_time_last_edit` int(11) NOT NULL,
  PRIMARY KEY  (`template_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL auto_increment,
  `user_name` varchar(255) NOT NULL default '',
  `user_name_short` varchar(255) NOT NULL,
  `user_email` varchar(255) NOT NULL default '',
  `user_phone` varchar(255) NOT NULL,
  `user_position` varchar(60) NOT NULL,
  `user_password` varchar(255) NOT NULL default '',
  `user_accesslevel` int(11) NOT NULL default '1',
  `user_invoice` tinyint(1) NOT NULL default '0',
  `user_invoice_setready` enum('0','1') NOT NULL,
  `user_area_default` int(11) NOT NULL,
  `user_areas` varchar(255) NOT NULL,
  PRIMARY KEY  (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;




-- --------------------------------------------------------

--
-- Database upgrades - 17.06.2010
-- Export of invoice data
--
ALTER TABLE `entry` ADD `invoice_exported_time` INT NOT NULL AFTER `invoice_email` ;
DROP TABLE `invoice` , `invoice_log` ;


--
-- Database upgrades - 22.06.2010
-- Users accesslevel is not longer in use
--
ALTER TABLE `users` ADD `user_access_useredit` BOOL NOT NULL DEFAULT '0' AFTER `user_areas` ,
ADD `user_access_changerights` BOOL NOT NULL DEFAULT '0' AFTER `user_access_useredit` ;

ALTER TABLE `users` ADD `user_access_areaadmin` BOOL NOT NULL DEFAULT '0' AFTER `user_access_useredit`  ,
ADD `user_access_entrytypeadmin` BOOL NOT NULL DEFAULT '0' AFTER `user_access_areaadmin` ,
ADD `user_access_importdn` BOOL NOT NULL DEFAULT '0' AFTER `user_access_entrytypeadmin` ,
ADD `user_access_productsadmin` BOOL NOT NULL DEFAULT '0' AFTER `user_access_importdn` ,
ADD `user_access_programadmin` BOOL NOT NULL DEFAULT '0' AFTER `user_access_productsadmin` ,
ADD `user_access_templateadmin` BOOL NOT NULL DEFAULT '0' AFTER `user_access_programadmin` ;

ALTER TABLE `users` ADD `user_access_userdelete` BOOL NOT NULL DEFAULT '0' AFTER `user_access_changerights`  ;
ALTER TABLE `users` ADD `deletet` BOOL NOT NULL DEFAULT '0' AFTER `user_access_userdelete` ;
ALTER TABLE `users` CHANGE `deletet` `deleted` TINYINT( 1 ) NOT NULL DEFAULT '0' ;
ALTER TABLE `users` CHANGE `deleted` `deactivated` TINYINT( 1 ) NOT NULL DEFAULT '0' ;
ALTER TABLE `users` CHANGE `user_access_userdelete` `user_access_userdeactivate`  TINYINT( 1 ) NOT NULL DEFAULT '0' ;


--
-- Database upgrades - 15.06.2011
-- Users has settings for Exchange web service (EWS) sync
--
ALTER TABLE `users` ADD `user_ews_sync` BOOL NOT NULL AFTER `user_areas` ,
ADD `user_ews_sync_email` VARCHAR( 255 ) NOT NULL AFTER `user_ews_sync` ;


--
-- Database upgrades - 21.06.2011 & 22.06.2011
-- Import from Datanova web reports
--
ALTER TABLE `mrbs_area` ADD `importdatanova_shop_id` INT NOT NULL AFTER `area_group` ,
ADD `importdatanova_alert_email` VARCHAR( 255 ) NOT NULL AFTER `importdatanova_shop_id` ;
CREATE TABLE `import_dn_tall_ikkeimportert` (
`vare_nr` char( 75 ) NOT NULL ,
`shop_id` int( 11 ) NOT NULL ,
`dag` int( 11 ) NOT NULL ,
`antall_barn` int( 11 ) NOT NULL ,
`antall_voksne` int( 11 ) NOT NULL ,
PRIMARY KEY ( `vare_nr` , `shop_id` , `dag` )
) ENGINE = InnoDB DEFAULT CHARSET = latin1;
ALTER TABLE `import_dn_tall_ikkeimportert` DROP `dag` ;
ALTER TABLE `import_dn_tall_ikkeimportert` DROP `antall_barn` ;
ALTER TABLE `import_dn_tall_ikkeimportert` DROP `antall_voksne` ;
ALTER TABLE `import_dn_tall_ikkeimportert` ADD `vare_navn` VARCHAR( 255 ) NOT NULL AFTER `shop_id` ,
ADD `vare_antall` INT( 11 ) NOT NULL AFTER `vare_navn` ,
ADD `area_id` INT( 11 ) NOT NULL AFTER `vare_antall` ,
ADD `vare_dager` INT NOT NULL AFTER `area_id` ;
ALTER TABLE `import_dn_tall` ADD `shop_id` INT NOT NULL AFTER `antall_voksne` ;
CREATE TABLE `jm-booking`.`import_dn_shops` (
`shop_id` INT NOT NULL ,
`shop_name` VARCHAR( 255 ) NOT NULL
) ENGINE = InnoDB ;


--
-- Database upgrades - 28.06.2011
-- Handling of invoice data
--
CREATE TABLE `jm-booking`.`invoiced` (
`invoiced_id` INT NOT NULL AUTO_INCREMENT ,
`created` BOOL NOT NULL ,
`emailed` BOOL NOT NULL ,
`emailed_time` INT NOT NULL ,
`pdf_name` VARCHAR( 255 ) NOT NULL ,
PRIMARY KEY ( `invoiced_id` )
) ENGINE = InnoDB ;
CREATE TABLE `jm-booking`.`entry_invoiced` (
`entry_id` INT NOT NULL ,
`invoiced_id` INT NOT NULL ,
PRIMARY KEY ( `entry_id` , `invoiced_id` )
) ENGINE = InnoDB ;
CREATE TABLE `jm-booking`.`invoiced_emails` (
`invoiced_id` INT NOT NULL ,
`email_addr` VARCHAR( 255 ) NOT NULL
) ENGINE = InnoDB ;
ALTER TABLE `invoiced` CHANGE `created` `created` INT( 11 ) NOT NULL ;



--
-- Database upgrades - 01.07.2011
-- Exchangesync handles all entries, not just the future
--
ALTER TABLE `entry_exchangesync` DROP `sync_until` ;
ALTER TABLE `entry_exchangesync` ADD `sync_from` INT NOT NULL AFTER `exchange_changekey` ,
ADD `sync_to` INT NOT NULL AFTER `sync_from` ;


--
-- Database upgrades - 05.07.2011
-- New password policies
--
ALTER TABLE `users` ADD `user_password_lastchanged` INT NOT NULL AFTER `user_password` ,
ADD `user_password_1` VARCHAR( 255 ) NOT NULL AFTER `user_password_lastchanged` ,
ADD `user_password_2` VARCHAR( 255 ) NOT NULL AFTER `user_password_1` ,
ADD `user_password_3` VARCHAR( 255 ) NOT NULL AFTER `user_password_2` ;
UPDATE `users` SET `user_password_1` = `user_password`;
UPDATE `users` SET `user_password_2` = `user_password`;
UPDATE `users` SET `user_password_3` = `user_password`;
ALTER TABLE `users` ADD `user_password_complex` BOOL NOT NULL AFTER `user_password` ;