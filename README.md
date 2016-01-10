#Barcode-Assistant
Barcode Assistant is a program written in Java, PHP, SQL, and Javascript to help out librarians with students checking in and out out in libraries. Its goal is to keep the flow of students as fast as possible as it aims towards the efficiency of students. No more waiting in line to write the name and time on a piece of paper!

In order to use barcode assistant, you must already have a MySQL server, HTTPS server capable of PHP, a remote computer with JRE for the scanner utility (The terminal students will sign in and out of), and any browser with JavaScript for viewing student data. 

To use the Java utility, simply make the changes your school needs in the Bell class (In order to adjust class schedual) or the Keyboard class (In order to adjust how many characters are allowed to be scanned at a time, for example, 7 digit student IDs). Then, point the Java client to your server in the Constants class. Finally, compile and create an executable Jar file and leave the application focused and running on a terminal with no keyboard access. Any standard barcode scanner should work.

To use the PHP server/mySQL utilities you will need 4 databases set up. You can set them up by running these queries on your server:

##### TODO: Write SQL queries needed to set these up!

When that is done, simply copy the web-server files to the root of your server and the root files to somewhere outside of your web folder.

### Notes: 
* If you would like to host it yourself, the JS Crypto library is hosted here: https://code.google.com/p/crypto-js/
* This program (sadly) uses jquery for the post request. 
* The Java program should be good at handling exceptions. The Java program must be running in order to log everyone out at the end of the day.
* In order to create new accounts on the fly, an administrator account can be created by changing the "IS_ADMIN" value in the SQL database. These users can (currently) only create other, standard users to prevent someone from creating many users on a server.
* For the most part, the log viewers are dynamic and should be viewable even on mobile.
* Credits can be found in the source code where necessary. 
