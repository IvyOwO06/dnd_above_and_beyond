-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 08, 2025 at 11:39 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dnm`
--

-- --------------------------------------------------------

--
-- Table structure for table `abilityscores`
--

CREATE TABLE `abilityscores` (
  `abilityScoreId` int(4) NOT NULL,
  `characterStr` int(4) NOT NULL,
  `characterDex` int(4) NOT NULL,
  `characterCon` int(4) NOT NULL,
  `characterInt` int(4) NOT NULL,
  `characterWis` int(4) NOT NULL,
  `proficiencyBonus` int(4) NOT NULL,
  `characterId` int(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `characters`
--

CREATE TABLE `characters` (
  `characterId` int(4) NOT NULL,
  `characterName` varchar(255) NOT NULL,
  `characterAge` int(4) NOT NULL,
  `raceId` int(4) NOT NULL,
  `classId` int(4) NOT NULL,
  `subClassId` int(4) NOT NULL,
  `alignmentId` int(4) NOT NULL,
  `backgroundId` int(4) NOT NULL,
  `level` int(4) NOT NULL,
  `backstory` text NOT NULL,
  `characterImage` varchar(255) NOT NULL,
  `userId` int(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `class`
--

CREATE TABLE `class` (
  `classId` int(4) NOT NULL,
  `className` varchar(255) NOT NULL,
  `classShortInformation` text NOT NULL,
  `classInformation` text NOT NULL,
  `classImage` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `class`
--

INSERT INTO `class` (`classId`, `className`, `classShortInformation`, `classInformation`, `classImage`) VALUES
(1, 'Barbarian', 'Barbarians are mighty warriors who are powered by primal forces of the multiverse that manifest as a Rage.', 'Barbarians are mighty warriors who are powered by primal forces of the multiverse that manifest as a Rage. More than a mere emotion—and not limited to anger—this Rage is an incarnation of a predator’s ferocity, a storm’s fury, and a sea’s turmoil.\r\n\r\nSome Barbarians personify their Rage as a fierce spirit or revered forebear. Others see it as a connection to the pain and anguish of the world, as an impersonal tangle of wild magic, or as an expression of their own deepest self. For every Barbarian, their Rage is a power that fuels not just battle prowess, but also uncanny reflexes and heightened senses.\r\n\r\nBarbarians often serve as protectors and leaders in their communities. They charge headlong into danger so those under their protection don’t have to. Their courage in the face of danger makes Barbarians perfectly suited for adventure.', 'images/classDummyImage.png'),
(2, 'Bard', 'Bards are expert at inspiring others, soothing hurts, disheartening foes, and creating illusions.', 'Invoking magic through music, dance, and verse, Bards are expert at inspiring others, soothing hurts, disheartening foes, and creating illusions. Bards believe the multiverse was spoken into existence and that remnants of its Words of Creation still resound and glimmer on every plane of existence. Bardic magic attempts to harness those words, which transcend any language.\r\n\r\nAnything can inspire a new song or tale, so Bards are fascinated by almost everything. They become masters of many things, including performing music, working magic, and making jests.\r\n\r\nA Bard’s life is spent traveling, gathering lore, telling stories, and living on the gratitude of audiences, much like any other entertainer. But Bards’ depth of knowledge and mastery of magic sets them apart.', 'images/classDummyImage.png');

-- --------------------------------------------------------

--
-- Table structure for table `race`
--

CREATE TABLE `race` (
  `raceId` int(4) NOT NULL,
  `raceName` varchar(255) NOT NULL,
  `raceShortInformation` text NOT NULL,
  `raceInformation` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `race`
--

INSERT INTO `race` (`raceId`, `raceName`, `raceShortInformation`, `raceInformation`) VALUES
(1, 'Dragonborn', 'Dragonborn look very much like dragons standing erect in humanoid form, though they lack wings or a tail.', 'Born of dragons, as their name proclaims, the dragonborn walk proudly through a world that greets them with fearful incomprehension. Shaped by draconic gods or the dragons themselves, dragonborn originally hatched from dragon eggs as a unique race, combining the best attributes of dragons and humanoids. Some dragonborn are faithful servants to true dragons, others form the ranks of soldiers in great wars, and still others find themselves adrift, with no clear calling in life.'),
(2, 'Dwarf', 'Bold and hardy, dwarves are known as skilled warriors, miners, and workers of stone and metal.', 'Kingdoms rich in ancient grandeur, halls carved into the roots of mountains, the echoing of picks and hammers in deep mines and blazing forges, a commitment to clan and tradition, and a burning hatred of goblins and orcs—these common threads unite all dwarves.\n\nAnything can inspire a new song or tale, so Bards are fascinated by almost everything. They become masters of many things, including performing music, working magic, and making jests.\n\nA Bard’s life is spent traveling, gathering lore, telling stories, and living on the gratitude of audiences, much like any other entertainer. But Bards’ depth of knowledge and mastery of magic sets them apart.');

-- --------------------------------------------------------

--
-- Table structure for table `racebonus`
--

CREATE TABLE `racebonus` (
  `raceBonusId` int(4) NOT NULL,
  `raceBonusName` varchar(255) NOT NULL,
  `raceBonusInformation` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `racetraits`
--

CREATE TABLE `racetraits` (
  `raceTraitId` int(4) NOT NULL,
  `raceTraitName` varchar(255) NOT NULL,
  `raceTraitInformation` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `userId` int(4) NOT NULL,
  `userName` varchar(255) NOT NULL,
  `mail` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profilePicture` varchar(255) NOT NULL,
  `profileInformation` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `abilityscores`
--
ALTER TABLE `abilityscores`
  ADD PRIMARY KEY (`abilityScoreId`);

--
-- Indexes for table `characters`
--
ALTER TABLE `characters`
  ADD PRIMARY KEY (`characterId`);

--
-- Indexes for table `class`
--
ALTER TABLE `class`
  ADD PRIMARY KEY (`classId`);

--
-- Indexes for table `race`
--
ALTER TABLE `race`
  ADD PRIMARY KEY (`raceId`);

--
-- Indexes for table `racebonus`
--
ALTER TABLE `racebonus`
  ADD PRIMARY KEY (`raceBonusId`);

--
-- Indexes for table `racetraits`
--
ALTER TABLE `racetraits`
  ADD PRIMARY KEY (`raceTraitId`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`userId`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `abilityscores`
--
ALTER TABLE `abilityscores`
  MODIFY `abilityScoreId` int(4) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `characters`
--
ALTER TABLE `characters`
  MODIFY `characterId` int(4) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `class`
--
ALTER TABLE `class`
  MODIFY `classId` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `race`
--
ALTER TABLE `race`
  MODIFY `raceId` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `racebonus`
--
ALTER TABLE `racebonus`
  MODIFY `raceBonusId` int(4) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `racetraits`
--
ALTER TABLE `racetraits`
  MODIFY `raceTraitId` int(4) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `userId` int(4) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
