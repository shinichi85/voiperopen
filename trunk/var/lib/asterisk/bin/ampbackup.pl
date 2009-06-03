#!/usr/bin/perl -w
# ampbackup.pl Copyright (C) 2005 VerCom Systems, Inc. & Ron Hartmann (rhartmann@vercomsystems.com)
# Asterisk Management Portal Copyright (C) 2004 Coalescent Systems Inc. (info@coalescentsystems.ca)
# Voiper Management Portal Copyright (C) 2004-2008 SpheraIT (info@voiper.it)
#
# this program is in charge of looking into the database to pick up the backup sets name and options
# Then it creates the tar files and places them in the /var/lib/asterisk/backups folder
#
# The program if run from asterisk users crontab it is run as ampbackup.pl <Backup Job Record Number in Mysql>
# OR
# The program is called from the backup.php script and implemented immediately as such:
# ampbackup.pl <Backup_Name> <Backup_Voicemail_(yes/no)> <Backup_Recordings_(yes/no)> <Backup_Configuration_files(yes/no)>
# <Backup_CDR_(yes/no)> <Backup_FOP_(yes/no)
#
# example ampbackup.pl "My_Nightly_Backup" yes yes no no yes
#
# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU General Public License
# as published by the Free Software Foundation; either version 2
# of the License, or (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.

use DBI;
use Sys::Hostname;

open(FILE, "/etc/amportal.conf") || die "Failed to open amportal.conf\n";
while (<FILE>) {
    chomp;                  # no newline
    s/#.*//;                # no comments
    s/^\s+//;               # no leading white
    s/\s+$//;               # no trailing white
    next unless length;     # anything left?
    my ($var, $value) = split(/\s*=\s*/, $_, 2);
    $User_Preferences{$var} = $value;
}
close(FILE);

open(FILE, "/etc/ver_voiper") || die "Failed to open ver_voiper\n";
while (<FILE>) {
    chomp;                  # no newline
    s/#.*//;                # no comments
    s/^\s+//;               # no leading white
    s/\s+$//;               # no trailing white
    next unless length;     # anything left?
    my ($var, $value) = split(/\s*=\s*/, $_, 2);
    $voiperversion{$var} = $value;
}
close(FILE);

open(VOIPERPN, "/etc/voiper_pn") || die "Failed to open voiper_pn\n";
while (<VOIPERPN>) {
    $voiperpn = "$_";
}
close(VOIPERPN);

if (scalar @ARGV < 1) {

         print "Usage: $0 Backup-set-ID \n";
         print "  This script Reads the backup options from the BackupTable.\n";
         print "    then runs the backup picking up the items that were turned\n";
         print "    OR\n";
         print "    \n";
         print "    The program is called from the backup.php script and implemented immediately as such:\n";
         print "    ampbackup.pl <Backup_Name> <Backup_Voicemail_(yes/no)> <Backup_Recordings_(yes/no)> <Backup_Configuration_files(yes/no)>\n";
         print "    <Backup_CDR_(yes/no)> <Backup_FOP_(yes/no)\n";
         print "    \n";
         print "    example ampbackup.pl \"My_Nightly_Backup\" yes yes no no yes\n";
         exit(1);
}

################### BEGIN OF CONFIGURATION ####################
$table_name = "Backup";
$table_name_bkftp = "backupftp";
$ftpfile = "/tmp/voiper-backup.ftp";
$ftpfile_debug = "/tmp/voiper-backup-debug.ftp";
$host = hostname;
$ftpbackup = "no";
$voiperver = $voiperversion{"version"};

################################################################
$vmphostname = $User_Preferences{"AMPDBHOST"};
$vmpdatabase = $User_Preferences{"AMPDBNAME"};
$vmpusername = $User_Preferences{"AMPDBUSER"};
$vmppassword = $User_Preferences{"AMPDBPASS"};

################################################################
$vctihostname = $User_Preferences{"CTIDBHOST"};
$vctidatabase = $User_Preferences{"CTIDBNAME"};
# $vctiusername = $User_Preferences{"CTIDBUSER"};
# $vctipassword = $User_Preferences{"CTIDBPASS"};

################################################################
$pbhostname = $User_Preferences{"PHONEBOOKDBHOST"};
$pbpdatabase = $User_Preferences{"PHONEBOOKDBNAME"};
# $pbpusername = $User_Preferences{"PHONEBOOKDBUSER"};
# $pbppassword = $User_Preferences{"PHONEBOOKDBPASS"};

################################################################
$dbrootusername = $User_Preferences{"ROOTDBUSER"};
$dbrootpassword = $User_Preferences{"ROOTDBPASS"};

################################################################
$webroot = $User_Preferences{"AMPWEBROOT"};
$asteriskdrbd = "/mnt/drbd/asterisk";

################### END OF CONFIGURATION #######################

my $Stamp="voiper";
my $now = localtime time;
my ($sec,$min,$hour,$mday,$mon,$year, $wday,$yday,$isdst) = localtime time;
$year += 1900;
$mon +=1;

if (scalar @ARGV > 1) {

	    $Backup_Name = $ARGV[0];
	    $Backup_Voicemail = $ARGV[1];
	    $Backup_Recordings = $ARGV[2];
	    $Backup_Configurations = $ARGV[3];
	    $Backup_CDR = $ARGV[4];
	    $Backup_FOP = $ARGV[5];

    } else {

	        $dbh = DBI->connect("dbi:mysql:dbname=$vmpdatabase;host=$vmphostname", "$vmpusername", "$vmppassword");
        	$statement = "SELECT Name, Voicemail, Recordings, Configurations, CDR, FOP from $table_name where ID= $ARGV[0]";

        	$result = $dbh->selectall_arrayref($statement);
	        unless ($result) {
	        # check for errors after every single database call
	            print "dbh->selectall_arrayref($statement) failed!\n";
	            print "DBI::err=[$DBI::err]\n";
	            print "DBI::errstr=[$DBI::errstr]\n";
	        }

	        @resultSet = @{$result};
	        if ( $#resultSet == -1 ) {
	            print "No Backup Schedules defined in $table_name\n";
	            exit;
	        }

	        foreach my $row ( @{ $result } ) {
		        $Backup_Name = @{ $row }[0];
		        $Backup_Voicemail = @{ $row }[1];
		        $Backup_Recordings = @{ $row }[2];
		        $Backup_Configurations = @{ $row }[3];
		        $Backup_CDR = @{ $row }[4];
		        $Backup_FOP = @{ $row }[5];
		        #print "$Backup_Name $Backup_Voicemail $Backup_Recordings $Backup_Configurations $Backup_CDR $Backup_FOP\n";
	        }

        	$statement_bkftp = "SELECT * from $table_name_bkftp";
        	$result_bkftp = $dbh->selectall_arrayref($statement_bkftp);

	        foreach my $row ( @{ $result_bkftp } ) {
		        $ftpbackup = @{ $row }[1];
		        $ftpuser = @{ $row }[2];
		        $ftppassword = @{ $row }[3];
		        $ftpsubdir = @{ $row }[4];
		        $ftpserver = @{ $row }[5];
		        $ftpemail = @{ $row }[6];
		        #print "$ftpbackup $ftpuser $ftppassword $ftpsubdir $ftpserver $ftpemail\n";
	        }

}

	#print "$Backup_Name $Backup_Voicemail $Backup_Recordings $Backup_Configurations $Backup_CDR $Backup_FOP\n";
	system ("/bin/rm -rf /tmp/ampbackups.$Stamp > /dev/null 2> /dev/null");
	system ("/bin/mkdir /tmp/ampbackups.$Stamp > /dev/null 2> /dev/null");

    #Generate a hidden file with Touch for voiper PN and Voiper Version.
	system ("touch /tmp/ampbackups.$Stamp/.voiperpn-$voiperpn");
	system ("touch /tmp/ampbackups.$Stamp/.voiperver-$voiperver");

	if ( $Backup_Voicemail eq "yes" ){
		system ("/bin/tar -Pcz -f /tmp/ampbackups.$Stamp/voicemail.tar.gz /var/spool/asterisk/voicemail > /dev/null 2> /dev/null");
	}
	if ( $Backup_Recordings eq "yes" ){
		system ("/bin/tar -Pcz -f /tmp/ampbackups.$Stamp/recordings.tar.gz /var/lib/asterisk/sounds/custom /var/lib/asterisk/mohmp3 > /dev/null 2> /dev/null");
	}
	if ( $Backup_Configurations eq "yes" ){
        if (( $voiperpn == "0103" ) || ( $voiperpn == "0202" )) {
	        $bak_files = "/etc/asterisk /etc/amportal.conf /etc/ver_asterisk /etc/ver_voiper /etc/ver_zaptel /etc/ver_vcti $asteriskdrbd/etc/zaptel.conf /etc/zaptelhfc.conf /etc/httpd/conf/httpd.conf";
    	    $bak_files .= " ";
	        $bak_files .= "$asteriskdrbd/etc/misdn-init.conf /etc/ssh/sshd_config /etc/voiper_serial /etc/voiper_pn /etc/hosts /etc/resolv.conf /etc/snmp/snmpd.conf";
    	    $bak_files .= " ";
	        $bak_files .= "/etc/mail /etc/log.d/conf/logwatch.conf /etc/ddclient/ddclient.conf /etc/cron.daily/00-logwatch /etc/dhcpd.conf";
    	    $bak_files .= " ";
	        $bak_files .= "/etc/ntp.conf /etc/ntp/step-tickers /etc/vsftpd/vsftpd.conf /etc/vsftpd.user_list /etc/vsftpd.ftpusers /tftpboot";
    	    $bak_files .= " ";
	        $bak_files .= "/var/lib/asterisk/astdb /var/lib/asterisk/keys /var/lib/asterisk/licenses /var/argus/config /var/argus/users /root/.forward";
    	    $bak_files .= " ";
	        $bak_files .= "/usr/sbin/voiper";
        
            } else {
        
        	    $bak_files = "/etc/asterisk /etc/amportal.conf /etc/ver_asterisk /etc/ver_voiper /etc/ver_zaptel /etc/ver_vcti /etc/zaptel.conf /etc/zaptelhfc.conf /etc/httpd/conf/httpd.conf";
    	        $bak_files .= " ";
	            $bak_files .= "/etc/misdn-init.conf /etc/ssh/sshd_config /etc/voiper_serial /etc/voiper_pn /etc/hosts /etc/resolv.conf /etc/snmp/snmpd.conf";
    	        $bak_files .= " ";
	            $bak_files .= "/etc/mail /etc/log.d/conf/logwatch.conf /etc/ddclient/ddclient.conf /etc/cron.daily/00-logwatch /etc/dhcpd.conf";
    	        $bak_files .= " ";
	            $bak_files .= "/etc/ntp.conf /etc/ntp/step-tickers /etc/vsftpd/vsftpd.conf /etc/vsftpd.user_list /etc/vsftpd.ftpusers /tftpboot";
    	        $bak_files .= " ";
	            $bak_files .= "/var/lib/asterisk/astdb /var/lib/asterisk/keys /var/lib/asterisk/licenses /var/argus/config /var/argus/users /root/.forward";
    	        $bak_files .= " ";
	            $bak_files .= "/usr/sbin/voiper"; 
	    
	    }
		system ("/bin/tar -Pczh -f /tmp/ampbackups.$Stamp/configurations.tar.gz $bak_files > /dev/null 2> /dev/null");
		system ("mysqldump --add-drop-table -u $dbrootusername -h $vmphostname -p$dbrootpassword --database $vmpdatabase > /tmp/ampbackups.$Stamp/asterisk.sql");
		system ("mysqldump --add-drop-table -u $dbrootusername -h $pbhostname -p$dbrootpassword --database $pbpdatabase > /tmp/ampbackups.$Stamp/phonebook.sql");
		system ("mysqldump --add-drop-table -u $dbrootusername -h $vctihostname -p$dbrootpassword --database $vctidatabase > /tmp/ampbackups.$Stamp/vcti.sql");
	}
	if ( $Backup_CDR eq "yes" ){
		system ("/bin/tar -Pcz -f /tmp/ampbackups.$Stamp/cdr.tar.gz $webroot/private/cdr > /dev/null 2> /dev/null");
		system ("mysqldump --add-drop-table -u $dbrootusername -h $vmphostname -p$dbrootpassword --database asteriskcdrdb > /tmp/ampbackups.$Stamp/asteriskcdr.sql");
	}
	if ( $Backup_FOP eq "yes" ){
		system ("/bin/tar -Pcz -f /tmp/ampbackups.$Stamp/fop.tar.gz $webroot/public/panel > /dev/null 2> /dev/null");
	}
	    system ("/bin/mkdir -p '/var/lib/asterisk/backups/$Backup_Name' > /dev/null  2>&1");
	    system ("/bin/tar -Pcz -f '/var/lib/asterisk/backups/$Backup_Name/$Stamp.tar.gz' /tmp/ampbackups.$Stamp > /dev/null 2> /dev/null");
	    system ("/bin/rm -rf /tmp/ampbackups.$Stamp > /dev/null 2> /dev/null");

# Voiper SX

    if ( $voiperpn == "0101" ){
    system ("/bin/rm -f /mnt/flashcard/backups/Voiper-BAK/voiper.tar.gz > /dev/null 2> /dev/null");
    system ("/bin/cp -bfR --reply=yes /var/lib/asterisk/backups /mnt/flashcard > /dev/null 2> /dev/null");
    }

# FTP Sucessfull Backup's to FTPSERVER
#
# leave $ftpbackup which gets overwritten next time but can be checked to see if there were errors.
# IMPORTANT - if testing as root, delete files since backup runs as asterisk and will fail here since
#             root leave the file around and asterisk can't overwrite it.
#	      Note - the hardcoded full backup that cron does will overwrite each day at destination.

if ( $ftpbackup eq "yes" ) {

    open(FILE, ">$ftpfile") || die "Failed to open $ftpfile\n";
    printf FILE "user $ftpuser $ftppassword\n";
    printf FILE "binary\n";
    if ( $ftpsubdir ne "" ) {
        printf FILE "cd $ftpsubdir\n";
    }
    printf FILE "lcd /var/lib/asterisk/backups/$Backup_Name/\n";
    printf FILE "put $Stamp.tar.gz\n";
    printf FILE "bye\n";
    close(FILE);
		
    system ("ftp -idvn $ftpserver < $ftpfile > $ftpfile_debug");
	system ("cat $ftpfile_debug | grep -i '226' > /dev/null 2> /dev/null");

# Invia Email di Conferma quando il Trasferimento via ftp e' terminato.

    if ($? == 0) {

        if ($ftpemail) {
    
            system ('mime-construct --to '.$ftpemail.' --subject '.$host.'" Ftp System Backup" --string "The Backup file "'.$Stamp.'".tar.gz was successfully uploaded to this ftp address: "'.$ftpserver);
        }

    } else {

            if ($ftpemail) {
    
                system ('mime-construct --to '.$ftpemail.' --subject '.$host.'" Ftp System Backup" --string "The Backup file "'.$Stamp.'".tar.gz has not been uploaded to this ftp address: "'.$ftpserver.'", please contact the Administrator."');

            }
    }

}

exit 0;
