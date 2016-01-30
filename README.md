#Barcode-Assistant
Barcode Assistant is a program written in Java, PHP, SQL, and Javascript to help out librarians with students checking in and out out in libraries. Its goal is to keep the flow of students as fast as possible as it aims towards the efficiency of students. No more waiting in line to write the name and time on a piece of paper!

In order to use barcode assistant, you must already have a MySQL server, HTTPS server capable of PHP, a remote computer with JRE for the scanner utility (The terminal students will sign in and out of), and any browser with JavaScript for viewing student data. 

To use the Java utility, simply make the changes your school needs in the Keyboard class (In order to adjust how many characters are allowed to be scanned at a time, for example, 7 digit student IDs). Then, point the Java client to your server in the Constants class. Finally, compile and create an executable Jar file and leave the application focused and running on a terminal with no keyboard access. Any standard barcode scanner should work.

To use the PHP server/mySQL utilities you will need 4 databases set up. You can set them up by running these queries on your server:

##### Basic LOG table:
```SQL
CREATE TABLE IF NOT EXISTS `LOG` (
  `ID` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Unique ID of the entry in the database',
  `ROOM` text NOT NULL COMMENT 'Room student checked in or out of',
  `CHECKIN` tinyint(1) NOT NULL COMMENT 'True if the student is checking in',
  `STUDENT_ID` text NOT NULL COMMENT 'Actual ID of student',
  `STUDENT_NAME` text NOT NULL COMMENT 'Full name of student',
  `STUDENT_GRADE` int(2) NOT NULL COMMENT 'Grade of the student. ',
  `TIME` datetime NOT NULL COMMENT 'Date and time student checked in or out',
  `PERIOD` text NOT NULL COMMENT 'Period the student checked in/out',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Table used to store all logins and logouts of all rooms.' AUTO_INCREMENT=1 ;
```

##### Basic LOG_INSIDE table:
```SQL
CREATE TABLE IF NOT EXISTS `LOG_INSIDE` (
  `ID` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Index',
  `ROOM` text NOT NULL COMMENT 'Room this is a part of.',
  `STUDENT_ID` text NOT NULL COMMENT 'Actual STUDENT id of this data',
  `STUDENT_NAME` text NOT NULL COMMENT 'Name of student',
  `STUDENT_GRADE` int(11) NOT NULL COMMENT 'Grade of student',
  `TIME` datetime NOT NULL COMMENT 'Time they signed in.',
  `PERIOD` text NOT NULL COMMENT 'Period they signed in.',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Contains temporary data of the students that are inside the room.' AUTO_INCREMENT=1 ;
```

##### Basic STUDENT$ table:
```SQL
CREATE TABLE IF NOT EXISTS `STUDENT$` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `STUDENT_GRADE` text NOT NULL,
  `STUDENT_NAME` text NOT NULL,
  `STUDENT_ID` text NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Real data for student names etc.' AUTO_INCREMENT=1 ;
```

##### Basic USERS table:
```SQL
CREATE TABLE IF NOT EXISTS `USERS` (
  `ID` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Unique ID of the entry in the database',
  `USERNAME` text NOT NULL COMMENT 'Username of rooms',
  `PASSWORD` text NOT NULL COMMENT 'Password of that room. Will be stored as a hash on production.',
  `SALT` text NOT NULL COMMENT 'The password''s salt',
  `ISADMIN` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'true if this user is an admin,',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
```

When that is done, simply copy the web-server files to the root of your server and the root files to somewhere outside of your web folder You might need to change the times in the getPeriod function inside db.php for the school's bell schedual. 

### Notes: 
* If you would like to host it yourself, the JS Crypto library is hosted here: https://code.google.com/p/crypto-js/
* This program (sadly) uses jquery for the post request. 
* In order to create new accounts on the fly, an administrator account can be created by changing the "IS_ADMIN" value in the SQL database. These users can (currently) only create other standard users to prevent someone from creating many users on a server.
* For the most part, the log viewers are dynamic and should be viewable even on mobile.
* Credits can be found in the source code where necessary. 
* You might need to re-program the PHP code to create the first user accoutnt. 
* This project was for a computer security class. 
* This project is actually being used at my school :thumbsup:
