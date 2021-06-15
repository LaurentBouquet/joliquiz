
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  joliquiz
--

-- --------------------------------------------------------

--
-- Structure de la table migration_versions
--

CREATE TABLE migration_versions (
  `version` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table tbl_answer
--

CREATE TABLE tbl_answer (
  `id` int(10) UNSIGNED NOT NULL,
  `question_id` int(10) UNSIGNED NOT NULL,
  `text` text NOT NULL,
  `correct` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table tbl_category
--

CREATE TABLE tbl_category (
  `id` int(10) UNSIGNED NOT NULL,
  `shortname` varchar(50) NOT NULL,
  `longname` varchar(255) NOT NULL,
  `language_id` varchar(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table tbl_history_answer
--

CREATE TABLE tbl_history_answer (
  `id` int(10) UNSIGNED NOT NULL,
  `question_history_id` int(10) UNSIGNED NOT NULL,
  `answer_id` int(10) UNSIGNED NOT NULL,
  `answer_text` text NOT NULL,
  `answer_correct` tinyint(1) NOT NULL,
  `correct_given` tinyint(1) NOT NULL,
  `answer_succes` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table tbl_history_question
--

CREATE TABLE tbl_history_question (
  `id` int(10) UNSIGNED NOT NULL,
  `workout_id` int(10) UNSIGNED NOT NULL,
  `question_id` int(10) UNSIGNED NOT NULL,
  `question_text` text NOT NULL,
  `completed` tinyint(1) NOT NULL,
  `question_success` tinyint(1) DEFAULT NULL,
  `duration` varchar(255) DEFAULT NULL,
  `started_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ended_at` timestamp NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table tbl_language
--

CREATE TABLE tbl_language (
  `id` varchar(2) NOT NULL,
  `english_name` varchar(50) NOT NULL,
  `native_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table tbl_question
--

CREATE TABLE tbl_question (
  `id` int(10) UNSIGNED NOT NULL,
  `text` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `language_id` varchar(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table tbl_question_category
--

CREATE TABLE tbl_question_category (
  `question_id` int(10) UNSIGNED NOT NULL,
  `category_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table tbl_quiz
--

CREATE TABLE tbl_quiz (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `summary` text,
  `number_of_questions` int(10) UNSIGNED NOT NULL,
  `active` tinyint(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `show_result_question` tinyint(1) NOT NULL,
  `show_result_quiz` tinyint(1) NOT NULL,
  `language_id` varchar(2) NOT NULL,
  `allow_anonymous_workout` tinyint(1) NOT NULL,
  `result_quiz_comment` text,
  `start_quiz_comment` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table tbl_quiz_category
--

CREATE TABLE tbl_quiz_category (
  `quiz_id` int(10) UNSIGNED NOT NULL,
  `category_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table tbl_user
--

CREATE TABLE tbl_user (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(64) NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `roles` text NOT NULL,
  `prefered_language_id` varchar(2) DEFAULT NULL,
  `token` varchar(255) DEFAULT NULL,
  `password_requested_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table tbl_workout
--

CREATE TABLE tbl_workout (
  `id` int(10) UNSIGNED NOT NULL,
  `student_id` int(10) UNSIGNED NOT NULL,
  `quiz_id` int(10) UNSIGNED NOT NULL,
  `started_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ended_at` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  `number_of_questions` int(10) UNSIGNED NOT NULL,
  `completed` tinyint(1) NOT NULL,
  `score` float DEFAULT NULL,
  `comment` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Index pour les tables exportées
--

--
-- Index pour la table migration_versions
--
ALTER TABLE migration_versions
  ADD PRIMARY KEY (`version`);

--
-- Index pour la table tbl_answer
--
ALTER TABLE tbl_answer
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table tbl_category
--
ALTER TABLE tbl_category
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table tbl_history_answer
--
ALTER TABLE tbl_history_answer
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table tbl_history_question
--
ALTER TABLE tbl_history_question
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table tbl_language
--
ALTER TABLE tbl_language
  ADD PRIMARY KEY (`english_name`);

--
-- Index pour la table tbl_question
--
ALTER TABLE tbl_question
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table tbl_question_category
--
ALTER TABLE tbl_question_category
  ADD PRIMARY KEY (`question_id`,`category_id`);

--
-- Index pour la table tbl_quiz
--
ALTER TABLE tbl_quiz
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table tbl_quiz_category
--
ALTER TABLE tbl_quiz_category
  ADD PRIMARY KEY (`quiz_id`,`category_id`);

--
-- Index pour la table tbl_user
--
ALTER TABLE tbl_user
  ADD PRIMARY KEY (`username`);

--
-- Index pour la table tbl_workout
--
ALTER TABLE tbl_workout
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table tbl_answer
--
ALTER TABLE tbl_answer
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=566;
--
-- AUTO_INCREMENT pour la table tbl_category
--
ALTER TABLE tbl_category
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
--
-- AUTO_INCREMENT pour la table tbl_history_answer
--
ALTER TABLE tbl_history_answer
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9170;
--
-- AUTO_INCREMENT pour la table tbl_history_question
--
ALTER TABLE tbl_history_question
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2401;
--
-- AUTO_INCREMENT pour la table tbl_question
--
ALTER TABLE tbl_question
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=148;
--
-- AUTO_INCREMENT pour la table tbl_quiz
--
ALTER TABLE tbl_quiz
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
--
-- AUTO_INCREMENT pour la table tbl_workout
--
ALTER TABLE tbl_workout
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=454;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
