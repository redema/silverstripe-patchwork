-- phpMyAdmin SQL Dump
-- version 4.0.10
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 13, 2013 at 11:17 AM
-- Server version: 5.5.34-0ubuntu0.12.04.1
-- PHP Version: 5.3.10-1ubuntu3.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `silverstripe_localhost`
--

-- --------------------------------------------------------

--
-- Table structure for table `Email_BounceRecord`
--

CREATE TABLE IF NOT EXISTS `Email_BounceRecord` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `ClassName` enum('Email_BounceRecord') DEFAULT 'Email_BounceRecord',
  `Created` datetime DEFAULT NULL,
  `LastEdited` datetime DEFAULT NULL,
  `BounceEmail` varchar(50) DEFAULT NULL,
  `BounceTime` datetime DEFAULT NULL,
  `BounceMessage` varchar(50) DEFAULT NULL,
  `MemberID` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `MemberID` (`MemberID`),
  KEY `ClassName` (`ClassName`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ErrorPage`
--

CREATE TABLE IF NOT EXISTS `ErrorPage` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `ErrorCode` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `ErrorPage`
--

INSERT INTO `ErrorPage` (`ID`, `ErrorCode`) VALUES
(4, 404),
(5, 500);

-- --------------------------------------------------------

--
-- Table structure for table `ErrorPage_Live`
--

CREATE TABLE IF NOT EXISTS `ErrorPage_Live` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `ErrorCode` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `ErrorPage_Live`
--

INSERT INTO `ErrorPage_Live` (`ID`, `ErrorCode`) VALUES
(4, 404),
(5, 500);

-- --------------------------------------------------------

--
-- Table structure for table `ErrorPage_versions`
--

CREATE TABLE IF NOT EXISTS `ErrorPage_versions` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `RecordID` int(11) NOT NULL DEFAULT '0',
  `Version` int(11) NOT NULL DEFAULT '0',
  `ErrorCode` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `RecordID_Version` (`RecordID`,`Version`),
  KEY `RecordID` (`RecordID`),
  KEY `Version` (`Version`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `File`
--

CREATE TABLE IF NOT EXISTS `File` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `ClassName` enum('File','Folder','Image','Image_Cached') DEFAULT 'File',
  `Created` datetime DEFAULT NULL,
  `LastEdited` datetime DEFAULT NULL,
  `Name` varchar(255) DEFAULT NULL,
  `Title` varchar(255) DEFAULT NULL,
  `Filename` mediumtext,
  `Content` mediumtext,
  `ShowInSearch` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `ParentID` int(11) DEFAULT NULL,
  `OwnerID` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `ParentID` (`ParentID`),
  KEY `OwnerID` (`OwnerID`),
  KEY `ClassName` (`ClassName`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `File`
--

INSERT INTO `File` (`ID`, `ClassName`, `Created`, `LastEdited`, `Name`, `Title`, `Filename`, `Content`, `ShowInSearch`, `ParentID`, `OwnerID`) VALUES
(1, 'Folder', '2013-01-17 15:08:39', '2013-01-17 15:08:39', 'Uploads', 'Uploads', 'assets/Uploads/', NULL, 1, 0, 0),
(2, 'File', '2013-01-17 15:08:39', '2013-01-17 15:08:39', 'error-404.html', 'error-404.html', 'assets/error-404.html', NULL, 1, 0, 0),
(3, 'File', '2013-01-17 15:08:39', '2013-01-17 15:08:39', 'error-500.html', 'error-500.html', 'assets/error-500.html', NULL, 1, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `Group`
--

CREATE TABLE IF NOT EXISTS `Group` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `ClassName` enum('Group') DEFAULT 'Group',
  `Created` datetime DEFAULT NULL,
  `LastEdited` datetime DEFAULT NULL,
  `Title` varchar(255) DEFAULT NULL,
  `Description` mediumtext,
  `Code` varchar(255) DEFAULT NULL,
  `Locked` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `Sort` int(11) NOT NULL DEFAULT '0',
  `HtmlEditorConfig` mediumtext,
  `ParentID` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `ParentID` (`ParentID`),
  KEY `ClassName` (`ClassName`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `Group`
--

INSERT INTO `Group` (`ID`, `ClassName`, `Created`, `LastEdited`, `Title`, `Description`, `Code`, `Locked`, `Sort`, `HtmlEditorConfig`, `ParentID`) VALUES
(1, 'Group', '2013-01-17 15:08:35', '2013-01-17 15:08:35', 'Content Authors', NULL, 'content-authors', 0, 1, NULL, 0),
(2, 'Group', '2013-01-17 15:08:36', '2013-01-17 15:08:36', 'Administrators', NULL, 'administrators', 0, 0, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `Group_Members`
--

CREATE TABLE IF NOT EXISTS `Group_Members` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `GroupID` int(11) NOT NULL DEFAULT '0',
  `MemberID` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `GroupID` (`GroupID`),
  KEY `MemberID` (`MemberID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `Group_Members`
--

INSERT INTO `Group_Members` (`ID`, `GroupID`, `MemberID`) VALUES
(1, 2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `Group_Roles`
--

CREATE TABLE IF NOT EXISTS `Group_Roles` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `GroupID` int(11) NOT NULL DEFAULT '0',
  `PermissionRoleID` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `GroupID` (`GroupID`),
  KEY `PermissionRoleID` (`PermissionRoleID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `LiteralRedirectorPage`
--

CREATE TABLE IF NOT EXISTS `LiteralRedirectorPage` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Redirect` mediumtext,
  `RedirectCode` enum('301','302','303','304','305','307') DEFAULT '301',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `LiteralRedirectorPage_Live`
--

CREATE TABLE IF NOT EXISTS `LiteralRedirectorPage_Live` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Redirect` mediumtext,
  `RedirectCode` enum('301','302','303','304','305','307') DEFAULT '301',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `LiteralRedirectorPage_versions`
--

CREATE TABLE IF NOT EXISTS `LiteralRedirectorPage_versions` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `RecordID` int(11) NOT NULL DEFAULT '0',
  `Version` int(11) NOT NULL DEFAULT '0',
  `Redirect` mediumtext,
  `RedirectCode` enum('301','302','303','304','305','307') DEFAULT '301',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `RecordID_Version` (`RecordID`,`Version`),
  KEY `RecordID` (`RecordID`),
  KEY `Version` (`Version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `LoginAttempt`
--

CREATE TABLE IF NOT EXISTS `LoginAttempt` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `ClassName` enum('LoginAttempt') DEFAULT 'LoginAttempt',
  `Created` datetime DEFAULT NULL,
  `LastEdited` datetime DEFAULT NULL,
  `Email` varchar(255) DEFAULT NULL,
  `Status` enum('Success','Failure') DEFAULT 'Success',
  `IP` varchar(255) DEFAULT NULL,
  `MemberID` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `MemberID` (`MemberID`),
  KEY `ClassName` (`ClassName`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `Member`
--

CREATE TABLE IF NOT EXISTS `Member` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `ClassName` enum('Member') DEFAULT 'Member',
  `Created` datetime DEFAULT NULL,
  `LastEdited` datetime DEFAULT NULL,
  `FirstName` varchar(50) DEFAULT NULL,
  `Surname` varchar(50) DEFAULT NULL,
  `Email` varchar(256) DEFAULT NULL,
  `Password` varchar(160) DEFAULT NULL,
  `RememberLoginToken` varchar(160) DEFAULT NULL,
  `NumVisit` int(11) NOT NULL DEFAULT '0',
  `LastVisited` datetime DEFAULT NULL,
  `Bounced` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `AutoLoginHash` varchar(160) DEFAULT NULL,
  `AutoLoginExpired` datetime DEFAULT NULL,
  `PasswordEncryption` varchar(50) DEFAULT NULL,
  `Salt` varchar(50) DEFAULT NULL,
  `PasswordExpiry` date DEFAULT NULL,
  `LockedOutUntil` datetime DEFAULT NULL,
  `Locale` varchar(6) DEFAULT NULL,
  `FailedLoginCount` int(11) NOT NULL DEFAULT '0',
  `DateFormat` varchar(30) DEFAULT NULL,
  `TimeFormat` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `Email` (`Email`(255)),
  KEY `ClassName` (`ClassName`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `Member`
--

INSERT INTO `Member` (`ID`, `ClassName`, `Created`, `LastEdited`, `FirstName`, `Surname`, `Email`, `Password`, `RememberLoginToken`, `NumVisit`, `LastVisited`, `Bounced`, `AutoLoginHash`, `AutoLoginExpired`, `PasswordEncryption`, `Salt`, `PasswordExpiry`, `LockedOutUntil`, `Locale`, `FailedLoginCount`, `DateFormat`, `TimeFormat`) VALUES
(1, 'Member', '2013-01-17 15:08:36', '2013-01-17 15:08:39', 'Default Admin', NULL, 'admin', '$2y$10$bd115ef24ce5d5b5c5912eMpfemHUbjxk7quRFv3DdLkfKjch4ZDS', NULL, 0, NULL, 0, NULL, NULL, 'blowfish', '10$bd115ef24ce5d5b5c5912e', NULL, NULL, 'en_US', 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `MemberPassword`
--

CREATE TABLE IF NOT EXISTS `MemberPassword` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `ClassName` enum('MemberPassword') DEFAULT 'MemberPassword',
  `Created` datetime DEFAULT NULL,
  `LastEdited` datetime DEFAULT NULL,
  `Password` varchar(160) DEFAULT NULL,
  `Salt` varchar(50) DEFAULT NULL,
  `PasswordEncryption` varchar(50) DEFAULT NULL,
  `MemberID` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `MemberID` (`MemberID`),
  KEY `ClassName` (`ClassName`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `MemberPassword`
--

INSERT INTO `MemberPassword` (`ID`, `ClassName`, `Created`, `LastEdited`, `Password`, `Salt`, `PasswordEncryption`, `MemberID`) VALUES
(1, 'MemberPassword', '2013-01-17 15:08:39', '2013-01-17 15:08:39', '$2y$10$bd115ef24ce5d5b5c5912eMpfemHUbjxk7quRFv3DdLkfKjch4ZDS', '10$bd115ef24ce5d5b5c5912e', 'blowfish', 1);

-- --------------------------------------------------------

--
-- Table structure for table `Page`
--

CREATE TABLE IF NOT EXISTS `Page` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `ScheduledPublish` datetime DEFAULT NULL,
  `ScheduledUnpublish` datetime DEFAULT NULL,
  `PublicTimestamp` datetime DEFAULT NULL,
  `MetaLabels` mediumtext,
  `SummaryTitle` mediumtext,
  `SummaryContent` mediumtext,
  `SummaryThumbnailID` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `SummaryThumbnailID` (`SummaryThumbnailID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `PageAggregate`
--

CREATE TABLE IF NOT EXISTS `PageAggregate` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `SearchNeedle` mediumtext,
  `SearchResultPageLength` int(11) NOT NULL DEFAULT '0',
  `SearchResultSort` enum('Relevance','Created','LastEdited','PublicTimestamp','Alphabetical','SiteTree') DEFAULT 'Relevance',
  `SearchExcludePageAggregates` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `SearchExcludeErrorPages` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `SearchFromDate` date DEFAULT NULL,
  `SearchToDate` date DEFAULT NULL,
  `SearchHierarchy` enum('Site','Children','Grandchildren','AllDescendants') DEFAULT 'Site',
  `SummaryBadge` mediumtext,
  `SummaryShowBadge` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `SummaryShowLabels` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `SearchFormShowNeedleField` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `SearchFormShowSortField` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `SearchFormShowDateFields` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `SearchFormShowCategoriesFields` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `SearchFormShowTagsFields` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `PageAggregate_Live`
--

CREATE TABLE IF NOT EXISTS `PageAggregate_Live` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `SearchNeedle` mediumtext,
  `SearchResultPageLength` int(11) NOT NULL DEFAULT '0',
  `SearchResultSort` enum('Relevance','Created','LastEdited','PublicTimestamp','Alphabetical','SiteTree') DEFAULT 'Relevance',
  `SearchExcludePageAggregates` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `SearchExcludeErrorPages` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `SearchFromDate` date DEFAULT NULL,
  `SearchToDate` date DEFAULT NULL,
  `SearchHierarchy` enum('Site','Children','Grandchildren','AllDescendants') DEFAULT 'Site',
  `SummaryBadge` mediumtext,
  `SummaryShowBadge` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `SummaryShowLabels` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `SearchFormShowNeedleField` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `SearchFormShowSortField` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `SearchFormShowDateFields` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `SearchFormShowCategoriesFields` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `SearchFormShowTagsFields` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `PageAggregate_versions`
--

CREATE TABLE IF NOT EXISTS `PageAggregate_versions` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `RecordID` int(11) NOT NULL DEFAULT '0',
  `Version` int(11) NOT NULL DEFAULT '0',
  `SearchNeedle` mediumtext,
  `SearchResultPageLength` int(11) NOT NULL DEFAULT '0',
  `SearchResultSort` enum('Relevance','Created','LastEdited','PublicTimestamp','Alphabetical','SiteTree') DEFAULT 'Relevance',
  `SearchExcludePageAggregates` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `SearchExcludeErrorPages` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `SearchFromDate` date DEFAULT NULL,
  `SearchToDate` date DEFAULT NULL,
  `SearchHierarchy` enum('Site','Children','Grandchildren','AllDescendants') DEFAULT 'Site',
  `SummaryBadge` mediumtext,
  `SummaryShowBadge` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `SummaryShowLabels` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `SearchFormShowNeedleField` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `SearchFormShowSortField` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `SearchFormShowDateFields` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `SearchFormShowCategoriesFields` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `SearchFormShowTagsFields` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `RecordID_Version` (`RecordID`,`Version`),
  KEY `RecordID` (`RecordID`),
  KEY `Version` (`Version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `PageContentItem`
--

CREATE TABLE IF NOT EXISTS `PageContentItem` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `ClassName` enum('PageContentItem') DEFAULT 'PageContentItem',
  `Created` datetime DEFAULT NULL,
  `LastEdited` datetime DEFAULT NULL,
  `Title` mediumtext,
  `Link` mediumtext,
  `Content` mediumtext,
  `ExtraClasses` mediumtext,
  `SpecialTemplate` mediumtext,
  `Sort` int(11) NOT NULL DEFAULT '0',
  `Version` int(11) NOT NULL DEFAULT '0',
  `PageID` int(11) DEFAULT NULL,
  `DesktopImageID` int(11) DEFAULT NULL,
  `TabletImageID` int(11) DEFAULT NULL,
  `MobileImageID` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `PageID` (`PageID`),
  KEY `DesktopImageID` (`DesktopImageID`),
  KEY `TabletImageID` (`TabletImageID`),
  KEY `MobileImageID` (`MobileImageID`),
  KEY `ClassName` (`ClassName`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `PageContentItem_Live`
--

CREATE TABLE IF NOT EXISTS `PageContentItem_Live` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `ClassName` enum('PageContentItem') DEFAULT 'PageContentItem',
  `Created` datetime DEFAULT NULL,
  `LastEdited` datetime DEFAULT NULL,
  `Title` mediumtext,
  `Link` mediumtext,
  `Content` mediumtext,
  `ExtraClasses` mediumtext,
  `SpecialTemplate` mediumtext,
  `Sort` int(11) NOT NULL DEFAULT '0',
  `Version` int(11) NOT NULL DEFAULT '0',
  `PageID` int(11) DEFAULT NULL,
  `DesktopImageID` int(11) DEFAULT NULL,
  `TabletImageID` int(11) DEFAULT NULL,
  `MobileImageID` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `PageID` (`PageID`),
  KEY `DesktopImageID` (`DesktopImageID`),
  KEY `TabletImageID` (`TabletImageID`),
  KEY `MobileImageID` (`MobileImageID`),
  KEY `ClassName` (`ClassName`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `PageContentItem_versions`
--

CREATE TABLE IF NOT EXISTS `PageContentItem_versions` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `RecordID` int(11) NOT NULL DEFAULT '0',
  `Version` int(11) NOT NULL DEFAULT '0',
  `WasPublished` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `AuthorID` int(11) NOT NULL DEFAULT '0',
  `PublisherID` int(11) NOT NULL DEFAULT '0',
  `ClassName` enum('PageContentItem') DEFAULT 'PageContentItem',
  `Created` datetime DEFAULT NULL,
  `LastEdited` datetime DEFAULT NULL,
  `Title` mediumtext,
  `Link` mediumtext,
  `Content` mediumtext,
  `ExtraClasses` mediumtext,
  `SpecialTemplate` mediumtext,
  `Sort` int(11) NOT NULL DEFAULT '0',
  `PageID` int(11) DEFAULT NULL,
  `DesktopImageID` int(11) DEFAULT NULL,
  `TabletImageID` int(11) DEFAULT NULL,
  `MobileImageID` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `RecordID_Version` (`RecordID`,`Version`),
  KEY `RecordID` (`RecordID`),
  KEY `Version` (`Version`),
  KEY `AuthorID` (`AuthorID`),
  KEY `PublisherID` (`PublisherID`),
  KEY `PageID` (`PageID`),
  KEY `DesktopImageID` (`DesktopImageID`),
  KEY `TabletImageID` (`TabletImageID`),
  KEY `MobileImageID` (`MobileImageID`),
  KEY `ClassName` (`ClassName`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `PageLabel`
--

CREATE TABLE IF NOT EXISTS `PageLabel` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `ClassName` enum('PageLabel','PageCategory','PageTag') DEFAULT 'PageLabel',
  `Created` datetime DEFAULT NULL,
  `LastEdited` datetime DEFAULT NULL,
  `Title` mediumtext,
  `TemplateName` mediumtext,
  `URLName` mediumtext,
  PRIMARY KEY (`ID`),
  KEY `ClassName` (`ClassName`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `Page_Categories`
--

CREATE TABLE IF NOT EXISTS `Page_Categories` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `PageID` int(11) NOT NULL DEFAULT '0',
  `PageCategoryID` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `PageID` (`PageID`),
  KEY `PageCategoryID` (`PageCategoryID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `Page_Live`
--

CREATE TABLE IF NOT EXISTS `Page_Live` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `ScheduledPublish` datetime DEFAULT NULL,
  `ScheduledUnpublish` datetime DEFAULT NULL,
  `PublicTimestamp` datetime DEFAULT NULL,
  `MetaLabels` mediumtext,
  `SummaryTitle` mediumtext,
  `SummaryContent` mediumtext,
  `SummaryThumbnailID` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `SummaryThumbnailID` (`SummaryThumbnailID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `Page_Tags`
--

CREATE TABLE IF NOT EXISTS `Page_Tags` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `PageID` int(11) NOT NULL DEFAULT '0',
  `PageTagID` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `PageID` (`PageID`),
  KEY `PageTagID` (`PageTagID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `Page_versions`
--

CREATE TABLE IF NOT EXISTS `Page_versions` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `RecordID` int(11) NOT NULL DEFAULT '0',
  `Version` int(11) NOT NULL DEFAULT '0',
  `ScheduledPublish` datetime DEFAULT NULL,
  `ScheduledUnpublish` datetime DEFAULT NULL,
  `PublicTimestamp` datetime DEFAULT NULL,
  `MetaLabels` mediumtext,
  `SummaryTitle` mediumtext,
  `SummaryContent` mediumtext,
  `SummaryThumbnailID` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `RecordID_Version` (`RecordID`,`Version`),
  KEY `RecordID` (`RecordID`),
  KEY `Version` (`Version`),
  KEY `SummaryThumbnailID` (`SummaryThumbnailID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `Permission`
--

CREATE TABLE IF NOT EXISTS `Permission` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `ClassName` enum('Permission') DEFAULT 'Permission',
  `Created` datetime DEFAULT NULL,
  `LastEdited` datetime DEFAULT NULL,
  `Code` varchar(50) DEFAULT NULL,
  `Arg` int(11) NOT NULL DEFAULT '0',
  `Type` int(11) NOT NULL DEFAULT '1',
  `GroupID` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `GroupID` (`GroupID`),
  KEY `Code` (`Code`),
  KEY `ClassName` (`ClassName`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `Permission`
--

INSERT INTO `Permission` (`ID`, `ClassName`, `Created`, `LastEdited`, `Code`, `Arg`, `Type`, `GroupID`) VALUES
(1, 'Permission', '2013-01-17 15:08:36', '2013-01-17 15:08:36', 'CMS_ACCESS_CMSMain', 0, 1, 1),
(2, 'Permission', '2013-01-17 15:08:36', '2013-01-17 15:08:36', 'CMS_ACCESS_AssetAdmin', 0, 1, 1),
(3, 'Permission', '2013-01-17 15:08:36', '2013-01-17 15:08:36', 'CMS_ACCESS_ReportAdmin', 0, 1, 1),
(4, 'Permission', '2013-01-17 15:08:36', '2013-01-17 15:08:36', 'SITETREE_REORGANISE', 0, 1, 1),
(5, 'Permission', '2013-01-17 15:08:36', '2013-01-17 15:08:36', 'ADMIN', 0, 1, 2);

-- --------------------------------------------------------

--
-- Table structure for table `PermissionRole`
--

CREATE TABLE IF NOT EXISTS `PermissionRole` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `ClassName` enum('PermissionRole') DEFAULT 'PermissionRole',
  `Created` datetime DEFAULT NULL,
  `LastEdited` datetime DEFAULT NULL,
  `Title` varchar(50) DEFAULT NULL,
  `OnlyAdminCanApply` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `ClassName` (`ClassName`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `PermissionRoleCode`
--

CREATE TABLE IF NOT EXISTS `PermissionRoleCode` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `ClassName` enum('PermissionRoleCode') DEFAULT 'PermissionRoleCode',
  `Created` datetime DEFAULT NULL,
  `LastEdited` datetime DEFAULT NULL,
  `Code` varchar(50) DEFAULT NULL,
  `RoleID` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `RoleID` (`RoleID`),
  KEY `ClassName` (`ClassName`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `RedirectorPage`
--

CREATE TABLE IF NOT EXISTS `RedirectorPage` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `RedirectionType` enum('Internal','External') DEFAULT 'Internal',
  `ExternalURL` varchar(2083) DEFAULT NULL,
  `LinkToID` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `LinkToID` (`LinkToID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `RedirectorPage_Live`
--

CREATE TABLE IF NOT EXISTS `RedirectorPage_Live` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `RedirectionType` enum('Internal','External') DEFAULT 'Internal',
  `ExternalURL` varchar(2083) DEFAULT NULL,
  `LinkToID` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `LinkToID` (`LinkToID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `RedirectorPage_versions`
--

CREATE TABLE IF NOT EXISTS `RedirectorPage_versions` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `RecordID` int(11) NOT NULL DEFAULT '0',
  `Version` int(11) NOT NULL DEFAULT '0',
  `RedirectionType` enum('Internal','External') DEFAULT 'Internal',
  `ExternalURL` varchar(2083) DEFAULT NULL,
  `LinkToID` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `RecordID_Version` (`RecordID`,`Version`),
  KEY `RecordID` (`RecordID`),
  KEY `Version` (`Version`),
  KEY `LinkToID` (`LinkToID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ScheduledJob`
--

CREATE TABLE IF NOT EXISTS `ScheduledJob` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `ClassName` enum('ScheduledJob') DEFAULT 'ScheduledJob',
  `Created` datetime DEFAULT NULL,
  `LastEdited` datetime DEFAULT NULL,
  `Task` mediumtext,
  `GetParams` mediumtext,
  `PostParams` mediumtext,
  `Uniqid` mediumtext,
  `Scheduled` datetime DEFAULT NULL,
  `Completed` datetime DEFAULT NULL,
  `Failed` datetime DEFAULT NULL,
  `Repeats` int(11) NOT NULL DEFAULT '0',
  `Reschedule` int(11) NOT NULL DEFAULT '0',
  `OwnerID` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `OwnerID` (`OwnerID`),
  KEY `ClassName` (`ClassName`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `SiteConfig`
--

CREATE TABLE IF NOT EXISTS `SiteConfig` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `ClassName` enum('SiteConfig') DEFAULT 'SiteConfig',
  `Created` datetime DEFAULT NULL,
  `LastEdited` datetime DEFAULT NULL,
  `Title` varchar(255) DEFAULT NULL,
  `Tagline` varchar(255) DEFAULT NULL,
  `Theme` varchar(255) DEFAULT NULL,
  `CanViewType` enum('Anyone','LoggedInUsers','OnlyTheseUsers') DEFAULT 'Anyone',
  `CanEditType` enum('LoggedInUsers','OnlyTheseUsers') DEFAULT 'LoggedInUsers',
  `CanCreateTopLevelType` enum('LoggedInUsers','OnlyTheseUsers') DEFAULT 'LoggedInUsers',
  PRIMARY KEY (`ID`),
  KEY `ClassName` (`ClassName`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `SiteConfig`
--

INSERT INTO `SiteConfig` (`ID`, `ClassName`, `Created`, `LastEdited`, `Title`, `Tagline`, `Theme`, `CanViewType`, `CanEditType`, `CanCreateTopLevelType`) VALUES
(1, 'SiteConfig', '2013-01-17 15:08:37', '2013-01-17 15:08:37', 'Your Site Name', 'your tagline here', NULL, 'Anyone', 'LoggedInUsers', 'LoggedInUsers');

-- --------------------------------------------------------

--
-- Table structure for table `SiteConfig_CreateTopLevelGroups`
--

CREATE TABLE IF NOT EXISTS `SiteConfig_CreateTopLevelGroups` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `SiteConfigID` int(11) NOT NULL DEFAULT '0',
  `GroupID` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `SiteConfigID` (`SiteConfigID`),
  KEY `GroupID` (`GroupID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `SiteConfig_EditorGroups`
--

CREATE TABLE IF NOT EXISTS `SiteConfig_EditorGroups` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `SiteConfigID` int(11) NOT NULL DEFAULT '0',
  `GroupID` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `SiteConfigID` (`SiteConfigID`),
  KEY `GroupID` (`GroupID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `SiteConfig_ViewerGroups`
--

CREATE TABLE IF NOT EXISTS `SiteConfig_ViewerGroups` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `SiteConfigID` int(11) NOT NULL DEFAULT '0',
  `GroupID` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `SiteConfigID` (`SiteConfigID`),
  KEY `GroupID` (`GroupID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `SiteTree`
--

CREATE TABLE IF NOT EXISTS `SiteTree` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `ClassName` enum('Page','ErrorPage','SiteTree','RedirectorPage','VirtualPage','PageAggregate','LiteralRedirectorPage') DEFAULT 'Page',
  `Created` datetime DEFAULT NULL,
  `LastEdited` datetime DEFAULT NULL,
  `URLSegment` varchar(255) DEFAULT NULL,
  `Title` varchar(255) DEFAULT NULL,
  `MenuTitle` varchar(100) DEFAULT NULL,
  `Content` mediumtext,
  `MetaTitle` varchar(255) DEFAULT NULL,
  `MetaDescription` mediumtext,
  `MetaKeywords` varchar(1024) DEFAULT NULL,
  `ExtraMeta` mediumtext,
  `ShowInMenus` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `ShowInSearch` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `Sort` int(11) NOT NULL DEFAULT '0',
  `HasBrokenFile` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `HasBrokenLink` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `ReportClass` varchar(50) DEFAULT NULL,
  `CanViewType` enum('Anyone','LoggedInUsers','OnlyTheseUsers','Inherit') DEFAULT 'Inherit',
  `CanEditType` enum('LoggedInUsers','OnlyTheseUsers','Inherit') DEFAULT 'Inherit',
  `Version` int(11) NOT NULL DEFAULT '0',
  `ParentID` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `ParentID` (`ParentID`),
  KEY `URLSegment` (`URLSegment`),
  KEY `ClassName` (`ClassName`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `SiteTree`
--

INSERT INTO `SiteTree` (`ID`, `ClassName`, `Created`, `LastEdited`, `URLSegment`, `Title`, `MenuTitle`, `Content`, `MetaTitle`, `MetaDescription`, `MetaKeywords`, `ExtraMeta`, `ShowInMenus`, `ShowInSearch`, `Sort`, `HasBrokenFile`, `HasBrokenLink`, `ReportClass`, `CanViewType`, `CanEditType`, `Version`, `ParentID`) VALUES
(1, 'Page', '2013-01-17 15:08:37', '2013-01-17 15:08:40', 'home', 'Home', NULL, '<p>Welcome to SilverStripe! This is the default homepage. You can edit this page by opening <a href="admin/">the CMS</a>. You can now access the <a href="http://doc.silverstripe.org">developer documentation</a>, or begin <a href="http://doc.silverstripe.org/doku.php?id=tutorials">the tutorials</a>.</p>', NULL, NULL, NULL, NULL, 1, 1, 1, 0, 0, NULL, 'Inherit', 'Inherit', 1, 0),
(2, 'Page', '2013-01-17 15:08:37', '2013-01-17 15:08:40', 'about-us', 'About Us', NULL, '<p>You can fill this page out with your own content, or delete it and create your own pages.<br></p>', NULL, NULL, NULL, NULL, 1, 1, 2, 0, 0, NULL, 'Inherit', 'Inherit', 1, 0),
(3, 'Page', '2013-01-17 15:08:38', '2013-01-17 15:08:40', 'contact-us', 'Contact Us', NULL, '<p>You can fill this page out with your own content, or delete it and create your own pages.<br></p>', NULL, NULL, NULL, NULL, 1, 1, 3, 0, 0, NULL, 'Inherit', 'Inherit', 1, 0),
(4, 'ErrorPage', '2013-01-17 15:08:38', '2013-01-17 15:08:40', 'page-not-found', 'Page not found', NULL, '<p>Sorry, it seems you were trying to access a page that doesn''t exist.</p><p>Please check the spelling of the URL you were trying to access and try again.</p>', NULL, NULL, NULL, NULL, 0, 0, 4, 0, 0, NULL, 'Inherit', 'Inherit', 1, 0),
(5, 'ErrorPage', '2013-01-17 15:08:39', '2013-01-17 15:08:40', 'server-error', 'Server error', NULL, '<p>Sorry, there was a problem with handling your request.</p>', NULL, NULL, NULL, NULL, 0, 0, 5, 0, 0, NULL, 'Inherit', 'Inherit', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `SiteTree_EditorGroups`
--

CREATE TABLE IF NOT EXISTS `SiteTree_EditorGroups` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `SiteTreeID` int(11) NOT NULL DEFAULT '0',
  `GroupID` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `SiteTreeID` (`SiteTreeID`),
  KEY `GroupID` (`GroupID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `SiteTree_ImageTracking`
--

CREATE TABLE IF NOT EXISTS `SiteTree_ImageTracking` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `SiteTreeID` int(11) NOT NULL DEFAULT '0',
  `FileID` int(11) NOT NULL DEFAULT '0',
  `FieldName` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `SiteTreeID` (`SiteTreeID`),
  KEY `FileID` (`FileID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `SiteTree_LinkTracking`
--

CREATE TABLE IF NOT EXISTS `SiteTree_LinkTracking` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `SiteTreeID` int(11) NOT NULL DEFAULT '0',
  `ChildID` int(11) NOT NULL DEFAULT '0',
  `FieldName` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `SiteTreeID` (`SiteTreeID`),
  KEY `ChildID` (`ChildID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `SiteTree_Live`
--

CREATE TABLE IF NOT EXISTS `SiteTree_Live` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `ClassName` enum('Page','ErrorPage','SiteTree','RedirectorPage','VirtualPage','PageAggregate','LiteralRedirectorPage') DEFAULT 'Page',
  `Created` datetime DEFAULT NULL,
  `LastEdited` datetime DEFAULT NULL,
  `URLSegment` varchar(255) DEFAULT NULL,
  `Title` varchar(255) DEFAULT NULL,
  `MenuTitle` varchar(100) DEFAULT NULL,
  `Content` mediumtext,
  `MetaTitle` varchar(255) DEFAULT NULL,
  `MetaDescription` mediumtext,
  `MetaKeywords` varchar(1024) DEFAULT NULL,
  `ExtraMeta` mediumtext,
  `ShowInMenus` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `ShowInSearch` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `Sort` int(11) NOT NULL DEFAULT '0',
  `HasBrokenFile` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `HasBrokenLink` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `ReportClass` varchar(50) DEFAULT NULL,
  `CanViewType` enum('Anyone','LoggedInUsers','OnlyTheseUsers','Inherit') DEFAULT 'Inherit',
  `CanEditType` enum('LoggedInUsers','OnlyTheseUsers','Inherit') DEFAULT 'Inherit',
  `Version` int(11) NOT NULL DEFAULT '0',
  `ParentID` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `ParentID` (`ParentID`),
  KEY `URLSegment` (`URLSegment`),
  KEY `ClassName` (`ClassName`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `SiteTree_Live`
--

INSERT INTO `SiteTree_Live` (`ID`, `ClassName`, `Created`, `LastEdited`, `URLSegment`, `Title`, `MenuTitle`, `Content`, `MetaTitle`, `MetaDescription`, `MetaKeywords`, `ExtraMeta`, `ShowInMenus`, `ShowInSearch`, `Sort`, `HasBrokenFile`, `HasBrokenLink`, `ReportClass`, `CanViewType`, `CanEditType`, `Version`, `ParentID`) VALUES
(1, 'Page', '2013-01-17 15:08:37', '2013-01-17 15:08:37', 'home', 'Home', NULL, '<p>Welcome to SilverStripe! This is the default homepage. You can edit this page by opening <a href="admin/">the CMS</a>. You can now access the <a href="http://doc.silverstripe.org">developer documentation</a>, or begin <a href="http://doc.silverstripe.org/doku.php?id=tutorials">the tutorials</a>.</p>', NULL, NULL, NULL, NULL, 1, 1, 1, 0, 0, NULL, 'Inherit', 'Inherit', 1, 0),
(2, 'Page', '2013-01-17 15:08:37', '2013-01-17 15:08:37', 'about-us', 'About Us', NULL, '<p>You can fill this page out with your own content, or delete it and create your own pages.<br></p>', NULL, NULL, NULL, NULL, 1, 1, 2, 0, 0, NULL, 'Inherit', 'Inherit', 1, 0),
(3, 'Page', '2013-01-17 15:08:38', '2013-01-17 15:08:38', 'contact-us', 'Contact Us', NULL, '<p>You can fill this page out with your own content, or delete it and create your own pages.<br></p>', NULL, NULL, NULL, NULL, 1, 1, 3, 0, 0, NULL, 'Inherit', 'Inherit', 1, 0),
(4, 'ErrorPage', '2013-01-17 15:08:38', '2013-01-17 15:08:49', 'page-not-found', 'Page not found', NULL, '<p>Sorry, it seems you were trying to access a page that doesn''t exist.</p><p>Please check the spelling of the URL you were trying to access and try again.</p>', NULL, NULL, NULL, NULL, 0, 0, 4, 0, 0, NULL, 'Inherit', 'Inherit', 1, 0),
(5, 'ErrorPage', '2013-01-17 15:08:39', '2013-01-17 15:08:39', 'server-error', 'Server error', NULL, '<p>Sorry, there was a problem with handling your request.</p>', NULL, NULL, NULL, NULL, 0, 0, 5, 0, 0, NULL, 'Inherit', 'Inherit', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `SiteTree_versions`
--

CREATE TABLE IF NOT EXISTS `SiteTree_versions` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `RecordID` int(11) NOT NULL DEFAULT '0',
  `Version` int(11) NOT NULL DEFAULT '0',
  `WasPublished` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `AuthorID` int(11) NOT NULL DEFAULT '0',
  `PublisherID` int(11) NOT NULL DEFAULT '0',
  `ClassName` enum('Page','ErrorPage','SiteTree','RedirectorPage','VirtualPage','PageAggregate','LiteralRedirectorPage') DEFAULT 'Page',
  `Created` datetime DEFAULT NULL,
  `LastEdited` datetime DEFAULT NULL,
  `URLSegment` varchar(255) DEFAULT NULL,
  `Title` varchar(255) DEFAULT NULL,
  `MenuTitle` varchar(100) DEFAULT NULL,
  `Content` mediumtext,
  `MetaTitle` varchar(255) DEFAULT NULL,
  `MetaDescription` mediumtext,
  `MetaKeywords` varchar(1024) DEFAULT NULL,
  `ExtraMeta` mediumtext,
  `ShowInMenus` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `ShowInSearch` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `Sort` int(11) NOT NULL DEFAULT '0',
  `HasBrokenFile` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `HasBrokenLink` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `ReportClass` varchar(50) DEFAULT NULL,
  `CanViewType` enum('Anyone','LoggedInUsers','OnlyTheseUsers','Inherit') DEFAULT 'Inherit',
  `CanEditType` enum('LoggedInUsers','OnlyTheseUsers','Inherit') DEFAULT 'Inherit',
  `ParentID` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `RecordID_Version` (`RecordID`,`Version`),
  KEY `RecordID` (`RecordID`),
  KEY `Version` (`Version`),
  KEY `AuthorID` (`AuthorID`),
  KEY `PublisherID` (`PublisherID`),
  KEY `ParentID` (`ParentID`),
  KEY `URLSegment` (`URLSegment`),
  KEY `ClassName` (`ClassName`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `SiteTree_versions`
--

INSERT INTO `SiteTree_versions` (`ID`, `RecordID`, `Version`, `WasPublished`, `AuthorID`, `PublisherID`, `ClassName`, `Created`, `LastEdited`, `URLSegment`, `Title`, `MenuTitle`, `Content`, `MetaTitle`, `MetaDescription`, `MetaKeywords`, `ExtraMeta`, `ShowInMenus`, `ShowInSearch`, `Sort`, `HasBrokenFile`, `HasBrokenLink`, `ReportClass`, `CanViewType`, `CanEditType`, `ParentID`) VALUES
(1, 1, 1, 1, 0, 0, 'Page', '2013-01-17 15:08:37', '2013-01-17 15:08:37', 'home', 'Home', NULL, '<p>Welcome to SilverStripe! This is the default homepage. You can edit this page by opening <a href="admin/">the CMS</a>. You can now access the <a href="http://doc.silverstripe.org">developer documentation</a>, or begin <a href="http://doc.silverstripe.org/doku.php?id=tutorials">the tutorials</a>.</p>', NULL, NULL, NULL, NULL, 1, 1, 1, 0, 0, NULL, 'Inherit', 'Inherit', 0),
(2, 2, 1, 1, 0, 0, 'Page', '2013-01-17 15:08:37', '2013-01-17 15:08:37', 'about-us', 'About Us', NULL, '<p>You can fill this page out with your own content, or delete it and create your own pages.<br></p>', NULL, NULL, NULL, NULL, 1, 1, 2, 0, 0, NULL, 'Inherit', 'Inherit', 0),
(3, 3, 1, 1, 0, 0, 'Page', '2013-01-17 15:08:38', '2013-01-17 15:08:38', 'contact-us', 'Contact Us', NULL, '<p>You can fill this page out with your own content, or delete it and create your own pages.<br></p>', NULL, NULL, NULL, NULL, 1, 1, 3, 0, 0, NULL, 'Inherit', 'Inherit', 0),
(4, 4, 1, 1, 0, 0, 'ErrorPage', '2013-01-17 15:08:38', '2013-01-17 15:08:38', 'page-not-found', 'Page not found', NULL, '<p>Sorry, it seems you were trying to access a page that doesn''t exist.</p><p>Please check the spelling of the URL you were trying to access and try again.</p>', NULL, NULL, NULL, NULL, 0, 0, 4, 0, 0, NULL, 'Inherit', 'Inherit', 0),
(5, 5, 1, 1, 0, 0, 'ErrorPage', '2013-01-17 15:08:39', '2013-01-17 15:08:39', 'server-error', 'Server error', NULL, '<p>Sorry, there was a problem with handling your request.</p>', NULL, NULL, NULL, NULL, 0, 0, 5, 0, 0, NULL, 'Inherit', 'Inherit', 0);

-- --------------------------------------------------------

--
-- Table structure for table `SiteTree_ViewerGroups`
--

CREATE TABLE IF NOT EXISTS `SiteTree_ViewerGroups` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `SiteTreeID` int(11) NOT NULL DEFAULT '0',
  `GroupID` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `SiteTreeID` (`SiteTreeID`),
  KEY `GroupID` (`GroupID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `VirtualPage`
--

CREATE TABLE IF NOT EXISTS `VirtualPage` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `VersionID` int(11) NOT NULL DEFAULT '0',
  `CopyContentFromID` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `CopyContentFromID` (`CopyContentFromID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `VirtualPage_Live`
--

CREATE TABLE IF NOT EXISTS `VirtualPage_Live` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `VersionID` int(11) NOT NULL DEFAULT '0',
  `CopyContentFromID` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `CopyContentFromID` (`CopyContentFromID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `VirtualPage_versions`
--

CREATE TABLE IF NOT EXISTS `VirtualPage_versions` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `RecordID` int(11) NOT NULL DEFAULT '0',
  `Version` int(11) NOT NULL DEFAULT '0',
  `VersionID` int(11) NOT NULL DEFAULT '0',
  `CopyContentFromID` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `RecordID_Version` (`RecordID`,`Version`),
  KEY `RecordID` (`RecordID`),
  KEY `Version` (`Version`),
  KEY `CopyContentFromID` (`CopyContentFromID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `ErrorPage`
--
ALTER TABLE `ErrorPage`
  ADD CONSTRAINT `871c71e7044a5b2d6a6c01a75e5a5e67` FOREIGN KEY (`ID`) REFERENCES `SiteTree` (`ID`) ON DELETE CASCADE;

--
-- Constraints for table `ErrorPage_Live`
--
ALTER TABLE `ErrorPage_Live`
  ADD CONSTRAINT `791067439b76df4edf3290a46af8fb63` FOREIGN KEY (`ID`) REFERENCES `SiteTree_Live` (`ID`) ON DELETE CASCADE;

--
-- Constraints for table `LiteralRedirectorPage`
--
ALTER TABLE `LiteralRedirectorPage`
  ADD CONSTRAINT `5fbca8249035655c37634581f7f32ae8` FOREIGN KEY (`ID`) REFERENCES `SiteTree` (`ID`) ON DELETE CASCADE;

--
-- Constraints for table `LiteralRedirectorPage_Live`
--
ALTER TABLE `LiteralRedirectorPage_Live`
  ADD CONSTRAINT `2d6ddaf8567f3ddf7e6a52386ddc7962` FOREIGN KEY (`ID`) REFERENCES `SiteTree_Live` (`ID`) ON DELETE CASCADE;

--
-- Constraints for table `Page`
--
ALTER TABLE `Page`
  ADD CONSTRAINT `b804587d9baeb05ea3994d9b3a40014c` FOREIGN KEY (`ID`) REFERENCES `SiteTree` (`ID`) ON DELETE CASCADE;

--
-- Constraints for table `PageAggregate`
--
ALTER TABLE `PageAggregate`
  ADD CONSTRAINT `3183a9de98add0570f4531af2639145c` FOREIGN KEY (`ID`) REFERENCES `SiteTree` (`ID`) ON DELETE CASCADE;

--
-- Constraints for table `PageAggregate_Live`
--
ALTER TABLE `PageAggregate_Live`
  ADD CONSTRAINT `3097229a436bbdaafe3eecad230ae93a` FOREIGN KEY (`ID`) REFERENCES `SiteTree_Live` (`ID`) ON DELETE CASCADE;

--
-- Constraints for table `PageContentItem`
--
ALTER TABLE `PageContentItem`
  ADD CONSTRAINT `a54c4ea68cea7b9535bcc30d2847c65c` FOREIGN KEY (`PageID`) REFERENCES `SiteTree` (`ID`) ON DELETE CASCADE;

--
-- Constraints for table `PageContentItem_Live`
--
ALTER TABLE `PageContentItem_Live`
  ADD CONSTRAINT `c1c73efd03aa8afbef7441cd2a45e024` FOREIGN KEY (`PageID`) REFERENCES `SiteTree_Live` (`ID`) ON DELETE CASCADE;

--
-- Constraints for table `Page_Live`
--
ALTER TABLE `Page_Live`
  ADD CONSTRAINT `001ae0d836a8473fe5cf0f75393213aa` FOREIGN KEY (`ID`) REFERENCES `SiteTree_Live` (`ID`) ON DELETE CASCADE;

--
-- Constraints for table `RedirectorPage`
--
ALTER TABLE `RedirectorPage`
  ADD CONSTRAINT `e2ae8dcff4805c0bc03522290cf31c0a` FOREIGN KEY (`ID`) REFERENCES `SiteTree` (`ID`) ON DELETE CASCADE;

--
-- Constraints for table `RedirectorPage_Live`
--
ALTER TABLE `RedirectorPage_Live`
  ADD CONSTRAINT `e9398e079c790e8af89a656a19e924f8` FOREIGN KEY (`ID`) REFERENCES `SiteTree_Live` (`ID`) ON DELETE CASCADE;

--
-- Constraints for table `VirtualPage`
--
ALTER TABLE `VirtualPage`
  ADD CONSTRAINT `abd88527c54d3a3ce37a39c0245d5df4` FOREIGN KEY (`ID`) REFERENCES `SiteTree` (`ID`) ON DELETE CASCADE;

--
-- Constraints for table `VirtualPage_Live`
--
ALTER TABLE `VirtualPage_Live`
  ADD CONSTRAINT `9605c749f9017c2eec61f15b753c684e` FOREIGN KEY (`ID`) REFERENCES `SiteTree_Live` (`ID`) ON DELETE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
