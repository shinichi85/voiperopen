<?php
//Copyright (C) 2004 Coalescent Systems Inc. (info@coalescentsystems.ca)
//Copyright (C) 2005-2008 SpheraIT.
//
//This program is free software; you can redistribute it and/or
//modify it under the terms of the GNU General Public License
//as published by the Free Software Foundation; either version 2
//of the License, or (at your option) any later version.
//
//This program is distributed in the hope that it will be useful,
//but WITHOUT ANY WARRANTY; without even the implied warranty of
//MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//GNU General Public License for more details.

require_once('DB.php'); //PEAR must be installed

$db_engine = $amp_conf["AMPDBENGINE"];

switch ($db_engine)
{
    case "pgsql":
    case "mysql":

# Database Asterisk

        $db_user = $amp_conf["AMPDBUSER"];
        $db_pass = $amp_conf["AMPDBPASS"];
        $db_host = $amp_conf["AMPDBHOST"];
        $db_name = $amp_conf["AMPDBNAME"];

        $datasource = $db_engine.'://'.$db_user.':'.$db_pass.'@'.$db_host.'/'.$db_name;
        $db = DB::connect($datasource);
        
#Database VCTI

        $db_hostcti = $amp_conf["CTIDBHOST"];
        $db_passcti = $amp_conf["CTIDBPASS"];
        $db_usercti = $amp_conf["CTIDBUSER"];
        $db_namecti = $amp_conf["CTIDBNAME"];

        $datasource2 = $db_engine.'://'.$db_usercti.':'.$db_passcti.'@'.$db_hostcti.'/'.$db_namecti;
        $db2 = DB::connect($datasource2);

#Database A2Billing

        $db_usera2b = $amp_conf["A2BDBUSER"];
        $db_passa2b = $amp_conf["A2BDBPASS"];
        $db_hosta2b = $amp_conf["A2BDBHOST"];
        $db_namea2b = $amp_conf["A2BDBNAME"];

        $datasource3 = $db_engine.'://'.$db_usera2b.':'.$db_passa2b.'@'.$db_hosta2b.'/'.$db_namea2b;
        $db3 = DB::connect($datasource3);

        break;

    case "sqlite":
        require_once('DB/sqlite.php');

        if (!isset($amp_conf["AMPDBFILE"]))
            die("You must setup properly AMPDBFILE in /etc/amportal.conf");

        if (isset($amp_conf["AMPDBFILE"]) == "")
            die("AMPDBFILE in /etc/amportal.conf cannot be blank");

        $DSN = array (
            "database" => $amp_conf["AMPDBFILE"],
            "mode" => 0666
        );

        $db = new DB_sqlite();
        $db->connect( $DSN );
        break;

    default:
        die( "Unknown SQL engine: [$db_engine]");
}

if(DB::isError($db)) {
    die($db->getDebugInfo());
}

if(DB::isError($db2)) {
    die($db2->getDebugInfo());
}

/* if(DB::isError($db3)) {
    die($db3->getDebugInfo());
}
*/
