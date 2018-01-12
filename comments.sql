DROP TABLE IF EXISTS comments;

CREATE TABLE comments (
  id int(10) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  title varchar(32) NOT NULL,
  comment varchar(200) NOT NULL,
  image varchar(100),
  time_sent timestamp DEFAULT CURRENT_TIMESTAMP,
  password varchar(4)
) ENGINE=INNODB CHARSET=utf8;
