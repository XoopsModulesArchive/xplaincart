CREATE TABLE `plain_cart` (
    `ct_id`         INT(10) UNSIGNED      NOT NULL AUTO_INCREMENT,
    `pd_id`         INT(10) UNSIGNED      NOT NULL DEFAULT '0',
    `ct_qty`        MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '1',
    `ct_session_id` CHAR(32)              NOT NULL DEFAULT '',
    `ct_date`       DATETIME              NOT NULL DEFAULT '0000-00-00 00:00:00',
    PRIMARY KEY (`ct_id`),
    KEY `pd_id` (`pd_id`),
    KEY `ct_session_id` (`ct_session_id`)
)
    ENGINE = ISAM
    AUTO_INCREMENT = 58;


-- --------------------------------------------------------

-- 
-- Table structure for table `plain_category`
-- 

CREATE TABLE `plain_category` (
    `cat_id`          INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `cat_parent_id`   INT(11)          NOT NULL DEFAULT '0',
    `cat_name`        VARCHAR(50)      NOT NULL DEFAULT '',
    `cat_title`       VARCHAR(255)     NOT NULL DEFAULT '',
    `cat_description` TEXT             NOT NULL DEFAULT '',
    `cat_image`       VARCHAR(255)     NOT NULL DEFAULT '',
    PRIMARY KEY (`cat_id`),
    KEY `cat_parent_id` (`cat_parent_id`),
    KEY `cat_name` (`cat_name`)
)
    ENGINE = ISAM
    AUTO_INCREMENT = 18;

-- 
-- Dumping data for table `plain_category`
-- 

INSERT INTO `plain_category` (`cat_id`, `cat_parent_id`, `cat_name`, `cat_description`, `cat_image`)
VALUES (17, 13, 'Hunter X Hunter', 'Story about hunter and combat', '');
INSERT INTO `plain_category` (`cat_id`, `cat_parent_id`, `cat_name`, `cat_description`, `cat_image`)
VALUES (12, 0, 'Cars', 'Expensive and luxurious cars', 'dce08605333d805106217aaab7f93b95.jpg');
INSERT INTO `plain_category` (`cat_id`, `cat_parent_id`, `cat_name`, `cat_description`, `cat_image`)
VALUES (13, 0, 'Manga', 'It''s all about manga, yay....', '2a5d7eb60c1625144b3bd785bf70342c.jpg');
INSERT INTO `plain_category` (`cat_id`, `cat_parent_id`, `cat_name`, `cat_description`, `cat_image`)
VALUES (14, 12, 'Volvo', 'Swedish luxury car', '');
INSERT INTO `plain_category` (`cat_id`, `cat_parent_id`, `cat_name`, `cat_description`, `cat_image`)
VALUES (15, 12, 'Mercedes-Benz', 'Expensive but real good', '');
INSERT INTO `plain_category` (`cat_id`, `cat_parent_id`, `cat_name`, `cat_description`, `cat_image`)
VALUES (16, 13, 'Naruto', 'This is the story of Naruto and all his gang', '');

-- --------------------------------------------------------

-- 
-- Table structure for table `plain_currency`
-- 

CREATE TABLE `plain_currency` (
    `cy_id`     INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `cy_code`   CHAR(3)          NOT NULL DEFAULT '',
    `cy_symbol` VARCHAR(8)       NOT NULL DEFAULT '',
    PRIMARY KEY (`cy_id`)
)
    ENGINE = ISAM
    AUTO_INCREMENT = 5;

-- 
-- Dumping data for table `plain_currency`
-- 

INSERT INTO `plain_currency` (`cy_id`, `cy_code`, `cy_symbol`)
VALUES (1, 'EUR', '&#8364;');
INSERT INTO `plain_currency` (`cy_id`, `cy_code`, `cy_symbol`)
VALUES (2, 'GBP', '&pound;');
INSERT INTO `plain_currency` (`cy_id`, `cy_code`, `cy_symbol`)
VALUES (3, 'JPY', '&yen;');
INSERT INTO `plain_currency` (`cy_id`, `cy_code`, `cy_symbol`)
VALUES (4, 'USD', '$');
INSERT INTO `plain_currency` (`cy_id`, `cy_code`, `cy_symbol`)
VALUES (5, 'CAD', '$');

-- --------------------------------------------------------

-- 
-- Table structure for table `plain_order`
-- 

CREATE TABLE `plain_order` (
    `od_id`                   INT(10) UNSIGNED                                        NOT NULL AUTO_INCREMENT,
    `od_date`                 DATETIME                                                         DEFAULT NULL,
    `od_last_update`          DATETIME                                                NOT NULL DEFAULT '0000-00-00 00:00:00',
    `od_status`               ENUM ('New', 'Paid', 'Shipped','Completed','Cancelled') NOT NULL DEFAULT 'New',
    `od_memo`                 VARCHAR(255)                                            NOT NULL DEFAULT '',
    `od_shipping_first_name`  VARCHAR(50)                                             NOT NULL DEFAULT '',
    `od_shipping_last_name`   VARCHAR(50)                                             NOT NULL DEFAULT '',
    `od_shipping_address1`    VARCHAR(100)                                            NOT NULL DEFAULT '',
    `od_shipping_address2`    VARCHAR(100)                                            NOT NULL DEFAULT '',
    `od_shipping_phone`       VARCHAR(32)                                             NOT NULL DEFAULT '',
    `od_shipping_city`        VARCHAR(100)                                            NOT NULL DEFAULT '',
    `od_shipping_state`       VARCHAR(32)                                             NOT NULL DEFAULT '',
    `od_shipping_postal_code` VARCHAR(10)                                             NOT NULL DEFAULT '',
    `od_shipping_cost`        DECIMAL(5, 2)                                                    DEFAULT '0.00',
    `od_payment_first_name`   VARCHAR(50)                                             NOT NULL DEFAULT '',
    `od_payment_last_name`    VARCHAR(50)                                             NOT NULL DEFAULT '',
    `od_payment_address1`     VARCHAR(100)                                            NOT NULL DEFAULT '',
    `od_payment_address2`     VARCHAR(100)                                            NOT NULL DEFAULT '',
    `od_payment_phone`        VARCHAR(32)                                             NOT NULL DEFAULT '',
    `od_payment_city`         VARCHAR(100)                                            NOT NULL DEFAULT '',
    `od_payment_state`        VARCHAR(32)                                             NOT NULL DEFAULT '',
    `od_payment_postal_code`  VARCHAR(10)                                             NOT NULL DEFAULT '',
    PRIMARY KEY (`od_id`)
)
    ENGINE = ISAM
    AUTO_INCREMENT = 1001;


-- --------------------------------------------------------

-- 
-- Table structure for table `plain_order_item`
-- 

CREATE TABLE `plain_order_item` (
    `od_id`  INT(10) UNSIGNED NOT NULL DEFAULT '0',
    `pd_id`  INT(10) UNSIGNED NOT NULL DEFAULT '0',
    `od_qty` INT(10) UNSIGNED NOT NULL DEFAULT '0',
    PRIMARY KEY (`od_id`, `pd_id`)
)
    ENGINE = ISAM;


-- --------------------------------------------------------

-- 
-- Table structure for table `plain_product`
-- 

CREATE TABLE `plain_product` (
    `pd_id`          INT(10) UNSIGNED     NOT NULL AUTO_INCREMENT,
    `cat_id`         INT(10) UNSIGNED     NOT NULL DEFAULT '0',
    `pd_name`        VARCHAR(100)         NOT NULL DEFAULT '',
    `pd_title`       VARCHAR(255)         NOT NULL DEFAULT '',
    `pd_description` TEXT                 NOT NULL,
    `pd_price`       DECIMAL(9, 2)        NOT NULL DEFAULT '0.00',
    `pd_qty`         SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0',
    `pd_image`       VARCHAR(200)                  DEFAULT NULL,
    `pd_thumbnail`   VARCHAR(200)                  DEFAULT NULL,
    `pd_date`        DATETIME             NOT NULL DEFAULT '0000-00-00 00:00:00',
    `pd_last_update` DATETIME             NOT NULL DEFAULT '0000-00-00 00:00:00',
    PRIMARY KEY (`pd_id`),
    KEY `cat_id` (`cat_id`),
    KEY `pd_name` (`pd_name`)
)
    ENGINE = ISAM
    AUTO_INCREMENT = 22;


-- --------------------------------------------------------

-- 
-- Table structure for table `plain_shop_config`
-- 

CREATE TABLE `plain_shop_config` (
    `sc_name`          VARCHAR(50)      NOT NULL DEFAULT '',
    `sc_address`       VARCHAR(100)     NOT NULL DEFAULT '',
    `sc_phone`         VARCHAR(30)      NOT NULL DEFAULT '',
    `sc_email`         VARCHAR(100)     NOT NULL DEFAULT '',
    `sc_shipping_cost` DECIMAL(5, 2)    NOT NULL DEFAULT '0.00',
    `sc_currency`      INT(10) UNSIGNED NOT NULL DEFAULT '1',
    `sc_order_email`   ENUM ('y','n')   NOT NULL DEFAULT 'n'
)
    ENGINE = ISAM;

-- 
-- Dumping data for table `plain_shop_config`
-- 

INSERT INTO `plain_shop_config` (`sc_name`, `sc_address`, `sc_phone`, `sc_email`, `sc_shipping_cost`, `sc_currency`, `sc_order_email`)
VALUES ('PlainCart - Just a plain online shop', 'Old warehouse under the bridge,\r\nWater Seven, Grand Line', '777-FRANKY', 'franky@tomsworkers.com', 5.00, 4, 'y');

-- --------------------------------------------------------

-- 
-- Table structure for table `plain_user`
-- 

CREATE TABLE `plain_user` (
    `user_id`         INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_name`       VARCHAR(20)      NOT NULL DEFAULT '',
    `user_password`   VARCHAR(50)      NOT NULL DEFAULT '',
    `user_regdate`    DATETIME         NOT NULL DEFAULT '0000-00-00 00:00:00',
    `user_last_login` DATETIME         NOT NULL DEFAULT '0000-00-00 00:00:00',
    PRIMARY KEY (`user_id`),
    UNIQUE KEY `user_name` (`user_name`)
)
    ENGINE = ISAM
    AUTO_INCREMENT = 4;

-- 
-- Dumping data for table `plain_user`
-- 

INSERT INTO `plain_user` (`user_id`, `user_name`, `user_password`, `user_regdate`, `user_last_login`)
VALUES (1, 'admin', '*4ACFE3202A5FF5CF467898FC58AAB1D615029441', '2005-02-20 17:35:44', '2005-03-02 21:00:14');
INSERT INTO `plain_user` (`user_id`, `user_name`, `user_password`, `user_regdate`, `user_last_login`)
VALUES (3, 'webmaster', '026cf3fc6e903caf', '2005-03-02 17:52:51', '0000-00-00 00:00:00');
