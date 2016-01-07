-- phpMyAdmin SQL Dump
-- version 4.1.14.8
-- http://www.phpmyadmin.net
--
-- Host: wedding.db.example.com
-- Server version: 5.1.73-log
-- PHP Version: 5.4.45-0+deb7u2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `wedding`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendees`
--

DROP TABLE IF EXISTS `attendees`;
CREATE TABLE IF NOT EXISTS `attendees` (
  `attendeeID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `userID` mediumint(8) unsigned NOT NULL,
  `displayName` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `isPlusOne` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `isAttending` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `isInfant` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `isChild` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`attendeeID`),
  KEY `userID` (`userID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci PACK_KEYS=0 AUTO_INCREMENT=8 ;

INSERT INTO `attendees` (`attendeeID`, `userID`, `displayName`, `isPlusOne`, `isAttending`, `isInfant`, `isChild`) VALUES
(1, 1, 'Administrator', 0, 1, 0, 0),
(2, 2, 'Jane Doe', 0, 0, 0, 0),
(3, 2, 'John Doe', 0, 0, 0, 0),
(4, 2, 'Child Doe', 0, 0, 0, 0),
(5, 2, 'Baby Doe', 0, 0, 0, 0),
(6, 3, 'Jack Smith', 0, 1, 0, 0),
(7, 3, 'Guest', 1, 1, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `cartItems`
--

DROP TABLE IF EXISTS `cartItems`;
CREATE TABLE IF NOT EXISTS `cartItems` (
  `cartID` smallint(5) unsigned NOT NULL,
  `inventoryID` smallint(5) unsigned NOT NULL,
  `qty` mediumint(9) NOT NULL,
  `timeAdded` datetime DEFAULT NULL,
  PRIMARY KEY (`cartID`,`inventoryID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `carts`
--

DROP TABLE IF EXISTS `carts`;
CREATE TABLE IF NOT EXISTS `carts` (
  `cartID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `userID` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`cartID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `inventoryItems`
--

DROP TABLE IF EXISTS `inventoryItems`;
CREATE TABLE IF NOT EXISTS `inventoryItems` (
  `inventoryID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `storeItemID` smallint(5) unsigned NOT NULL,
  `qty` smallint(5) unsigned NOT NULL,
  `qtyMax` smallint(5) unsigned DEFAULT '0',
  `orderByNum` mediumint(5) NOT NULL,
  PRIMARY KEY (`inventoryID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=23 ;

INSERT INTO `inventoryItems` (`inventoryID`, `storeItemID`, `qty`, `qtyMax`, `orderByNum`) VALUES
(4, 4, 35, 40, 400),
(5, 5, 0, 4, 500),
(6, 6, 6, 7, 600),
(7, 7, 4, 5, 700),
(8, 8, 1, 2, 800),
(9, 9, 4, 7, 900),
(10, 10, 0, 1, 1000),
(11, 11, 0, 2, 1100),
(12, 12, 0, 2, 1200),
(13, 13, 0, 1, 1300),
(14, 14, 0, 2, 1400),
(15, 15, 0, 1, 1500),
(16, 16, 0, 1, 1600),
(17, 17, 5, 7, 1700),
(18, 18, 4, 7, 1800),
(19, 19, 16, 16, 1900),
(20, 20, 2, 2, 2000),
(21, 21, 20, 20, 2100),
(22, 22, 3, 3, 550);
-- --------------------------------------------------------

--
-- Table structure for table `storeItems`
--

DROP TABLE IF EXISTS `storeItems`;
CREATE TABLE IF NOT EXISTS `storeItems` (
  `storeItemID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(254) COLLATE latin1_general_ci NOT NULL,
  `description` text COLLATE latin1_general_ci,
  `basePrice` int(11) NOT NULL,
  `imageFilename` varchar(254) COLLATE latin1_general_ci NOT NULL,
  `caption` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  PRIMARY KEY (`storeItemID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=23 ;

INSERT INTO `storeItems` (`storeItemID`, `title`, `description`, `basePrice`, `imageFilename`, `caption`) VALUES
(4, 'Airfare', 'A recent study has shown that flying is a much more efficient way to travel to many international destinations than walking would be. Don''t worry, we won''t ask any of you to pay for the whole ticket. But, if enough people pitch in, we can pretend that we got lots of $50 plane tickets, instead of just two far more expensive ones. And we''ll get there with enough time to enjoy ourselves before having to come back!', 5000, 'airfare.jpg', 'Stunt plane, not our plane'),
(5, 'A Night At The Hotel', 'Even though we''ll be busy exploring the jungle and doing awesome things, we''ll still need somewhere to sleep (and shower).', 12500, 'midas.jpg', 'Quaint jungle bungalows'),
(6, 'A Wholesome Breakfast', 'The most important meal of the day, because this is what will fuel us while we''re hiking, snorkelling, zip-lining, or riding horses through the jungle. You wouldn''t want us to not eat a proper breakfast, then get lost in the jungle because we were too hungry to think straight, would you? Didn''t think so.', 2500, 'breakfast.png', 'The most important meal...love'),
(7, 'Lunch On The Go', 'We''ll find second breakfast and elevensies on our own if you buy us lunch. A casual stop as we''re between activities, we''ll probably be looking for hamburgers, tacos, or whatever fare is common down in the jungles.', 3000, 'lunch.jpg', 'Grab and go!'),
(8, 'A Picnic Lunch', 'Oh good, it looks like we actually planned ahead and packed a lunch for one of our full-day outings. Thanks for reminding us to bring it with us! (Hint: this would be perfect to pair with a hike or beach day excursion!)', 2500, 'picnic.jpg', 'Click to see what we''re eating!'),
(9, 'A Romantic Dinner', 'Breakfast may well be the most important meal of the day, but dinner is certainly the most extravagant. We''re on our honeymoon, so Calories don''t count (everyone knows this to be a universal truth). Which means we will be looking forward to enjoying not only tasty dinners, but plenty of sumptuous desserts! We''ll deal with the fallout after we''re back home.', 6000, 'dinner.jpg', 'What things should we try?'),
(10, 'A Jungle Trail Ride', 'We''ll be taking a ride along a trail that rambles through the jungles for an exciting and unique half-day excursion!', 15000, 'trail_ride.jpg', 'Horse riding expertise will be handy'),
(11, 'A Zipline Adventure', 'Could there be a more awesome way to experience the excitement of the jungle than by flying through the canopy between the trees like a bird in a harness? Help us get the parrot''s-eye-view of our honeymoon habitat with this adrenaline-filled half-day activity. (Note: this might be a good thing to add on some drinks or a bottle of wine to. We might need it to calm our nerves afterwards.)', 10000, 'zipline.jpg', 'WHEEEEEeeeeeeee.....'),
(12, 'Mayan Ruins Exploration', 'With this full-day guided excursion, we''ll be able to take a step back in time and explore the nearby ancient Mayan ruins. Wandering through ruined temples and cities slowly being reclaimed by the surrounding jungle is the stuff RPGs are made of! We promise we''ll try to stay out of any dungeon entrances, and definitely won''t read any ancient texts that could unleash horrible plagues or monsters on the world.', 15000, 'ruins.jpg', 'Archaeology and learning'),
(13, 'Hike To The Falls', 'A casual, self-guided hike back through the resort''s grounds, we''ll be hunting for waterfalls and secluded mineral hot springs. We''ll also be excited to see just how many amazing (and bizarre) tropical birds we can spot! This might not be a costly excursion, but it will be one that we talk about for years to come!', 5000, 'waterfall_hike.jpg', 'A favorite outdoor destination'),
(14, 'A Half-Day Snorkeling Excursion', 'Three hours sailing on beautiful tropical Caribbean waters and swimming with colorful fish and sea turtles... yes, this is paradise!', 7500, 'snorkeling.jpg', 'Which one is Dory?'),
(15, 'A Relaxing Day On The Beach', 'After our days of activity and adventuring, we''ll take a break and take it easy on the beach for a whole day. Nothing but sun, surf, and relaxation! While the beach doesn''t charge admission, this "excursion"''s cost will cover things like towel rentals, a cabana, sunscreen (important!), and a couple tropical drinks to set the mood.', 5000, 'beach.jpg', 'I hear the sun is better there?'),
(16, 'A Romantic Couple''s Massage', 'Not technically an excursion or an activity, we''ll sure be looking forward to a little relaxation after our jungle adventuring days! Help us get our chis in sync with a romantic, side-by-side massage on a jungle veranda.', 12500, 'massage.jpg', 'Relaxation: defined'),
(17, 'A Spot Of Coffee', 'With as much activity as we''ll be having, we''ll probably need something to keep us going between meals in the form of caffeinated beverages. Help out your favourite coffee-holics and buy us a cuppa!', 1000, 'coffee.jpg', 'Strong and dark'),
(18, 'A Bottle Of Wine', 'Wine just makes everything better. It makes whatever you''re doing more enjoyable, the mood more relaxed, the food taste better (we could go on)... Add on a bottle of wine to a dinner, a picnic, or order one up just because! We won''t mind!', 3000, 'wine.jpg', 'Earthy? Jammy? Tannic?'),
(19, 'Tropical Drinks', 'We imagine that we''ll probably spend at least one of our honeymoon days lounging around the poolside, admiring the gorgeous jungle views and taking advantage of the mineral hot springs. The only thing that could make a day like that more enjoyable is sipping fruity blended drinks out of either a coconut or hurricane glass with a little umbrella on it. You can make that happen!', 1500, 'drinks.jpg', 'Little umbrellas are essential!'),
(20, 'Snacks And Munchies', 'Stock our fridge! There will inevitably be some instance during our trip that one or both of us gets the munchies and it''s nowhere near mealtime. Thankfully, our rooms will be equipped with a small refrigerator where we can stash snacks and drinks in case of just that occurrence!', 2500, 'snacks.png', 'Om nom nom'),
(21, 'Build-Your-Own Gift', 'Don''t see something on this list that you''d like us to experience on our honeymoon? Well, by george, add it in! You can write in whatever you want us to do on your customized certificate. When you check out, please set your own price for this item.', 100, 'question.jpg', 'What do YOU think we should do?'),
(22, 'A Night At The Portofino Resort', 'After our romp through the jungles, we''ll be finishing out our honeymoon by spending a few days as beach bums. Buy a night for us in our private beach-front cabana on the island.', 15000, 'portofino.jpg', 'A few steps to the beach');


-- --------------------------------------------------------

--
-- Table structure for table `transactionItems`
--

DROP TABLE IF EXISTS `transactionItems`;
CREATE TABLE IF NOT EXISTS `transactionItems` (
  `transactionID` smallint(5) unsigned NOT NULL,
  `inventoryID` smallint(5) unsigned NOT NULL,
  `qty` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`transactionID`,`inventoryID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

DROP TABLE IF EXISTS `transactions`;
CREATE TABLE IF NOT EXISTS `transactions` (
  `transactionID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `userID` smallint(5) unsigned NOT NULL,
  `timeCheckedOut` datetime NOT NULL,
  PRIMARY KEY (`transactionID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `userID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(254) COLLATE latin1_general_ci NOT NULL,
  `isAdmin` tinyint(1) unsigned DEFAULT '0',
  `passwordPlaintext` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `name` varchar(254) COLLATE latin1_general_ci NOT NULL,
  `email` varchar(254) COLLATE latin1_general_ci DEFAULT NULL,
  `isRSVP` tinyint(1) unsigned DEFAULT '0',
  `lastRSVPTime` datetime DEFAULT NULL,
  `isThankYouSent` tinyint(1) unsigned DEFAULT '0',
  `address` text COLLATE latin1_general_ci,
  `gift` text COLLATE latin1_general_ci,
  `thankYouCardNotes` text COLLATE latin1_general_ci,
  `notesRSVP` text COLLATE latin1_general_ci,
  `lastLoginTime` datetime DEFAULT NULL,
  `password` binary(20) DEFAULT NULL,
  `isBridalShower` tinyint(1) unsigned DEFAULT '0',
  PRIMARY KEY (`userID`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci PACK_KEYS=0 AUTO_INCREMENT=4 ;

INSERT INTO `users` (`userID`, `username`, `isAdmin`, `passwordPlaintext`, `name`, `email`, `isRSVP`, `lastRSVPTime`, `isThankYouSent`, `address`, `gift`, `thankYouCardNotes`, `notesRSVP`, `lastLoginTime`, `password`, `isBridalShower`) VALUES
(1, 'admin', 1, NULL, 'Administrator', 'webmaster@example.com', 1, '2013-09-06 02:43:18', 0, NULL, NULL, NULL, '', '2016-01-06 21:47:07', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', 0),
(2, 'DoeFamily', 0, 'password', 'Doe Family', 'doe@example.com', 0, NULL, 0, '123 Pine St, City, CA, USA', NULL, NULL, '', NULL, '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', 0),
(3, 'JSmith', 0, 'password', 'Jack Smith', 'smith@example.com', 1, '2013-09-06 02:43:18', 0, '456 Street Pl., City, CA, USA', NULL, NULL, '', '2013-09-06 02:43:18', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', 0);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
