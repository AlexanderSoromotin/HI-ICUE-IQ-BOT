-- phpMyAdmin SQL Dump
-- version 4.7.9
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Время создания: Сен 28 2021 г., 15:56
-- Версия сервера: 5.7.22-22-log
-- Версия PHP: 5.6.37

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `a369307_1`
--

-- --------------------------------------------------------

--
-- Структура таблицы `acts_during_break`
--

CREATE TABLE `acts_during_break` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `function` varchar(255) NOT NULL,
  `chat_id` int(11) NOT NULL,
  `status` int(2) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=cp1251;

-- --------------------------------------------------------

--
-- Структура таблицы `chats`
--

CREATE TABLE `chats` (
  `id` int(11) NOT NULL,
  `chat_id` int(11) DEFAULT NULL,
  `users` text COLLATE utf8_unicode_ci,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `total_iq` int(20) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Дамп данных таблицы `chats`
--

INSERT INTO `chats` (`id`, `chat_id`, `users`, `date`, `total_iq`) VALUES
(1, 235359833, 'a:1:{i:0;i:235359833;}', '2021-07-28 14:13:27', 112),
(3, 2000000001, 'a:4:{i:0;i:186934537;i:1;i:235359833;i:2;i:507220083;i:3;i:295082014;}', '2021-07-28 14:13:27', 609),
(5, 2000000002, 'a:3:{i:0;i:235359833;i:1;i:295082014;i:2;s:9:\"507220083\";}', '2021-07-28 14:13:27', 328),
(6, 2000000003, 'a:8:{i:0;i:186934537;i:1;i:225564419;i:2;i:235359833;i:3;i:290972470;i:4;i:295082014;i:5;i:221722743;i:6;i:305920000;i:7;i:365804255;}', '2021-07-28 14:13:27', 1443),
(7, 2000000004, 'a:4:{i:0;i:235359833;i:1;i:391938279;i:2;i:186934537;i:3;i:429699846;}', '2021-07-28 14:13:27', 393),
(8, 2000000005, 'a:9:{i:0;i:433967116;i:1;i:295082014;i:2;i:447933178;i:3;i:235359833;i:4;i:301524723;i:5;i:346885102;i:6;i:303196179;i:7;i:152863856;i:8;i:272519379;}', '2021-09-08 14:26:17', 726),
(9, 507220083, 'a:1:{i:0;i:507220083;}', '2021-09-12 10:24:58', 39),
(11, 535843557, 'a:1:{i:0;i:535843557;}', '2021-09-17 06:39:54', 21),
(12, 2000000006, 'a:10:{i:0;i:447933178;i:1;i:325931171;i:2;i:292832454;i:3;i:547445094;i:4;i:387524318;i:5;i:608964244;i:6;i:313550696;i:7;i:423204284;i:8;i:393663077;i:9;i:639144023;}', '2021-09-17 06:51:08', 264),
(13, 2000000009, 'a:9:{i:0;i:235359833;i:1;i:429699846;i:2;i:186934537;i:3;i:391938279;i:4;i:535843557;i:5;i:214608811;i:6;i:413821266;i:7;i:462746561;i:8;i:209743564;}', '2021-09-24 16:31:41', 530),
(14, 2000000011, 'a:2:{i:0;i:662668710;i:1;i:535843557;}', '2021-09-26 17:55:49', 47);

-- --------------------------------------------------------

--
-- Структура таблицы `logs`
--

CREATE TABLE `logs` (
  `id` int(11) NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` int(11) NOT NULL,
  `function` varchar(255) NOT NULL,
  `description` varchar(2048) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `logs`
--

INSERT INTO `logs` (`id`, `date`, `user_id`, `function`, `description`) VALUES
(1, '2021-09-18 20:04:07', 1, '1', '1');

-- --------------------------------------------------------

--
-- Структура таблицы `promo`
--

CREATE TABLE `promo` (
  `code` text,
  `count` int(11) DEFAULT '0',
  `value` int(11) NOT NULL DEFAULT '0',
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=cp1251;

-- --------------------------------------------------------

--
-- Структура таблицы `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `break` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `settings`
--

INSERT INTO `settings` (`id`, `break`) VALUES
(1, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` text CHARACTER SET utf8,
  `last_name` text CHARACTER SET utf8,
  `iq` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) DEFAULT NULL,
  `chat_id` int(11) DEFAULT NULL,
  `registration_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `cmd_date` varchar(255) COLLATE utf8_unicode_ci DEFAULT '01, 01, 2021',
  `joined` text CHARACTER SET utf8
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `iq`, `user_id`, `chat_id`, `registration_date`, `cmd_date`, `joined`) VALUES
(2, 'Александр', 'Соромотин', 137, 235359833, 235359833, '2021-07-28 14:13:27', '27, 09, 2021', 'true'),
(4, 'Олег', 'Цыгвинцев', 189, 186934537, 2000000009, '2021-07-28 14:13:27', '28, 09, 2021', 'true'),
(6, 'Макар', 'Колотов', 217, 225564419, 2000000003, '2021-07-28 14:13:27', '28, 09, 2021', 'true'),
(7, 'Андрей', 'Петунин', 253, 290972470, 2000000003, '2021-07-28 14:13:27', '28, 09, 2021', 'true'),
(9, 'Макс', 'Мустафаев', 250, 221722743, 2000000003, '2021-07-28 14:13:27', '28, 09, 2021', NULL),
(10, 'Данил', 'Щепелин', 122, 305920000, 2000000003, '2021-07-28 14:13:27', '08, 09, 2021', NULL),
(11, 'Никита', 'Панкратов', 1, 365804255, 2000000003, '2021-07-28 14:13:27', '06, 08, 2021', NULL),
(12, 'Вадим', 'Красильников', 97, 391938279, 2000000009, '2021-07-28 14:13:27', '28, 09, 2021', 'true'),
(14, 'Катя', 'Павук', 56, 433967116, 2000000005, '2021-09-08 14:26:16', '24, 09, 2021', NULL),
(15, 'Евгений', 'Сушилов', 274, 295082014, 2000000003, '2021-09-08 14:51:39', '28, 09, 2021', 'true'),
(16, 'Анастасия', 'Неред\'', 75, 447933178, 2000000006, '2021-09-08 15:18:15', '28, 09, 2021', 'true'),
(17, 'Катерина', 'Яковлева', 33, 301524723, 2000000005, '2021-09-08 15:18:18', '28, 09, 2021', NULL),
(18, 'Тимофей', 'Продатов', 46, 346885102, 2000000005, '2021-09-09 04:34:59', '25, 09, 2021', NULL),
(19, 'Дарья', 'Павук', 62, 303196179, 2000000005, '2021-09-09 08:45:17', '28, 09, 2021', 'true'),
(21, 'Матвей', 'Иллюминатов', 4, 152863856, 2000000005, '2021-09-13 03:01:05', '13, 09, 2021', NULL),
(22, 'Юра', 'Кринжулькин', 39, 272519379, 2000000005, '2021-09-13 04:41:43', '28, 09, 2021', 'true'),
(23, 'Марк', 'Ковальский', 26, 429699846, 2000000009, '2021-09-16 06:37:55', '28, 09, 2021', 'true'),
(25, 'Матвей', 'Чечеткин', 18, 535843557, 2000000011, '2021-09-17 06:36:17', '28, 09, 2021', 'true'),
(26, 'Игорь', 'Гец', 24, 325931171, 2000000006, '2021-09-17 06:51:20', '28, 09, 2021', NULL),
(27, 'Матвей', 'Бояркин', 34, 292832454, 2000000006, '2021-09-17 06:52:17', '28, 09, 2021', NULL),
(28, 'Вера', 'Савичева', 23, 547445094, 2000000006, '2021-09-17 06:53:19', '26, 09, 2021', NULL),
(29, 'Степан', 'Шумилов', 28, 387524318, 2000000006, '2021-09-17 06:54:08', '26, 09, 2021', NULL),
(30, 'Анастасия', 'Неред', 10, 608964244, 2000000006, '2021-09-17 06:57:00', '17, 09, 2021', NULL),
(31, 'Рафаиль', 'Миндибаев', 28, 313550696, 2000000006, '2021-09-17 07:04:39', '28, 09, 2021', NULL),
(32, 'Екатерина', 'Глушкова', 25, 423204284, 2000000006, '2021-09-17 08:09:01', '20, 09, 2021', NULL),
(33, 'Рустам', 'Учинин', 7, 393663077, 2000000006, '2021-09-19 12:24:04', '19, 09, 2021', NULL),
(35, 'Михаил', 'Аверинцев', 39, 507220083, 2000000001, '2021-09-19 13:26:11', '26, 09, 2021', 'true'),
(36, 'Алёна', 'Волошина', 9, 214608811, 2000000009, '2021-09-24 16:40:03', '26, 09, 2021', NULL),
(37, 'Алёна', 'Мордвинова', 34, 413821266, 2000000009, '2021-09-24 16:41:47', '28, 09, 2021', 'true'),
(38, 'Егор', 'Бурылов', 20, 244261009, 244261009, '2021-09-24 18:10:43', '01, 01, 2021', 'true'),
(39, 'Виктория', 'Лосева', 2, 462746561, 2000000009, '2021-09-25 18:45:26', '25, 09, 2021', NULL),
(40, 'Ульяна', 'Литвинова', 18, 209743564, 2000000009, '2021-09-26 12:38:18', '27, 09, 2021', 'true'),
(41, 'Галя', 'Венигрет', 29, 662668710, 2000000011, '2021-09-26 17:55:49', '28, 09, 2021', 'true'),
(42, 'Вячеслав', 'Козубенко', 10, 639144023, 2000000006, '2021-09-28 05:42:38', '28, 09, 2021', NULL);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `acts_during_break`
--
ALTER TABLE `acts_during_break`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `chats`
--
ALTER TABLE `chats`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `promo`
--
ALTER TABLE `promo`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `acts_during_break`
--
ALTER TABLE `acts_during_break`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=205;

--
-- AUTO_INCREMENT для таблицы `chats`
--
ALTER TABLE `chats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT для таблицы `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `promo`
--
ALTER TABLE `promo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
