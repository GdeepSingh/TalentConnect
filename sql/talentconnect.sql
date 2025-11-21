-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 21, 2025 at 07:04 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `talentconnect`
--

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

CREATE TABLE `companies` (
  `cid` int(11) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `industry` text NOT NULL,
  `hq_city` varchar(255) NOT NULL,
  `owner_name` varchar(255) NOT NULL,
  `owner_email` varchar(255) NOT NULL,
  `banner` text NOT NULL,
  `logo` text NOT NULL,
  `parent_cid` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `companies`
--

INSERT INTO `companies` (`cid`, `company_name`, `email`, `password`, `description`, `industry`, `hq_city`, `owner_name`, `owner_email`, `banner`, `logo`, `parent_cid`) VALUES
(14, 'Apple\'s Company', 'apple@gmail.com', '$2y$10$M0OeUKzDbHm/Pji8GOCCDOuW9I/UD4GAiL42gIdtbwUwqBE8fLIWW', 'Apple Computer Company was founded on April 1, 1976, by Steve Jobs, Steve Wozniak, and Ronald Wayne as a partnership', 'Tech', 'USA', 'Apple', 'app@gmail.com', 'uploads/1763691981_banner_apple_logo_PNG19674.png', 'uploads/1763691981_logo_apple_logo_PNG19674.png', NULL),
(15, 'Google\'s Company', 'google@gmail.com', '$2y$10$aSKUlivSfxwo4vKmt1tUReqTEJkTrEJcIRxOfl2KboTBYUCgSj.T.', 'Google LLC is an American multinational technology corporation focused on information technology, online advertising, search engine technology, email, cloud computing, software, quantum computing, e-commerce, consumer electronics, and artificial intelligence .', 'Information', 'England', 'Google', 'go@gmail.com', 'uploads/1763692265_banner_google-logo-png-hd-11659866438lpwuqaonqq.png', 'uploads/1763692257_logo_google-logo-png-hd-11659866438lpwuqaonqq.png', NULL),
(16, 'Samsung\'s Company', 'samsung@gmail.com', '$2y$10$UFPE0AhuOSLG348r0vXYaO4SDDdCnNwsZihqMyJtSsLgIbfFEw6Ra', 'Samsung stylised as SΛMSUNG) is a South Korean multinational manufacturing conglomerate headquartered in the Samsung Town office complex in Seoul.', 'Tech', 'China', 'Samsung', 'sams@gmail.com', 'uploads/1763692504_banner_samsung-logo-png-1285.png', 'uploads/1763692504_logo_samsung-logo-png-1285.png', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `company_codes`
--

CREATE TABLE `company_codes` (
  `code` varchar(10) NOT NULL,
  `comp_cid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `company_codes`
--

INSERT INTO `company_codes` (`code`, `comp_cid`) VALUES
('G9GKK3DN', 15),
('JQB24R3D', 16),
('KQFQU28R', 14);

-- --------------------------------------------------------

--
-- Table structure for table `company_employees`
--

CREATE TABLE `company_employees` (
  `ceid` int(11) NOT NULL,
  `employee_uid` int(11) NOT NULL,
  `employee_cid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `company_employees`
--

INSERT INTO `company_employees` (`ceid`, `employee_uid`, `employee_cid`) VALUES
(3, 15, 15),
(4, 16, 15),
(5, 17, 14),
(6, 18, 14),
(7, 19, 16),
(8, 20, 16);

-- --------------------------------------------------------

--
-- Table structure for table `education`
--

CREATE TABLE `education` (
  `eid` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `school_name` varchar(255) NOT NULL,
  `field_of_study` varchar(255) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `degree` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `education`
--

INSERT INTO `education` (`eid`, `user_id`, `school_name`, `field_of_study`, `start_date`, `end_date`, `degree`) VALUES
(14, 23, 'Lanagra College', 'Computer Science', '2021-01-01', '2024-01-01', 'Diploma'),
(15, 23, 'Lanagra College', 'Computer Science', '2021-01-01', '2024-01-01', 'Diploma'),
(16, 24, 'Thompson Rivers University', 'Computer Science', '2023-01-01', '2025-01-01', 'BSc'),
(17, 25, 'Kwantlen Polytechnic University', 'Health Care Assistant', '2020-01-01', '2024-01-01', 'Certificate');

-- --------------------------------------------------------

--
-- Table structure for table `jobpost`
--

CREATE TABLE `jobpost` (
  `pid` int(11) NOT NULL,
  `emp_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `jobtype` varchar(255) NOT NULL,
  `min_salary` varchar(255) NOT NULL,
  `max_salary` varchar(255) NOT NULL,
  `qualification` text NOT NULL,
  `description` text NOT NULL,
  `visibility` enum('yes','no') NOT NULL DEFAULT 'yes'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jobpost`
--

INSERT INTO `jobpost` (`pid`, `emp_id`, `title`, `location`, `jobtype`, `min_salary`, `max_salary`, `qualification`, `description`, `visibility`) VALUES
(5, 3, 'Frontend Developer', 'Vancouver', 'full_time', '50000', '110000', 'Diploma', 'Optimize sites/apps to improve performance and efficiency\r\n', 'yes'),
(6, 3, 'Backend Developer', 'America', 'full_time', '100000', '200000', 'Diploma', 'Collaborate with web designers and back-end developers to complete projects\r\nCreate wireframes and mockups of site/application designs', 'yes'),
(7, 3, 'IT Applications Specialist', 'Spain', 'contract', '120000', '150000', 'Bachelor\'s Degree', 'Own and support engineering applications like SolidWorks, CATIA, NX, Teamcenter, or Windchill.\r\nKeep everything licensed, patched, and secure.', 'yes'),
(8, 3, 'IT Support Specialist', 'Surrey', 'internship', '50000', '70000', 'Diploma', 'The Westland story is all about growth, and that means plenty of possibility for everyone on our team. Every day, Westland proves that building a great business means taking care of communities, clients, and each other with equal commitment. As we continue to open new doors, we\'re inviting amazing people like you to join us.  ', 'yes'),
(9, 3, 'IT Manager', 'Africa', 'temporary', '50000', '70000', 'Diploma', '5–8+ years of progressive IT experience, with at least 2 years in an IT management or team lead role.\r\nStrong technical knowledge of desktop/laptop hardware, Windows OS, Microsoft 365 suite (especially Outlook, SharePoint), and network infrastructure.\r\nProven experience supporting teams in the construction, engineering, or related field services industries.', 'yes'),
(10, 4, 'Account Executive (Managed IT Services)', 'Vancouver', 'full_time', '90000', '110000', 'Bachelor\'s Degree', 'Are you energized by face-to-face client engagement and committed to building lasting relationships in Vancouver?', 'yes'),
(11, 4, 'IT Advisor - Cybersecurity Remediation Advisor', 'Burnaby', 'part_time', '80000', '120000', 'Bachelor\'s Degree', 'At BC Hydro, we’re working towards creating a cleaner and more sustainable future for all British Columbians and need people like you to help us.', 'yes'),
(12, 4, 'Network Planner', 'Surrey', 'full_time', '90000', '105000', 'Bachelor\'s in computer science, engineering, or information systems', 'Reporting to the Inside Plant Senior Operations Manager, the Network Planning Manager will be leading and facilitating the network architecture and design for the CityWest infrastructure. ', 'yes'),
(13, 4, 'Senior Sales Representative -IT Distribution Services', 'Richmond', 'full_time', '47000', '70000', 'IT Product/ Service distribution sales: 5 years (required)', 'We are seeking a Business Development & Account Manager to join Foreseeson’s IT Distribution team. This individual will play a key role in expanding our national footprint, managing key accounts, and driving revenue growth through proactive relationship management and solution selling. ', 'yes'),
(14, 4, 'Salesforce Commerce Cloud Developer', 'Canada', 'full_time', '120000', '150000', 'Bachelor\'s degree in Computer Science, Software Engineering', 'Manage requirements and development work specific to Salesforce Commerce Cloud\r\nWork with stakeholders, design and UX to gather requirements and design visually appealing features that meet business needs', 'yes'),
(15, 5, 'Cyber Security Engineer', 'Vancouver', 'full_time', '65000', '95000', 'Bachelor\'s degree in Computer Science, Software Engineering', 'We’re seeking a Cyber Security Engineer who thrives at the intersection of technology, customer success, and solutions delivery — someone who can communicate complex security concepts with clarity, conduct engaging product demonstrations, and build trusted relationships with enterprise clients.', 'yes'),
(16, 5, 'Specialist: Full-Time, Part-Time, and Part-Time Temporary', 'Surrey', 'full_time', '120000', '150000', 'Diploma', 'Apple Retail is where the best of Apple comes together. We bring our expertise to help people do what they love, delivering an only-at-Apple experience. We believe inclusion is a shared responsibility and we work together to foster a culture where everyone belongs and is inspired to do their best work.', 'yes'),
(17, 5, 'Junior IT Systems Administrator', 'Delta', 'part_time', '90000', '11000', 'Bachelor\'s degree in Computer Science, Software Engineering', 'Analyze phishing emails and create cybersecurity awareness campaigns.\r\nReview and investigate firewall and web filter logs, including managing web filter allow and disallow lists.', 'yes'),
(18, 5, 'IT MANAGER, INSFRASTRUCTURE & OPERATIONS', 'Richmond', 'contract', '120000', '150000', 'IT Product/ Service distribution sales: 5 years (required)', 'We are looking for a skilled IT professional to manage and secure our Microsoft 365 environment. You will be responsible for maintaining a secure, well-governed, and high-performing Microsoft ecosystem, including Entra ID, Intune, Defender, Exchange Online, SharePoint, and Teams.', 'yes'),
(19, 5, 'IT Firewall Administrator', 'Spain', 'internship', '150000', '200000', 'Bachelor\'s degree in Computer Science, Software Engineering', 'The Project Services department is tasked with delivering diverse IT infrastructure and security projects for the Health Authorities of British Columbia. These projects ensure the security, reliability, and compliance of both on-premises and cloud environments with organizational standards.', 'yes'),
(20, 6, 'Administrative Assistant', 'Delta', 'part_time', '120000', '150000', 'High School', 'Wage rate: $37.00 per hour for 40 hours a week\r\nEducation: Secondary education\r\nExperience : 1 to 2 years of prior experience is required', 'yes'),
(21, 6, 'Senior Software Development Manager, Flow', 'Seattle', 'full_time', '120000', '150000', 'High School', 'This is a fully remote role open to candidates across Canada, except Quebec. If you\'re located in the Vancouver, BC Lower Mainland, you can choose to work remotely, on-site at our Surrey headquarters, or in a hybrid arrangement.\r\nCompensation: Annual base salary: $162,400 to $184,800 CAD, plus eligibility for a profit-sharing bonus.', 'yes'),
(22, 6, 'Principal Software Developer, AI Enablement', 'India', 'contract', '170000', '200000', 'Bachelor\'s degree in Computer Science, Software Engineering', 'This is a fully remote role open to candidates across Canada, except Quebec. If you\'re located in the Vancouver, BC Lower Mainland, you can choose to work remotely, on-site at our Surrey headquarters, or in a hybrid arrangement.\r\nCompensation: Annual base salary: $171,300 to $191,500 CAD, plus eligibility for a profit-sharing bonus.', 'yes'),
(23, 6, 'Firmware Engineer ', 'Surrey', 'part_time', '90000', '110000', 'Bachelor\'s degree in Computer Science, Software Engineering', 'We\'re seeking a highly skilled Firmware Engineer to design, develop, and optimize embedded firmware for cutting-edge AI and edge computing systems. The ideal candidate brings deep technical expertise in embedded systems, NVIDIA Jetson platforms, and low-level programming, with a passion for building high-performance, real-time solutions.', 'yes'),
(24, 6, 'Portfolio Manager', 'Vancouver', 'full_time', '90000', '110000', 'High School', 'The role will support several digital initiatives focused on improving their intranet, website, and internal processes.\r\nSupport configuration, layout updates, and content adjustments within the client\'s WordPress site using the Divi theme.\r\nAssist with the build and enhancements of the SharePoint Online intranet site.', 'yes'),
(25, 7, 'Web Developer', 'Vancouver', 'full_time', '90000', '110000', 'High School', ' The role will support several digital initiatives focused on improving their intranet, website, and internal processes.\r\nSupport configuration, layout updates, and content adjustments within the client\'s WordPress site using the Divi theme.\r\nAssist with the build and enhancements of the SharePoint Online intranet site.\r\nDevelop and organize pages, document libraries, and templates within SharePoint.', 'yes'),
(26, 7, ' Manager, Territory Sales ', 'Surrey', 'part_time', '90000', '110000', 'High School', 'At Samsung Electronics Canada, we take pride in the creativity and diversity of our talented people – they are at the forefront of everything we do. Their skillset and mindset drive our continued success. We want the best of the best at Samsung to join our team, not just those who fit into our Culture but those who will ADD to our Culture and make Samsung an even better place to work.', 'yes'),
(27, 7, 'Samsung Digital Appliance Promoter', 'Richmond', 'part_time', '90000', '110000', 'Bachelor\'s degree in Computer Science, Software Engineering', 'Represent one of the globe\'s most groundbreaking consumer electronics brands. Samsung is at the forefront of innovation, and you\'ll be the driving force behind making it shine in Best Buy retail stores!\r\nStep into a world where collaboration and culture take center stage. Our team is all about working together, having fun, and achieving success together. We believe in compensating you fairly for your dedication and hard work.', 'yes'),
(28, 7, 'Samsung DA Field Marketing Representative', 'Spain', 'contract', '90000', '110000', 'Bachelor\'s degree in Computer Science, Software Engineering', 'Premium Retail Services is a part of Acosta Group – a collective of the industry’s most trusted retail, marketing and foodservice agencies reimagining the way people connect with brands at every point in their shopping journey.\r\n', 'yes'),
(29, 7, 'Samsung Digital Appliance Promoter ', 'Delta', 'internship', '120000', '150000', 'IT Product/ Service distribution sales: 5 years (required)', '\r\nRepresent one of the globe\'s most groundbreaking consumer electronics brands. Samsung is at the forefront of innovation, and you\'ll be the driving force behind making it shine in Best Buy retail stores!', 'yes'),
(30, 8, 'Senior Specialist I, Field Support (Consumer Electronics)', 'Vancouver', 'full_time', '120000', '150000', 'IT Product/ Service distribution sales: 5 years (required)', 'Provides technical support to Authorized Service Centers through phone, email, SAW & fax.\r\nOn regular basis, conduct ASC Tech ride-along visiting consumers during in home service dispatches. Monitor tech technical and empathy performance and provide coaching when require', 'yes'),
(31, 8, 'Senior Data Engineer, Samsung ', 'Dubai', 'contract', '90000', '110000', 'Bachelor\'s Degree', 'We are looking for a Senior Data Engineer who is passionate about big data technology and delivering data solutions to handle TB scale data per day and help the business meet the distinct needs of different customer cohorts.', 'yes'),
(32, 8, 'Manager, Corporate Brand Management', 'Mississauga', 'temporary', '90000', '110000', 'Bachelor\'s Degree', 'This role will be the champion for all cross-divisional marketing programs working alongside other internal brand & sales marketing teams to ensure consistency and alignment with strategic marketing messaging. ', 'yes'),
(33, 8, 'Senior Manager, Corporate Brand & Paid Media', 'Québec', 'part_time', '50000', '150000', 'IT Product/ Service distribution sales: 5 years (required)', 'As a Field Marketing Representative, you\'ll help drive Samsung\'s sales by engaging store management and associates and showcasing Samsung\'s Digital Appliance products.\r\n', 'yes'),
(34, 8, 'Manager, Product Management', 'Delta', 'full_time', '120000', '150000', 'Bachelor\'s Degree', 'At Samsung Electronics Canada, we take pride in the creativity and diversity of our talented people – they are at the forefront of everything we do. Their skillset and mindset drive our continued success. We want the best of the best at Samsung to join our team, not just those who fit into our Culture but those who will ADD to our Culture and make Samsung an even better place to work.', 'yes');

-- --------------------------------------------------------

--
-- Table structure for table `job_application`
--

CREATE TABLE `job_application` (
  `jaid` int(11) NOT NULL,
  `job_uid` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `status` enum('accepted','waitlist','rejected','progress') NOT NULL,
  `applied_at` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `job_application`
--

INSERT INTO `job_application` (`jaid`, `job_uid`, `job_id`, `status`, `applied_at`) VALUES
(10, 23, 34, 'rejected', '2025-11-20'),
(11, 25, 34, 'accepted', '2025-11-20'),
(12, 25, 32, 'rejected', '2025-11-20'),
(13, 25, 17, 'waitlist', '2025-11-20'),
(14, 25, 6, 'waitlist', '2025-11-20'),
(15, 25, 5, 'accepted', '2025-11-20'),
(16, 25, 12, 'accepted', '2025-11-20'),
(17, 23, 33, 'accepted', '2025-11-20'),
(18, 23, 25, 'waitlist', '2025-11-20'),
(19, 23, 15, 'rejected', '2025-11-20'),
(20, 23, 9, 'accepted', '2025-11-20'),
(21, 23, 7, 'rejected', '2025-11-20'),
(22, 24, 21, 'accepted', '2025-11-20'),
(23, 24, 19, 'waitlist', '2025-11-20'),
(24, 24, 9, 'rejected', '2025-11-20'),
(25, 24, 33, 'waitlist', '2025-11-20'),
(26, 24, 29, 'rejected', '2025-11-20'),
(27, 24, 5, 'progress', '2025-11-20'),
(28, 24, 7, 'waitlist', '2025-11-20');

-- --------------------------------------------------------

--
-- Table structure for table `skills`
--

CREATE TABLE `skills` (
  `sid` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `skill_name` varchar(500) DEFAULT NULL,
  `languages` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `skills`
--

INSERT INTO `skills` (`sid`, `user_id`, `skill_name`, `languages`) VALUES
(14, 23, 'Java, Flow, Css', 'English, French'),
(15, 24, 'Java, PHP', 'English, Hindi'),
(16, 25, 'JavaScript, HTML/CSS', 'English, French');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `uid` int(11) NOT NULL,
  `fname` varchar(50) NOT NULL,
  `lname` varchar(50) NOT NULL,
  `age` int(11) NOT NULL,
  `gender` enum('Male','Female','Others') NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone_number` varchar(15) DEFAULT NULL,
  `resume` varchar(255) DEFAULT NULL,
  `photo` varchar(255) NOT NULL,
  `role` enum('JobSeeker','CompanyEmployee') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`uid`, `fname`, `lname`, `age`, `gender`, `email`, `password`, `phone_number`, `resume`, `photo`, `role`, `created_at`) VALUES
(15, 'Zain', 'Anderson', 0, 'Others', 'Zain@gmail.com', '$2y$10$2oFFz96uQH4O1OshLSGgeeLlY9cEZyXMqT6SHXO6cW4oAOI2u2yXW', NULL, NULL, 'default.png', 'CompanyEmployee', '2025-11-21 02:59:20'),
(16, 'Rico', 'Moretti', 0, 'Others', 'rico@gmail.com', '$2y$10$nBtdyXag.iVcbhfli2SNtuX8IqOcXVt1/UgAJ43P.ZfZiqmi1PN26', NULL, NULL, 'default.png', 'CompanyEmployee', '2025-11-21 03:09:28'),
(17, 'Rock', 'Smith', 0, 'Others', 'rock@gmail.com', '$2y$10$0lipEpK/7UadKerVRI3zpuqimYAuGJgn9QoXRlI.3rhugySeNVaDW', NULL, NULL, 'default.png', 'CompanyEmployee', '2025-11-21 03:22:44'),
(18, 'Harman', 'Singh', 0, 'Others', 'harman@gmail.com', '$2y$10$qo7.K.lZ9gLLj/H4jJ47ceUuTrx4L8uC9eNgJVTiSb7UrAAG/dnYS', NULL, NULL, 'default.png', 'CompanyEmployee', '2025-11-21 03:38:01'),
(19, 'Guranshdeep', 'Singh', 0, 'Others', 'guranshdeep@gmail.com', '$2y$10$Mw32M.Myaxt82.XW.oYdKes19EGG9QTeEHp6qLFW2sKqsf3U94kvm', NULL, NULL, 'default.png', 'CompanyEmployee', '2025-11-21 03:45:39'),
(20, 'Akif', 'Baig', 0, 'Others', 'akif@gmail.com', '$2y$10$epl7XBKeZDssiWuAvSs2Puxe43g7JEHkDPlCsXsSSHTuek7m.9Xbq', NULL, NULL, 'default.png', 'CompanyEmployee', '2025-11-21 03:53:33'),
(23, 'Rishi', 'Jadhav', 20, 'Male', 'rishi@gmail.com', '$2y$10$7MtDv2yxQJfyZQlGAoM5Nus8Lq.hfeMY8e3Z7FEu//Qo783rzBT26', '25647849', 'resume_23_1763702054.pdf', 'photo_23_1763704581.png', 'JobSeeker', '2025-11-21 05:13:07'),
(24, 'Kevin', 'Soni', 21, 'Male', 'kevin@gmail.com', '$2y$10$DUgQT1R4QhnrCCot8NPn9ee0Kzy2PqMeJEZy8Hv560tRiJx0.PVmG', '604 555 0000', NULL, 'photo_24_1763704635.webp', 'JobSeeker', '2025-11-21 05:28:07'),
(25, 'Thomson', 'Patel', 25, 'Male', 'thomson@gmail.com', '$2y$10$LMNFWvnEiGWFosHibyHsu.9bfwIw93AoFvBS2sfFm.MOnm1/TKnM.', '245789631', NULL, 'photo_25_1763703366.webp', 'JobSeeker', '2025-11-21 05:34:18');

-- --------------------------------------------------------

--
-- Table structure for table `work_experience`
--

CREATE TABLE `work_experience` (
  `weid` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `job_title` varchar(255) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `work_experience`
--

INSERT INTO `work_experience` (`weid`, `user_id`, `job_title`, `company_name`, `location`, `start_date`, `end_date`, `description`) VALUES
(16, 23, 'WAREHOUSE ASSOCIATE', 'Adidas', 'Surrey', '2024-01-04', '2025-11-02', NULL),
(17, 23, 'Retail Sales Assistant', 'Adidas', 'Surrey', '2024-01-01', '2025-11-12', NULL),
(18, 24, 'Software Developer Intern', 'TELUS', 'Burnaby', '2022-05-11', '2024-12-19', 'Built REST APIs using PHP and MySQL, improved dashboard UI, fixed bugs, optimized database queries'),
(19, 25, 'Junior Web Developer', 'Freelance', 'Remote', '2024-01-20', NULL, 'Developing client websites using HTML, CSS, JavaScript and PHP. Working on UI/UX improvements');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`cid`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_parent_id` (`parent_cid`);

--
-- Indexes for table `company_codes`
--
ALTER TABLE `company_codes`
  ADD PRIMARY KEY (`code`,`comp_cid`),
  ADD KEY `fk_company_id` (`comp_cid`);

--
-- Indexes for table `company_employees`
--
ALTER TABLE `company_employees`
  ADD PRIMARY KEY (`ceid`),
  ADD KEY `fk_company_employee` (`employee_cid`),
  ADD KEY `fk_user_employee` (`employee_uid`);

--
-- Indexes for table `education`
--
ALTER TABLE `education`
  ADD PRIMARY KEY (`eid`),
  ADD KEY `fk_education_user` (`user_id`);

--
-- Indexes for table `jobpost`
--
ALTER TABLE `jobpost`
  ADD PRIMARY KEY (`pid`),
  ADD KEY `fk_emp_id` (`emp_id`);

--
-- Indexes for table `job_application`
--
ALTER TABLE `job_application`
  ADD PRIMARY KEY (`jaid`),
  ADD KEY `fk_job_uid` (`job_uid`),
  ADD KEY `fk_job_id` (`job_id`);

--
-- Indexes for table `skills`
--
ALTER TABLE `skills`
  ADD PRIMARY KEY (`sid`),
  ADD KEY `fk_skills_user` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`uid`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `work_experience`
--
ALTER TABLE `work_experience`
  ADD PRIMARY KEY (`weid`),
  ADD KEY `fk_work_user` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
  MODIFY `cid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `company_employees`
--
ALTER TABLE `company_employees`
  MODIFY `ceid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `education`
--
ALTER TABLE `education`
  MODIFY `eid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `jobpost`
--
ALTER TABLE `jobpost`
  MODIFY `pid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `job_application`
--
ALTER TABLE `job_application`
  MODIFY `jaid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `skills`
--
ALTER TABLE `skills`
  MODIFY `sid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `uid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `work_experience`
--
ALTER TABLE `work_experience`
  MODIFY `weid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `companies`
--
ALTER TABLE `companies`
  ADD CONSTRAINT `fk_parent_id` FOREIGN KEY (`parent_cid`) REFERENCES `companies` (`cid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `company_codes`
--
ALTER TABLE `company_codes`
  ADD CONSTRAINT `fk_company_id` FOREIGN KEY (`comp_cid`) REFERENCES `companies` (`cid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `company_employees`
--
ALTER TABLE `company_employees`
  ADD CONSTRAINT `fk_company_employee` FOREIGN KEY (`employee_cid`) REFERENCES `companies` (`cid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_user_employee` FOREIGN KEY (`employee_uid`) REFERENCES `users` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `education`
--
ALTER TABLE `education`
  ADD CONSTRAINT `fk_education_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `jobpost`
--
ALTER TABLE `jobpost`
  ADD CONSTRAINT `fk_emp_id` FOREIGN KEY (`emp_id`) REFERENCES `company_employees` (`ceid`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `job_application`
--
ALTER TABLE `job_application`
  ADD CONSTRAINT `fk_job_id` FOREIGN KEY (`job_id`) REFERENCES `jobpost` (`pid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_job_uid` FOREIGN KEY (`job_uid`) REFERENCES `users` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `skills`
--
ALTER TABLE `skills`
  ADD CONSTRAINT `fk_skills_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `work_experience`
--
ALTER TABLE `work_experience`
  ADD CONSTRAINT `fk_work_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
