# RssReader

## Installation instructions:

To set up RssReader:

1. Download and extract [RssReader](https://github.com/robcaw/RssReader/archive/master.zip)
2. Install [Composer](https://getcomposer.org/)
3. Run `php composer.phar update` to install dependencies
4. Create a database user and a database
5. Add tables to database using sql below
6. Copy `config.default.json` to `config.json`, and update it's database configuration options
7. Generate a new crypto secret string in config.json
8. Set your web server to point to rss/web/index.php

## SQL


```sql
SET NAMES utf8;
SET foreign_key_checks = 0;
SET time_zone = '+00:00';
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `feeds`;
CREATE TABLE `feeds` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `feeds_ibfk_4` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hash` varchar(13) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `hash` (`hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
```

## Licence
[Open source MIT licence](http://opensource.org/licenses/MIT)