<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| DATABASE CONNECTIVITY SETTINGS
| -------------------------------------------------------------------
| This file will contain the settings needed to access your database.
|
| For complete instructions please consult the "Database Connection"
| page of the User Guide.
|
| -------------------------------------------------------------------
| EXPLANATION OF VARIABLES
| -------------------------------------------------------------------
|
|	['hostname'] The hostname of your database server.
|	['username'] The username used to connect to the database
|	['password'] The password used to connect to the database
|	['database'] The name of the database you want to connect to
|	['dbdriver'] The database type. ie: mysql.  Currently supported:
				 mysql, mysqli, postgre, odbc, mssql, sqlite, oci8
|	['dbprefix'] You can add an optional prefix, which will be added
|				 to the table name when using the  Active Record class
|	['pconnect'] TRUE/FALSE - Whether to use a persistent connection
|	['db_debug'] TRUE/FALSE - Whether database errors should be displayed.
|	['cache_on'] TRUE/FALSE - Enables/disables query caching
|	['cachedir'] The path to the folder where cache files should be stored
|	['char_set'] The character set used in communicating with the database
|	['dbcollat'] The character collation used in communicating with the database
|
| The $active_group variable lets you choose which connection group to
| make active.  By default there is only one group (the "default" group).
|
| The $active_record variables lets you determine whether or not to load
| the active record class
*/

$active_group = "dev";
$active_record = TRUE;

$db['dev']['hostname'] 	= "localhost";
$db['dev']['port'] 		= 5432;
$db['dev']['username'] 	= "postgres" ;//   wwwssi_root 
$db['dev']['password'] 	= "j1135#weep";
$db['dev']['database'] 	= "wwwssi_endeavor";
$db['dev']['dbdriver'] 	= "postgre";
$db['dev']['dbprefix'] 	= "";
$db['dev']['pconnect'] 	= TRUE;
$db['dev']['db_debug'] 	= TRUE;
$db['dev']['cache_on'] 	= FALSE;
$db['dev']['cachedir'] 	= "";
$db['dev']['char_set'] 	= "utf8";
$db['dev']['dbcollat'] 	= "utf8_general_ci";

$db['test']['hostname'] 	= "localhost";
$db['test']['port'] 		= 5432;
$db['test']['username'] 	= "postgres";
$db['test']['password'] 	= "j1135#weep";
$db['test']['database'] 	= "test";
$db['test']['dbdriver'] 	= "postgre";
$db['test']['dbprefix'] 	= "";
$db['test']['pconnect'] 	= TRUE;
$db['test']['db_debug'] 	= TRUE;
$db['test']['cache_on'] 	= FALSE;
$db['test']['cachedir'] 	= "";
$db['test']['char_set'] 	= "utf8";
$db['test']['dbcollat'] 	= "utf8_general_ci";

$db['stage']['hostname'] 	= "localhost";
$db['stage']['port'] 		= 5432;
$db['stage']['username'] 	= "postgres";
$db['stage']['password'] 	= "j1135#weep";
$db['stage']['database'] 	= "stage";
$db['stage']['dbdriver'] 	= "postgre";
$db['stage']['dbprefix'] 	= "";
$db['stage']['pconnect'] 	= TRUE;
$db['stage']['db_debug'] 	= TRUE;
$db['stage']['cache_on'] 	= FALSE;
$db['stage']['cachedir'] 	= "";
$db['stage']['char_set'] 	= "utf8";
$db['stage']['dbcollat'] 	= "utf8_general_ci";

$db['live']['hostname'] 	= "localhost";
$db['live']['port'] 		= 5432;
$db['live']['username'] 	= "postgres";
$db['live']['password'] 	= "j1135#weep";
$db['live']['database'] 	= "live";
$db['live']['dbdriver'] 	= "postgre";
$db['live']['dbprefix'] 	= "";
$db['live']['pconnect'] 	= TRUE;
$db['live']['db_debug'] 	= TRUE;
$db['live']['cache_on'] 	= FALSE;
$db['live']['cachedir'] 	= "";
$db['live']['char_set'] 	= "utf8";
$db['live']['dbcollat'] 	= "utf8_general_ci";

$db['endeavor']['hostname']     = "208.71.168.52";
$db['endeavor']['port']         = 3306;
$db['endeavor']['username']     = "postgres";
$db['endeavor']['password']     = "j1135#weep";
$db['endeavor']['database']     = "MASTER";
$db['endeavor']['dbdriver']     = "mysql";
$db['endeavor']['dbprefix']     = "";
$db['endeavor']['pconnect']     = TRUE;
$db['endeavor']['db_debug']     = TRUE;
$db['endeavor']['cache_on']     = FALSE;
$db['endeavor']['cachedir']     = "";
$db['endeavor']['char_set']     = "utf8";
$db['endeavor']['dbcollat']     = "utf8_general_ci";




                            
/* End of file database.php */
/* Location: ./system/application/config/database.php */