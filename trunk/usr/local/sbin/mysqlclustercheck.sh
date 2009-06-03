#!/bin/sh

MYSQLCMD1=`/usr/bin/mysql -u root -pvoiper -e 'SHOW SLAVE STATUS\G' | grep -i 'Slave_IO_Running:' | cut -d: -f2 | awk '{ print $1}'`
MYSQLCMD2=`/usr/bin/mysql -u root -pvoiper -e 'SHOW SLAVE STATUS\G' | grep -i 'Slave_SQL_Running:' | cut -d: -f2 | awk '{ print $1}'`

if [ "$MYSQLCMD1" == "Yes" ] && [ "$MYSQLCMD2" == "Yes" ]

then

	echo "yes"

	exit 0

	else

		echo "no"

		exit 1

fi


