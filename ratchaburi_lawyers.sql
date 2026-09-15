SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `law_documents`;
CREATE TABLE `law_documents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `category_id` bigint unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `document_no` varchar(255) DEFAULT NULL,
  `year_be` varchar(255) DEFAULT NULL,
  `file_path` varchar(255) NOT NULL,
  `download_count` int unsigned NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `law_documents_category_id_foreign` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `news`;
CREATE TABLE `news` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `category_id` bigint unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `cover_image` varchar(255) DEFAULT NULL,
  `content` longtext NOT NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT 1,
  `published_at` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `gallery_images` json DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `news_slug_unique` (`slug`),
  KEY `news_category_id_foreign` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `payload` longtext NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES ('1', 'Fee', 'feemubankru48@gmail.com', NULL, '$2y$12$pabcTKJ1L/ln8hj7FMH9qOpDThQ2qlBVLHWPgfRQ2Cztw3MgM11A.', '41hwbdfzeOsBuHM6QZMTXKSj3IbPTmpoe1hoPIbPr9FEUPdxDFgFFrlzm1DZ', '2026-09-12 09:42:02', '2026-09-12 09:42:02');

INSERT INTO `categories` (`id`, `name`, `slug`, `type`, `created_at`, `updated_at`) VALUES ('2', 'พระราชบัญญัติและระเบียบ', 'test@example.com', 'news', '2026-09-12 09:59:33', '2026-09-12 09:59:33');
INSERT INTO `categories` (`id`, `name`, `slug`, `type`, `created_at`, `updated_at`) VALUES ('3', 'พระราชบัญญัติและระเบียบ', 'fff', 'law_document', '2026-09-12 10:03:29', '2026-09-12 10:03:29');
INSERT INTO `categories` (`id`, `name`, `slug`, `type`, `created_at`, `updated_at`) VALUES ('4', 'พระราชบัญญัติและกฎกระทรวง', 'act-and-regulations', 'law_document', '2026-09-12 10:25:18', '2026-09-12 10:25:18');
INSERT INTO `categories` (`id`, `name`, `slug`, `type`, `created_at`, `updated_at`) VALUES ('5', 'ข้อบังคับสภาทนายความ', 'lawyers-council-rules', 'law_document', '2026-09-12 10:25:18', '2026-09-12 10:25:18');
INSERT INTO `categories` (`id`, `name`, `slug`, `type`, `created_at`, `updated_at`) VALUES ('6', 'แบบฟอร์มคำขอและคดีความ', 'application-forms', 'law_document', '2026-09-12 10:25:18', '2026-09-12 10:25:18');
INSERT INTO `categories` (`id`, `name`, `slug`, `type`, `created_at`, `updated_at`) VALUES ('7', 'ข่าวประชาสัมพันธ์', 'announcements', 'news', '2026-09-12 10:25:18', '2026-09-12 10:25:18');
INSERT INTO `categories` (`id`, `name`, `slug`, `type`, `created_at`, `updated_at`) VALUES ('8', 'กิจกรรมสภาทนายความ', 'activities', 'news', '2026-09-12 10:25:18', '2026-09-12 10:25:18');
INSERT INTO `categories` (`id`, `name`, `slug`, `type`, `created_at`, `updated_at`) VALUES ('9', 'ประกาศและหนังสือเวียน', 'official-announcements', 'news', '2026-09-13 07:52:16', '2026-09-13 07:52:16');

INSERT INTO `law_documents` (`id`, `category_id`, `title`, `document_no`, `year_be`, `file_path`, `download_count`, `created_at`, `updated_at`) VALUES ('1', '3', 'test', '111111', '2555', 'law-documents/01M2AH5HG1RQAVPSPJ45K4Z4QX.pdf', '1', '2026-09-12 10:03:50', '2026-09-12 10:04:40');
INSERT INTO `law_documents` (`id`, `category_id`, `title`, `document_no`, `year_be`, `file_path`, `download_count`, `created_at`, `updated_at`) VALUES ('2', '3', 'ระเบียบ', '123456', '2544', 'law-documents/01M2AJ882MQ6MNCA3VKCSJA3CB.pdf', '0', '2026-09-12 10:22:47', '2026-09-12 10:22:47');
INSERT INTO `law_documents` (`id`, `category_id`, `title`, `document_no`, `year_be`, `file_path`, `download_count`, `created_at`, `updated_at`) VALUES ('3', '4', 'พระราชบัญญัติทนายความ พ.ศ. 2528', 'ฉบับที่ 1', '2528', 'demo/sample.pdf', '50', '2026-09-12 10:25:18', '2026-09-12 10:30:32');
INSERT INTO `law_documents` (`id`, `category_id`, `title`, `document_no`, `year_be`, `file_path`, `download_count`, `created_at`, `updated_at`) VALUES ('4', '5', 'ข้อบังคับสภาทนายความ ว่าด้วยมรรยาททนายความ พ.ศ. 2529', 'หมวด 1-4', '2529', 'demo/sample.pdf', '21', '2026-09-12 10:25:18', '2026-09-12 10:33:38');
INSERT INTO `law_documents` (`id`, `category_id`, `title`, `document_no`, `year_be`, `file_path`, `download_count`, `created_at`, `updated_at`) VALUES ('5', '5', 'ข้อบังคับว่าด้วยการฝึกอบรมวิชาว่าความและการทดสอบ พ.ศ. 2565', 'ฉบับปรับปรุง', '2565', 'demo/sample.pdf', '40', '2026-09-12 10:25:18', '2026-09-12 10:25:18');
INSERT INTO `law_documents` (`id`, `category_id`, `title`, `document_no`, `year_be`, `file_path`, `download_count`, `created_at`, `updated_at`) VALUES ('6', '6', 'แบบฟอร์มคำขอขึ้นทะเบียนและรับใบอนุญาตให้เป็นทนายความ', 'ท.1', '2567', 'demo/sample.pdf', '14', '2026-09-12 10:25:18', '2026-09-12 10:25:18');
INSERT INTO `law_documents` (`id`, `category_id`, `title`, `document_no`, `year_be`, `file_path`, `download_count`, `created_at`, `updated_at`) VALUES ('7', '6', 'แบบฟอร์มขอต่ออายุใบอนุญาตให้เป็นทนายความ', 'ท.2', '2567', 'demo/sample.pdf', '34', '2026-09-12 10:25:18', '2026-09-12 10:25:18');
INSERT INTO `law_documents` (`id`, `category_id`, `title`, `document_no`, `year_be`, `file_path`, `download_count`, `created_at`, `updated_at`) VALUES ('8', '6', 'แบบฟอร์มขอรับความช่วยเหลือทางกฎหมายสำหรับประชาชน', 'สคป.01', '2568', 'demo/sample.pdf', '32', '2026-09-12 10:25:18', '2026-09-12 10:25:18');

INSERT INTO `news` (`id`, `category_id`, `title`, `slug`, `cover_image`, `content`, `is_published`, `published_at`, `created_at`, `updated_at`, `gallery_images`) VALUES ('3', '8', 'สภาทนายความจังหวัดราชบุรี จัดโครงการอบรมสัมมนาวิชาการ เรื่อง \"เทคนิคการว่าความและการสืบพยานในศาลชั้นต้น\" ประจำปี 2569', 'training-litigation-techniques-2026', 'news-covers/seminar-2026.jpg', '<p>เมื่อวันที่ 10 กันยายน 2569 สภาทนายความจังหวัดราชบุรี นำโดย <strong>คุณมนตรี อิ่มจิตร ประธานสภาทนายความจังหวัดราชบุรี</strong> พร้อมด้วยคณะกรรมการบริหาร ได้จัดโครงการสัมมนาเชิงวิชาการเพื่อพัฒนาทักษะวิชาชีพ ในหัวข้อ <em>\"เทคนิคการว่าความและการสืบพยานคดีแพ่งและคดีอาญาในศาลชั้นต้น\"</em> ณ ห้องประชุมศาลจังหวัดราชบุรี</p><p>โดยการอบรมในครั้งนี้มีวัตถุประสงค์เพื่อเสริมสร้างองค์ความรู้ ความเชี่ยวชาญ และความเข้าใจในระเบียบวิธีพิจารณาความใหม่ๆ รวมถึงการนำพยานหลักฐานทางอิเล็กทรอนิกส์เข้าสู่สำนวนคดี โดยได้รับเกียรติจากผู้พิพากษาหัวหน้าศาลจังหวัดราชบุรี และผู้เชี่ยวชาญด้านกฎหมายร่วมเป็นวิทยากรบรรยายพิเศษ</p><p>มีสมาชิกทนายความในจังหวัดราชบุรีและจังหวัดใกล้เคียงเข้าร่วมสัมมนาเป็นจำนวนกว่า 80 ท่าน ซึ่งบรรยากาศเป็นไปด้วยความอบอุ่นและได้รับความรู้ที่เป็นประโยชน์ต่อการปฏิบัติวิชาชีพอย่างยิ่ง</p>', '1', '2026-09-10 00:00:00', '2026-09-12 10:45:39', '2026-09-12 10:45:39', '[\"news-galleries\\/seminar-g1.jpg\",\"news-galleries\\/seminar-g2.jpg\"]');
INSERT INTO `news` (`id`, `category_id`, `title`, `slug`, `cover_image`, `content`, `is_published`, `published_at`, `created_at`, `updated_at`, `gallery_images`) VALUES ('4', '7', 'ประกาศสภาทนายความจังหวัดราชบุรี เรื่อง การให้บริการทนายความอาสาให้คำปรึกษาทางกฎหมายฟรีแก่ประชาชน ณ ศาลจังหวัดราชบุรี', 'legal-aid-consultation-service-announcement', 'news-covers/legal-aid-2026.jpg', '<p>สภาทนายความจังหวัดราชบุรี ขอประชาสัมพันธ์การให้บริการ <strong>ทนายความอาสาประจำศาลจังหวัดราชบุรี</strong> เพื่อช่วยเหลือประชาชนผู้ยากไร้หรือไม่ได้รับความเป็นธรรมทางกฎหมาย โดยไม่มีค่าใช้จ่ายใดๆ ทั้งสิ้น</p><p><strong>รายละเอียดการให้บริการ:</strong></p><ul><li><strong>วันและเวลาทำการ:</strong> วันจันทร์ – วันศุกร์ เวลา 08.30 – 16.30 น. (เว้นวันหยุดราชการและวันหยุดนักขัตฤกษ์)</li><li><strong>สถานที่:</strong> ห้องทนายความอาสา อาคารศาลจังหวัดราชบุรี อำเภอเมือง จังหวัดราชบุรี</li><li><strong>ขอบเขตการให้บริการ:</strong> ให้คำปรึกษาคดีความทั่วไป การเจรจาไกล่เกลี่ยข้อพิพาท คำแนะนำในการยื่นคำร้อง และการจัดหาทนายความว่าความให้แก่ผู้ยากไร้</li></ul><p>ประชาชนที่มีข้อสงสัยหรือต้องการสอบถามรายละเอียดเพิ่มเติม สามารถติดต่อได้ที่สำนักงานสภาทนายความจังหวัดราชบุรี โทร. <strong>097-195-2029</strong> ในวันและเวลาราชการ</p>', '1', '2026-09-08 00:00:00', '2026-09-12 10:45:39', '2026-09-12 10:45:39', '[\"news-galleries\\/seminar-g2.jpg\",\"news-galleries\\/seminar-g1.jpg\"]');
INSERT INTO `news` (`id`, `category_id`, `title`, `slug`, `cover_image`, `content`, `is_published`, `published_at`, `created_at`, `updated_at`, `gallery_images`) VALUES ('5', '8', 'คณะกรรมการสภาทนายความจังหวัดราชบุรี ร่วมพิธีวางพวงมาลาเนื่องใน \"วันรพี\" พระบิดาแห่งกฎหมายไทย ประจำปี 2569', 'rapee-day-wreath-laying-ceremony-2026', 'news-covers/seminar-2026.jpg', '<p>เมื่อวันที่ 7 สิงหาคม 2569 คณะกรรมการสภาทนายความจังหวัดราชบุรี นำโดยประธานสภาทนายความจังหวัดราชบุรี พร้อมด้วยมวลสมาชิกทนายความจังหวัดราชบุรี ได้ร่วมพิธีวางพวงมาลาถวายสักการะเบื้องหน้าพระรูป <strong>พระเจ้าบรมวงศ์เธอ พระองค์เจ้ารพีพัฒนศักดิ์ กรมหลวงราชบุรีดิเรกฤทธิ์ \"พระบิดาแห่งกฎหมายไทย\"</strong> เนื่องใน <em>\"วันรพี\"</em> ณ บริเวณหน้าอาคารศาลจังหวัดราชบุรี</p><p>พิธีดังกล่าวจัดขึ้นเพื่อรำลึกถึงพระกรุณาธิคุณของพระองค์ท่าน ผู้ทรงวางรากฐานระบบการศึกษากฎหมายและการศาลไทยให้มีความเจริญก้าวหน้าทัดเทียมนานาอารยประเทศ โดยมีคณะผู้พิพากษา อัยการ เจ้าหน้าที่ในกระบวนการยุติธรรม และหน่วยงานราชการในจังหวัดราชบุรีเข้าร่วมพิธีอย่างพร้อมเพรียง</p>', '1', '2026-08-07 00:00:00', '2026-09-12 10:45:39', '2026-09-12 10:45:39', '[\"news-galleries\\/seminar-g1.jpg\"]');
INSERT INTO `news` (`id`, `category_id`, `title`, `slug`, `cover_image`, `content`, `is_published`, `published_at`, `created_at`, `updated_at`, `gallery_images`) VALUES ('6', '9', 'ประกาศสภาทนายความจังหวัดราชบุรี ที่ ๑/๒๕๖๙ เรื่อง กำหนดการยื่นคำขอต่ออายุใบอนุญาตให้เป็นทนายความ ประจำปี ๒๕๖๙', 'announcement-lawyer-license-renewal-2026', NULL, '<p>ด้วยสภาทนายความจังหวัดราชบุรี ขอแจ้งกำหนดการยื่นคำขอต่ออายุใบอนุญาตให้เป็นทนายความ (ตั๋วทนาย) ประจำปี พ.ศ. ๒๕๖๙ สำหรับทนายความที่มีภูมิลำเนาหรือสำนักงานตั้งอยู่ในเขตจังหวัดราชบุรี</p><p><strong>กำหนดการและสถานที่:</strong></p><ul><li><strong>ระยะเวลายื่นคำขอ:</strong> ตั้งแต่วันที่ ๑ ตุลาคม ๒๕๖๙ ถึง ๓๐ พฤศจิกายน ๒๕๖๙ ในวันและเวลาราชการ</li><li><strong>สถานที่ยื่นคำขอ:</strong> สำนักงานสภาทนายความจังหวัดราชบุรี อาคารศาลจังหวัดราชบุรี</li><li><strong>เอกสารที่ต้องใช้:</strong> แบบฟอร์ม ท.๒, สำเนาใบอนุญาตเดิม, รูปถ่ายชุดครุยทนายความขนาด ๑ นิ้ว จำนวน ๓ รูป, และหลักฐานการชำระค่าธรรมเนียม</li></ul><p>สอบถามรายละเอียดเพิ่มเติมได้ที่เจ้าหน้าที่สภาทนายความจังหวัดราชบุรี โทร. ๐๙๗-๑๙๕-๒๐๒๙</p>', '1', '2026-09-11 00:00:00', '2026-09-13 07:52:16', '2026-09-13 07:52:16', NULL);
INSERT INTO `news` (`id`, `category_id`, `title`, `slug`, `cover_image`, `content`, `is_published`, `published_at`, `created_at`, `updated_at`, `gallery_images`) VALUES ('7', '9', 'หนังสือเวียน เรื่อง แนวทางปฏิบัติของทนายความขอแรงและทนายความอาสาในการให้คำปรึกษาประชาชน ณ ศาลจังหวัดราชบุรี', 'circular-duty-lawyers-guidelines-ratchaburi', NULL, '<p>เพื่อความเรียบร้อยและมีประสิทธิภาพสูงสุดในการอำนวยความยุติธรรมแก่ประชาชน สภาทนายความจังหวัดราชบุรีขอเวียนแจ้งแนวทางปฏิบัติสำหรับทนายความอาสาประจำศาลจังหวัดราชบุรี ดังต่อไปนี้:</p><ol><li>ขอให้ทนายความเวรมาปฏิบัติหน้าที่ตรงตามเวลาที่กำหนด (๐๘.๓๐ - ๑๖.๓๐ น.)</li><li>แต่งกายด้วยชุดสุภาพตามข้อบังคับสภาทนายความว่าด้วยมรรยาททนายความ</li><li>บันทึกรายละเอียดการให้คำปรึกษาอรรถคดีลงในสมุดบันทึกสถิติประจำวันเพื่อเป็นข้อมูลรายงานต่อสภาทนายความส่วนกลาง</li></ol><p>จึงเรียนมาเพื่อโปรดทราบและถือปฏิบัติโดยพร้อมเพรียงกัน</p>', '1', '2026-09-05 00:00:00', '2026-09-13 07:52:16', '2026-09-13 07:52:16', NULL);
INSERT INTO `news` (`id`, `category_id`, `title`, `slug`, `cover_image`, `content`, `is_published`, `published_at`, `created_at`, `updated_at`, `gallery_images`) VALUES ('8', '9', 'ประกาศรายชื่อทนายความผู้ผ่านการอบรมหลักสูตร \"การคุ้มครองสิทธิเด็ก เยาวชน และสตรีในคดีครอบครัว\" ประจำปี ๒๕๖๙', 'notice-certified-lawyers-juvenile-family-2026', NULL, '<p>ตามที่สภาทนายความจังหวัดราชบุรีได้จัดการอบรมหลักสูตรเฉพาะทางด้านการคุ้มครองสิทธิเด็ก เยาวชน และสตรีในคดีครอบครัว ประจำปี พ.ศ. ๒๕๖๙ บัดนี้การอบรมและการทดสอบได้เสร็จสิ้นเรียบร้อยแล้ว จึงขอประกาศรายชื่อทนายความผู้ผ่านเกณฑ์เพื่อขึ้นทะเบียนเป็นทนายความผู้เชี่ยวชาญคดีครอบครัวประจำศาลเยาวชนและครอบครัวจังหวัดราชบุรี</p><p>ทนายความที่มีรายชื่อตามประกาศสามารถติดต่อรับหนังสือรับรองได้ที่สำนักงานสภาทนายความจังหวัดราชบุรี ตั้งแต่วันที่ ๑๕ กันยายน ๒๕๖๙ เป็นต้นไป</p>', '1', '2026-08-28 00:00:00', '2026-09-13 07:52:16', '2026-09-13 07:52:16', NULL);

SET FOREIGN_KEY_CHECKS=1;
