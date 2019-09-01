CREATE TABLE `cities` (
  `id` int(11) NOT NULL,
  `governorateId` int(11) NOT NULL,
  `nameAr` varchar(200) NOT NULL,
  `nameEn` varchar(200) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


INSERT INTO `cities` (`id`, `governorateId`, `nameAr`, `nameEn`) VALUES
(1, 1, 'القاهره', 'Cairo'),
(2, 2, 'الجيزة', 'Giza'),
(3, 2, 'السادس من أكتوبر', 'Sixth of October'),
(4, 2, 'الشيخ زايد\n', 'Cheikh Zayed'),
(5, 2, 'الحوامدية', 'Hawamdiyah'),
(6, 2, 'البدرشين', 'Al Badrasheen'),
(7, 2, 'الصف', 'Saf'),
(8, 2, 'أطفيح', 'Atfih'),
(9, 2, 'العياط', 'Al Ayat'),
(10, 2, 'الباويطي', 'Al-Bawaiti'),
(11, 2, 'منشأة القناطر', 'ManshiyetAl Qanater'),
(12, 2, 'أوسيم', 'Oaseem'),
(13, 2, 'كرداسة', 'Kerdasa'),
(14, 2, 'أبو النمرس', 'Abu Nomros'),
(15, 2, 'كفر غطاطي', 'Kafr Ghati'),
(16, 2, 'منشأة البكاري', 'Manshiyet Al Bakari'),
(17, 3, 'الأسكندرية', 'Alexandria'),
(18, 3, 'برج العرب', 'Burj Al Arab'),
(19, 3, 'برج العرب الجديدة', 'New Burj Al Arab'),
(20, 12, 'بنها', 'Banha'),
(21, 12, 'قليوب', 'Qalyub'),
(22, 12, 'شبرا الخيمة', 'Shubra Al Khaimah'),
(23, 12, 'القناطر الخيرية', 'Al Qanater Charity'),
(24, 12, 'الخانكة', 'Khanka'),
(25, 12, 'كفر شكر', 'Kafr Shukr'),
(26, 12, 'طوخ', 'Tukh'),
(27, 12, 'قها', 'Qaha'),
(28, 12, 'العبور', 'Obour'),
(29, 12, 'الخصوص', 'Khosous'),
(30, 12, 'شبين القناطر', 'Shibin Al Qanater'),
(31, 6, 'دمنهور', 'Damanhour'),
(32, 6, 'كفر الدوار', 'Kafr El Dawar'),
(33, 6, 'رشيد', 'Rashid'),
(34, 6, 'إدكو', 'Edco'),
(35, 6, 'أبو المطامير', 'Abu al-Matamir'),
(36, 6, 'أبو حمص', 'Abu Homs'),
(37, 6, 'الدلنجات', 'Delengat'),
(38, 6, 'المحمودية', 'Mahmoudiyah'),
(39, 6, 'الرحمانية', 'Rahmaniyah'),
(40, 6, 'إيتاي البارود', 'Itai Baroud'),
(41, 6, 'حوش عيسى', 'Housh Eissa'),
(42, 6, 'شبراخيت', 'Shubrakhit'),
(43, 6, 'كوم حمادة', 'Kom Hamada'),
(44, 6, 'بدر', 'Badr'),
(45, 6, 'وادي النطرون', 'Wadi Natrun'),
(46, 6, 'النوبارية الجديدة', 'New Nubaria'),
(47, 23, 'مرسى مطروح', 'Marsa Matrouh'),
(48, 23, 'الحمام', 'El Hamam'),
(49, 23, 'العلمين', 'Alamein'),
(50, 23, 'الضبعة', 'Dabaa'),
(51, 23, 'النجيلة', 'Al-Nagila'),
(52, 23, 'سيدي براني', 'Sidi Brani'),
(53, 23, 'السلوم', 'Salloum'),
(54, 23, 'سيوة', 'Siwa'),
(55, 19, 'دمياط', 'Damietta'),
(56, 19, 'دمياط الجديدة', 'New Damietta'),
(57, 19, 'رأس البر', 'Ras El Bar'),
(58, 19, 'فارسكور', 'Faraskour'),
(59, 19, 'الزرقا', 'Zarqa'),
(60, 19, 'السرو', 'alsaru'),
(61, 19, 'الروضة', 'alruwda'),
(62, 19, 'كفر البطيخ', 'Kafr El-Batikh'),
(63, 19, 'عزبة البرج', 'Azbet Al Burg'),
(64, 19, 'ميت أبو غالب', 'Meet Abou Ghalib'),
(65, 19, 'كفر سعد', 'Kafr Saad'),
(66, 4, 'المنصورة', 'Mansoura'),
(67, 4, 'طلخا', 'Talkha'),
(68, 4, 'ميت غمر', 'Mitt Ghamr'),
(69, 4, 'دكرنس', 'Dekernes'),
(70, 4, 'أجا', 'Aga'),
(71, 4, 'منية النصر', 'Menia El Nasr'),
(72, 4, 'السنبلاوين', 'Sinbillawin'),
(73, 4, 'الكردي', 'El Kurdi'),
(74, 4, 'بني عبيد', 'Bani Ubaid'),
(75, 4, 'المنزلة', 'Al Manzala'),
(76, 4, 'تمي الأمديد', 'tami al\'amdid'),
(77, 4, 'الجمالية', 'aljamalia'),
(78, 4, 'شربين', 'Sherbin'),
(79, 4, 'المطرية', 'Mataria'),
(80, 4, 'بلقاس', 'Belqas'),
(81, 4, 'ميت سلسيل', 'Meet Salsil'),
(82, 4, 'جمصة', 'Gamasa'),
(83, 4, 'محلة دمنة', 'Mahalat Damana'),
(84, 4, 'نبروه', 'Nabroh'),
(85, 22, 'كفر الشيخ', 'Kafr El Sheikh'),
(86, 22, 'دسوق', 'Desouq'),
(87, 22, 'فوه', 'Fooh'),
(88, 22, 'مطوبس', 'Metobas'),
(89, 22, 'برج البرلس', 'Burg Al Burullus'),
(90, 22, 'بلطيم', 'Baltim'),
(91, 22, 'مصيف بلطيم', 'Masief Baltim'),
(92, 22, 'الحامول', 'Hamol'),
(93, 22, 'بيلا', 'Bella'),
(94, 22, 'الرياض', 'Riyadh'),
(95, 22, 'سيدي سالم', 'Sidi Salm'),
(96, 22, 'قلين', 'Qellen'),
(97, 22, 'سيدي غازي', 'Sidi Ghazi'),
(98, 8, 'طنطا', 'Tanta'),
(99, 8, 'المحلة الكبرى', 'Al Mahalla Al Kobra'),
(100, 8, 'كفر الزيات', 'Kafr El Zayat'),
(101, 8, 'زفتى', 'Zefta'),
(102, 8, 'السنطة', 'El Santa'),
(103, 8, 'قطور', 'Qutour'),
(104, 8, 'بسيون', 'Basion'),
(105, 8, 'سمنود', 'Samannoud'),
(106, 10, 'شبين الكوم', 'Shbeen El Koom'),
(107, 10, 'مدينة السادات', 'Sadat City'),
(108, 10, 'منوف', 'Menouf'),
(109, 10, 'سرس الليان', 'Sars El-Layan'),
(110, 10, 'أشمون', 'Ashmon'),
(111, 10, 'الباجور', 'Al Bagor'),
(112, 10, 'قويسنا', 'Quesna'),
(113, 10, 'بركة السبع', 'Berkat El Saba'),
(114, 10, 'تلا', 'Tala'),
(115, 10, 'الشهداء', 'Al Shohada'),
(116, 20, 'الزقازيق', 'Zagazig'),
(117, 20, 'العاشر من رمضان', 'Al Ashr Men Ramadan'),
(118, 20, 'منيا القمح', 'Minya Al Qamh'),
(119, 20, 'بلبيس', 'Belbeis'),
(120, 20, 'مشتول السوق', 'Mashtoul El Souq'),
(121, 20, 'القنايات', 'Qenaiat'),
(122, 20, 'أبو حماد', 'Abu Hammad'),
(123, 20, 'القرين', 'El Qurain'),
(124, 20, 'ههيا', 'Hehia'),
(125, 20, 'أبو كبير', 'Abu Kabir'),
(126, 20, 'فاقوس', 'Faccus'),
(127, 20, 'الصالحية الجديدة', 'El Salihia El Gedida'),
(128, 20, 'الإبراهيمية', 'Al Ibrahimiyah'),
(129, 20, 'ديرب نجم', 'Deirb Negm'),
(130, 20, 'كفر صقر', 'Kafr Saqr'),
(131, 20, 'أولاد صقر', 'Awlad Saqr'),
(132, 20, 'الحسينية', 'Husseiniya'),
(133, 20, 'صان الحجر القبلية', 'san alhajar alqablia'),
(134, 20, 'منشأة أبو عمر', 'Manshayat Abu Omar'),
(135, 18, 'بورسعيد', 'PorSaid'),
(136, 18, 'بورفؤاد', 'PorFouad'),
(137, 9, 'الإسماعيلية', 'Ismailia'),
(138, 9, 'فايد', 'Fayed'),
(139, 9, 'القنطرة شرق', 'Qantara Sharq'),
(140, 9, 'القنطرة غرب', 'Qantara Gharb'),
(141, 9, 'التل الكبير', 'El Tal El Kabier'),
(142, 9, 'أبو صوير', 'Abu Sawir'),
(143, 9, 'القصاصين الجديدة', 'Kasasien El Gedida'),
(144, 14, 'السويس', 'Suez'),
(145, 26, 'العريش', 'Arish'),
(146, 26, 'الشيخ زويد', 'Sheikh Zowaid'),
(147, 26, 'نخل', 'Nakhl'),
(148, 26, 'رفح', 'Rafah'),
(149, 26, 'بئر العبد', 'Bir al-Abed'),
(150, 26, 'الحسنة', 'Al Hasana'),
(151, 21, 'الطور', 'Al Toor'),
(152, 21, 'شرم الشيخ', 'Sharm El-Shaikh'),
(153, 21, 'دهب', 'Dahab'),
(154, 21, 'نويبع', 'Nuweiba'),
(155, 21, 'طابا', 'Taba'),
(156, 21, 'سانت كاترين', 'Saint Catherine'),
(157, 21, 'أبو رديس', 'Abu Redis'),
(158, 21, 'أبو زنيمة', 'Abu Zenaima'),
(159, 21, 'رأس سدر', 'Ras Sidr'),
(160, 17, 'بني سويف', 'Bani Sweif'),
(161, 17, 'بني سويف الجديدة', 'Beni Suef El Gedida'),
(162, 17, 'الواسطى', 'Al Wasta'),
(163, 17, 'ناصر', 'Naser'),
(164, 17, 'إهناسيا', 'Ehnasia'),
(165, 17, 'ببا', 'beba'),
(166, 17, 'الفشن', 'Fashn'),
(167, 17, 'سمسطا', 'Somasta'),
(168, 7, 'الفيوم', 'Fayoum'),
(169, 7, 'الفيوم الجديدة', 'Fayoum El Gedida'),
(170, 7, 'طامية', 'Tamiya'),
(171, 7, 'سنورس', 'Snores'),
(172, 7, 'إطسا', 'Etsa'),
(173, 7, 'إبشواي', 'Epschway'),
(174, 7, 'يوسف الصديق', 'Yusuf El Sediaq'),
(175, 11, 'المنيا', 'Minya'),
(176, 11, 'المنيا الجديدة', 'Minya El Gedida'),
(177, 11, 'العدوة', 'El Adwa'),
(178, 11, 'مغاغة', 'Magagha'),
(179, 11, 'بني مزار', 'Bani Mazar'),
(180, 11, 'مطاي', 'Mattay'),
(181, 11, 'سمالوط', 'Samalut'),
(182, 11, 'المدينة الفكرية', 'Madinat El Fekria'),
(183, 11, 'ملوي', 'Meloy'),
(184, 11, 'دير مواس', 'Deir Mawas'),
(185, 16, 'أسيوط', 'Assiut'),
(186, 16, 'أسيوط الجديدة', 'Assiut El Gedida'),
(187, 16, 'ديروط', 'Dayrout'),
(188, 16, 'منفلوط', 'Manfalut'),
(189, 16, 'القوصية', 'Qusiya'),
(190, 16, 'أبنوب', 'Abnoub'),
(191, 16, 'أبو تيج', 'Abu Tig'),
(192, 16, 'الغنايم', 'El Ghanaim'),
(193, 16, 'ساحل سليم', 'Sahel Selim'),
(194, 16, 'البداري', 'El Badari'),
(195, 16, 'صدفا', 'Sidfa'),
(196, 13, 'الخارجة', 'El Kharga'),
(197, 13, 'باريس', 'Paris'),
(198, 13, 'موط', 'Mout'),
(199, 13, 'الفرافرة', 'Farafra'),
(200, 13, 'بلاط', 'Balat'),
(201, 5, 'الغردقة', 'Hurghada'),
(202, 5, 'رأس غارب', 'Ras Ghareb'),
(203, 5, 'سفاجا', 'Safaga'),
(204, 5, 'القصير', 'El Qusiar'),
(205, 5, 'مرسى علم', 'Marsa Alam'),
(206, 5, 'الشلاتين', 'Shalatin'),
(207, 5, 'حلايب', 'Halaib'),
(208, 27, 'سوهاج', 'Sohag'),
(209, 27, 'سوهاج الجديدة', 'Sohag El Gedida'),
(210, 27, 'أخميم', 'Akhmeem'),
(211, 27, 'أخميم الجديدة', 'Akhmim El Gedida'),
(212, 27, 'البلينا', 'Albalina'),
(213, 27, 'المراغة', 'El Maragha'),
(214, 27, 'المنشأة', 'almunsha\'a'),
(215, 27, 'دار السلام', 'Dar AISalaam'),
(216, 27, 'جرجا', 'Gerga'),
(217, 27, 'جهينة الغربية', 'Jahina Al Gharbia'),
(218, 27, 'ساقلته', 'Saqilatuh'),
(219, 27, 'طما', 'Tama'),
(220, 27, 'طهطا', 'Tahta'),
(221, 25, 'قنا', 'Qena'),
(222, 25, 'قنا الجديدة', 'New Qena'),
(223, 25, 'أبو تشت', 'Abu Tesht'),
(224, 25, 'نجع حمادي', 'Nag Hammadi'),
(225, 25, 'دشنا', 'Deshna'),
(226, 25, 'الوقف', 'Alwaqf'),
(227, 25, 'قفط', 'Qaft'),
(228, 25, 'نقادة', 'Naqada'),
(229, 25, 'فرشوط', 'Farshout'),
(230, 25, 'قوص', 'Quos'),
(231, 24, 'الأقصر', 'Luxor'),
(232, 24, 'الأقصر الجديدة', 'New Luxor'),
(233, 24, 'إسنا', 'Esna'),
(234, 24, 'طيبة الجديدة', 'New Tiba'),
(235, 24, 'الزينية', 'Al ziynia'),
(236, 24, 'البياضية', 'Al Bayadieh'),
(237, 24, 'القرنة', 'Al Qarna'),
(238, 24, 'أرمنت', 'Armant'),
(239, 24, 'الطود', 'Al Tud'),
(240, 15, 'أسوان', 'Aswan'),
(241, 15, 'أسوان الجديدة', 'Aswan El Gedida'),
(242, 15, 'دراو', 'Drau'),
(243, 15, 'كوم أمبو', 'Kom Ombo'),
(244, 15, 'نصر النوبة', 'Nasr Al Nuba'),
(245, 15, 'كلابشة', 'Kalabsha'),
(246, 15, 'إدفو', 'Edfu'),
(247, 15, 'الرديسية', 'Al-Radisiyah'),
(248, 15, 'البصيلية', 'Al Basilia'),
(249, 15, 'السباعية', 'Al Sibaeia'),
(250, 15, 'ابوسمبل السياحية', 'Abo Simbl Al Siyahia');


CREATE TABLE `governorates` (
  `id` int(11) NOT NULL,
  `nameAr` varchar(50) NOT NULL,
  `nameEn` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `governorates` (`id`, `nameAr`, `nameEn`) VALUES
(1, 'القاهرة', 'Cairo'),
(2, 'الجيزة', 'Giza'),
(3, 'الأسكندرية', 'Alexandria'),
(4, 'الدقهلية', 'Dakahlia'),
(5, 'البحر الأحمر', 'Red Sea'),
(6, 'البحيرة', 'Beheira'),
(7, 'الفيوم', 'Fayoum'),
(8, 'الغربية', 'Gharbiya'),
(9, 'الإسماعلية', 'Ismailia'),
(10, 'المنوفية', 'Monofia'),
(11, 'المنيا', 'Minya'),
(12, 'القليوبية', 'Qaliubiya'),
(13, 'الوادي الجديد', 'New Valley'),
(14, 'السويس', 'Suez'),
(15, 'اسوان', 'Aswan'),
(16, 'اسيوط', 'Assiut'),
(17, 'بني سويف', 'Beni Suef'),
(18, 'بورسعيد', 'Port Said'),
(19, 'دمياط', 'Damietta'),
(20, 'الشرقية', 'Sharkia'),
(21, 'جنوب سيناء', 'South Sinai'),
(22, 'كفر الشيخ', 'Kafr Al sheikh'),
(23, 'مطروح', 'Matrouh'),
(24, 'الأقصر', 'Luxor'),
(25, 'قنا', 'Qena'),
(26, 'شمال سيناء', 'North Sinai'),
(27, 'سوهاج', 'Sohag');

ALTER TABLE `cities`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `governorates`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `cities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=251;

ALTER TABLE `governorates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;
COMMIT;
