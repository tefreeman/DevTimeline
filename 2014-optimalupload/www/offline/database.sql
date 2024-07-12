/*
Date: 2012-01-19 22:10:32
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for `banned_ips`
-- ----------------------------
DROP TABLE IF EXISTS `banned_ips`;
CREATE TABLE `banned_ips` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ipAddress` varchar(30) NOT NULL,
  `dateBanned` datetime NOT NULL,
  `banType` varchar(30) NOT NULL,
  `banNotes` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of banned_ips
-- ----------------------------

-- ----------------------------
-- Table structure for `file`
-- ----------------------------
DROP TABLE IF EXISTS `file`;
CREATE TABLE `file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `originalFilename` varchar(255) NOT NULL,
  `shortUrl` varchar(255) DEFAULT NULL,
  `fileType` varchar(50) DEFAULT NULL,
  `extension` varchar(10) DEFAULT NULL,
  `fileSize` int(11) DEFAULT NULL,
  `localFilePath` varchar(255) DEFAULT NULL,
  `userId` int(11) DEFAULT NULL,
  `totalDownload` int(11) DEFAULT NULL,
  `uploadedIP` varchar(50) DEFAULT NULL,
  `uploadedDate` timestamp NULL DEFAULT NULL,
  `statusId` int(2) DEFAULT NULL,
  `visits` int(11) DEFAULT '0',
  `lastAccessed` timestamp NULL DEFAULT NULL,
  `deleteHash` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=700 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of file
-- ----------------------------

-- ----------------------------
-- Table structure for `file_status`
-- ----------------------------
DROP TABLE IF EXISTS `file_status`;
CREATE TABLE `file_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of file_status
-- ----------------------------
INSERT INTO file_status VALUES ('1', 'active');
INSERT INTO file_status VALUES ('2', 'user removed');
INSERT INTO file_status VALUES ('3', 'admin removed');
INSERT INTO file_status VALUES ('4', 'copyright removed');
INSERT INTO file_status VALUES ('5', 'system expired');

-- ----------------------------
-- Table structure for `language`
-- ----------------------------
DROP TABLE IF EXISTS `language`;
CREATE TABLE `language` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `languageName` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of language
-- ----------------------------
INSERT INTO language VALUES ('1', 'English (en)');

-- ----------------------------
-- Table structure for `language_content`
-- ----------------------------
DROP TABLE IF EXISTS `language_content`;
CREATE TABLE `language_content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `languageKeyId` varchar(150) NOT NULL,
  `languageId` int(11) NOT NULL,
  `content` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1062 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of language_content
-- ----------------------------
INSERT INTO language_content VALUES ('581', '1', '1', 'home');
INSERT INTO language_content VALUES ('582', '3', '1', 'banned words / urls');
INSERT INTO language_content VALUES ('583', '4', '1', 'admin users');
INSERT INTO language_content VALUES ('584', '5', '1', 'banned ips');
INSERT INTO language_content VALUES ('585', '6', '1', 'site settings');
INSERT INTO language_content VALUES ('586', '7', '1', 'languages');
INSERT INTO language_content VALUES ('587', '8', '1', 'logout');
INSERT INTO language_content VALUES ('588', '9', '1', 'Language Details');
INSERT INTO language_content VALUES ('589', '10', '1', 'Are you sure you want to remove this IP ban?');
INSERT INTO language_content VALUES ('590', '11', '1', 'Are you sure you want to update the status of this user?');
INSERT INTO language_content VALUES ('591', '12', '1', 'view');
INSERT INTO language_content VALUES ('592', '13', '1', 'disable');
INSERT INTO language_content VALUES ('593', '14', '1', 'enable');
INSERT INTO language_content VALUES ('594', '15', '1', 'Are you sure you want to remove this banned word?');
INSERT INTO language_content VALUES ('595', '16', '1', 'IP address appears to be invalid, please try again.');
INSERT INTO language_content VALUES ('596', '17', '1', 'IP address is already in the blocked list.');
INSERT INTO language_content VALUES ('597', '18', '1', 'There was a problem inserting/updating the record, please try again later.');
INSERT INTO language_content VALUES ('598', '19', '1', 'Banned word is already in the list.');
INSERT INTO language_content VALUES ('599', '20', '1', 'Language already in the system.');
INSERT INTO language_content VALUES ('600', '21', '1', 'Username must be between 6-16 characters long.');
INSERT INTO language_content VALUES ('601', '22', '1', 'Password must be between 6-16 characters long.');
INSERT INTO language_content VALUES ('602', '23', '1', 'Please enter the firstname.');
INSERT INTO language_content VALUES ('603', '24', '1', 'Please enter the lastname.');
INSERT INTO language_content VALUES ('604', '25', '1', 'Please enter the email address.');
INSERT INTO language_content VALUES ('605', '26', '1', 'The email address you entered appears to be invalid.');
INSERT INTO language_content VALUES ('606', '27', '1', 'Copyright');
INSERT INTO language_content VALUES ('607', '28', '1', 'Support');
INSERT INTO language_content VALUES ('608', '30', '1', 'Admin Panel');
INSERT INTO language_content VALUES ('609', '31', '1', 'Logged in as');
INSERT INTO language_content VALUES ('610', '32', '1', 'To ban an IP Address <a href=\"#\" onClick=\"displayBannedIpPopup(); return false;\">click here</a> or delete any existing ones below:');
INSERT INTO language_content VALUES ('611', '33', '1', 'Add banned IP address');
INSERT INTO language_content VALUES ('612', '34', '1', 'remove');
INSERT INTO language_content VALUES ('613', '35', '1', 'IP Address');
INSERT INTO language_content VALUES ('614', '36', '1', 'Ban From');
INSERT INTO language_content VALUES ('615', '37', '1', 'Notes');
INSERT INTO language_content VALUES ('616', '38', '1', 'Add Banned IP');
INSERT INTO language_content VALUES ('617', '39', '1', 'There was an error submitting the form, please try again later.');
INSERT INTO language_content VALUES ('618', '40', '1', 'Enter IP Address details');
INSERT INTO language_content VALUES ('619', '41', '1', 'To ban an word within the original url <a href=\"#\" onClick=\"displayBannedWordsPopup(); return false;\">click here</a> or delete any existing ones below:');
INSERT INTO language_content VALUES ('620', '42', '1', 'Add banned word');
INSERT INTO language_content VALUES ('621', '43', '1', 'Banned Word');
INSERT INTO language_content VALUES ('622', '44', '1', 'Date Banned');
INSERT INTO language_content VALUES ('623', '45', '1', 'Ban Notes');
INSERT INTO language_content VALUES ('624', '46', '1', 'Action');
INSERT INTO language_content VALUES ('625', '47', '1', 'Enter Banned Word details');
INSERT INTO language_content VALUES ('626', '48', '1', 'Use the main navigation above to manage this site. A quick overview of the site can be seen below:');
INSERT INTO language_content VALUES ('627', '49', '1', 'New Files (last 14 days)');
INSERT INTO language_content VALUES ('628', '50', '1', 'New Files (last 12 months)');
INSERT INTO language_content VALUES ('629', '51', '1', 'Urls');
INSERT INTO language_content VALUES ('630', '52', '1', 'active');
INSERT INTO language_content VALUES ('631', '53', '1', 'disabled');
INSERT INTO language_content VALUES ('632', '54', '1', 'spam');
INSERT INTO language_content VALUES ('633', '55', '1', 'expired');
INSERT INTO language_content VALUES ('634', '56', '1', 'Total active files:');
INSERT INTO language_content VALUES ('635', '57', '1', 'Total disabled files:');
INSERT INTO language_content VALUES ('636', '58', '1', 'Total downloads to all files:');
INSERT INTO language_content VALUES ('637', '59', '1', 'Item Name');
INSERT INTO language_content VALUES ('638', '60', '1', 'Value');
INSERT INTO language_content VALUES ('639', '61', '1', 'Manage the available content for the selected language. Click on any of the \'Translated Content\' cells to edit the value.');
INSERT INTO language_content VALUES ('640', '62', '1', 'Select a language to manage or <a href=\'#\' onClick=\'displayAddLanguagePopup(); return false;\'>add a new one here</a>. NOTE: Once translated, to set the site default language go to the <a href=\'settings.php\'>site settings</a> area.');
INSERT INTO language_content VALUES ('641', '63', '1', 'Language Key');
INSERT INTO language_content VALUES ('642', '64', '1', 'Default Content');
INSERT INTO language_content VALUES ('643', '65', '1', 'Translated Content');
INSERT INTO language_content VALUES ('644', '66', '1', 'Error: Changes to this section can not be made within demo mode.');
INSERT INTO language_content VALUES ('645', '67', '1', 'Manage other languages');
INSERT INTO language_content VALUES ('646', '68', '1', 'There is no available content.');
INSERT INTO language_content VALUES ('647', '69', '1', 'select language');
INSERT INTO language_content VALUES ('648', '70', '1', 'Add Language');
INSERT INTO language_content VALUES ('649', '71', '1', 'Language Name');
INSERT INTO language_content VALUES ('650', '72', '1', 'Click on any of the items within the \"Config Value\" column below to edit:');
INSERT INTO language_content VALUES ('651', '73', '1', 'Group');
INSERT INTO language_content VALUES ('652', '74', '1', 'Config Description');
INSERT INTO language_content VALUES ('653', '75', '1', 'Config Value');
INSERT INTO language_content VALUES ('654', '76', '1', 'Filter results:');
INSERT INTO language_content VALUES ('655', '77', '1', 'Double click on any of the users below to edit the account information or <a href=\"#\" onClick=\"displayUserPopup(); return false;\">click here to add a new user</a>:');
INSERT INTO language_content VALUES ('656', '78', '1', 'Add new user');
INSERT INTO language_content VALUES ('657', '79', '1', 'Username');
INSERT INTO language_content VALUES ('658', '80', '1', 'Email Address');
INSERT INTO language_content VALUES ('659', '81', '1', 'Account Type');
INSERT INTO language_content VALUES ('660', '82', '1', 'Last Login');
INSERT INTO language_content VALUES ('661', '83', '1', 'Account Status');
INSERT INTO language_content VALUES ('662', '84', '1', 'Password');
INSERT INTO language_content VALUES ('663', '85', '1', 'Title');
INSERT INTO language_content VALUES ('664', '86', '1', 'Firstname');
INSERT INTO language_content VALUES ('665', '87', '1', 'Lastname');
INSERT INTO language_content VALUES ('666', '88', '1', 'Enter user details');
INSERT INTO language_content VALUES ('667', '90', '1', 'Terms &amp; Conditions');
INSERT INTO language_content VALUES ('668', '515', '1', 'Main Navigation');
INSERT INTO language_content VALUES ('669', '92', '1', 'Created By');
INSERT INTO language_content VALUES ('670', '94', '1', 'You are not permitted to upload files on this site.');
INSERT INTO language_content VALUES ('671', '95', '1', 'There was an error with the url you supplied, please check and try again.');
INSERT INTO language_content VALUES ('672', '96', '1', 'You can not upload files on this site.');
INSERT INTO language_content VALUES ('673', '97', '1', 'The date you entered is incorrect.');
INSERT INTO language_content VALUES ('674', '98', '1', 'That custom short url already exists, please try another.');
INSERT INTO language_content VALUES ('675', '99', '1', 'There was a problem uploading the file, please try again later.');
INSERT INTO language_content VALUES ('676', '100', '1', 'This url is access restricted, please enter the password below:');
INSERT INTO language_content VALUES ('677', '107', '1', 'Redirecting to');
INSERT INTO language_content VALUES ('678', '108', '1', 'please wait');
INSERT INTO language_content VALUES ('679', '109', '1', 'There was a general site error, please try again later.');
INSERT INTO language_content VALUES ('680', '110', '1', 'Error');
INSERT INTO language_content VALUES ('681', '153', '1', 'visits:');
INSERT INTO language_content VALUES ('682', '154', '1', 'created:');
INSERT INTO language_content VALUES ('683', '155', '1', 'Visitors');
INSERT INTO language_content VALUES ('684', '156', '1', 'Countries');
INSERT INTO language_content VALUES ('685', '157', '1', 'Top Referrers');
INSERT INTO language_content VALUES ('686', '158', '1', 'Browsers');
INSERT INTO language_content VALUES ('687', '159', '1', 'Operating Systems');
INSERT INTO language_content VALUES ('688', '160', '1', 'last 24 hours');
INSERT INTO language_content VALUES ('689', '161', '1', 'last 7 days');
INSERT INTO language_content VALUES ('690', '162', '1', 'last 30 days');
INSERT INTO language_content VALUES ('691', '163', '1', 'last 12 months');
INSERT INTO language_content VALUES ('692', '164', '1', 'Hour');
INSERT INTO language_content VALUES ('693', '165', '1', 'Visits');
INSERT INTO language_content VALUES ('694', '166', '1', 'Date');
INSERT INTO language_content VALUES ('695', '167', '1', 'Total visits');
INSERT INTO language_content VALUES ('696', '168', '1', 'Percentage');
INSERT INTO language_content VALUES ('697', '169', '1', 'Day');
INSERT INTO language_content VALUES ('698', '170', '1', 'Month');
INSERT INTO language_content VALUES ('699', '171', '1', 'Country');
INSERT INTO language_content VALUES ('700', '172', '1', 'Site');
INSERT INTO language_content VALUES ('701', '173', '1', 'Browser');
INSERT INTO language_content VALUES ('702', '174', '1', 'Operating System');
INSERT INTO language_content VALUES ('703', '175', '1', 'Andorra');
INSERT INTO language_content VALUES ('704', '176', '1', 'United Arab Emirates');
INSERT INTO language_content VALUES ('705', '177', '1', 'Afghanistan');
INSERT INTO language_content VALUES ('706', '178', '1', 'Antigua And Barbuda');
INSERT INTO language_content VALUES ('707', '179', '1', 'Anguilla');
INSERT INTO language_content VALUES ('708', '180', '1', 'Albania');
INSERT INTO language_content VALUES ('709', '181', '1', 'Armenia');
INSERT INTO language_content VALUES ('710', '182', '1', 'Netherlands Antilles');
INSERT INTO language_content VALUES ('711', '183', '1', 'Angola');
INSERT INTO language_content VALUES ('712', '184', '1', 'Antarctica');
INSERT INTO language_content VALUES ('713', '185', '1', 'Argentina');
INSERT INTO language_content VALUES ('714', '186', '1', 'American Samoa');
INSERT INTO language_content VALUES ('715', '187', '1', 'Austria');
INSERT INTO language_content VALUES ('716', '188', '1', 'Australia');
INSERT INTO language_content VALUES ('717', '189', '1', 'Aruba');
INSERT INTO language_content VALUES ('718', '190', '1', 'Azerbaijan');
INSERT INTO language_content VALUES ('719', '191', '1', 'Bosnia And Herzegovina');
INSERT INTO language_content VALUES ('720', '192', '1', 'Barbados');
INSERT INTO language_content VALUES ('721', '193', '1', 'Bangladesh');
INSERT INTO language_content VALUES ('722', '194', '1', 'Belgium');
INSERT INTO language_content VALUES ('723', '195', '1', 'Burkina Faso');
INSERT INTO language_content VALUES ('724', '196', '1', 'Bulgaria');
INSERT INTO language_content VALUES ('725', '197', '1', 'Bahrain');
INSERT INTO language_content VALUES ('726', '198', '1', 'Burundi');
INSERT INTO language_content VALUES ('727', '199', '1', 'Benin');
INSERT INTO language_content VALUES ('728', '200', '1', 'Bermuda');
INSERT INTO language_content VALUES ('729', '201', '1', 'Brunei Darussalam');
INSERT INTO language_content VALUES ('730', '202', '1', 'Bolivia');
INSERT INTO language_content VALUES ('731', '203', '1', 'Brazil');
INSERT INTO language_content VALUES ('732', '204', '1', 'Bahamas');
INSERT INTO language_content VALUES ('733', '205', '1', 'Bhutan');
INSERT INTO language_content VALUES ('734', '206', '1', 'Botswana');
INSERT INTO language_content VALUES ('735', '207', '1', 'Belarus');
INSERT INTO language_content VALUES ('736', '208', '1', 'Belize');
INSERT INTO language_content VALUES ('737', '209', '1', 'Canada');
INSERT INTO language_content VALUES ('738', '210', '1', 'The Democratic Republic Of The Congo');
INSERT INTO language_content VALUES ('739', '211', '1', 'Central African Republic');
INSERT INTO language_content VALUES ('740', '212', '1', 'Congo');
INSERT INTO language_content VALUES ('741', '213', '1', 'Switzerland');
INSERT INTO language_content VALUES ('742', '214', '1', 'Cote Divoire');
INSERT INTO language_content VALUES ('743', '215', '1', 'Cook Islands');
INSERT INTO language_content VALUES ('744', '216', '1', 'Chile');
INSERT INTO language_content VALUES ('745', '217', '1', 'Cameroon');
INSERT INTO language_content VALUES ('746', '218', '1', 'China');
INSERT INTO language_content VALUES ('747', '219', '1', 'Colombia');
INSERT INTO language_content VALUES ('748', '220', '1', 'Costa Rica');
INSERT INTO language_content VALUES ('749', '221', '1', 'Serbia And Montenegro');
INSERT INTO language_content VALUES ('750', '222', '1', 'Cuba');
INSERT INTO language_content VALUES ('751', '223', '1', 'Cape Verde');
INSERT INTO language_content VALUES ('752', '224', '1', 'Cyprus');
INSERT INTO language_content VALUES ('753', '225', '1', 'Czech Republic');
INSERT INTO language_content VALUES ('754', '226', '1', 'Germany');
INSERT INTO language_content VALUES ('755', '227', '1', 'Djibouti');
INSERT INTO language_content VALUES ('756', '228', '1', 'Denmark');
INSERT INTO language_content VALUES ('757', '229', '1', 'Dominica');
INSERT INTO language_content VALUES ('758', '230', '1', 'Dominican Republic');
INSERT INTO language_content VALUES ('759', '231', '1', 'Algeria');
INSERT INTO language_content VALUES ('760', '232', '1', 'Ecuador');
INSERT INTO language_content VALUES ('761', '233', '1', 'Estonia');
INSERT INTO language_content VALUES ('762', '234', '1', 'Egypt');
INSERT INTO language_content VALUES ('763', '235', '1', 'Eritrea');
INSERT INTO language_content VALUES ('764', '236', '1', 'Spain');
INSERT INTO language_content VALUES ('765', '237', '1', 'Ethiopia');
INSERT INTO language_content VALUES ('766', '238', '1', 'European Union');
INSERT INTO language_content VALUES ('767', '239', '1', 'Finland');
INSERT INTO language_content VALUES ('768', '240', '1', 'Fiji');
INSERT INTO language_content VALUES ('769', '241', '1', 'Falkland Islands (Malvinas)');
INSERT INTO language_content VALUES ('770', '242', '1', 'Federated States Of Micronesia');
INSERT INTO language_content VALUES ('771', '243', '1', 'Faroe Islands');
INSERT INTO language_content VALUES ('772', '244', '1', 'France');
INSERT INTO language_content VALUES ('773', '245', '1', 'Gabon');
INSERT INTO language_content VALUES ('774', '246', '1', 'United Kingdom');
INSERT INTO language_content VALUES ('775', '247', '1', 'Grenada');
INSERT INTO language_content VALUES ('776', '248', '1', 'Georgia');
INSERT INTO language_content VALUES ('777', '249', '1', 'French Guiana');
INSERT INTO language_content VALUES ('778', '250', '1', 'Ghana');
INSERT INTO language_content VALUES ('779', '251', '1', 'Gibraltar');
INSERT INTO language_content VALUES ('780', '252', '1', 'Greenland');
INSERT INTO language_content VALUES ('781', '253', '1', 'Gambia');
INSERT INTO language_content VALUES ('782', '254', '1', 'Guinea');
INSERT INTO language_content VALUES ('783', '255', '1', 'Guadeloupe');
INSERT INTO language_content VALUES ('784', '256', '1', 'Equatorial Guinea');
INSERT INTO language_content VALUES ('785', '257', '1', 'Greece');
INSERT INTO language_content VALUES ('786', '258', '1', 'South Georgia And The South Sandwich Islands');
INSERT INTO language_content VALUES ('787', '259', '1', 'Guatemala');
INSERT INTO language_content VALUES ('788', '260', '1', 'Guam');
INSERT INTO language_content VALUES ('789', '261', '1', 'Guinea-Bissau');
INSERT INTO language_content VALUES ('790', '262', '1', 'Guyana');
INSERT INTO language_content VALUES ('791', '263', '1', 'Hong Kong');
INSERT INTO language_content VALUES ('792', '264', '1', 'Honduras');
INSERT INTO language_content VALUES ('793', '265', '1', 'Croatia');
INSERT INTO language_content VALUES ('794', '266', '1', 'Haiti');
INSERT INTO language_content VALUES ('795', '267', '1', 'Hungary');
INSERT INTO language_content VALUES ('796', '268', '1', 'Indonesia');
INSERT INTO language_content VALUES ('797', '269', '1', 'Ireland');
INSERT INTO language_content VALUES ('798', '270', '1', 'Israel');
INSERT INTO language_content VALUES ('799', '271', '1', 'India');
INSERT INTO language_content VALUES ('800', '272', '1', 'British Indian Ocean Territory');
INSERT INTO language_content VALUES ('801', '273', '1', 'Iraq');
INSERT INTO language_content VALUES ('802', '274', '1', 'Islamic Republic Of Iran');
INSERT INTO language_content VALUES ('803', '275', '1', 'Iceland');
INSERT INTO language_content VALUES ('804', '276', '1', 'Italy');
INSERT INTO language_content VALUES ('805', '277', '1', 'Jamaica');
INSERT INTO language_content VALUES ('806', '278', '1', 'Jordan');
INSERT INTO language_content VALUES ('807', '279', '1', 'Japan');
INSERT INTO language_content VALUES ('808', '280', '1', 'Kenya');
INSERT INTO language_content VALUES ('809', '281', '1', 'Kyrgyzstan');
INSERT INTO language_content VALUES ('810', '282', '1', 'Cambodia');
INSERT INTO language_content VALUES ('811', '283', '1', 'Kiribati');
INSERT INTO language_content VALUES ('812', '284', '1', 'Comoros');
INSERT INTO language_content VALUES ('813', '285', '1', 'Saint Kitts And Nevis');
INSERT INTO language_content VALUES ('814', '286', '1', 'Republic Of Korea');
INSERT INTO language_content VALUES ('815', '287', '1', 'Kuwait');
INSERT INTO language_content VALUES ('816', '288', '1', 'Cayman Islands');
INSERT INTO language_content VALUES ('817', '289', '1', 'Kazakhstan');
INSERT INTO language_content VALUES ('818', '290', '1', 'Lao Peoples Democratic Republic');
INSERT INTO language_content VALUES ('819', '291', '1', 'Lebanon');
INSERT INTO language_content VALUES ('820', '292', '1', 'Saint Lucia');
INSERT INTO language_content VALUES ('821', '293', '1', 'Liechtenstein');
INSERT INTO language_content VALUES ('822', '294', '1', 'Sri Lanka');
INSERT INTO language_content VALUES ('823', '295', '1', 'Liberia');
INSERT INTO language_content VALUES ('824', '296', '1', 'Lesotho');
INSERT INTO language_content VALUES ('825', '297', '1', 'Lithuania');
INSERT INTO language_content VALUES ('826', '298', '1', 'Luxembourg');
INSERT INTO language_content VALUES ('827', '299', '1', 'Latvia');
INSERT INTO language_content VALUES ('828', '300', '1', 'Libyan Arab Jamahiriya');
INSERT INTO language_content VALUES ('829', '301', '1', 'Morocco');
INSERT INTO language_content VALUES ('830', '302', '1', 'Monaco');
INSERT INTO language_content VALUES ('831', '303', '1', 'Republic Of Moldova');
INSERT INTO language_content VALUES ('832', '304', '1', 'Madagascar');
INSERT INTO language_content VALUES ('833', '305', '1', 'Marshall Islands');
INSERT INTO language_content VALUES ('834', '306', '1', 'The Former Yugoslav Republic Of Macedonia');
INSERT INTO language_content VALUES ('835', '307', '1', 'Mali');
INSERT INTO language_content VALUES ('836', '308', '1', 'Myanmar');
INSERT INTO language_content VALUES ('837', '309', '1', 'Mongolia');
INSERT INTO language_content VALUES ('838', '310', '1', 'Macao');
INSERT INTO language_content VALUES ('839', '311', '1', 'Northern Mariana Islands');
INSERT INTO language_content VALUES ('840', '312', '1', 'Martinique');
INSERT INTO language_content VALUES ('841', '313', '1', 'Mauritania');
INSERT INTO language_content VALUES ('842', '314', '1', 'Malta');
INSERT INTO language_content VALUES ('843', '315', '1', 'Mauritius');
INSERT INTO language_content VALUES ('844', '316', '1', 'Maldives');
INSERT INTO language_content VALUES ('845', '317', '1', 'Malawi');
INSERT INTO language_content VALUES ('846', '318', '1', 'Mexico');
INSERT INTO language_content VALUES ('847', '319', '1', 'Malaysia');
INSERT INTO language_content VALUES ('848', '320', '1', 'Mozambique');
INSERT INTO language_content VALUES ('849', '321', '1', 'Namibia');
INSERT INTO language_content VALUES ('850', '322', '1', 'New Caledonia');
INSERT INTO language_content VALUES ('851', '323', '1', 'Niger');
INSERT INTO language_content VALUES ('852', '324', '1', 'Norfolk Island');
INSERT INTO language_content VALUES ('853', '325', '1', 'Nigeria');
INSERT INTO language_content VALUES ('854', '326', '1', 'Nicaragua');
INSERT INTO language_content VALUES ('855', '327', '1', 'Netherlands');
INSERT INTO language_content VALUES ('856', '328', '1', 'Norway');
INSERT INTO language_content VALUES ('857', '329', '1', 'Nepal');
INSERT INTO language_content VALUES ('858', '330', '1', 'Nauru');
INSERT INTO language_content VALUES ('859', '331', '1', 'Niue');
INSERT INTO language_content VALUES ('860', '332', '1', 'New Zealand');
INSERT INTO language_content VALUES ('861', '333', '1', 'Oman');
INSERT INTO language_content VALUES ('862', '334', '1', 'Panama');
INSERT INTO language_content VALUES ('863', '335', '1', 'Peru');
INSERT INTO language_content VALUES ('864', '336', '1', 'French Polynesia');
INSERT INTO language_content VALUES ('865', '337', '1', 'Papua New Guinea');
INSERT INTO language_content VALUES ('866', '338', '1', 'Philippines');
INSERT INTO language_content VALUES ('867', '339', '1', 'Pakistan');
INSERT INTO language_content VALUES ('868', '340', '1', 'Poland');
INSERT INTO language_content VALUES ('869', '341', '1', 'Puerto Rico');
INSERT INTO language_content VALUES ('870', '342', '1', 'Palestinian Territory');
INSERT INTO language_content VALUES ('871', '343', '1', 'Portugal');
INSERT INTO language_content VALUES ('872', '344', '1', 'Palau');
INSERT INTO language_content VALUES ('873', '345', '1', 'Paraguay');
INSERT INTO language_content VALUES ('874', '346', '1', 'Qatar');
INSERT INTO language_content VALUES ('875', '347', '1', 'Reunion');
INSERT INTO language_content VALUES ('876', '348', '1', 'Romania');
INSERT INTO language_content VALUES ('877', '349', '1', 'Russian Federation');
INSERT INTO language_content VALUES ('878', '350', '1', 'Rwanda');
INSERT INTO language_content VALUES ('879', '351', '1', 'Saudi Arabia');
INSERT INTO language_content VALUES ('880', '352', '1', 'Solomon Islands');
INSERT INTO language_content VALUES ('881', '353', '1', 'Seychelles');
INSERT INTO language_content VALUES ('882', '354', '1', 'Sudan');
INSERT INTO language_content VALUES ('883', '355', '1', 'Sweden');
INSERT INTO language_content VALUES ('884', '356', '1', 'Singapore');
INSERT INTO language_content VALUES ('885', '357', '1', 'Slovenia');
INSERT INTO language_content VALUES ('886', '358', '1', 'Slovakia (Slovak Republic)');
INSERT INTO language_content VALUES ('887', '359', '1', 'Sierra Leone');
INSERT INTO language_content VALUES ('888', '360', '1', 'San Marino');
INSERT INTO language_content VALUES ('889', '361', '1', 'Senegal');
INSERT INTO language_content VALUES ('890', '362', '1', 'Somalia');
INSERT INTO language_content VALUES ('891', '363', '1', 'Suriname');
INSERT INTO language_content VALUES ('892', '364', '1', 'Sao Tome And Principe');
INSERT INTO language_content VALUES ('893', '365', '1', 'El Salvador');
INSERT INTO language_content VALUES ('894', '366', '1', 'Syrian Arab Republic');
INSERT INTO language_content VALUES ('895', '367', '1', 'Swaziland');
INSERT INTO language_content VALUES ('896', '368', '1', 'Chad');
INSERT INTO language_content VALUES ('897', '369', '1', 'French Southern Territories');
INSERT INTO language_content VALUES ('898', '370', '1', 'Togo');
INSERT INTO language_content VALUES ('899', '371', '1', 'Thailand');
INSERT INTO language_content VALUES ('900', '372', '1', 'Tajikistan');
INSERT INTO language_content VALUES ('901', '373', '1', 'Tokelau');
INSERT INTO language_content VALUES ('902', '374', '1', 'Timor-Leste');
INSERT INTO language_content VALUES ('903', '375', '1', 'Turkmenistan');
INSERT INTO language_content VALUES ('904', '376', '1', 'Tunisia');
INSERT INTO language_content VALUES ('905', '377', '1', 'Tonga');
INSERT INTO language_content VALUES ('906', '378', '1', 'Turkey');
INSERT INTO language_content VALUES ('907', '379', '1', 'Trinidad And Tobago');
INSERT INTO language_content VALUES ('908', '380', '1', 'Tuvalu');
INSERT INTO language_content VALUES ('909', '381', '1', 'Taiwan Province Of China');
INSERT INTO language_content VALUES ('910', '382', '1', 'United Republic Of Tanzania');
INSERT INTO language_content VALUES ('911', '383', '1', 'Ukraine');
INSERT INTO language_content VALUES ('912', '384', '1', 'Uganda');
INSERT INTO language_content VALUES ('913', '385', '1', 'United States');
INSERT INTO language_content VALUES ('914', '386', '1', 'Uruguay');
INSERT INTO language_content VALUES ('915', '387', '1', 'Uzbekistan');
INSERT INTO language_content VALUES ('916', '388', '1', 'Holy See (Vatican City State)');
INSERT INTO language_content VALUES ('917', '389', '1', 'Saint Vincent And The Grenadines');
INSERT INTO language_content VALUES ('918', '390', '1', 'Venezuela');
INSERT INTO language_content VALUES ('919', '391', '1', 'Virgin Islands');
INSERT INTO language_content VALUES ('920', '392', '1', 'Virgin Islands');
INSERT INTO language_content VALUES ('921', '393', '1', 'Viet Nam');
INSERT INTO language_content VALUES ('922', '394', '1', 'Vanuatu');
INSERT INTO language_content VALUES ('923', '395', '1', 'Samoa');
INSERT INTO language_content VALUES ('924', '396', '1', 'Yemen');
INSERT INTO language_content VALUES ('925', '397', '1', 'Mayotte');
INSERT INTO language_content VALUES ('926', '398', '1', 'Serbia And Montenegro (Formally Yugoslavia)');
INSERT INTO language_content VALUES ('927', '399', '1', 'South Africa');
INSERT INTO language_content VALUES ('928', '400', '1', 'Zambia');
INSERT INTO language_content VALUES ('929', '401', '1', 'Zimbabwe');
INSERT INTO language_content VALUES ('930', '402', '1', 'Unknown');
INSERT INTO language_content VALUES ('931', '404', '1', 'Show disabled:');
INSERT INTO language_content VALUES ('932', '405', '1', 'register');
INSERT INTO language_content VALUES ('933', '408', '1', 'Login');
INSERT INTO language_content VALUES ('934', '409', '1', 'Account Home');
INSERT INTO language_content VALUES ('935', '410', '1', 'Registration completed');
INSERT INTO language_content VALUES ('936', '411', '1', 'Your registration has been completed.');
INSERT INTO language_content VALUES ('937', '412', '1', 'registration, completed, file, hosting, site');
INSERT INTO language_content VALUES ('938', '413', '1', 'Thank you for registering!');
INSERT INTO language_content VALUES ('939', '414', '1', 'We\'ve sent an email to your registered email address with your access password. Please check your spam filters to ensure emails from this site get through. ');
INSERT INTO language_content VALUES ('940', '415', '1', 'Emails from this site are sent from ');
INSERT INTO language_content VALUES ('941', '416', '1', 'Login');
INSERT INTO language_content VALUES ('942', '417', '1', 'Login to your account');
INSERT INTO language_content VALUES ('943', '418', '1', 'login, register, short url');
INSERT INTO language_content VALUES ('944', '419', '1', 'Your username and password are invalid');
INSERT INTO language_content VALUES ('945', '420', '1', 'Account Login');
INSERT INTO language_content VALUES ('946', '421', '1', 'Please enter your username and password below to login.');
INSERT INTO language_content VALUES ('947', '422', '1', 'Your account username. 6 characters or more and alpha numeric.');
INSERT INTO language_content VALUES ('948', '423', '1', 'Your account password. Min 6 characters, alpha numeric, no spaces.');
INSERT INTO language_content VALUES ('949', '428', '1', 'Please enter your username');
INSERT INTO language_content VALUES ('950', '429', '1', 'Account Home');
INSERT INTO language_content VALUES ('951', '430', '1', 'Your Account Home');
INSERT INTO language_content VALUES ('952', '431', '1', 'account, home, file, hosting, members, area');
INSERT INTO language_content VALUES ('953', '433', '1', 'faq');
INSERT INTO language_content VALUES ('954', '434', '1', 'FAQ');
INSERT INTO language_content VALUES ('955', '435', '1', 'Frequently Asked Questions');
INSERT INTO language_content VALUES ('956', '436', '1', 'faq, frequently, asked, questions, file, hosting, site');
INSERT INTO language_content VALUES ('957', '437', '1', 'Please enter your password');
INSERT INTO language_content VALUES ('958', '511', '1', 'Report Abuse');
INSERT INTO language_content VALUES ('959', '441', '1', 'Register Account');
INSERT INTO language_content VALUES ('960', '444', '1', 'email confirm');
INSERT INTO language_content VALUES ('961', '445', '1', 'stats');
INSERT INTO language_content VALUES ('962', '446', '1', 'info');
INSERT INTO language_content VALUES ('963', '447', '1', 'Email Confirm');
INSERT INTO language_content VALUES ('964', '449', '1', 'Created/Last Visited:');
INSERT INTO language_content VALUES ('965', '450', '1', 'Status:');
INSERT INTO language_content VALUES ('966', '451', '1', 'Options:');
INSERT INTO language_content VALUES ('967', '452', '1', 'upload file');
INSERT INTO language_content VALUES ('968', '453', '1', 'Register');
INSERT INTO language_content VALUES ('969', '454', '1', 'Register for an account');
INSERT INTO language_content VALUES ('970', '455', '1', 'register, account, short, url, user');
INSERT INTO language_content VALUES ('971', '456', '1', 'your files');
INSERT INTO language_content VALUES ('972', '457', '1', 'File has been removed.');
INSERT INTO language_content VALUES ('973', '458', '1', 'Uploaded');
INSERT INTO language_content VALUES ('974', '459', '1', 'downloads');
INSERT INTO language_content VALUES ('975', '460', '1', 'download now');
INSERT INTO language_content VALUES ('976', '461', '1', 'loading file, please wait');
INSERT INTO language_content VALUES ('977', '462', '1', 'Download File');
INSERT INTO language_content VALUES ('978', '463', '1', 'Download file');
INSERT INTO language_content VALUES ('979', '464', '1', 'download, file, upload, mp3, avi, zip');
INSERT INTO language_content VALUES ('980', '465', '1', 'Your Files');
INSERT INTO language_content VALUES ('981', '466', '1', 'Download Url:');
INSERT INTO language_content VALUES ('982', '467', '1', 'Uploaded/Last Visited:');
INSERT INTO language_content VALUES ('983', '468', '1', 'Download Url/Filename:');
INSERT INTO language_content VALUES ('984', '469', '1', 'Total Active Files');
INSERT INTO language_content VALUES ('985', '470', '1', 'Total Inactive Files');
INSERT INTO language_content VALUES ('986', '471', '1', 'Total Downloads');
INSERT INTO language_content VALUES ('987', '472', '1', 'user removed');
INSERT INTO language_content VALUES ('988', '473', '1', 'files');
INSERT INTO language_content VALUES ('989', '474', '1', 'Manage Files');
INSERT INTO language_content VALUES ('990', '475', '1', 'Filter Results:');
INSERT INTO language_content VALUES ('991', '476', '1', 'Show Disabled');
INSERT INTO language_content VALUES ('992', '477', '1', 'Export File Data');
INSERT INTO language_content VALUES ('993', '478', '1', 'File has been removed by the site administrator.');
INSERT INTO language_content VALUES ('994', '479', '1', 'Show Removed');
INSERT INTO language_content VALUES ('995', '480', '1', 'admin removed');
INSERT INTO language_content VALUES ('996', '481', '1', 'Delete File');
INSERT INTO language_content VALUES ('997', '482', '1', 'Delete File');
INSERT INTO language_content VALUES ('998', '483', '1', 'delete, remove, file');
INSERT INTO language_content VALUES ('999', '484', '1', 'Delete File');
INSERT INTO language_content VALUES ('1000', '485', '1', 'Please confirm whether to delete the file below.');
INSERT INTO language_content VALUES ('1001', '486', '1', 'Cancel');
INSERT INTO language_content VALUES ('1002', '487', '1', 'report file');
INSERT INTO language_content VALUES ('1003', '488', '1', 'upgrade account');
INSERT INTO language_content VALUES ('1004', '489', '1', 'Terms and Conditions');
INSERT INTO language_content VALUES ('1005', '490', '1', 'Terms and Conditions');
INSERT INTO language_content VALUES ('1006', '491', '1', 'terms, and, conditions, file, hosting, site');
INSERT INTO language_content VALUES ('1007', '492', '1', 'extend account');
INSERT INTO language_content VALUES ('1008', '493', '1', 'Extend Account');
INSERT INTO language_content VALUES ('1009', '494', '1', 'Extend Your Account');
INSERT INTO language_content VALUES ('1010', '495', '1', 'extend, account, paid, membership, upload, download, site');
INSERT INTO language_content VALUES ('1011', '496', '1', 'Payment Complete');
INSERT INTO language_content VALUES ('1012', '497', '1', 'Payment Complete');
INSERT INTO language_content VALUES ('1013', '498', '1', 'payment, complete, file, hosting, site');
INSERT INTO language_content VALUES ('1014', '499', '1', 'premium account benefits');
INSERT INTO language_content VALUES ('1015', '500', '1', 'account benefits');
INSERT INTO language_content VALUES ('1016', '501', '1', ' Information');
INSERT INTO language_content VALUES ('1017', '502', '1', 'Information about ');
INSERT INTO language_content VALUES ('1018', '503', '1', ', share, information, file, upload, download, site');
INSERT INTO language_content VALUES ('1019', '504', '1', 'download urls');
INSERT INTO language_content VALUES ('1020', '505', '1', 'statistics');
INSERT INTO language_content VALUES ('1021', '506', '1', 'share');
INSERT INTO language_content VALUES ('1022', '507', '1', 'other options');
INSERT INTO language_content VALUES ('1023', '508', '1', 'Enter the details of the file (as above) you wish to report.');
INSERT INTO language_content VALUES ('1024', '510', '1', 'Please enter the details of the reported file.');
INSERT INTO language_content VALUES ('1025', '516', '1', 'Legal Bits');
INSERT INTO language_content VALUES ('1026', '517', '1', 'Your Account');
INSERT INTO language_content VALUES ('1027', '518', '1', 'days');
INSERT INTO language_content VALUES ('1028', '519', '1', 'premium');
INSERT INTO language_content VALUES ('1029', '520', '1', 'Pay via PayPal');
INSERT INTO language_content VALUES ('1030', '521', '1', 'secure payment');
INSERT INTO language_content VALUES ('1031', '522', '1', '100% Safe & Anonymous');
INSERT INTO language_content VALUES ('1032', '523', '1', 'Add files...');
INSERT INTO language_content VALUES ('1033', '524', '1', 'Start upload');
INSERT INTO language_content VALUES ('1034', '525', '1', 'Cancel upload');
INSERT INTO language_content VALUES ('1035', '526', '1', 'Select files');
INSERT INTO language_content VALUES ('1036', '527', '1', 'Drag &amp; drop files here or click to browse...');
INSERT INTO language_content VALUES ('1037', '528', '1', 'Max file size');
INSERT INTO language_content VALUES ('1038', '529', '1', 'add file');
INSERT INTO language_content VALUES ('1039', '530', '1', 'copy all links');
INSERT INTO language_content VALUES ('1040', '531', '1', 'File uploads completed.');
INSERT INTO language_content VALUES ('1041', '532', '1', 'Delete Url');
INSERT INTO language_content VALUES ('1042', '533', '1', 'Stats Url');
INSERT INTO language_content VALUES ('1043', '534', '1', 'HTML Code');
INSERT INTO language_content VALUES ('1044', '535', '1', 'Forum Code');
INSERT INTO language_content VALUES ('1045', '536', '1', 'Full Info');
INSERT INTO language_content VALUES ('1046', '537', '1', 'click here');
INSERT INTO language_content VALUES ('1047', '538', '1', 'extend');
INSERT INTO language_content VALUES ('1048', '539', '1', 'reverts to free account');
INSERT INTO language_content VALUES ('1049', '540', '1', 'never');
INSERT INTO language_content VALUES ('1050', '541', '1', 'filename');
INSERT INTO language_content VALUES ('1051', '542', '1', 'download');
INSERT INTO language_content VALUES ('1052', '543', '1', 'filesize');
INSERT INTO language_content VALUES ('1053', '544', '1', 'url');
INSERT INTO language_content VALUES ('1054', '545', '1', 'Download from');
INSERT INTO language_content VALUES ('1055', '546', '1', 'share file');
INSERT INTO language_content VALUES ('1056', '549', '1', 'upload, share, track, file, hosting, host');
INSERT INTO language_content VALUES ('1057', '548', '1', 'Upload, share, track, manage your files in one simple to use file host.');
INSERT INTO language_content VALUES ('1058', '547', '1', 'Upload Files');
INSERT INTO language_content VALUES ('1059', '550', '1', 'Please enter your firstname');
INSERT INTO language_content VALUES ('1060', '550', '1', 'Please enter your firstname');
INSERT INTO language_content VALUES ('1061', '551', '1', 'Click here to browse your files...');

-- ----------------------------
-- Table structure for `language_key`
-- ----------------------------
DROP TABLE IF EXISTS `language_key`;
CREATE TABLE `language_key` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `languageKey` varchar(255) NOT NULL,
  `defaultContent` text NOT NULL,
  `isAdminArea` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `languageKey` (`languageKey`)
) ENGINE=MyISAM AUTO_INCREMENT=552 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of language_key
-- ----------------------------
INSERT INTO language_key VALUES ('1', 'home', 'home', '1');
INSERT INTO language_key VALUES ('3', 'banned_words_urls', 'banned words / urls', '1');
INSERT INTO language_key VALUES ('4', 'admin_users', 'admin users', '1');
INSERT INTO language_key VALUES ('5', 'banned_ips', 'banned ips', '1');
INSERT INTO language_key VALUES ('6', 'site_settings', 'site settings', '1');
INSERT INTO language_key VALUES ('7', 'languages', 'languages', '1');
INSERT INTO language_key VALUES ('8', 'logout', 'logout', '1');
INSERT INTO language_key VALUES ('9', 'language_details', 'Language Details', '1');
INSERT INTO language_key VALUES ('10', 'are_you_sure_you_want_to_remove_this_ip_ban', 'Are you sure you want to remove this IP ban?', '1');
INSERT INTO language_key VALUES ('11', 'are_you_sure_update_user_status', 'Are you sure you want to update the status of this user?', '1');
INSERT INTO language_key VALUES ('12', 'view', 'view', '1');
INSERT INTO language_key VALUES ('13', 'disable', 'disable', '1');
INSERT INTO language_key VALUES ('14', 'enable', 'enable', '1');
INSERT INTO language_key VALUES ('15', 'are_you_sure_remove_banned_word', 'Are you sure you want to remove this banned word?', '1');
INSERT INTO language_key VALUES ('16', 'ip_address_invalid_try_again', 'IP address appears to be invalid, please try again.', '1');
INSERT INTO language_key VALUES ('17', 'ip_address_already_blocked', 'IP address is already in the blocked list.', '1');
INSERT INTO language_key VALUES ('18', 'error_problem_record', 'There was a problem inserting/updating the record, please try again later.', '1');
INSERT INTO language_key VALUES ('19', 'banned_word_already_in_list', 'Banned word is already in the list.', '1');
INSERT INTO language_key VALUES ('20', 'language_already_in_system', 'Language already in the system.', '1');
INSERT INTO language_key VALUES ('21', 'username_length_invalid', 'Username must be between 6-16 characters long.', '1');
INSERT INTO language_key VALUES ('22', 'password_length_invalid', 'Password must be between 6-16 characters long.', '1');
INSERT INTO language_key VALUES ('23', 'enter_first_name', 'Please enter the firstname.', '1');
INSERT INTO language_key VALUES ('24', 'enter_last_name', 'Please enter the lastname.', '1');
INSERT INTO language_key VALUES ('25', 'enter_email_address', 'Please enter the email address.', '1');
INSERT INTO language_key VALUES ('26', 'entered_email_address_invalid', 'The email address you entered appears to be invalid.', '1');
INSERT INTO language_key VALUES ('27', 'copyright', 'Copyright', '1');
INSERT INTO language_key VALUES ('28', 'support', 'Support', '1');
INSERT INTO language_key VALUES ('30', 'admin_panel', 'Admin Panel', '1');
INSERT INTO language_key VALUES ('31', 'logged_in_as', 'Logged in as', '1');
INSERT INTO language_key VALUES ('32', 'banned_ips_intro', 'To ban an IP Address <a href=\"#\" onClick=\"displayBannedIpPopup(); return false;\">click here</a> or delete any existing ones below:', '1');
INSERT INTO language_key VALUES ('33', 'banned_ips_add_banned_ip', 'Add banned IP address', '1');
INSERT INTO language_key VALUES ('34', 'remove', 'remove', '1');
INSERT INTO language_key VALUES ('35', 'ip_address', 'IP Address', '1');
INSERT INTO language_key VALUES ('36', 'ban_from', 'Ban From', '1');
INSERT INTO language_key VALUES ('37', 'notes', 'Notes', '1');
INSERT INTO language_key VALUES ('38', 'add_banned_ip', 'Add Banned IP', '1');
INSERT INTO language_key VALUES ('39', 'error_submitting_form', 'There was an error submitting the form, please try again later.', '1');
INSERT INTO language_key VALUES ('40', 'enter_ip_address_details', 'Enter IP Address details', '1');
INSERT INTO language_key VALUES ('41', 'banned_terms_intro', 'To ban an word within the original url <a href=\"#\" onClick=\"displayBannedWordsPopup(); return false;\">click here</a> or delete any existing ones below:', '1');
INSERT INTO language_key VALUES ('42', 'add_banned_term', 'Add banned word', '1');
INSERT INTO language_key VALUES ('43', 'banned_term', 'Banned Word', '1');
INSERT INTO language_key VALUES ('44', 'date_banned', 'Date Banned', '1');
INSERT INTO language_key VALUES ('45', 'ban_notes', 'Ban Notes', '1');
INSERT INTO language_key VALUES ('46', 'action', 'Action', '1');
INSERT INTO language_key VALUES ('47', 'enter_banned_term_details', 'Enter Banned Word details', '1');
INSERT INTO language_key VALUES ('48', 'dashboard_intro', 'Use the main navigation above to manage this site. A quick overview of the site can be seen below:', '1');
INSERT INTO language_key VALUES ('49', 'dashboard_graph_last_14_days_title', 'New Files (last 14 days)', '1');
INSERT INTO language_key VALUES ('50', 'dashboard_graph_last_12_months_title', 'New Files (last 12 months)', '1');
INSERT INTO language_key VALUES ('51', 'urls', 'Urls', '1');
INSERT INTO language_key VALUES ('52', 'active', 'active', '1');
INSERT INTO language_key VALUES ('53', 'disabled', 'disabled', '1');
INSERT INTO language_key VALUES ('54', 'spam', 'spam', '1');
INSERT INTO language_key VALUES ('55', 'expired', 'expired', '1');
INSERT INTO language_key VALUES ('56', 'dashboard_total_active_urls', 'Total active files:', '1');
INSERT INTO language_key VALUES ('57', 'dashboard_total_disabled_urls', 'Total disabled files:', '1');
INSERT INTO language_key VALUES ('58', 'dashboard_total_visits_to_all_urls', 'Total downloads to all files:', '1');
INSERT INTO language_key VALUES ('59', 'item_name', 'Item Name', '1');
INSERT INTO language_key VALUES ('60', 'value', 'Value', '1');
INSERT INTO language_key VALUES ('61', 'manage_languages_intro_2', 'Manage the available content for the selected language. Click on any of the \'Translated Content\' cells to edit the value.', '1');
INSERT INTO language_key VALUES ('62', 'manage_languages_intro_1', 'Select a language to manage or <a href=\'#\' onClick=\'displayAddLanguagePopup(); return false;\'>add a new one here</a>. NOTE: Once translated, to set the site default language go to the <a href=\'settings.php\'>site settings</a> area.', '1');
INSERT INTO language_key VALUES ('63', 'language_key', 'Language Key', '1');
INSERT INTO language_key VALUES ('64', 'default_content', 'Default Content', '1');
INSERT INTO language_key VALUES ('65', 'translated_content', 'Translated Content', '1');
INSERT INTO language_key VALUES ('66', 'no_changes_in_demo_mode', 'Error: Changes to this section can not be made within demo mode.', '1');
INSERT INTO language_key VALUES ('67', 'manage_other_languages', 'Manage other languages', '1');
INSERT INTO language_key VALUES ('68', 'no_available_content', 'There is no available content.', '1');
INSERT INTO language_key VALUES ('69', 'select_language', 'select language', '1');
INSERT INTO language_key VALUES ('70', 'add_language', 'Add Language', '1');
INSERT INTO language_key VALUES ('71', 'language_name', 'Language Name', '1');
INSERT INTO language_key VALUES ('72', 'settings_intro', 'Click on any of the items within the \"Config Value\" column below to edit:', '1');
INSERT INTO language_key VALUES ('73', 'group', 'Group', '1');
INSERT INTO language_key VALUES ('74', 'config_description', 'Config Description', '1');
INSERT INTO language_key VALUES ('75', 'config_value', 'Config Value', '1');
INSERT INTO language_key VALUES ('76', 'shorturls_filter_results', 'Filter results:', '1');
INSERT INTO language_key VALUES ('77', 'user_management_intro', 'Double click on any of the users below to edit the account information or <a href=\"#\" onClick=\"displayUserPopup(); return false;\">click here to add a new user</a>:', '1');
INSERT INTO language_key VALUES ('78', 'add_new_user', 'Add new user', '1');
INSERT INTO language_key VALUES ('79', 'username', 'Username', '1');
INSERT INTO language_key VALUES ('80', 'email_address', 'Email Address', '1');
INSERT INTO language_key VALUES ('81', 'account_type', 'Account Type', '1');
INSERT INTO language_key VALUES ('82', 'last_login', 'Last Login', '1');
INSERT INTO language_key VALUES ('83', 'account_status', 'Account Status', '1');
INSERT INTO language_key VALUES ('84', 'password', 'Password', '1');
INSERT INTO language_key VALUES ('85', 'title', 'Title', '1');
INSERT INTO language_key VALUES ('86', 'firstname', 'Firstname', '1');
INSERT INTO language_key VALUES ('87', 'lastname', 'Lastname', '1');
INSERT INTO language_key VALUES ('88', 'enter_user_details', 'Enter user details', '1');
INSERT INTO language_key VALUES ('90', 'term_and_conditions', 'Terms &amp; Conditions', '0');
INSERT INTO language_key VALUES ('515', 'main_navigation', 'Main Navigation', '0');
INSERT INTO language_key VALUES ('92', 'created_by', 'Created By', '0');
INSERT INTO language_key VALUES ('94', 'not_permitted_to_create_urls_on_site', 'You are not permitted to upload files on this site.', '0');
INSERT INTO language_key VALUES ('95', 'error_with_url', 'There was an error with the url you supplied, please check and try again.', '0');
INSERT INTO language_key VALUES ('96', 'can_not_create_url_on_this_site', 'You can not upload files on this site.', '0');
INSERT INTO language_key VALUES ('97', 'date_entered_is_incorrect', 'The date you entered is incorrect.', '0');
INSERT INTO language_key VALUES ('98', 'custom_short_url_already_exits', 'That custom short url already exists, please try another.', '0');
INSERT INTO language_key VALUES ('99', 'problem_creating_short_url', 'There was a problem uploading the file, please try again later.', '0');
INSERT INTO language_key VALUES ('100', 'access_restricted_enter_password', 'This url is access restricted, please enter the password below:', '0');
INSERT INTO language_key VALUES ('107', 'redirecting_to', 'Redirecting to', '0');
INSERT INTO language_key VALUES ('108', 'please_wait', 'please wait', '0');
INSERT INTO language_key VALUES ('109', 'general_site_error', 'There was a general site error, please try again later.', '0');
INSERT INTO language_key VALUES ('110', 'error', 'Error', '0');
INSERT INTO language_key VALUES ('153', 'visits_', 'visits:', '0');
INSERT INTO language_key VALUES ('154', 'created_', 'created:', '0');
INSERT INTO language_key VALUES ('155', 'visitors', 'Visitors', '0');
INSERT INTO language_key VALUES ('156', 'countries', 'Countries', '0');
INSERT INTO language_key VALUES ('157', 'top_referrers', 'Top Referrers', '0');
INSERT INTO language_key VALUES ('158', 'browsers', 'Browsers', '0');
INSERT INTO language_key VALUES ('159', 'operating_systems', 'Operating Systems', '0');
INSERT INTO language_key VALUES ('160', 'last_24_hours', 'last 24 hours', '0');
INSERT INTO language_key VALUES ('161', 'last_7_days', 'last 7 days', '0');
INSERT INTO language_key VALUES ('162', 'last_30_days', 'last 30 days', '0');
INSERT INTO language_key VALUES ('163', 'last_12_months', 'last 12 months', '0');
INSERT INTO language_key VALUES ('164', 'hour', 'Hour', '0');
INSERT INTO language_key VALUES ('165', 'visits', 'Visits', '0');
INSERT INTO language_key VALUES ('166', 'date', 'Date', '0');
INSERT INTO language_key VALUES ('167', 'total_visits', 'Total visits', '0');
INSERT INTO language_key VALUES ('168', 'percentage', 'Percentage', '0');
INSERT INTO language_key VALUES ('169', 'day', 'Day', '0');
INSERT INTO language_key VALUES ('170', 'month', 'Month', '0');
INSERT INTO language_key VALUES ('171', 'country', 'Country', '0');
INSERT INTO language_key VALUES ('172', 'site', 'Site', '0');
INSERT INTO language_key VALUES ('173', 'browser', 'Browser', '0');
INSERT INTO language_key VALUES ('174', 'operating_system', 'Operating System', '0');
INSERT INTO language_key VALUES ('175', 'AD', 'Andorra', '0');
INSERT INTO language_key VALUES ('176', 'AE', 'United Arab Emirates', '0');
INSERT INTO language_key VALUES ('177', 'AF', 'Afghanistan', '0');
INSERT INTO language_key VALUES ('178', 'AG', 'Antigua And Barbuda', '0');
INSERT INTO language_key VALUES ('179', 'AI', 'Anguilla', '0');
INSERT INTO language_key VALUES ('180', 'AL', 'Albania', '0');
INSERT INTO language_key VALUES ('181', 'AM', 'Armenia', '0');
INSERT INTO language_key VALUES ('182', 'AN', 'Netherlands Antilles', '0');
INSERT INTO language_key VALUES ('183', 'AO', 'Angola', '0');
INSERT INTO language_key VALUES ('184', 'AQ', 'Antarctica', '0');
INSERT INTO language_key VALUES ('185', 'AR', 'Argentina', '0');
INSERT INTO language_key VALUES ('186', 'AS', 'American Samoa', '0');
INSERT INTO language_key VALUES ('187', 'AT', 'Austria', '0');
INSERT INTO language_key VALUES ('188', 'AU', 'Australia', '0');
INSERT INTO language_key VALUES ('189', 'AW', 'Aruba', '0');
INSERT INTO language_key VALUES ('190', 'AZ', 'Azerbaijan', '0');
INSERT INTO language_key VALUES ('191', 'BA', 'Bosnia And Herzegovina', '0');
INSERT INTO language_key VALUES ('192', 'BB', 'Barbados', '0');
INSERT INTO language_key VALUES ('193', 'BD', 'Bangladesh', '0');
INSERT INTO language_key VALUES ('194', 'BE', 'Belgium', '0');
INSERT INTO language_key VALUES ('195', 'BF', 'Burkina Faso', '0');
INSERT INTO language_key VALUES ('196', 'BG', 'Bulgaria', '0');
INSERT INTO language_key VALUES ('197', 'BH', 'Bahrain', '0');
INSERT INTO language_key VALUES ('198', 'BI', 'Burundi', '0');
INSERT INTO language_key VALUES ('199', 'BJ', 'Benin', '0');
INSERT INTO language_key VALUES ('200', 'BM', 'Bermuda', '0');
INSERT INTO language_key VALUES ('201', 'BN', 'Brunei Darussalam', '0');
INSERT INTO language_key VALUES ('202', 'BO', 'Bolivia', '0');
INSERT INTO language_key VALUES ('203', 'BR', 'Brazil', '0');
INSERT INTO language_key VALUES ('204', 'BS', 'Bahamas', '0');
INSERT INTO language_key VALUES ('205', 'BT', 'Bhutan', '0');
INSERT INTO language_key VALUES ('206', 'BW', 'Botswana', '0');
INSERT INTO language_key VALUES ('207', 'BY', 'Belarus', '0');
INSERT INTO language_key VALUES ('208', 'BZ', 'Belize', '0');
INSERT INTO language_key VALUES ('209', 'CA', 'Canada', '0');
INSERT INTO language_key VALUES ('210', 'CD', 'The Democratic Republic Of The Congo', '0');
INSERT INTO language_key VALUES ('211', 'CF', 'Central African Republic', '0');
INSERT INTO language_key VALUES ('212', 'CG', 'Congo', '0');
INSERT INTO language_key VALUES ('213', 'CH', 'Switzerland', '0');
INSERT INTO language_key VALUES ('214', 'CI', 'Cote Divoire', '0');
INSERT INTO language_key VALUES ('215', 'CK', 'Cook Islands', '0');
INSERT INTO language_key VALUES ('216', 'CL', 'Chile', '0');
INSERT INTO language_key VALUES ('217', 'CM', 'Cameroon', '0');
INSERT INTO language_key VALUES ('218', 'CN', 'China', '0');
INSERT INTO language_key VALUES ('219', 'CO', 'Colombia', '0');
INSERT INTO language_key VALUES ('220', 'CR', 'Costa Rica', '0');
INSERT INTO language_key VALUES ('221', 'CS', 'Serbia And Montenegro', '0');
INSERT INTO language_key VALUES ('222', 'CU', 'Cuba', '0');
INSERT INTO language_key VALUES ('223', 'CV', 'Cape Verde', '0');
INSERT INTO language_key VALUES ('224', 'CY', 'Cyprus', '0');
INSERT INTO language_key VALUES ('225', 'CZ', 'Czech Republic', '0');
INSERT INTO language_key VALUES ('226', 'DE', 'Germany', '0');
INSERT INTO language_key VALUES ('227', 'DJ', 'Djibouti', '0');
INSERT INTO language_key VALUES ('228', 'DK', 'Denmark', '0');
INSERT INTO language_key VALUES ('229', 'DM', 'Dominica', '0');
INSERT INTO language_key VALUES ('230', 'DO', 'Dominican Republic', '0');
INSERT INTO language_key VALUES ('231', 'DZ', 'Algeria', '0');
INSERT INTO language_key VALUES ('232', 'EC', 'Ecuador', '0');
INSERT INTO language_key VALUES ('233', 'EE', 'Estonia', '0');
INSERT INTO language_key VALUES ('234', 'EG', 'Egypt', '0');
INSERT INTO language_key VALUES ('235', 'ER', 'Eritrea', '0');
INSERT INTO language_key VALUES ('236', 'ES', 'Spain', '0');
INSERT INTO language_key VALUES ('237', 'ET', 'Ethiopia', '0');
INSERT INTO language_key VALUES ('238', 'EU', 'European Union', '0');
INSERT INTO language_key VALUES ('239', 'FI', 'Finland', '0');
INSERT INTO language_key VALUES ('240', 'FJ', 'Fiji', '0');
INSERT INTO language_key VALUES ('241', 'FK', 'Falkland Islands (Malvinas)', '0');
INSERT INTO language_key VALUES ('242', 'FM', 'Federated States Of Micronesia', '0');
INSERT INTO language_key VALUES ('243', 'FO', 'Faroe Islands', '0');
INSERT INTO language_key VALUES ('244', 'FR', 'France', '0');
INSERT INTO language_key VALUES ('245', 'GA', 'Gabon', '0');
INSERT INTO language_key VALUES ('246', 'GB', 'United Kingdom', '0');
INSERT INTO language_key VALUES ('247', 'GD', 'Grenada', '0');
INSERT INTO language_key VALUES ('248', 'GE', 'Georgia', '0');
INSERT INTO language_key VALUES ('249', 'GF', 'French Guiana', '0');
INSERT INTO language_key VALUES ('250', 'GH', 'Ghana', '0');
INSERT INTO language_key VALUES ('251', 'GI', 'Gibraltar', '0');
INSERT INTO language_key VALUES ('252', 'GL', 'Greenland', '0');
INSERT INTO language_key VALUES ('253', 'GM', 'Gambia', '0');
INSERT INTO language_key VALUES ('254', 'GN', 'Guinea', '0');
INSERT INTO language_key VALUES ('255', 'GP', 'Guadeloupe', '0');
INSERT INTO language_key VALUES ('256', 'GQ', 'Equatorial Guinea', '0');
INSERT INTO language_key VALUES ('257', 'GR', 'Greece', '0');
INSERT INTO language_key VALUES ('258', 'GS', 'South Georgia And The South Sandwich Islands', '0');
INSERT INTO language_key VALUES ('259', 'GT', 'Guatemala', '0');
INSERT INTO language_key VALUES ('260', 'GU', 'Guam', '0');
INSERT INTO language_key VALUES ('261', 'GW', 'Guinea-Bissau', '0');
INSERT INTO language_key VALUES ('262', 'GY', 'Guyana', '0');
INSERT INTO language_key VALUES ('263', 'HK', 'Hong Kong', '0');
INSERT INTO language_key VALUES ('264', 'HN', 'Honduras', '0');
INSERT INTO language_key VALUES ('265', 'HR', 'Croatia', '0');
INSERT INTO language_key VALUES ('266', 'HT', 'Haiti', '0');
INSERT INTO language_key VALUES ('267', 'HU', 'Hungary', '0');
INSERT INTO language_key VALUES ('268', 'ID', 'Indonesia', '0');
INSERT INTO language_key VALUES ('269', 'IE', 'Ireland', '0');
INSERT INTO language_key VALUES ('270', 'IL', 'Israel', '0');
INSERT INTO language_key VALUES ('271', 'IN', 'India', '0');
INSERT INTO language_key VALUES ('272', 'IO', 'British Indian Ocean Territory', '0');
INSERT INTO language_key VALUES ('273', 'IQ', 'Iraq', '0');
INSERT INTO language_key VALUES ('274', 'IR', 'Islamic Republic Of Iran', '0');
INSERT INTO language_key VALUES ('275', 'IS', 'Iceland', '0');
INSERT INTO language_key VALUES ('276', 'IT', 'Italy', '0');
INSERT INTO language_key VALUES ('277', 'JM', 'Jamaica', '0');
INSERT INTO language_key VALUES ('278', 'JO', 'Jordan', '0');
INSERT INTO language_key VALUES ('279', 'JP', 'Japan', '0');
INSERT INTO language_key VALUES ('280', 'KE', 'Kenya', '0');
INSERT INTO language_key VALUES ('281', 'KG', 'Kyrgyzstan', '0');
INSERT INTO language_key VALUES ('282', 'KH', 'Cambodia', '0');
INSERT INTO language_key VALUES ('283', 'KI', 'Kiribati', '0');
INSERT INTO language_key VALUES ('284', 'KM', 'Comoros', '0');
INSERT INTO language_key VALUES ('285', 'KN', 'Saint Kitts And Nevis', '0');
INSERT INTO language_key VALUES ('286', 'KR', 'Republic Of Korea', '0');
INSERT INTO language_key VALUES ('287', 'KW', 'Kuwait', '0');
INSERT INTO language_key VALUES ('288', 'KY', 'Cayman Islands', '0');
INSERT INTO language_key VALUES ('289', 'KZ', 'Kazakhstan', '0');
INSERT INTO language_key VALUES ('290', 'LA', 'Lao Peoples Democratic Republic', '0');
INSERT INTO language_key VALUES ('291', 'LB', 'Lebanon', '0');
INSERT INTO language_key VALUES ('292', 'LC', 'Saint Lucia', '0');
INSERT INTO language_key VALUES ('293', 'LI', 'Liechtenstein', '0');
INSERT INTO language_key VALUES ('294', 'LK', 'Sri Lanka', '0');
INSERT INTO language_key VALUES ('295', 'LR', 'Liberia', '0');
INSERT INTO language_key VALUES ('296', 'LS', 'Lesotho', '0');
INSERT INTO language_key VALUES ('297', 'LT', 'Lithuania', '0');
INSERT INTO language_key VALUES ('298', 'LU', 'Luxembourg', '0');
INSERT INTO language_key VALUES ('299', 'LV', 'Latvia', '0');
INSERT INTO language_key VALUES ('300', 'LY', 'Libyan Arab Jamahiriya', '0');
INSERT INTO language_key VALUES ('301', 'MA', 'Morocco', '0');
INSERT INTO language_key VALUES ('302', 'MC', 'Monaco', '0');
INSERT INTO language_key VALUES ('303', 'MD', 'Republic Of Moldova', '0');
INSERT INTO language_key VALUES ('304', 'MG', 'Madagascar', '0');
INSERT INTO language_key VALUES ('305', 'MH', 'Marshall Islands', '0');
INSERT INTO language_key VALUES ('306', 'MK', 'The Former Yugoslav Republic Of Macedonia', '0');
INSERT INTO language_key VALUES ('307', 'ML', 'Mali', '0');
INSERT INTO language_key VALUES ('308', 'MM', 'Myanmar', '0');
INSERT INTO language_key VALUES ('309', 'MN', 'Mongolia', '0');
INSERT INTO language_key VALUES ('310', 'MO', 'Macao', '0');
INSERT INTO language_key VALUES ('311', 'MP', 'Northern Mariana Islands', '0');
INSERT INTO language_key VALUES ('312', 'MQ', 'Martinique', '0');
INSERT INTO language_key VALUES ('313', 'MR', 'Mauritania', '0');
INSERT INTO language_key VALUES ('314', 'MT', 'Malta', '0');
INSERT INTO language_key VALUES ('315', 'MU', 'Mauritius', '0');
INSERT INTO language_key VALUES ('316', 'MV', 'Maldives', '0');
INSERT INTO language_key VALUES ('317', 'MW', 'Malawi', '0');
INSERT INTO language_key VALUES ('318', 'MX', 'Mexico', '0');
INSERT INTO language_key VALUES ('319', 'MY', 'Malaysia', '0');
INSERT INTO language_key VALUES ('320', 'MZ', 'Mozambique', '0');
INSERT INTO language_key VALUES ('321', 'NA', 'Namibia', '0');
INSERT INTO language_key VALUES ('322', 'NC', 'New Caledonia', '0');
INSERT INTO language_key VALUES ('323', 'NE', 'Niger', '0');
INSERT INTO language_key VALUES ('324', 'NF', 'Norfolk Island', '0');
INSERT INTO language_key VALUES ('325', 'NG', 'Nigeria', '0');
INSERT INTO language_key VALUES ('326', 'NI', 'Nicaragua', '0');
INSERT INTO language_key VALUES ('327', 'NL', 'Netherlands', '0');
INSERT INTO language_key VALUES ('328', 'NO', 'Norway', '0');
INSERT INTO language_key VALUES ('329', 'NP', 'Nepal', '0');
INSERT INTO language_key VALUES ('330', 'NR', 'Nauru', '0');
INSERT INTO language_key VALUES ('331', 'NU', 'Niue', '0');
INSERT INTO language_key VALUES ('332', 'NZ', 'New Zealand', '0');
INSERT INTO language_key VALUES ('333', 'OM', 'Oman', '0');
INSERT INTO language_key VALUES ('334', 'PA', 'Panama', '0');
INSERT INTO language_key VALUES ('335', 'PE', 'Peru', '0');
INSERT INTO language_key VALUES ('336', 'PF', 'French Polynesia', '0');
INSERT INTO language_key VALUES ('337', 'PG', 'Papua New Guinea', '0');
INSERT INTO language_key VALUES ('338', 'PH', 'Philippines', '0');
INSERT INTO language_key VALUES ('339', 'PK', 'Pakistan', '0');
INSERT INTO language_key VALUES ('340', 'PL', 'Poland', '0');
INSERT INTO language_key VALUES ('341', 'PR', 'Puerto Rico', '0');
INSERT INTO language_key VALUES ('342', 'PS', 'Palestinian Territory', '0');
INSERT INTO language_key VALUES ('343', 'PT', 'Portugal', '0');
INSERT INTO language_key VALUES ('344', 'PW', 'Palau', '0');
INSERT INTO language_key VALUES ('345', 'PY', 'Paraguay', '0');
INSERT INTO language_key VALUES ('346', 'QA', 'Qatar', '0');
INSERT INTO language_key VALUES ('347', 'RE', 'Reunion', '0');
INSERT INTO language_key VALUES ('348', 'RO', 'Romania', '0');
INSERT INTO language_key VALUES ('349', 'RU', 'Russian Federation', '0');
INSERT INTO language_key VALUES ('350', 'RW', 'Rwanda', '0');
INSERT INTO language_key VALUES ('351', 'SA', 'Saudi Arabia', '0');
INSERT INTO language_key VALUES ('352', 'SB', 'Solomon Islands', '0');
INSERT INTO language_key VALUES ('353', 'SC', 'Seychelles', '0');
INSERT INTO language_key VALUES ('354', 'SD', 'Sudan', '0');
INSERT INTO language_key VALUES ('355', 'SE', 'Sweden', '0');
INSERT INTO language_key VALUES ('356', 'SG', 'Singapore', '0');
INSERT INTO language_key VALUES ('357', 'SI', 'Slovenia', '0');
INSERT INTO language_key VALUES ('358', 'SK', 'Slovakia (Slovak Republic)', '0');
INSERT INTO language_key VALUES ('359', 'SL', 'Sierra Leone', '0');
INSERT INTO language_key VALUES ('360', 'SM', 'San Marino', '0');
INSERT INTO language_key VALUES ('361', 'SN', 'Senegal', '0');
INSERT INTO language_key VALUES ('362', 'SO', 'Somalia', '0');
INSERT INTO language_key VALUES ('363', 'SR', 'Suriname', '0');
INSERT INTO language_key VALUES ('364', 'ST', 'Sao Tome And Principe', '0');
INSERT INTO language_key VALUES ('365', 'SV', 'El Salvador', '0');
INSERT INTO language_key VALUES ('366', 'SY', 'Syrian Arab Republic', '0');
INSERT INTO language_key VALUES ('367', 'SZ', 'Swaziland', '0');
INSERT INTO language_key VALUES ('368', 'TD', 'Chad', '0');
INSERT INTO language_key VALUES ('369', 'TF', 'French Southern Territories', '0');
INSERT INTO language_key VALUES ('370', 'TG', 'Togo', '0');
INSERT INTO language_key VALUES ('371', 'TH', 'Thailand', '0');
INSERT INTO language_key VALUES ('372', 'TJ', 'Tajikistan', '0');
INSERT INTO language_key VALUES ('373', 'TK', 'Tokelau', '0');
INSERT INTO language_key VALUES ('374', 'TL', 'Timor-Leste', '0');
INSERT INTO language_key VALUES ('375', 'TM', 'Turkmenistan', '0');
INSERT INTO language_key VALUES ('376', 'TN', 'Tunisia', '0');
INSERT INTO language_key VALUES ('377', 'TO', 'Tonga', '0');
INSERT INTO language_key VALUES ('378', 'TR', 'Turkey', '0');
INSERT INTO language_key VALUES ('379', 'TT', 'Trinidad And Tobago', '0');
INSERT INTO language_key VALUES ('380', 'TV', 'Tuvalu', '0');
INSERT INTO language_key VALUES ('381', 'TW', 'Taiwan Province Of China', '0');
INSERT INTO language_key VALUES ('382', 'TZ', 'United Republic Of Tanzania', '0');
INSERT INTO language_key VALUES ('383', 'UA', 'Ukraine', '0');
INSERT INTO language_key VALUES ('384', 'UG', 'Uganda', '0');
INSERT INTO language_key VALUES ('385', 'US', 'United States', '0');
INSERT INTO language_key VALUES ('386', 'UY', 'Uruguay', '0');
INSERT INTO language_key VALUES ('387', 'UZ', 'Uzbekistan', '0');
INSERT INTO language_key VALUES ('388', 'VA', 'Holy See (Vatican City State)', '0');
INSERT INTO language_key VALUES ('389', 'VC', 'Saint Vincent And The Grenadines', '0');
INSERT INTO language_key VALUES ('390', 'VE', 'Venezuela', '0');
INSERT INTO language_key VALUES ('391', 'VG', 'Virgin Islands', '0');
INSERT INTO language_key VALUES ('392', 'VI', 'Virgin Islands', '0');
INSERT INTO language_key VALUES ('393', 'VN', 'Viet Nam', '0');
INSERT INTO language_key VALUES ('394', 'VU', 'Vanuatu', '0');
INSERT INTO language_key VALUES ('395', 'WS', 'Samoa', '0');
INSERT INTO language_key VALUES ('396', 'YE', 'Yemen', '0');
INSERT INTO language_key VALUES ('397', 'YT', 'Mayotte', '0');
INSERT INTO language_key VALUES ('398', 'YU', 'Serbia And Montenegro (Formally Yugoslavia)', '0');
INSERT INTO language_key VALUES ('399', 'ZA', 'South Africa', '0');
INSERT INTO language_key VALUES ('400', 'ZM', 'Zambia', '0');
INSERT INTO language_key VALUES ('401', 'ZW', 'Zimbabwe', '0');
INSERT INTO language_key VALUES ('402', 'ZZ', 'Unknown', '0');
INSERT INTO language_key VALUES ('404', 'shorturl_filter_disabled', 'Show disabled:', '0');
INSERT INTO language_key VALUES ('405', 'register', 'register', '0');
INSERT INTO language_key VALUES ('408', 'login', 'Login', '0');
INSERT INTO language_key VALUES ('409', 'account_home', 'Account Home', '0');
INSERT INTO language_key VALUES ('410', 'register_complete_page_name', 'Registration completed', '0');
INSERT INTO language_key VALUES ('411', 'register_complete_meta_description', 'Your registration has been completed.', '0');
INSERT INTO language_key VALUES ('412', 'register_complete_meta_keywords', 'registration, completed, file, hosting, site', '0');
INSERT INTO language_key VALUES ('413', 'register_complete_sub_title', 'Thank you for registering!', '0');
INSERT INTO language_key VALUES ('414', 'register_complete_main_text', 'We\'ve sent an email to your registered email address with your access password. Please check your spam filters to ensure emails from this site get through. ', '0');
INSERT INTO language_key VALUES ('415', 'register_complete_email_from', 'Emails from this site are sent from ', '0');
INSERT INTO language_key VALUES ('416', 'login_page_name', 'Login', '0');
INSERT INTO language_key VALUES ('417', 'login_meta_description', 'Login to your account', '0');
INSERT INTO language_key VALUES ('418', 'login_meta_keywords', 'login, register, short url', '0');
INSERT INTO language_key VALUES ('419', 'username_and_password_is_invalid', 'Your username and password are invalid', '0');
INSERT INTO language_key VALUES ('420', 'account_login', 'Account Login', '0');
INSERT INTO language_key VALUES ('421', 'login_intro_text', 'Please enter your username and password below to login.', '0');
INSERT INTO language_key VALUES ('422', 'username_requirements', 'Your account username. 6 characters or more and alpha numeric.', '0');
INSERT INTO language_key VALUES ('423', 'password_requirements', 'Your account password. Min 6 characters, alpha numeric, no spaces.', '0');
INSERT INTO language_key VALUES ('551', 'click_here_to_browse_your_files', 'Click here to browse your files...', '0');
INSERT INTO language_key VALUES ('549', 'index_meta_keywords', 'upload, share, track, file, hosting, host', '0');
INSERT INTO language_key VALUES ('550', 'please_enter_your_firstname', 'Please enter your firstname', '0');
INSERT INTO language_key VALUES ('428', 'please_enter_your_username', 'Please enter your username', '0');
INSERT INTO language_key VALUES ('429', 'account_home_page_name', 'Account Home', '0');
INSERT INTO language_key VALUES ('430', 'account_home_meta_description', 'Your Account Home', '0');
INSERT INTO language_key VALUES ('431', 'account_home_meta_keywords', 'account, home, file, hosting, members, area', '0');
INSERT INTO language_key VALUES ('433', 'faq', 'faq', '0');
INSERT INTO language_key VALUES ('434', 'faq_page_name', 'FAQ', '0');
INSERT INTO language_key VALUES ('435', 'faq_meta_description', 'Frequently Asked Questions', '0');
INSERT INTO language_key VALUES ('436', 'faq_meta_keywords', 'faq, frequently, asked, questions, file, hosting, site', '0');
INSERT INTO language_key VALUES ('437', 'please_enter_your_password', 'Please enter your password', '0');
INSERT INTO language_key VALUES ('511', 'report_abuse', 'Report Abuse', '0');
INSERT INTO language_key VALUES ('441', 'register_account', 'Register Account', '0');
INSERT INTO language_key VALUES ('548', 'index_meta_description', 'Upload, share, track, manage your files in one simple to use file host.', '0');
INSERT INTO language_key VALUES ('444', 'email_confirm', 'email confirm', '0');
INSERT INTO language_key VALUES ('445', 'stats', 'stats', '0');
INSERT INTO language_key VALUES ('446', 'info', 'info', '0');
INSERT INTO language_key VALUES ('447', 'email_address_confirm', 'Email Confirm', '0');
INSERT INTO language_key VALUES ('547', 'index_page_name', 'Upload Files', '0');
INSERT INTO language_key VALUES ('449', 'created_last_visited', 'Created/Last Visited:', '0');
INSERT INTO language_key VALUES ('450', 'status', 'Status:', '0');
INSERT INTO language_key VALUES ('451', 'options', 'Options:', '0');
INSERT INTO language_key VALUES ('452', 'upload_file', 'upload file', '0');
INSERT INTO language_key VALUES ('453', 'register_page_name', 'Register', '0');
INSERT INTO language_key VALUES ('454', 'register_meta_description', 'Register for an account', '0');
INSERT INTO language_key VALUES ('455', 'register_meta_keywords', 'register, account, short, url, user', '0');
INSERT INTO language_key VALUES ('456', 'your_files', 'your files', '0');
INSERT INTO language_key VALUES ('457', 'error_file_has_been_removed_by_user', 'File has been removed.', '0');
INSERT INTO language_key VALUES ('458', 'uploaded', 'Uploaded', '0');
INSERT INTO language_key VALUES ('459', 'downloads', 'downloads', '0');
INSERT INTO language_key VALUES ('460', 'download_now', 'download now', '0');
INSERT INTO language_key VALUES ('461', 'loading_file_please_wait', 'loading file, please wait', '0');
INSERT INTO language_key VALUES ('462', 'file_download_title', 'Download File', '0');
INSERT INTO language_key VALUES ('463', 'file_download_description', 'Download file', '0');
INSERT INTO language_key VALUES ('464', 'file_download_keywords', 'download, file, upload, mp3, avi, zip', '0');
INSERT INTO language_key VALUES ('465', 'your_recent_files', 'Your Files', '0');
INSERT INTO language_key VALUES ('466', 'download_url', 'Download Url:', '0');
INSERT INTO language_key VALUES ('467', 'uploaded_last_visited', 'Uploaded/Last Visited:', '0');
INSERT INTO language_key VALUES ('468', 'download_url_filename', 'Download Url/Filename:', '0');
INSERT INTO language_key VALUES ('469', 'dashboard_total_active_files', 'Total Active Files', '0');
INSERT INTO language_key VALUES ('470', 'dashboard_total_disabled_files', 'Total Inactive Files', '0');
INSERT INTO language_key VALUES ('471', 'dashboard_total_downloads_to_all', 'Total Downloads', '0');
INSERT INTO language_key VALUES ('472', 'user removed', 'user removed', '0');
INSERT INTO language_key VALUES ('473', 'files', 'files', '0');
INSERT INTO language_key VALUES ('474', 'manage_files', 'Manage Files', '0');
INSERT INTO language_key VALUES ('475', 'files_filter_results', 'Filter Results:', '0');
INSERT INTO language_key VALUES ('476', 'files_filter_disabled', 'Show Disabled', '0');
INSERT INTO language_key VALUES ('477', 'export_files_as_csv', 'Export File Data', '0');
INSERT INTO language_key VALUES ('478', 'error_file_has_been_removed_by_admin', 'File has been removed by the site administrator.', '0');
INSERT INTO language_key VALUES ('479', 'files_filter_removed', 'Show Removed', '0');
INSERT INTO language_key VALUES ('480', 'admin removed', 'admin removed', '0');
INSERT INTO language_key VALUES ('481', 'delete_file_page_name', 'Delete File', '0');
INSERT INTO language_key VALUES ('482', 'delete_file_meta_description', 'Delete File', '0');
INSERT INTO language_key VALUES ('483', 'delete_file_meta_keywords', 'delete, remove, file', '0');
INSERT INTO language_key VALUES ('484', 'delete_file', 'Delete File', '0');
INSERT INTO language_key VALUES ('485', 'delete_file_intro', 'Please confirm whether to delete the file below.', '0');
INSERT INTO language_key VALUES ('486', 'cancel', 'Cancel', '0');
INSERT INTO language_key VALUES ('487', 'report_file', 'report file', '0');
INSERT INTO language_key VALUES ('488', 'uprade_account', 'upgrade account', '0');
INSERT INTO language_key VALUES ('489', 'terms_page_name', 'Terms and Conditions', '0');
INSERT INTO language_key VALUES ('490', 'terms_meta_description', 'Terms and Conditions', '0');
INSERT INTO language_key VALUES ('491', 'terms_meta_keywords', 'terms, and, conditions, file, hosting, site', '0');
INSERT INTO language_key VALUES ('492', 'extend_account', 'extend account', '0');
INSERT INTO language_key VALUES ('493', 'upgrade_page_name', 'Extend Account', '0');
INSERT INTO language_key VALUES ('494', 'upgrade_meta_description', 'Extend Your Account', '0');
INSERT INTO language_key VALUES ('495', 'upgrade_meta_keywords', 'extend, account, paid, membership, upload, download, site', '0');
INSERT INTO language_key VALUES ('496', 'payment_complete_page_name', 'Payment Complete', '0');
INSERT INTO language_key VALUES ('497', 'payment_complete_meta_description', 'Payment Complete', '0');
INSERT INTO language_key VALUES ('498', 'payment_complete_meta_keywords', 'payment, complete, file, hosting, site', '0');
INSERT INTO language_key VALUES ('499', 'premium_account_benefits', 'premium account benefits', '0');
INSERT INTO language_key VALUES ('500', 'account_benefits', 'account benefits', '0');
INSERT INTO language_key VALUES ('501', 'file_information_page_name', ' Information', '0');
INSERT INTO language_key VALUES ('502', 'file_information_description', 'Information about ', '0');
INSERT INTO language_key VALUES ('503', 'file_information_meta_keywords', ', share, information, file, upload, download, site', '0');
INSERT INTO language_key VALUES ('504', 'download_urls', 'download urls', '0');
INSERT INTO language_key VALUES ('505', 'statistics', 'statistics', '0');
INSERT INTO language_key VALUES ('506', 'share', 'share', '0');
INSERT INTO language_key VALUES ('507', 'other_options', 'other options', '0');
INSERT INTO language_key VALUES ('508', 'problem_file_requirements', 'Enter the details of the file (as above) you wish to report.', '0');
INSERT INTO language_key VALUES ('510', 'report_abuse_error_no_content', 'Please enter the details of the reported file.', '0');
INSERT INTO language_key VALUES ('516', 'legal_bits', 'Legal Bits', '0');
INSERT INTO language_key VALUES ('517', 'your_account', 'Your Account', '0');
INSERT INTO language_key VALUES ('518', 'days', 'days', '0');
INSERT INTO language_key VALUES ('519', 'premium', 'premium', '0');
INSERT INTO language_key VALUES ('520', 'pay_via_paypal', 'Pay via PayPal', '0');
INSERT INTO language_key VALUES ('521', 'secure_payment', 'secure payment', '0');
INSERT INTO language_key VALUES ('522', 'safe_and_anonymous', '100% Safe & Anonymous', '0');
INSERT INTO language_key VALUES ('523', 'add_files', 'Add files...', '0');
INSERT INTO language_key VALUES ('524', 'start_upload', 'Start upload', '0');
INSERT INTO language_key VALUES ('525', 'cancel_upload', 'Cancel upload', '0');
INSERT INTO language_key VALUES ('526', 'select_files', 'Select files', '0');
INSERT INTO language_key VALUES ('527', 'drag_and_drop_files_here_or_click_to_browse', 'Drag &amp; drop files here or click to browse...', '0');
INSERT INTO language_key VALUES ('528', 'max_file_size', 'Max file size', '0');
INSERT INTO language_key VALUES ('529', 'add_file', 'add file', '0');
INSERT INTO language_key VALUES ('530', 'copy_all_links', 'copy all links', '0');
INSERT INTO language_key VALUES ('531', 'file_upload_completed', 'File uploads completed.', '0');
INSERT INTO language_key VALUES ('532', 'delete_url', 'Delete Url', '0');
INSERT INTO language_key VALUES ('533', 'stats_url', 'Stats Url', '0');
INSERT INTO language_key VALUES ('534', 'html_code', 'HTML Code', '0');
INSERT INTO language_key VALUES ('535', 'forum_code', 'Forum Code', '0');
INSERT INTO language_key VALUES ('536', 'full_info', 'Full Info', '0');
INSERT INTO language_key VALUES ('537', 'click_here', 'click here', '0');
INSERT INTO language_key VALUES ('538', 'extend', 'extend', '0');
INSERT INTO language_key VALUES ('539', 'reverts_to_free_account', 'reverts to free account', '0');
INSERT INTO language_key VALUES ('540', 'never', 'never', '0');
INSERT INTO language_key VALUES ('541', 'filename', 'filename', '0');
INSERT INTO language_key VALUES ('542', 'download', 'download', '0');
INSERT INTO language_key VALUES ('543', 'filesize', 'filesize', '0');
INSERT INTO language_key VALUES ('544', 'url', 'url', '0');
INSERT INTO language_key VALUES ('545', 'download_from', 'Download from', '0');
INSERT INTO language_key VALUES ('546', 'share_file', 'share file', '0');

-- ----------------------------
-- Table structure for `payment_log`
-- ----------------------------
DROP TABLE IF EXISTS `payment_log`;
CREATE TABLE `payment_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `date_created` datetime NOT NULL,
  `amount` float(9,2) NOT NULL,
  `currency_code` varchar(3) NOT NULL,
  `from_email` varchar(255) NOT NULL,
  `to_email` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `request_log` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of payment_log
-- ----------------------------

-- ----------------------------
-- Table structure for `premium_order`
-- ----------------------------
DROP TABLE IF EXISTS `premium_order`;
CREATE TABLE `premium_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `payment_hash` varchar(32) NOT NULL,
  `days` int(11) NOT NULL,
  `amount` float NOT NULL,
  `order_status` enum('pending','cancelled','completed') NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of premium_order
-- ----------------------------

-- ----------------------------
-- Table structure for `sessions`
-- ----------------------------
DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `data` text COLLATE utf8_unicode_ci NOT NULL,
  `updated_on` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of sessions
-- ----------------------------

-- ----------------------------
-- Table structure for `site_config`
-- ----------------------------
DROP TABLE IF EXISTS `site_config`;
CREATE TABLE `site_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `config_key` varchar(100) NOT NULL,
  `config_value` text NOT NULL,
  `config_description` varchar(255) NOT NULL,
  `availableValues` varchar(255) NOT NULL,
  `config_type` varchar(30) NOT NULL,
  `config_group` varchar(100) NOT NULL DEFAULT 'Default',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=42 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of site_config
-- ----------------------------
INSERT INTO site_config VALUES ('32', 'cost_for_7_days_premium', '4.99', 'The cost for 7 days premium access. Without any curency symbol. i.e. 4.99', '', 'integer', 'Premium Pricing');
INSERT INTO site_config VALUES ('2', 'redirect_delay_seconds', '60', 'How long to wait before redirecting if Meta Delay Redirect', '', 'integer', 'Default');
INSERT INTO site_config VALUES ('21', 'language_show_key', 'translation', 'Show translation value or key. (use \'key\' to debug translations, \'translation\' to show actual translated value)', '[\'key\',\'translation\']', 'select', 'Language');
INSERT INTO site_config VALUES ('4', 'page_extension', 'html', 'Whether to use html or php front-end page extensions', '[\"html\", \"php\"]', 'select', 'Page Options');
INSERT INTO site_config VALUES ('5', 'date_time_format', 'd/m/Y H:i:s', 'Date time format in php', '', 'string', 'Local');
INSERT INTO site_config VALUES ('6', 'date_format', 'd/m/Y', 'Date format in php', '', 'string', 'Local');
INSERT INTO site_config VALUES ('7', 'site_name', 'File Upload Script', 'Site name', '', 'string', 'Page Options');
INSERT INTO site_config VALUES ('9', 'site_theme', 'blue_v2', 'Site template theme', '[\"blue_v2\"]', 'select', 'Page Options');
INSERT INTO site_config VALUES ('10', 'max_files_per_day', '50', 'Spam protect: Max files a user IP address can create per day. Leave blank for unlimited.', '', 'integer', 'File Uploads');
INSERT INTO site_config VALUES ('11', 'date_time_format_js', '%d-%m-%Y %H:%i', 'Date time format in javascript', '', 'string', 'Local');
INSERT INTO site_config VALUES ('33', 'cost_for_30_days_premium', '9.99', 'The cost for 30 days premium access. Without any curency symbol. i.e. 9.99', '', 'integer', 'Premium Pricing');
INSERT INTO site_config VALUES ('15', 'advert_site_footer', '<a target=\"_blank\" href=\"http://www.dreamhost.com/r.cgi?606181\"><img height=\"60\" width=\"468\" src=\"http://images.dreamhost.com/rewards/468x60-b.gif\" alt=\"468x60\"/></a>', 'Site footer ads across the site (html)', '', 'textarea', 'Adverts');
INSERT INTO site_config VALUES ('16', 'advert_delayed_redirect_top', '<a target=\"_blank\" href=\"http://www.dreamhost.com/r.cgi?606181\"><img height=\"60\" width=\"468\" src=\"http://images.dreamhost.com/rewards/468x60-d.gif\" alt=\"468x60\"/></a>', 'Delayed redirect top advert (html)', '', 'textarea', 'Adverts');
INSERT INTO site_config VALUES ('18', 'advert_delayed_redirect_bottom', '<a target=\"_blank\" href=\"http://www.dreamhost.com/r.cgi?606181\"><img height=\"60\" width=\"468\" src=\"http://images.dreamhost.com/rewards/468x60-c.gif\" alt=\"468x60\"/></a>', 'Delayed redirect bottom advert (html)', '', 'textarea', 'Adverts');
INSERT INTO site_config VALUES ('19', 'report_abuse_email', 'abuse@yoursite.com', 'Email address for which all abuse reports are sent.', '', 'string', 'Page Options');
INSERT INTO site_config VALUES ('20', 'site_language', 'English (en)', 'Site language for text conversions <a href=\'manage_languages.php\'>(manage languages)</a>', 'SELECT languageName AS itemValue FROM language ORDER BY languageName', 'select', 'Language');
INSERT INTO site_config VALUES ('31', 'next_check_for_file_removals', '1327013029', 'System value. The next time to delete any files which haven\'t recently been accessed. Timestamp. Do not edit.', '', 'integer', 'System');
INSERT INTO site_config VALUES ('23', 'stats_only_count_unique', 'yes', 'Revisits in the same day, by the same IP address will not be counted on stats.', '[\"yes\", \"no\"]', 'select', 'Default');
INSERT INTO site_config VALUES ('24', 'default_email_address_from', 'email@yoursite.com', 'The default email address to send emails from.', '', 'string', 'Page Options');
INSERT INTO site_config VALUES ('25', 'default_email_address_from', 'email@yoursite.com', 'The email address new account registrations will be sent from.', '', 'string', 'Default');
INSERT INTO site_config VALUES ('26', 'free_user_max_upload_filesize', '104857600', 'The max upload filesize for free users (in bytes)', '', 'integer', 'Free User Settings');
INSERT INTO site_config VALUES ('27', 'premium_user_max_upload_filesize', '1073741824', 'The max upload filesize for premium users (in bytes)', '', 'integer', 'Premium User Settings');
INSERT INTO site_config VALUES ('28', 'accepted_upload_file_types', '', 'The file extensions which are permitted. Leave blank for all. Separate by semi-colon. i.e. .jpg;.gif;.doc;', '', 'string', 'File Uploads');
INSERT INTO site_config VALUES ('29', 'free_user_upload_removal_days', '60', 'The amount of days after non-active files are removed for free users. Leave blank for unlimited.', '', 'integer', 'Free User Settings');
INSERT INTO site_config VALUES ('30', 'premium_user_upload_removal_days', '', 'The amount of days after non-active files are removed for paid users. Leave blank for unlimited.', '', 'integer', 'Premium User Settings');
INSERT INTO site_config VALUES ('34', 'cost_for_90_days_premium', '19.99', 'The cost for 90 days premium access. Without any curency symbol. i.e. 19.99', '', 'integer', 'Premium Pricing');
INSERT INTO site_config VALUES ('35', 'cost_for_180_days_premium', '34.99', 'The cost for 180 days premium access. Without any curency symbol. i.e. 34.99', '', 'integer', 'Premium Pricing');
INSERT INTO site_config VALUES ('36', 'cost_for_365_days_premium', '59.99', 'The cost for 365 days premium access. Without any curency symbol. i.e. 59.99', '', 'integer', 'Premium Pricing');
INSERT INTO site_config VALUES ('37', 'cost_currency_symbol', '$', 'The symbol to use for currency. i.e. $', '[\"$\", \"£\", \"€\"]', 'string', 'Premium Pricing');
INSERT INTO site_config VALUES ('38', 'cost_currency_code', 'USD', 'The currency code for the current currency. i.e. USD', '[\"USD\", \"GBP\", \"EUR\"]', 'select', 'Premium Pricing');
INSERT INTO site_config VALUES ('39', 'paypal_payments_email_address', 'paypal@yoursite.com', 'The PayPal email account you wish to receive payments at.', '', 'string', 'Premium Pricing');
INSERT INTO site_config VALUES ('40', 'free_user_max_download_speed', '50000', 'Maximum download speed for free/non-users, in bytes per second. i.e. 50000. Use 0 for unlimited. ', '', 'integer', 'Free User Settings');
INSERT INTO site_config VALUES ('41', 'premium_user_max_download_speed', '0', 'Maximum download speed for premium users, in bytes per second. i.e. 50000. Use 0 for unlimited. ', '', 'integer', 'Premiuim User Settings');
INSERT INTO site_config VALUES ('42', 'email_method', 'php', 'The method for sending emails via the script.', '[\"php\",\"smtp\"]', 'select', 'Email Settings');
INSERT INTO site_config VALUES ('43', 'email_smtp_host', 'mail.yoursite.com', 'Your SMTP host if you\'ve selected SMTP email method. (leave blank is email_method = php)', '', 'string', 'Email Settings');
INSERT INTO site_config VALUES ('44', 'email_smtp_port', '25', 'Your SMTP port if you\'ve selected SMTP email method. (Normally 25)', '', 'integer', 'Email Settings');
INSERT INTO site_config VALUES ('45', 'email_smtp_requires_auth', 'no', 'Whether your SMTP server requires authentication.', '[\"yes\",\"no\"]', 'select', 'Email Settings');
INSERT INTO site_config VALUES ('46', 'email_smtp_auth_username', '', 'Your SMTP username if SMTP auth is required.', '', 'string', 'Email Settings');
INSERT INTO site_config VALUES ('47', 'email_smtp_auth_password', '', 'Your SMTP password if SMTP auth is required.', '', 'string', 'Email Settings');

-- ----------------------------
-- Table structure for `stats`
-- ----------------------------
DROP TABLE IF EXISTS `stats`;
CREATE TABLE `stats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `referer` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `referer_is_local` tinyint(4) NOT NULL DEFAULT '0',
  `url` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `page_title` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `country` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `img_search` tinyint(4) NOT NULL DEFAULT '0',
  `browser_family` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `browser_version` varchar(15) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `os` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `os_version` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `ip` varchar(15) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `user_agent` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `base_url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=47 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of stats
-- ----------------------------

-- ----------------------------
-- Table structure for `users`
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(65) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(65) COLLATE utf8_unicode_ci NOT NULL,
  `level` enum('free user','paid user','admin') COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(65) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lastlogindate` timestamp NULL DEFAULT NULL,
  `lastloginip` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `status` enum('active','pending','disabled','suspended') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'active',
  `title` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `firstname` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `lastname` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `datecreated` timestamp NULL DEFAULT NULL,
  `createdip` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lastPayment` timestamp NULL DEFAULT NULL,
  `paidExpiryDate` timestamp NULL DEFAULT NULL,
  `paymentTracker` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of users
-- ----------------------------
INSERT INTO users VALUES ('1', 'admin', '5f4dcc3b5aa765d61d8327deb882cf99', 'admin', 'email@yoursite.com', '2012-01-19 21:44:07', '192.168.2.100', 'active', 'Mr', 'Admin', 'User', null, null, '2011-12-27 13:45:22', '2012-01-12 13:45:16', '5f4dcc3b5aa765d61d8327deb882cf99');

INSERT INTO `language_key` (`languageKey`, `defaultContent`, `isAdminArea`) VALUES ('optional_account_expiry', 'Paid Expiry Y-m-d (optional)', '1');
INSERT INTO `language_key` (`languageKey`, `defaultContent`, `isAdminArea`) VALUES ('account_expiry_invalid', 'Account expiry date is invalid. It should be in the format YYYY-mm-dd', '1');

DROP TABLE IF EXISTS `file_folder`;
CREATE TABLE `file_folder` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `folderName` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `file_server`
-- ----------------------------
DROP TABLE IF EXISTS `file_server`;
CREATE TABLE `file_server` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `serverLabel` varchar(100) NOT NULL,
  `serverType` enum('remote','local') DEFAULT NULL,
  `ipAddress` varchar(15) NOT NULL,
  `connectionMethod` enum('ftp') NOT NULL DEFAULT 'ftp',
  `ftpPort` int(11) NOT NULL DEFAULT '21',
  `ftpUsername` varchar(50) NOT NULL,
  `ftpPassword` varchar(50) DEFAULT NULL,
  `statusId` int(11) NOT NULL DEFAULT '1',
  `storagePath` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `statusId` (`statusId`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `file_server_status`
-- ----------------------------
DROP TABLE IF EXISTS `file_server_status`;
CREATE TABLE `file_server_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of file_server_status
-- ----------------------------
INSERT INTO `file_server_status` VALUES ('1', 'disabled');
INSERT INTO `file_server_status` VALUES ('2', 'active');
INSERT INTO `file_server_status` VALUES ('3', 'read only');

-- ----------------------------
-- Records of file_server
-- ----------------------------
INSERT INTO `file_server` VALUES ('1', 'Local Default', 'local', '', '', '0', '', null, '2', null);

ALTER TABLE `file` ADD `folderId` INT( 11 ) NULL DEFAULT NULL;

INSERT INTO `site_config` (`config_key`, `config_value`, `config_description`, `availableValues`, `config_type`, `config_group`) VALUES ('file_url_show_filename', 'no', 'Show the original filename on the end of the generated url.', '[\"yes\",\"no\"]', 'select', 'File Uploads');

ALTER TABLE `file`
ADD COLUMN `serverId`  int(11) DEFAULT 1 AFTER `folderId`;

INSERT INTO `language_key` (`languageKey`, `defaultContent`, `isAdminArea`) VALUES ('admin_file_servers', 'File Servers', '1');
INSERT INTO `language_key` (`languageKey`, `defaultContent`, `isAdminArea`) VALUES ('ftp_host', 'FTP Ip Address', '1');
INSERT INTO `language_key` (`languageKey`, `defaultContent`, `isAdminArea`) VALUES ('ftp_port', 'FTP Port', '1');

INSERT INTO `site_config` VALUES ('49', 'default_file_server', 'Local Default', 'The file server to use for all new uploads. Only used if \'active\' state and \'server selection method\' is \'specific server\'.', 'SELECT serverLabel AS itemValue FROM file_server LEFT JOIN file_server_status ON file_server.statusId = file_server_status.id ORDER BY serverLabel', 'select', 'File Uploads');
INSERT INTO `site_config` VALUES ('50', 'c_file_server_selection_method', 'Least Used Space', 'Server selection method. How to select the file server to use.', '[\"Least Used Space\",\"Specific Server\"]', 'select', 'File Uploads');


ALTER TABLE `file_folder`
ADD COLUMN `isPublic`  int(1) NOT NULL DEFAULT 0 AFTER `folderName`,
ADD COLUMN `accessPassword`  varchar(32) NULL AFTER `isPublic`;

ALTER TABLE `users`
ADD COLUMN `passwordResetHash`  varchar(32) NULL AFTER `paymentTracker`;

INSERT INTO `site_config` (`config_key`, `config_value`, `config_description`, `availableValues`, `config_type`, `config_group`) VALUES ('free_user_show_captcha', 'no', 'Show the captcha after a free user sees the countdown timer.', '[\"yes\",\"no\"]', 'select', 'Captcha');
INSERT INTO `site_config` (`config_key`, `config_value`, `config_description`, `availableValues`, `config_type`, `config_group`) VALUES ('captcha_private_key', '6LeuAc4SAAAAAL71eifhISYsbL-yPTtNZVnXTHVt', 'Private key for captcha. Register at https://www.google.com/recaptcha', '', 'string', 'Captcha');
INSERT INTO `site_config` (`config_key`, `config_value`, `config_description`, `availableValues`, `config_type`, `config_group`) VALUES ('captcha_public_key', '6LeuAc4SAAAAAOSry8eo2xW64K1sjHEKsQ5CaS10', 'Public key for captcha. Register at https://www.google.com/recaptcha', '', 'string', 'Captcha');




ALTER TABLE `users` ADD `identifier` VARCHAR( 32 ) NOT NULL ;
ALTER TABLE `premium_order` ADD `upgrade_file_id` INT( 11 ) NOT NULL;
ALTER TABLE `premium_order` ADD `upgrade_user_id` INT( 11 ) NOT NULL;
UPDATE `site_config` SET `config_group` = 'Premium User Settings' WHERE `site_config`.`id` =41;
UPDATE `site_config` SET availableValues = REPLACE(availableValues, '\'', '"');
ALTER TABLE `language` ADD `isLocked` INT( 1 ) NOT NULL;
UPDATE `language` SET `isLocked` = '1' WHERE `language`.`id` =1;
ALTER TABLE `language` ADD `flag` VARCHAR( 20 ) NOT NULL;
UPDATE `language` SET `flag` = 'us' WHERE `language`.`id` =1;
UPDATE `language_content` SET content = REPLACE(content , ':', '') WHERE languageId=1;
UPDATE `language_key` SET defaultContent = REPLACE(defaultContent, ':', '');

ALTER TABLE `users` ADD INDEX ( `level` );
ALTER TABLE `users` ADD INDEX ( `datecreated` );
ALTER TABLE `users` ADD INDEX ( `username` );
ALTER TABLE `stats` ADD INDEX ( `dt` );
ALTER TABLE `stats` ADD INDEX ( `page_title` );
ALTER TABLE `banned_ips` ADD INDEX ( `ipAddress` );
ALTER TABLE `file` ADD INDEX ( `shortUrl` );
ALTER TABLE `file` ADD INDEX ( `originalFilename` );
ALTER TABLE `file` ADD INDEX ( `fileSize` );
ALTER TABLE `file` ADD INDEX ( `visits` );
ALTER TABLE `file` ADD INDEX ( `lastAccessed` );
ALTER TABLE `file` ADD INDEX ( `extension` );
ALTER TABLE `site_config` ADD INDEX ( `config_key` );

CREATE TABLE `download_tracker` (
`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`file_id` INT( 11 ) NOT NULL ,
`ip_address` VARCHAR( 15 ) NOT NULL ,
`date_started` DATETIME NOT NULL ,
`date_updated` DATETIME NOT NULL ,
`date_finished` DATETIME NOT NULL ,
`status` ENUM( 'downloading','finished','error','cancelled' ) NOT NULL
) ENGINE = MyISAM;

ALTER TABLE `download_tracker` ADD INDEX ( `ip_address` );
ALTER TABLE `download_tracker` ADD INDEX ( `date_updated` );
ALTER TABLE `download_tracker` ADD INDEX ( `status` );
ALTER TABLE `download_tracker` ADD INDEX ( `file_id` );
ALTER TABLE `download_tracker` ADD `start_offset` BIGINT( 20 ) NOT NULL;
ALTER TABLE `download_tracker` ADD `seek_end` BIGINT( 20 ) NOT NULL;

UPDATE `site_config` SET `availableValues` = 'SELECT serverLabel AS itemValue FROM file_server LEFT JOIN file_server_status ON file_server.statusId = file_server_status.id WHERE statusId=2 ORDER BY serverLabel' WHERE `config_key` = 'default_file_server';

INSERT INTO `site_config` (`id`, `config_key`, `config_value`, `config_description`, `availableValues`, `config_type`, `config_group`) VALUES (NULL, 'reserved_usernames', 'admin|administrator|localhost|support|billing|sales|payments', 'Any usernames listed here will be blocked from the main registration. Pipe separated list.', '', 'string', 'Default');

ALTER TABLE `language` ADD `isActive` INT( 1 ) NOT NULL DEFAULT '1' AFTER `isLocked`;

UPDATE `site_config` SET `config_description` = 'Site language for text conversions <a href="translation_manage.php">(manage languages)</a>' WHERE `config_key` = 'site_language';

INSERT INTO `site_config` (`id`, `config_key`, `config_value`, `config_description`, `availableValues`, `config_type`, `config_group`) VALUES (NULL, 'show_multi_language_selector', 'hide', 'Whether to show or hide the multi language selector on the site.', '["hide","show"]', 'select', 'Language');

ALTER TABLE `file_server` DROP `connectionMethod`;
ALTER TABLE `file_server` CHANGE `serverType` `serverType` ENUM( 'remote', 'local', 'ftp', 'sftp' ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
UPDATE `file_server` SET `serverType` = 'ftp' WHERE `serverType` = 'remote';

ALTER TABLE `file_server` ADD `sftpHost` VARCHAR( 255 ) NOT NULL AFTER `ftpPassword` ,
ADD `sftpPort` INT(11) NOT NULL DEFAULT '22' AFTER `sftpHost` ,
ADD `sftpUsername` VARCHAR( 50 ) NOT NULL AFTER `sftpPort` ,
ADD `sftpPassword` VARCHAR( 50 ) NOT NULL AFTER `sftpUsername`;

CREATE TABLE `plugin` (
`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`plugin_name` VARCHAR( 150 ) NOT NULL ,
`folder_name` VARCHAR( 100 ) NOT NULL ,
`plugin_description` VARCHAR( 255 ) NOT NULL ,
`is_installed` INT( 1 ) NOT NULL DEFAULT '0',
`date_installed` DATETIME NOT NULL
) ENGINE = MYISAM;

ALTER TABLE `plugin` ADD `plugin_settings` TEXT NOT NULL;
ALTER TABLE `plugin` ADD `plugin_enabled` INT( 1 ) NOT NULL DEFAULT '1';

ALTER TABLE `file` ADD `adminNotes` TEXT NOT NULL;

INSERT INTO site_config (config_key, config_value, config_description, config_type, config_group) SELECT 'site_admin_email', config_value, 'The email address all site admin emails will be sent.', 'string', 'Page Options' FROM site_config WHERE config_key='report_abuse_email';

UPDATE site_config SET config_description='How long non/free users have to wait before being able to download (in seconds). Set to 0 to allow non/free users direct download access.', `config_group` = 'Free User Settings' WHERE config_key = 'redirect_delay_seconds';
INSERT INTO `site_config` (`id`, `config_key`, `config_value`, `config_description`, `availableValues`, `config_type`, `config_group`) VALUES (NULL, 'require_user_account_upload', 'no', 'Users must register for an account to upload.', '["yes","no"]', 'select', 'File Uploads');
INSERT INTO `site_config` (`id`, `config_key`, `config_value`, `config_description`, `availableValues`, `config_type`, `config_group`) VALUES (NULL, 'require_user_account_download', 'no', 'Users must register for an account to download.', '["yes","no"]', 'select', 'File Downloads');
INSERT INTO `site_config` (`id`, `config_key`, `config_value`, `config_description`, `availableValues`, `config_type`, `config_group`) VALUES (NULL, 'generate_upload_url_type', 'Shortest', 'What format to generate the file url in. Shortest will increment based on the previous upload. Hashed will create a longer random character hash.', '["Shortest","Medium Hash","Long Hash"]', 'select', 'File Uploads');
INSERT INTO `site_config` (`id`, `config_key`, `config_value`, `config_description`, `availableValues`, `config_type`, `config_group`) VALUES (NULL, 'free_user_max_download_filesize', '0', 'The maximum filesize a non/free user can download (in bytes). Set to 0 (zero) to ignore.', '', 'integer', 'Free User Settings');
INSERT INTO `site_config` (`id`, `config_key`, `config_value`, `config_description`, `availableValues`, `config_type`, `config_group`) VALUES (NULL, 'free_user_max_download_threads', '0', 'The maximum concurrent downloads a non/free user can do at once. Set to 0 (zero) for no limit.', '', 'integer', 'Free User Settings');

ALTER TABLE `file` ADD INDEX ( `userId` );
ALTER TABLE `users` ADD INDEX ( `status` );
ALTER TABLE `file` ADD INDEX ( `statusId` );
ALTER TABLE `users` ADD INDEX ( `email` );

INSERT INTO `site_config` (`id`, `config_key`, `config_value`, `config_description`, `availableValues`, `config_type`, `config_group`) VALUES (NULL, 'free_user_max_remote_urls', '5', 'The maximum remote urls a non/free user can specify at once.', '', 'integer', 'Free User Settings');
INSERT INTO `site_config` (`id`, `config_key`, `config_value`, `config_description`, `availableValues`, `config_type`, `config_group`) VALUES (NULL, 'premium_user_max_remote_urls', '50', 'The maximum remote urls a paid user can specify at once.', '', 'integer', 'Free User Settings');

ALTER TABLE `file` CHANGE `fileSize` `fileSize` BIGINT( 15 ) NULL DEFAULT NULL;
INSERT INTO `plugin` VALUES(NULL, 'PayPal Payment Integration', 'paypal', 'Accept payments using PayPal.', 1, '0000-00-00 00:00:00', '{"paypal_email":"paypal@yoursite.com"}', 1);
UPDATE plugin p, site_config c SET p.plugin_settings = CONCAT('{"paypal_email":"', c.config_value, '"}') WHERE c.config_key = 'paypal_payments_email_address' AND p.folder_name = 'paypal';

ALTER TABLE `download_tracker` ADD `download_username` VARCHAR( 65 ) NULL AFTER `ip_address`;

INSERT INTO `site_config` (`id`, `config_key`, `config_value`, `config_description`, `availableValues`, `config_type`, `config_group`) VALUES (NULL, 'free_user_max_concurrent_uploads', '50', 'The maximum amount of files that can be uploaded at the same time for free users.', '', 'integer', 'Free User Settings');
INSERT INTO `site_config` (`id`, `config_key`, `config_value`, `config_description`, `availableValues`, `config_type`, `config_group`) VALUES (NULL, 'premium_user_max_concurrent_uploads', '100', 'The maximum amount of files that can be uploaded at the same time for paid users.', '', 'integer', 'Premium User Settings');

INSERT INTO `site_config` (`id`, `config_key`, `config_value`, `config_description`, `availableValues`, `config_type`, `config_group`) VALUES (NULL, 'register_form_show_captcha', 'no', 'Whether to display the captcha on the site registration form.', '["yes","no"]', 'select', 'Captcha');

ALTER TABLE `users`  ADD `apikey` VARCHAR(32) NOT NULL;

DELETE FROM site_config WHERE config_key='paypal_payments_email_address';

INSERT INTO `site_config` (`id`, `config_key`, `config_value`, `config_description`, `availableValues`, `config_type`, `config_group`) VALUES (NULL, 'downloads_track_current_downloads', 'yes', 'Whether to track current downloads/connections in the admin area. Note: This should be enabled if you also want to limit concurrent download connections.', '["yes","no"]', 'select', 'File Downloads');

UPDATE `site_config` SET `config_description` = 'The maximum concurrent downloads a non/free user can do at once. Set to 0 (zero) for no limit. Note: Ensure the \'downloads_track_current_downloads\' is also set to ''yes'' to enable this.' WHERE `site_config`.`config_key` = 'free_user_max_download_threads';

ALTER TABLE `download_tracker` ADD INDEX(`download_username`);

INSERT INTO `site_config` (`id`, `config_key`, `config_value`, `config_description`, `availableValues`, `config_type`, `config_group`) VALUES (NULL, 'paid_user_max_download_threads', '0', 'The maximum concurrent downloads a paid user can do at once. Set to 0 (zero) for no limit. Note: Ensure the \'downloads_track_current_downloads\' is also set to \'yes\' to enable this.', '', 'integer', 'Premium User Settings');

INSERT INTO `site_config` (`id`, `config_key`, `config_value`, `config_description`, `availableValues`, `config_type`, `config_group`) VALUES (NULL, 'free_user_wait_between_downloads', '0', 'How long a free user must wait between downloads, in seconds. Set to 0 (zero) to disable. Note: Ensure the \'downloads_track_current_downloads\' is also set to \'yes\' to enable this.', '', 'integer', 'Free User Settings');

ALTER TABLE `file_server` CHANGE `ipAddress` `ipAddress` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

ALTER TABLE `file_server` CHANGE `serverType` `serverType` ENUM('remote','local','ftp','sftp','direct') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE `file_server`  ADD `fileServerDomainName` VARCHAR(255) NULL,  ADD `scriptPath` VARCHAR(150) NULL;
ALTER TABLE `file` ADD `accessPassword` VARCHAR( 32 ) NULL;

CREATE TABLE `session_transfer` (`id` INT(11) NULL, `transfer_key` VARCHAR(32) NOT NULL, `session_id` VARCHAR(32) NOT NULL, PRIMARY KEY (`id`)) ENGINE = MyISAM;
ALTER TABLE `session_transfer`  ADD `date_added` DATETIME NOT NULL;
ALTER TABLE `session_transfer` ADD INDEX ( `date_added` );
ALTER TABLE `session_transfer` ADD INDEX ( `session_id` );
ALTER TABLE `session_transfer` ADD INDEX ( `transfer_key` );
ALTER TABLE `session_transfer` CHANGE `id` `id` INT( 11 ) NOT NULL AUTO_INCREMENT;


ALTER TABLE `file` ADD `fileHash` VARCHAR( 32 ) NULL;

INSERT INTO `site_config` (`id`, `config_key`, `config_value`, `config_description`, `availableValues`, `config_type`, `config_group`) VALUES (NULL, 'free_user_max_downloads_per_day', '0', 'The maximum files a free user can download in a 24 hour period. Set to 0 (zero) to disable.', '', 'integer', 'Free User Settings');
INSERT INTO `site_config` (`id`, `config_key`, `config_value`, `config_description`, `availableValues`, `config_type`, `config_group`) VALUES (NULL, 'session_expiry', '86400', 'The amount of time a user can be inactive before their session will expire. In seconds. Default is 86400 (1 day)', '', 'integer', 'Page Options');

CREATE TABLE IF NOT EXISTS `country_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `iso_alpha2` varchar(2) DEFAULT NULL,
  `iso_alpha3` varchar(3) DEFAULT NULL,
  `name` varchar(200) DEFAULT NULL,
  `currency_code` char(3) DEFAULT NULL,
  `currency_name` varchar(32) DEFAULT NULL,
  `currrency_symbol` varchar(3) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=248 ;

--
-- Dumping data for table `country_info`
--
DELETE FROM `country_info`;
INSERT INTO `country_info` (`id`, `iso_alpha2`, `iso_alpha3`, `name`, `currency_code`, `currency_name`, `currrency_symbol`) VALUES
(1, 'AD', 'AND', 'Andorra', 'EUR', 'Euro', 'â‚¬'),
(2, 'AE', 'ARE', 'United Arab Emirates', 'AED', 'Dirham', NULL),
(3, 'AF', 'AFG', 'Afghanistan', 'AFN', 'Afghani', 'Ø‹'),
(4, 'AG', 'ATG', 'Antigua and Barbuda', 'XCD', 'Dollar', '$'),
(5, 'AI', 'AIA', 'Anguilla', 'XCD', 'Dollar', '$'),
(6, 'AL', 'ALB', 'Albania', 'ALL', 'Lek', 'Lek'),
(7, 'AM', 'ARM', 'Armenia', 'AMD', 'Dram', NULL),
(8, 'AN', 'ANT', 'Netherlands Antilles', 'ANG', 'Guilder', 'Æ’'),
(9, 'AO', 'AGO', 'Angola', 'AOA', 'Kwanza', 'Kz'),
(10, 'AQ', 'ATA', 'Antarctica', '', '', NULL),
(11, 'AR', 'ARG', 'Argentina', 'ARS', 'Peso', '$'),
(12, 'AS', 'ASM', 'American Samoa', 'USD', 'Dollar', '$'),
(13, 'AT', 'AUT', 'Austria', 'EUR', 'Euro', 'â‚¬'),
(14, 'AU', 'AUS', 'Australia', 'AUD', 'Dollar', '$'),
(15, 'AW', 'ABW', 'Aruba', 'AWG', 'Guilder', 'Æ’'),
(16, 'AX', 'ALA', 'Aland Islands', 'EUR', 'Euro', 'â‚¬'),
(17, 'AZ', 'AZE', 'Azerbaijan', 'AZN', 'Manat', 'Ð¼Ð°Ð½'),
(18, 'BA', 'BIH', 'Bosnia and Herzegovina', 'BAM', 'Marka', 'KM'),
(19, 'BB', 'BRB', 'Barbados', 'BBD', 'Dollar', '$'),
(20, 'BD', 'BGD', 'Bangladesh', 'BDT', 'Taka', NULL),
(21, 'BE', 'BEL', 'Belgium', 'EUR', 'Euro', 'â‚¬'),
(22, 'BF', 'BFA', 'Burkina Faso', 'XOF', 'Franc', NULL),
(23, 'BG', 'BGR', 'Bulgaria', 'BGN', 'Lev', 'Ð»Ð²'),
(24, 'BH', 'BHR', 'Bahrain', 'BHD', 'Dinar', NULL),
(25, 'BI', 'BDI', 'Burundi', 'BIF', 'Franc', NULL),
(26, 'BJ', 'BEN', 'Benin', 'XOF', 'Franc', NULL),
(27, 'BL', 'BLM', 'Saint BarthÃ©lemy', 'EUR', 'Euro', 'â‚¬'),
(28, 'BM', 'BMU', 'Bermuda', 'BMD', 'Dollar', '$'),
(29, 'BN', 'BRN', 'Brunei', 'BND', 'Dollar', '$'),
(30, 'BO', 'BOL', 'Bolivia', 'BOB', 'Boliviano', '$b'),
(31, 'BR', 'BRA', 'Brazil', 'BRL', 'Real', 'R$'),
(32, 'BS', 'BHS', 'Bahamas', 'BSD', 'Dollar', '$'),
(33, 'BT', 'BTN', 'Bhutan', 'BTN', 'Ngultrum', NULL),
(34, 'BV', 'BVT', 'Bouvet Island', 'NOK', 'Krone', 'kr'),
(35, 'BW', 'BWA', 'Botswana', 'BWP', 'Pula', 'P'),
(36, 'BY', 'BLR', 'Belarus', 'BYR', 'Ruble', 'p.'),
(37, 'BZ', 'BLZ', 'Belize', 'BZD', 'Dollar', 'BZ$'),
(38, 'CA', 'CAN', 'Canada', 'CAD', 'Dollar', '$'),
(39, 'CC', 'CCK', 'Cocos Islands', 'AUD', 'Dollar', '$'),
(40, 'CD', 'COD', 'Democratic Republic of the Congo', 'CDF', 'Franc', NULL),
(41, 'CF', 'CAF', 'Central African Republic', 'XAF', 'Franc', 'FCF'),
(42, 'CG', 'COG', 'Republic of the Congo', 'XAF', 'Franc', 'FCF'),
(43, 'CH', 'CHE', 'Switzerland', 'CHF', 'Franc', 'CHF'),
(44, 'CI', 'CIV', 'Ivory Coast', 'XOF', 'Franc', NULL),
(45, 'CK', 'COK', 'Cook Islands', 'NZD', 'Dollar', '$'),
(46, 'CL', 'CHL', 'Chile', 'CLP', 'Peso', NULL),
(47, 'CM', 'CMR', 'Cameroon', 'XAF', 'Franc', 'FCF'),
(48, 'CN', 'CHN', 'China', 'CNY', 'Yuan Renminbi', 'Â¥'),
(49, 'CO', 'COL', 'Colombia', 'COP', 'Peso', '$'),
(50, 'CR', 'CRI', 'Costa Rica', 'CRC', 'Colon', 'â‚¡'),
(51, 'CU', 'CUB', 'Cuba', 'CUP', 'Peso', 'â‚±'),
(52, 'CV', 'CPV', 'Cape Verde', 'CVE', 'Escudo', NULL),
(53, 'CX', 'CXR', 'Christmas Island', 'AUD', 'Dollar', '$'),
(54, 'CY', 'CYP', 'Cyprus', 'CYP', 'Pound', NULL),
(55, 'CZ', 'CZE', 'Czech Republic', 'CZK', 'Koruna', 'KÄ'),
(56, 'DE', 'DEU', 'Germany', 'EUR', 'Euro', 'â‚¬'),
(57, 'DJ', 'DJI', 'Djibouti', 'DJF', 'Franc', NULL),
(58, 'DK', 'DNK', 'Denmark', 'DKK', 'Krone', 'kr'),
(59, 'DM', 'DMA', 'Dominica', 'XCD', 'Dollar', '$'),
(60, 'DO', 'DOM', 'Dominican Republic', 'DOP', 'Peso', 'RD$'),
(61, 'DZ', 'DZA', 'Algeria', 'DZD', 'Dinar', NULL),
(62, 'EC', 'ECU', 'Ecuador', 'USD', 'Dollar', '$'),
(63, 'EE', 'EST', 'Estonia', 'EEK', 'Kroon', 'kr'),
(64, 'EG', 'EGY', 'Egypt', 'EGP', 'Pound', 'Â£'),
(65, 'EH', 'ESH', 'Western Sahara', 'MAD', 'Dirham', NULL),
(66, 'ER', 'ERI', 'Eritrea', 'ERN', 'Nakfa', 'Nfk'),
(67, 'ES', 'ESP', 'Spain', 'EUR', 'Euro', 'â‚¬'),
(68, 'ET', 'ETH', 'Ethiopia', 'ETB', 'Birr', NULL),
(69, 'FI', 'FIN', 'Finland', 'EUR', 'Euro', 'â‚¬'),
(70, 'FJ', 'FJI', 'Fiji', 'FJD', 'Dollar', '$'),
(71, 'FK', 'FLK', 'Falkland Islands', 'FKP', 'Pound', 'Â£'),
(72, 'FM', 'FSM', 'Micronesia', 'USD', 'Dollar', '$'),
(73, 'FO', 'FRO', 'Faroe Islands', 'DKK', 'Krone', 'kr'),
(74, 'FR', 'FRA', 'France', 'EUR', 'Euro', 'â‚¬'),
(75, 'GA', 'GAB', 'Gabon', 'XAF', 'Franc', 'FCF'),
(76, 'GB', 'GBR', 'United Kingdom', 'GBP', 'Pound', 'Â£'),
(77, 'GD', 'GRD', 'Grenada', 'XCD', 'Dollar', '$'),
(78, 'GE', 'GEO', 'Georgia', 'GEL', 'Lari', NULL),
(79, 'GF', 'GUF', 'French Guiana', 'EUR', 'Euro', 'â‚¬'),
(80, 'GG', 'GGY', 'Guernsey', 'GGP', 'Pound', 'Â£'),
(81, 'GH', 'GHA', 'Ghana', 'GHC', 'Cedi', 'Â¢'),
(82, 'GI', 'GIB', 'Gibraltar', 'GIP', 'Pound', 'Â£'),
(83, 'GL', 'GRL', 'Greenland', 'DKK', 'Krone', 'kr'),
(84, 'GM', 'GMB', 'Gambia', 'GMD', 'Dalasi', 'D'),
(85, 'GN', 'GIN', 'Guinea', 'GNF', 'Franc', NULL),
(86, 'GP', 'GLP', 'Guadeloupe', 'EUR', 'Euro', 'â‚¬'),
(87, 'GQ', 'GNQ', 'Equatorial Guinea', 'XAF', 'Franc', 'FCF'),
(88, 'GR', 'GRC', 'Greece', 'EUR', 'Euro', 'â‚¬'),
(89, 'GS', 'SGS', 'South Georgia and the South Sandwich Islands', 'GBP', 'Pound', 'Â£'),
(90, 'GT', 'GTM', 'Guatemala', 'GTQ', 'Quetzal', 'Q'),
(91, 'GU', 'GUM', 'Guam', 'USD', 'Dollar', '$'),
(92, 'GW', 'GNB', 'Guinea-Bissau', 'XOF', 'Franc', NULL),
(93, 'GY', 'GUY', 'Guyana', 'GYD', 'Dollar', '$'),
(94, 'HK', 'HKG', 'Hong Kong', 'HKD', 'Dollar', '$'),
(95, 'HM', 'HMD', 'Heard Island and McDonald Islands', 'AUD', 'Dollar', '$'),
(96, 'HN', 'HND', 'Honduras', 'HNL', 'Lempira', 'L'),
(97, 'HR', 'HRV', 'Croatia', 'HRK', 'Kuna', 'kn'),
(98, 'HT', 'HTI', 'Haiti', 'HTG', 'Gourde', 'G'),
(99, 'HU', 'HUN', 'Hungary', 'HUF', 'Forint', 'Ft'),
(100, 'ID', 'IDN', 'Indonesia', 'IDR', 'Rupiah', 'Rp'),
(101, 'IE', 'IRL', 'Ireland', 'EUR', 'Euro', 'â‚¬'),
(102, 'IL', 'ISR', 'Israel', 'ILS', 'Shekel', 'â‚ª'),
(103, 'IM', 'IMN', 'Isle of Man', 'GPD', 'Pound', 'Â£'),
(104, 'IN', 'IND', 'India', 'INR', 'Rupee', 'â‚¨'),
(105, 'IO', 'IOT', 'British Indian Ocean Territory', 'USD', 'Dollar', '$'),
(106, 'IQ', 'IRQ', 'Iraq', 'IQD', 'Dinar', NULL),
(107, 'IR', 'IRN', 'Iran', 'IRR', 'Rial', 'ï·¼'),
(108, 'IS', 'ISL', 'Iceland', 'ISK', 'Krona', 'kr'),
(109, 'IT', 'ITA', 'Italy', 'EUR', 'Euro', 'â‚¬'),
(110, 'JE', 'JEY', 'Jersey', 'JEP', 'Pound', 'Â£'),
(111, 'JM', 'JAM', 'Jamaica', 'JMD', 'Dollar', '$'),
(112, 'JO', 'JOR', 'Jordan', 'JOD', 'Dinar', NULL),
(113, 'JP', 'JPN', 'Japan', 'JPY', 'Yen', 'Â¥'),
(114, 'KE', 'KEN', 'Kenya', 'KES', 'Shilling', NULL),
(115, 'KG', 'KGZ', 'Kyrgyzstan', 'KGS', 'Som', 'Ð»Ð²'),
(116, 'KH', 'KHM', 'Cambodia', 'KHR', 'Riels', 'áŸ›'),
(117, 'KI', 'KIR', 'Kiribati', 'AUD', 'Dollar', '$'),
(118, 'KM', 'COM', 'Comoros', 'KMF', 'Franc', NULL),
(119, 'KN', 'KNA', 'Saint Kitts and Nevis', 'XCD', 'Dollar', '$'),
(120, 'KP', 'PRK', 'North Korea', 'KPW', 'Won', 'â‚©'),
(121, 'KR', 'KOR', 'South Korea', 'KRW', 'Won', 'â‚©'),
(122, 'KW', 'KWT', 'Kuwait', 'KWD', 'Dinar', NULL),
(123, 'KY', 'CYM', 'Cayman Islands', 'KYD', 'Dollar', '$'),
(124, 'KZ', 'KAZ', 'Kazakhstan', 'KZT', 'Tenge', 'Ð»Ð²'),
(125, 'LA', 'LAO', 'Laos', 'LAK', 'Kip', 'â‚­'),
(126, 'LB', 'LBN', 'Lebanon', 'LBP', 'Pound', 'Â£'),
(127, 'LC', 'LCA', 'Saint Lucia', 'XCD', 'Dollar', '$'),
(128, 'LI', 'LIE', 'Liechtenstein', 'CHF', 'Franc', 'CHF'),
(129, 'LK', 'LKA', 'Sri Lanka', 'LKR', 'Rupee', 'â‚¨'),
(130, 'LR', 'LBR', 'Liberia', 'LRD', 'Dollar', '$'),
(131, 'LS', 'LSO', 'Lesotho', 'LSL', 'Loti', 'L'),
(132, 'LT', 'LTU', 'Lithuania', 'LTL', 'Litas', 'Lt'),
(133, 'LU', 'LUX', 'Luxembourg', 'EUR', 'Euro', 'â‚¬'),
(134, 'LV', 'LVA', 'Latvia', 'LVL', 'Lat', 'Ls'),
(135, 'LY', 'LBY', 'Libya', 'LYD', 'Dinar', NULL),
(136, 'MA', 'MAR', 'Morocco', 'MAD', 'Dirham', NULL),
(137, 'MC', 'MCO', 'Monaco', 'EUR', 'Euro', 'â‚¬'),
(138, 'MD', 'MDA', 'Moldova', 'MDL', 'Leu', NULL),
(139, 'ME', 'MNE', 'Montenegro', 'EUR', 'Euro', 'â‚¬'),
(140, 'MF', 'MAF', 'Saint Martin', 'EUR', 'Euro', 'â‚¬'),
(141, 'MG', 'MDG', 'Madagascar', 'MGA', 'Ariary', NULL),
(142, 'MH', 'MHL', 'Marshall Islands', 'USD', 'Dollar', '$'),
(143, 'MK', 'MKD', 'Macedonia', 'MKD', 'Denar', 'Ð´ÐµÐ½'),
(144, 'ML', 'MLI', 'Mali', 'XOF', 'Franc', NULL),
(145, 'MM', 'MMR', 'Myanmar', 'MMK', 'Kyat', 'K'),
(146, 'MN', 'MNG', 'Mongolia', 'MNT', 'Tugrik', 'â‚®'),
(147, 'MO', 'MAC', 'Macao', 'MOP', 'Pataca', 'MOP'),
(148, 'MP', 'MNP', 'Northern Mariana Islands', 'USD', 'Dollar', '$'),
(149, 'MQ', 'MTQ', 'Martinique', 'EUR', 'Euro', 'â‚¬'),
(150, 'MR', 'MRT', 'Mauritania', 'MRO', 'Ouguiya', 'UM'),
(151, 'MS', 'MSR', 'Montserrat', 'XCD', 'Dollar', '$'),
(152, 'MT', 'MLT', 'Malta', 'MTL', 'Lira', NULL),
(153, 'MU', 'MUS', 'Mauritius', 'MUR', 'Rupee', 'â‚¨'),
(154, 'MV', 'MDV', 'Maldives', 'MVR', 'Rufiyaa', 'Rf'),
(155, 'MW', 'MWI', 'Malawi', 'MWK', 'Kwacha', 'MK'),
(156, 'MX', 'MEX', 'Mexico', 'MXN', 'Peso', '$'),
(157, 'MY', 'MYS', 'Malaysia', 'MYR', 'Ringgit', 'RM'),
(158, 'MZ', 'MOZ', 'Mozambique', 'MZN', 'Meticail', 'MT'),
(159, 'NA', 'NAM', 'Namibia', 'NAD', 'Dollar', '$'),
(160, 'NC', 'NCL', 'New Caledonia', 'XPF', 'Franc', NULL),
(161, 'NE', 'NER', 'Niger', 'XOF', 'Franc', NULL),
(162, 'NF', 'NFK', 'Norfolk Island', 'AUD', 'Dollar', '$'),
(163, 'NG', 'NGA', 'Nigeria', 'NGN', 'Naira', 'â‚¦'),
(164, 'NI', 'NIC', 'Nicaragua', 'NIO', 'Cordoba', 'C$'),
(165, 'NL', 'NLD', 'Netherlands', 'EUR', 'Euro', 'â‚¬'),
(166, 'NO', 'NOR', 'Norway', 'NOK', 'Krone', 'kr'),
(167, 'NP', 'NPL', 'Nepal', 'NPR', 'Rupee', 'â‚¨'),
(168, 'NR', 'NRU', 'Nauru', 'AUD', 'Dollar', '$'),
(169, 'NU', 'NIU', 'Niue', 'NZD', 'Dollar', '$'),
(170, 'NZ', 'NZL', 'New Zealand', 'NZD', 'Dollar', '$'),
(171, 'OM', 'OMN', 'Oman', 'OMR', 'Rial', 'ï·¼'),
(172, 'PA', 'PAN', 'Panama', 'PAB', 'Balboa', 'B/.'),
(173, 'PE', 'PER', 'Peru', 'PEN', 'Sol', 'S/.'),
(174, 'PF', 'PYF', 'French Polynesia', 'XPF', 'Franc', NULL),
(175, 'PG', 'PNG', 'Papua New Guinea', 'PGK', 'Kina', NULL),
(176, 'PH', 'PHL', 'Philippines', 'PHP', 'Peso', 'Php'),
(177, 'PK', 'PAK', 'Pakistan', 'PKR', 'Rupee', 'â‚¨'),
(178, 'PL', 'POL', 'Poland', 'PLN', 'Zloty', 'zÅ‚'),
(179, 'PM', 'SPM', 'Saint Pierre and Miquelon', 'EUR', 'Euro', 'â‚¬'),
(180, 'PN', 'PCN', 'Pitcairn', 'NZD', 'Dollar', '$'),
(181, 'PR', 'PRI', 'Puerto Rico', 'USD', 'Dollar', '$'),
(182, 'PS', 'PSE', 'Palestinian Territory', 'ILS', 'Shekel', 'â‚ª'),
(183, 'PT', 'PRT', 'Portugal', 'EUR', 'Euro', 'â‚¬'),
(184, 'PW', 'PLW', 'Palau', 'USD', 'Dollar', '$'),
(185, 'PY', 'PRY', 'Paraguay', 'PYG', 'Guarani', 'Gs'),
(186, 'QA', 'QAT', 'Qatar', 'QAR', 'Rial', 'ï·¼'),
(187, 'RE', 'REU', 'Reunion', 'EUR', 'Euro', 'â‚¬'),
(188, 'RO', 'ROU', 'Romania', 'RON', 'Leu', 'lei'),
(189, 'RS', 'SRB', 'Serbia', 'RSD', 'Dinar', 'Ð”Ð¸Ð½'),
(190, 'RU', 'RUS', 'Russia', 'RUB', 'Ruble', 'Ñ€ÑƒÐ±'),
(191, 'RW', 'RWA', 'Rwanda', 'RWF', 'Franc', NULL),
(192, 'SA', 'SAU', 'Saudi Arabia', 'SAR', 'Rial', 'ï·¼'),
(193, 'SB', 'SLB', 'Solomon Islands', 'SBD', 'Dollar', '$'),
(194, 'SC', 'SYC', 'Seychelles', 'SCR', 'Rupee', 'â‚¨'),
(195, 'SD', 'SDN', 'Sudan', 'SDD', 'Dinar', NULL),
(196, 'SE', 'SWE', 'Sweden', 'SEK', 'Krona', 'kr'),
(197, 'SG', 'SGP', 'Singapore', 'SGD', 'Dollar', '$'),
(198, 'SH', 'SHN', 'Saint Helena', 'SHP', 'Pound', 'Â£'),
(199, 'SI', 'SVN', 'Slovenia', 'EUR', 'Euro', 'â‚¬'),
(200, 'SJ', 'SJM', 'Svalbard and Jan Mayen', 'NOK', 'Krone', 'kr'),
(201, 'SK', 'SVK', 'Slovakia', 'SKK', 'Koruna', 'Sk'),
(202, 'SL', 'SLE', 'Sierra Leone', 'SLL', 'Leone', 'Le'),
(203, 'SM', 'SMR', 'San Marino', 'EUR', 'Euro', 'â‚¬'),
(204, 'SN', 'SEN', 'Senegal', 'XOF', 'Franc', NULL),
(205, 'SO', 'SOM', 'Somalia', 'SOS', 'Shilling', 'S'),
(206, 'SR', 'SUR', 'Suriname', 'SRD', 'Dollar', '$'),
(207, 'ST', 'STP', 'Sao Tome and Principe', 'STD', 'Dobra', 'Db'),
(208, 'SV', 'SLV', 'El Salvador', 'SVC', 'Colone', '$'),
(209, 'SY', 'SYR', 'Syria', 'SYP', 'Pound', 'Â£'),
(210, 'SZ', 'SWZ', 'Swaziland', 'SZL', 'Lilangeni', NULL),
(211, 'TC', 'TCA', 'Turks and Caicos Islands', 'USD', 'Dollar', '$'),
(212, 'TD', 'TCD', 'Chad', 'XAF', 'Franc', NULL),
(213, 'TF', 'ATF', 'French Southern Territories', 'EUR', 'Euro  ', 'â‚¬'),
(214, 'TG', 'TGO', 'Togo', 'XOF', 'Franc', NULL),
(215, 'TH', 'THA', 'Thailand', 'THB', 'Baht', 'à¸¿'),
(216, 'TJ', 'TJK', 'Tajikistan', 'TJS', 'Somoni', NULL),
(217, 'TK', 'TKL', 'Tokelau', 'NZD', 'Dollar', '$'),
(218, 'TL', 'TLS', 'East Timor', 'USD', 'Dollar', '$'),
(219, 'TM', 'TKM', 'Turkmenistan', 'TMM', 'Manat', 'm'),
(220, 'TN', 'TUN', 'Tunisia', 'TND', 'Dinar', NULL),
(221, 'TO', 'TON', 'Tonga', 'TOP', 'Pa''anga', 'T$'),
(222, 'TR', 'TUR', 'Turkey', 'TRY', 'Lira', 'YTL'),
(223, 'TT', 'TTO', 'Trinidad and Tobago', 'TTD', 'Dollar', 'TT$'),
(224, 'TV', 'TUV', 'Tuvalu', 'AUD', 'Dollar', '$'),
(225, 'TW', 'TWN', 'Taiwan', 'TWD', 'Dollar', 'NT$'),
(226, 'TZ', 'TZA', 'Tanzania', 'TZS', 'Shilling', NULL),
(227, 'UA', 'UKR', 'Ukraine', 'UAH', 'Hryvnia', 'â‚´'),
(228, 'UG', 'UGA', 'Uganda', 'UGX', 'Shilling', NULL),
(229, 'UM', 'UMI', 'United States Minor Outlying Islands', 'USD', 'Dollar ', '$'),
(230, 'US', 'USA', 'United States', 'USD', 'Dollar', '$'),
(231, 'UY', 'URY', 'Uruguay', 'UYU', 'Peso', '$U'),
(232, 'UZ', 'UZB', 'Uzbekistan', 'UZS', 'Som', 'Ð»Ð²'),
(233, 'VA', 'VAT', 'Vatican', 'EUR', 'Euro', 'â‚¬'),
(234, 'VC', 'VCT', 'Saint Vincent and the Grenadines', 'XCD', 'Dollar', '$'),
(235, 'VE', 'VEN', 'Venezuela', 'VEF', 'Bolivar', 'Bs'),
(236, 'VG', 'VGB', 'British Virgin Islands', 'USD', 'Dollar', '$'),
(237, 'VI', 'VIR', 'U.S. Virgin Islands', 'USD', 'Dollar', '$'),
(238, 'VN', 'VNM', 'Vietnam', 'VND', 'Dong', 'â‚«'),
(239, 'VU', 'VUT', 'Vanuatu', 'VUV', 'Vatu', 'Vt'),
(240, 'WF', 'WLF', 'Wallis and Futuna', 'XPF', 'Franc', NULL),
(241, 'WS', 'WSM', 'Samoa', 'WST', 'Tala', 'WS$'),
(242, 'YE', 'YEM', 'Yemen', 'YER', 'Rial', 'ï·¼'),
(243, 'YT', 'MYT', 'Mayotte', 'EUR', 'Euro', 'â‚¬'),
(244, 'ZA', 'ZAF', 'South Africa', 'ZAR', 'Rand', 'R'),
(245, 'ZM', 'ZMB', 'Zambia', 'ZMK', 'Kwacha', 'ZK'),
(246, 'ZW', 'ZWE', 'Zimbabwe', 'ZWD', 'Dollar', 'Z$'),
(247, 'CS', 'SCG', 'Serbia and Montenegro', 'RSD', 'Dinar', 'Ð”Ð¸Ð½');

INSERT INTO `site_config` (`id`, `config_key`, `config_value`, `config_description`, `availableValues`, `config_type`, `config_group`) VALUES (NULL, 'logging_log_enabled', 'yes', 'Whether to enable logging or not. The /logs/ folder should have write permissions on it. (chmod 755 or 777)', '["yes","no"]', 'select', 'Logs');
INSERT INTO `site_config` (`id`, `config_key`, `config_value`, `config_description`, `availableValues`, `config_type`, `config_group`) VALUES (NULL, 'logging_log_type', 'Serious Errors Only', 'The types of log messages to store in the log files. \'Serious Errors Only\' will log the important ones. Ensure logging is enabled for this setting to work.', '["Serious Errors Only","Serious Errors and Warnings","All Error Types"]', 'select', 'Logs');
INSERT INTO `site_config` (`id`, `config_key`, `config_value`, `config_description`, `availableValues`, `config_type`, `config_group`) VALUES (NULL, 'logging_log_output', 'no', 'Whether to output serious errors to screen, if possible. Always set this to \'no\' for your live site.', '["yes","no"]', 'select', 'Logs');

ALTER TABLE `file_folder` ADD `parentId` INT( 11 ) NULL DEFAULT NULL AFTER `userId`;

INSERT INTO `site_config` (`id`, `config_key`, `config_value`, `config_description`, `availableValues`, `config_type`, `config_group`) VALUES (NULL, 'adverts_show_to_premium', 'hide', 'Whether to show or hide adverts to paid and admin users.', '["show","hide"]', 'select', 'Adverts');

ALTER TABLE `plugin` ADD `load_order` INT( 3 ) NULL DEFAULT 999;
ALTER TABLE `plugin` ADD INDEX ( `load_order` );

ALTER TABLE  `payment_log` ADD INDEX (  `date_created` );
ALTER TABLE  `payment_log` ADD INDEX (  `description` );
ALTER TABLE  `payment_log` ADD INDEX (  `user_id` );

CREATE TABLE `file_report` (`id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, `file_id` INT(11) NOT NULL, `report_date` DATETIME NOT NULL, `reported_by_name` VARCHAR(150) NOT NULL, `reported_by_email` VARCHAR(255) NOT NULL, `reported_by_address` TEXT NOT NULL, `reported_by_telephone_number` VARCHAR(30) NOT NULL, `digital_signature` VARCHAR(150) NOT NULL, `report_status` ENUM('pending','cancelled','accepted') NOT NULL, `reported_by_ip` VARCHAR(15) NOT NULL, `other_information` TEXT NOT NULL, INDEX (`file_id`)) ENGINE = MyISAM;

ALTER TABLE `language_content` ADD INDEX ( `languageKeyId` );
ALTER TABLE `language_content` ADD INDEX ( `languageId` );

ALTER TABLE `users` ADD `storageLimitOverride` BIGINT( 15 ) NULL DEFAULT NULL;

INSERT INTO `site_config` (`id`, `config_key`, `config_value`, `config_description`, `availableValues`, `config_type`, `config_group`) VALUES (NULL, 'free_user_maximum_storage', '0', 'Maximum storage permitted for free users, in bytes. Use 0 (zero) for no limits.', '', 'integer', 'Free User Settings');
INSERT INTO `site_config` (`id`, `config_key`, `config_value`, `config_description`, `availableValues`, `config_type`, `config_group`) VALUES (NULL, 'premium_user_maximum_storage', '0', 'Maximum storage permitted for paid users, in bytes. Use 0 (zero) for no limits.', '', 'integer', 'Premium User Settings');

INSERT INTO `site_config` (`id`, `config_key`, `config_value`, `config_description`, `availableValues`, `config_type`, `config_group`) (SELECT NULL, 'non_user_max_concurrent_uploads', config_value, 'The maximum amount of files that can be uploaded at the same time for non-logged in users.', availableValues, config_type, 'Non User Settings' FROM site_config WHERE config_key = 'free_user_max_concurrent_uploads');
INSERT INTO `site_config` (`id`, `config_key`, `config_value`, `config_description`, `availableValues`, `config_type`, `config_group`) (SELECT NULL, 'non_user_redirect_delay_seconds', config_value, 'How long non users have to wait before being able to download (in seconds). Set to 0 to allow non users direct download access.', availableValues, config_type, 'Non User Settings' FROM site_config WHERE config_key = 'redirect_delay_seconds');
UPDATE site_config SET config_key = 'free_user_redirect_delay_seconds', config_description = 'How long free users have to wait before being able to download (in seconds). Set to 0 to allow free users direct download access.' WHERE config_key = 'redirect_delay_seconds';

INSERT INTO `site_config` (`id`, `config_key`, `config_value`, `config_description`, `availableValues`, `config_type`, `config_group`) (SELECT NULL, 'non_user_max_downloads_per_day', config_value, 'The maximum files a non-logged in user can download in a 24 hour period. Set to 0 (zero) to disable. Note: Ensure the \'downloads_track_current_downloads\' is also set to \'yes\' to enable this.', availableValues, config_type, 'Non User Settings' FROM site_config WHERE config_key = 'free_user_max_downloads_per_day');
INSERT INTO `site_config` (`id`, `config_key`, `config_value`, `config_description`, `availableValues`, `config_type`, `config_group`) (SELECT NULL, 'premium_user_max_downloads_per_day', config_value, 'The maximum files a paid user can download in a 24 hour period. Set to 0 (zero) to disable. Note: Ensure the \'downloads_track_current_downloads\' is also set to \'yes\' to enable this.', availableValues, config_type, 'Premium User Settings' FROM site_config WHERE config_key = 'free_user_max_downloads_per_day');

INSERT INTO `site_config` (`id`, `config_key`, `config_value`, `config_description`, `availableValues`, `config_type`, `config_group`) (SELECT NULL, 'non_user_max_download_filesize', config_value, 'The maximum filesize a non-logged in user can download (in bytes). Set to 0 (zero) to ignore.', availableValues, config_type, 'Non User Settings' FROM site_config WHERE config_key = 'free_user_max_download_filesize');
UPDATE site_config SET config_description = 'The maximum filesize a free user can download (in bytes). Set to 0 (zero) to ignore.' WHERE config_key = 'free_user_max_download_filesize';

INSERT INTO `site_config` (`id`, `config_key`, `config_value`, `config_description`, `availableValues`, `config_type`, `config_group`) (SELECT NULL, 'non_user_max_download_speed', config_value, 'Maximum download speed for non-logged in users, in bytes per second. i.e. 50000. Use 0 for unlimited. ', availableValues, config_type, 'Non User Settings' FROM site_config WHERE config_key = 'free_user_max_download_speed');
UPDATE site_config SET config_description = 'Maximum download speed for free users, in bytes per second. i.e. 50000. Use 0 for unlimited. ' WHERE config_key = 'free_user_max_download_speed';

INSERT INTO `site_config` (`id`, `config_key`, `config_value`, `config_description`, `availableValues`, `config_type`, `config_group`) (SELECT NULL, 'non_user_max_remote_urls', config_value, 'The maximum remote urls a non-logged in user can specify at once.', availableValues, config_type, 'Non User Settings' FROM site_config WHERE config_key = 'free_user_max_remote_urls');
UPDATE site_config SET config_description = 'The maximum remote urls a free user can specify at once.' WHERE config_key = 'free_user_max_remote_urls';

INSERT INTO `site_config` (`id`, `config_key`, `config_value`, `config_description`, `availableValues`, `config_type`, `config_group`) (SELECT NULL, 'non_user_max_upload_filesize', config_value, 'The max upload filesize for non-logged in users (in bytes)', availableValues, config_type, 'Non User Settings' FROM site_config WHERE config_key = 'free_user_max_upload_filesize');
INSERT INTO `site_config` (`id`, `config_key`, `config_value`, `config_description`, `availableValues`, `config_type`, `config_group`) (SELECT NULL, 'non_user_show_captcha', config_value, 'Show the captcha after a non-logged in user sees the countdown timer.', availableValues, config_type, 'Non User Settings' FROM site_config WHERE config_key = 'free_user_show_captcha');
INSERT INTO `site_config` (`id`, `config_key`, `config_value`, `config_description`, `availableValues`, `config_type`, `config_group`) (SELECT NULL, 'non_user_upload_removal_days', config_value, 'The amount of days after non-active files are removed for non account users. Leave blank for unlimited.', availableValues, config_type, 'Non User Settings' FROM site_config WHERE config_key = 'free_user_upload_removal_days');
INSERT INTO `site_config` (`id`, `config_key`, `config_value`, `config_description`, `availableValues`, `config_type`, `config_group`) (SELECT NULL, 'non_user_wait_between_downloads', config_value, 'How long a non-logged in user must wait between downloads, in seconds. Set to 0 (zero) to disable. Note: Ensure the \'downloads_track_current_downloads\' is also set to \'yes\' to enable this.', availableValues, config_type, 'Non User Settings' FROM site_config WHERE config_key = 'free_user_wait_between_downloads');

ALTER TABLE `sessions` ADD `user_id` INT NULL DEFAULT NULL , ADD INDEX ( `user_id` );
INSERT INTO `site_config` (`id`, `config_key`, `config_value`, `config_description`, `availableValues`, `config_type`, `config_group`) VALUES (NULL, 'premium_user_block_account_sharing', 'no', 'Block paid account sharing. Accounts will only allow 1 login session. Any open sessions will be closed on a new login.', '["yes","no"]', 'select', 'Premium User Settings');

INSERT INTO `site_config` (`id`, `config_key`, `config_value`, `config_description`, `availableValues`, `config_type`, `config_group`) VALUES (NULL, 'language_separate_language_images', 'no', 'Use different images/css for each language. If yes, copy all the files from images/styles in /themes/blue_v2/ to /themes/blue_v2/[flag_code]/, keeping the folders. Replace \'[flag_code]\' with the 2 letter language flag code. i.e. /themes/blue_v2/es/', '["yes","no"]', 'select', 'Language');

UPDATE `site_config` SET `config_description` = 'Show translation value or key. (use "key" to debug translations, "translation" to show actual translated value. "key title text" to show the key as a title tag around the text content)',
`availableValues` = '["key","translation", "key title text"]' WHERE `site_config`.`config_key` ='language_show_key';

ALTER TABLE `users` ADD `languageId` INT( 11 ) NULL AFTER `lastname`;

INSERT INTO `site_config` (`id`, `config_key`, `config_value`, `config_description`, `availableValues`, `config_type`, `config_group`) VALUES (NULL, 'language_user_select_language', 'no', 'Give users the option to set their account language. Available as a drop-down in account settings. Automatically sets the language of the site on login.', '["yes","no"]', 'select', 'Language');
INSERT INTO `site_config` (`id`, `config_key`, `config_value`, `config_description`, `availableValues`, `config_type`, `config_group`) VALUES (NULL, 'maintenance_mode', 'no', 'Whether to place the entire site into maintenance mode. Useful for site upgrades or server moves. Admin area is still accessible. Maintenance page content is in _maintenance_page.inc.php.', '["yes","no"]', 'select', 'Page Options');

ALTER TABLE `payment_log` ADD `payment_method` VARCHAR( 50 ) NULL;

CREATE TABLE `user_level` (
`id` INT( 5 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`level_id` INT( 5 ) NOT NULL ,
`label` VARCHAR( 20 ) NOT NULL
) ENGINE = MYISAM ;

INSERT INTO `user_level` (`id`, `level_id`, `label`) VALUES (NULL, '1', 'free user'), (NULL, '2', 'paid user');
INSERT INTO `user_level` (`id`, `level_id`, `label`) VALUES (NULL, '10', 'moderator'), (NULL, '20', 'admin');
ALTER TABLE `users` ADD `level_id` INT( 5 ) NOT NULL DEFAULT '1' AFTER `level`;
UPDATE users SET level_id = 1 WHERE level = 'free user';
UPDATE users SET level_id = 2 WHERE level = 'paid user';
UPDATE users SET level_id = 10 WHERE level = 'moderator';
UPDATE users SET level_id = 20 WHERE level = 'admin';
ALTER TABLE `users` DROP `level`;

UPDATE `site_config` SET `config_group` = 'Site Options' WHERE `config_group` = 'Page Options';

INSERT INTO `site_config` (`id`, `config_key`, `config_value`, `config_description`, `availableValues`, `config_type`, `config_group`) VALUES (NULL, 'enable_user_registration', 'yes', 'Whether to enable user registration on the site.', '["yes","no"]', 'select', 'Site Options');

UPDATE `site_config` SET `config_description` = "Server selection method. How to select the file server to use. If using 'until full', you'll also need to set the file server priority on each.",
`availableValues` = '["Least Used Space","Specific Server","Until Full"]' WHERE `site_config`.`config_key` = 'c_file_server_selection_method';
ALTER TABLE `file_server` ADD `priority` INT( 11 ) NOT NULL DEFAULT '0';
ALTER TABLE `file_server` ADD `maximumStorageBytes` BIGINT( 20 ) NOT NULL DEFAULT '0' AFTER `scriptPath`;
ALTER TABLE `file_server` ADD `totalSpaceUsed` FLOAT( 18, 0 ) NOT NULL DEFAULT '0' AFTER `scriptPath`;

ALTER TABLE `file` CHANGE `fileType` `fileType` VARCHAR( 150 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
UPDATE file SET fileType = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' WHERE extension = 'xlsx';
UPDATE file SET fileType = 'application/vnd.openxmlformats-officedocument.spreadsheetml.template' WHERE extension = 'xltx';
UPDATE file SET fileType = 'application/vnd.openxmlformats-officedocument.presentationml.template' WHERE extension = 'potx';
UPDATE file SET fileType = 'application/vnd.openxmlformats-officedocument.presentationml.slideshow' WHERE extension = 'ppsx';
UPDATE file SET fileType = 'application/vnd.openxmlformats-officedocument.presentationml.presentation' WHERE extension = 'pptx';
UPDATE file SET fileType = 'application/vnd.openxmlformats-officedocument.presentationml.slide' WHERE extension = 'sldx';
UPDATE file SET fileType = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' WHERE extension = 'docx';
UPDATE file SET fileType = 'application/vnd.openxmlformats-officedocument.wordprocessingml.template' WHERE extension = 'dotx';
UPDATE file SET fileType = 'application/vnd.ms-excel.addin.macroEnabled.12' WHERE extension = 'xlam';
UPDATE file SET fileType = 'application/vnd.ms-excel.sheet.binary.macroEnabled.12' WHERE extension = 'xlsb';

UPDATE `site_config` SET `config_group` = 'Premium User Settings' WHERE `config_key` = 'premium_user_max_remote_urls';

ALTER TABLE `file` CHANGE `adminNotes` `adminNotes` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL;
ALTER TABLE `users` CHANGE `lastloginip` `lastloginip` VARCHAR( 32 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL;




ALTER TABLE `file` CHANGE `fileType` `fileType` VARCHAR( 150 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
UPDATE file SET fileType = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' WHERE extension = 'xlsx';
UPDATE file SET fileType = 'application/vnd.openxmlformats-officedocument.spreadsheetml.template' WHERE extension = 'xltx';
UPDATE file SET fileType = 'application/vnd.openxmlformats-officedocument.presentationml.template' WHERE extension = 'potx';
UPDATE file SET fileType = 'application/vnd.openxmlformats-officedocument.presentationml.slideshow' WHERE extension = 'ppsx';
UPDATE file SET fileType = 'application/vnd.openxmlformats-officedocument.presentationml.presentation' WHERE extension = 'pptx';
UPDATE file SET fileType = 'application/vnd.openxmlformats-officedocument.presentationml.slide' WHERE extension = 'sldx';
UPDATE file SET fileType = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' WHERE extension = 'docx';
UPDATE file SET fileType = 'application/vnd.openxmlformats-officedocument.wordprocessingml.template' WHERE extension = 'dotx';
UPDATE file SET fileType = 'application/vnd.ms-excel.addin.macroEnabled.12' WHERE extension = 'xlam';
UPDATE file SET fileType = 'application/vnd.ms-excel.sheet.binary.macroEnabled.12' WHERE extension = 'xlsb';

CREATE TABLE IF NOT EXISTS `download_token` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `token` varchar(64) COLLATE utf8_bin NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `ip_address` varchar(15) COLLATE utf8_bin NOT NULL,
  `file_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `expiry` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

UPDATE `site_config` SET `config_group` = 'Premium User Settings' WHERE `config_key` = 'premium_user_max_remote_urls';

ALTER TABLE `file` CHANGE `adminNotes` `adminNotes` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL;
ALTER TABLE `users` CHANGE `lastloginip` `lastloginip` VARCHAR( 32 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL;

ALTER TABLE `file_server` ADD `routeViaMainSite` INT( 1 ) NOT NULL DEFAULT '0';

INSERT INTO `site_config` (`id`, `config_key`, `config_value`, `config_description`, `availableValues`, `config_type`, `config_group`) VALUES (NULL, 'file_manager_default_view', 'icon', 'The default view for the file manager.', '["icon", "list"]', 'select', 'File Manager');
INSERT INTO site_config (SELECT null, 'site_contact_form_email', config_value, 'The email address all contact form queries will be sent', '', 'string', 'Contact Form' FROM site_config WHERE config_key = 'site_admin_email');
INSERT INTO `site_config` (`id`, `config_key`, `config_value`, `config_description`, `availableValues`, `config_type`, `config_group`) VALUES (NULL, 'contact_form_show_captcha', 'yes', 'Show the captcha on the contact form.', '["yes","no"]', 'select', 'Contact Form');
INSERT INTO `site_config` (`id`, `config_key`, `config_value`, `config_description`, `availableValues`, `config_type`, `config_group`) VALUES (NULL, 'performance_js_file_minify', 'no', 'Whether to automatically group and minify js files. ''yes'' increases page load times. Use ''no'' if you have any issues or in dev. The ''cache'' folder must be writable.', '["yes","no"]', 'select', 'Site Options');

ALTER TABLE `language_content` CHANGE `languageKeyId` `languageKeyId` INT( 11 ) NOT NULL;
ALTER TABLE `stats` DROP INDEX `page_title`;
ALTER TABLE `stats` DROP INDEX `dt`;
ALTER TABLE `stats` CHANGE `page_title` `file_id` INT( 11 ) NOT NULL;
ALTER TABLE `stats` ADD INDEX ( `file_id` );
ALTER TABLE `stats` CHANGE `dt` `download_date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00';
ALTER TABLE `stats` ADD INDEX ( `download_date` );
ALTER TABLE `stats` DROP `img_search`;
ALTER TABLE `stats` CHANGE `country` `country` VARCHAR( 6 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `stats` DROP `url`;
ALTER TABLE `stats` DROP `os_version`;
ALTER TABLE `stats` DROP `browser_version`;

INSERT INTO `site_config` (`id`, `config_key`, `config_value`, `config_description`, `availableValues`, `config_type`, `config_group`) VALUES (NULL, 'non_user_show_upgrade_page', 'yes', 'Show the premium account upgrade page for non logged in users.', '["yes","no"]', 'select', 'Non User Settings');
INSERT INTO `site_config` (`id`, `config_key`, `config_value`, `config_description`, `availableValues`, `config_type`, `config_group`) VALUES (NULL, 'free_user_show_upgrade_page', 'yes', 'Show the premium account upgrade page for logged in free users.', '["yes","no"]', 'select', 'Free User Settings');
INSERT INTO `site_config` (`id`, `config_key`, `config_value`, `config_description`, `availableValues`, `config_type`, `config_group`) VALUES (NULL, 'paid_user_show_upgrade_page', 'yes', 'Show the premium account upgrade page for paid users.', '["yes","no"]', 'select', 'Premium User Settings');

INSERT INTO `site_config` (`id`, `config_key`, `config_value`, `config_description`, `availableValues`, `config_type`, `config_group`) VALUES (NULL, 'non_user_max_download_threads', '1', 'The maximum concurrent downloads a non user can do at once. Set to 0 (zero) for no limit. Note: Ensure the ''downloads_track_current_downloads'' is also set to ''yes'' to enable this.', '', 'integer', 'Non User Settings');
UPDATE `site_config` SET `config_description` = 'The maximum concurrent downloads a free user can do at once. Set to 0 (zero) for no limit. Note: Ensure the ''downloads_track_current_downloads'' is also set to ''yes'' to enable this.' WHERE `site_config`.`config_key` = 'free_user_max_download_threads';

ALTER TABLE `language_key` ADD `foundOnScan` INT( 1 ) NOT NULL DEFAULT '0';
DELETE FROM language_content WHERE languageKeyId IN (SELECT id FROM language_key WHERE languageKey IN ('not_permitted_to_create_urls_on_site','error_with_url','can_not_create_url_on_this_site','date_entered_is_incorrect','custom_short_url_already_exits','problem_creating_short_url','access_restricted_enter_password','redirecting_to','shorturl_filter_disabled','account_home','email_confirm','stats'));
DELETE FROM language_key WHERE languageKey IN ('not_permitted_to_create_urls_on_site','error_with_url','can_not_create_url_on_this_site','date_entered_is_incorrect','custom_short_url_already_exits','problem_creating_short_url','access_restricted_enter_password','redirecting_to','shorturl_filter_disabled','account_home','email_confirm','stats');
DELETE FROM language_content WHERE languageKeyId NOT IN (SELECT id FROM language_key);

CREATE TABLE IF NOT EXISTS `download_page` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `download_page` varchar(100) COLLATE utf8_bin NOT NULL,
  `user_level_id` int(11) NOT NULL,
  `page_order` int(5) NOT NULL DEFAULT '0',
  `additional_javascript_code` text COLLATE utf8_bin NOT NULL,
  `additional_settings` text COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=3;

ALTER TABLE `users` ADD `privateFileStatistics` INT( 1 ) NOT NULL DEFAULT '0';

INSERT INTO `download_page` (SELECT null, '_download_page_compare_timed.inc.php', 0, 1, '', CONCAT('{"download_wait":',config_value,'}') FROM site_config WHERE config_key = 'non_user_redirect_delay_seconds');
INSERT INTO `download_page` (SELECT null, '_download_page_compare_timed.inc.php', 1, 1, '', CONCAT('{"download_wait":',config_value,'}') FROM site_config WHERE config_key = 'free_user_redirect_delay_seconds');
DELETE FROM `site_config` WHERE config_key = 'non_user_redirect_delay_seconds';
DELETE FROM `site_config` WHERE config_key = 'free_user_redirect_delay_seconds';

INSERT INTO `site_config` (`id`, `config_key`, `config_value`, `config_description`, `availableValues`, `config_type`, `config_group`) VALUES (NULL, 'non_user_show_adverts', 'yes', 'Show adverts for non logged in users.', '["yes","no"]', 'select', 'Non User Settings');
INSERT INTO `site_config` (`id`, `config_key`, `config_value`, `config_description`, `availableValues`, `config_type`, `config_group`) VALUES (NULL, 'free_user_show_adverts', 'yes', 'Show adverts for logged in free users.', '["yes","no"]', 'select', 'Free User Settings');
INSERT INTO `site_config` (SELECT null, 'paid_user_show_adverts', IF(config_value = 'hide', 'no', 'yes'), 'Show adverts for paid users.', '["yes","no"]', 'select', 'Premium User Settings' FROM site_config WHERE config_key = 'adverts_show_to_premium');
DELETE FROM `site_config` WHERE config_key = 'adverts_show_to_premium';

INSERT INTO `site_config` (`id`, `config_key`, `config_value`, `config_description`, `availableValues`, `config_type`, `config_group`) VALUES (NULL, 'paid_user_allow_uploads', 'yes', 'Allow paid users to upload.', '["yes","no"]', 'select', 'Premium User Settings');
INSERT INTO `site_config` (`id`, `config_key`, `config_value`, `config_description`, `availableValues`, `config_type`, `config_group`) VALUES (NULL, 'free_user_allow_uploads', 'yes', 'Allow free users to upload.', '["yes","no"]', 'select', 'Free User Settings');
INSERT INTO `site_config` (SELECT null, 'non_user_allow_uploads', IF(config_value = 'no', 'yes', 'no'), 'Allow non logged in users to upload.', '["yes","no"]', 'select', 'Non User Settings' FROM site_config WHERE config_key = 'require_user_account_upload');
DELETE FROM `site_config` WHERE config_key = 'require_user_account_upload';
