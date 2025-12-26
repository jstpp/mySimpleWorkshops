SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+01:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `workshops_db`
--

CREATE TABLE `courses` (
  `id` int(11) NOT NULL,
  `round_id` int(11) NOT NULL,
  `title` text NOT NULL,
  `leader` text NOT NULL,
  `max_seats` int(11) NOT NULL,
  `available_seats` int(11) NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `rounds` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL,
  `open_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `close_time` timestamp NULL DEFAULT NULL,
  `color1` text NOT NULL DEFAULT 'rgba(43,79,196,1)',
  `color2` text NOT NULL DEFAULT 'rgba(43,79,196,1)',
  `color3` text NOT NULL DEFAULT 'rgba(40,203,237,1)',
  `color_title` text NOT NULL DEFAULT 'white'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `signins` (
  `id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `user_id` text NOT NULL,
  `round_id` int(11) NOT NULL,
  `nameandsurname` text NOT NULL DEFAULT 'unknown'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `errors` (
  `id` int(11) NOT NULL,
  `error_code` text NOT NULL,
  `description` text NOT NULL DEFAULT 'unknown'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `round_id` (`round_id`);

ALTER TABLE `rounds`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `errors`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `signins`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`,`round_id`),
  ADD KEY `signin-round` (`round_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `errors`
--
ALTER TABLE `errors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `rounds`
--
ALTER TABLE `rounds`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `signins`
--
ALTER TABLE `signins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `courses`
--
ALTER TABLE `courses`
  ADD CONSTRAINT `course-round` FOREIGN KEY (`round_id`) REFERENCES `rounds` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `signins`
--
ALTER TABLE `signins`
  ADD CONSTRAINT `signin-course` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`),
  ADD CONSTRAINT `signin-round` FOREIGN KEY (`round_id`) REFERENCES `rounds` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
