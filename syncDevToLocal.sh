#!/bin/bash

# create dump of DB on dev server
echo -e "DUMPING THE DATABASE FROM DEV"
ssh webdev@bobbiensis-dev.vital-it.ch -t "mysqldump bobbiensis_dev > ~/bobbiensis_dev.sql" # the credentials are stored in .my.cnf on the server

# # copy the dump locally
echo -e "COPY THE DUMP FROM DEV TO LOCAL"
scp webdev@bobbiensis-dev.vital-it.ch:/home/webdev/bobbiensis_dev.sql ~/bobbiensis_dev.sql

#restore DB
echo -e "IMPORTING THE DATABASE INTO LOCAL ===> TODO"
# ddev import-db --src=~/bobbiensis_dev.sql --target-db=db
mysql -h 0.0.0.0 -u root -pbobbiensis bobbiensis <~/bobbiensis_dev.sql

# rsync files from remote to local
echo -e "rsync content files from DEV to LOCAL"
rsync -rtvPhix --stats webdev@bobbiensis-dev.vital-it.ch:/var/vhosts/vital-it.ch/bobbiensis-dev/htdocs/storage/app/public/ storage/app/public --delete
