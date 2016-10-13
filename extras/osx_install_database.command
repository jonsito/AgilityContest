#!/bin/sh
cat <<_EOF | /Applications/XAMPP/bin/mysql -u root
DROP DATABASE IF EXISTS agility;
CREATE DATABASE agility;
USE agility;
SOURCE /Applications/XAMPP/htdocs/AgilityContest-master/extras/agility.sql;
SOURCE /Applications/XAMPP/htdocs/AgilityContest-master/extras/users.sql;
_EOF
