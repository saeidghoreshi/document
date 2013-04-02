<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/* for login captcha. # of failed attemps allowed before captcha required */


// function required for security constants
function getSpectrumKey($SSI_ENC_KEY)
{
	$ua = "SPECTRUMKEY-".time();
	srand((double) microtime() * 1000000); 
	$td = mcrypt_module_open('tripledes', '', 'ecb', '');
	$keysize = mcrypt_enc_get_key_size($td);
	$ivsize = mcrypt_enc_get_iv_size($td);
	$iv = mcrypt_create_iv($ivsize,MCRYPT_DEV_URANDOM);
	$key = substr(md5($SSI_ENC_KEY),0,$keysize);
	mcrypt_generic_init($td, $key, $iv);
	$ua = mcrypt_generic($td,$ua);
	mcrypt_generic_deinit($td);
	mcrypt_module_close($td);
	$ua = base64_encode("$ua||$iv");
	$ua = bin2hex($ua);	
	return $ua;
}


/*
|--------------------------------------------------------------------------
| System State
|--------------------------------------------------------------------------
|
| This statement reads the domain name and sets the system state as DEV,
| STAGE, LIVE, or FALLBACK. FALLBACK codes should be rectified immediately
| once found to mitigate unexpected results by adding the domain into the
| case group below.
|
*/
switch($_SERVER['HTTP_HOST'])
{
	case "brad.endeavor.servilliansolutionsinc.com":
	case "sam.endeavor.servilliansolutionsinc.com":
	case "ryan.endeavor.servilliansolutionsinc.com":
	case "test.endeavor.servilliansolutionsinc.com":
		define('SYS_STATE','DEV');
		break;

	case "stage.spectrum.servilliansolutionsinc.com":
		define('SYS_STATE','STAGE');
		break;

	case "live.spectrum.servilliansolutionsinc.com":
	case "cc.servilliansolutionsinc.com":
	case "io.servilliansolutionsinc.com":
		define('SYS_STATE','LIVE');
		break;

	default:
		define('SYS_STATE','FALLBACK');
		break;
		
}

/*
|--------------------------------------------------------------------------
| Spectrum
|--------------------------------------------------------------------------
|
| Spectrum Constants
|
*/
define('VERSION', 				file_get_contents('version.txt'));
define('PRODUCT_NAME',			'Spectrum | Online Sports Management');
define('DEFAULT_LEAGUE_DOMAIN',	'playerspectrum.com');
define('SPECTRUM_USER_AGENT', 	'Spectrum/2.1');  
define('ORG_TYPE_SYSTEM',		1);
define('ORG_TYPE_ASSOC',		2);
define('ORG_TYPE_ASSOCIATION',	2);
define('ORG_TYPE_LEAGUE',		3);
define('ORG_TYPE_TEAM',			6);

/*
|--------------------------------------------------------------------------
| Orbital Checkout :: Chase Paymentech
|--------------------------------------------------------------------------
|
| Required for Credit Card Payments
|
*/
define('CHASE_TERMINALID',				'001');
define('CHASE_BIN',						'000002');
define('CHASE_INDUSTRYTYPE',			'EC');

switch(SYS_STATE)
{
	case 'DEV':
		define('CHASE_MERCHANDID'       ,'700000203090');
	    define('CHASE_URL'              ,'https://orbitalvar1.paymentech.net');
	    define('SYS_GATEWAY_URL'        ,'http://sam.endeavor.servilliansolutionsinc.com');
	    break;
	case 'STAGE':
		define('CHASE_MERCHANDID'       ,'700000203090');
	    define('CHASE_URL'              ,'https://orbitalvar1.paymentech.net');
	    define('SYS_GATEWAY_URL'        ,'http://stage.spectrum.servilliansolutionsinc.com');
	    break;
	default:
	//case 'STAGE':
	case 'LIVE':
		define('CHASE_MERCHANDID',		'030000054957');
	    define('CHASE_URL',				'https://orbital1.paymentech.net');
	    define('SYS_GATEWAY_URL',		'https://cc.servilliansolutionsinc.com');
		break;
}

/*
|--------------------------------------------------------------------------
| Hosted Checkout :: Chase Paymentech
|--------------------------------------------------------------------------
|
| Required for Interac Online Payments
|
*/
switch(SYS_STATE)
{
	case 'DEV':
	    define('INTERAC_LOGINID'        ,'HCO-SERVI-426');
	    define('INTERAC_TRANSACTIONKEY' ,'KuZ3QgH~Waj0jXoR21h7');
	    define('INTERAC_RESPONSEKEY'    ,'oiC6vwP7UK2hOvfTaYHn');
	    define('INTERAC_URL'            ,'https://rpm-demo.e-xact.com/payment');
	    break;
	
	default:
	case 'STAGE':
	case 'LIVE':
	    define('INTERAC_LOGINID',		'WSP-SERVI-of&AMgAGGQ');
	    define('INTERAC_TRANSACTIONKEY','R~sIdx2yWJByOrtiiubP');
	    define('INTERAC_RESPONSEKEY',	'e5l2f38u3X33s~HDbZYf');
	    define('INTERAC_URL',			'https://rpm-demo.e-xact.com/payment');
		break;
}

/*
|--------------------------------------------------------------------------
| Security
|--------------------------------------------------------------------------
|
| Encryption Keys and other Security related constants
|
*/
define('FAILED_LOGINS_ALLOWED',	5);
define('SSI_ENC_KEY',			'j1135#weep-6a313133352377656570');
define('SSI_CURL_VAR',			getSpectrumKey(SSI_ENC_KEY));

/*
|--------------------------------------------------------------------------
| Getting Started Screen
|--------------------------------------------------------------------------
|
| Form ID numbers for getting started screen
|
*/
define('_GS_WELCOME',	1);
define('_GS_PASSWORD',	2);
define('_GS_BEFORE',	3);
define('_GS_PERSON',	4);
define('_GS_ADDRESS',	5);
define('_GS_CONTACT',	6);
define('_GS_ORGDETAILS',7);
define('_GS_ORGADDRESS',8);
define('_GS_USERS',		9);
define('_GS_SIGN',		10);
define('_GS_BANK',		11);
define('_GS_SUMMARY',	12);
define('_GS_PAYMENT',	13);
define('_GS_TERMS',		14);
define('_GS_FINAL',		15);

/*
|--------------------------------------------------------------------------
| API KEYS
|--------------------------------------------------------------------------
|
| All API Keys for interaction with outside systems (i.e. Google, Intervals)
|
*/
define('INTERVALS_API_KEY',	'dq0ci87zsf4');
define('MAILCHIMPAPI',		'7a07a8d7e99c864b808a45e8a2c9076f-us2'); #API for interacting with mailchimp

define('GMAP_VERSION',      '3'); 

switch(SYS_STATE)
{
	case 'DEV':
        (GMAP_VERSION=='3')?define('GOOGLEAPIKEY', 'AIzaSyCqtYOomoimfaYuzxKfkZJK67IQQwYxAEE'):define('GOOGLEAPIKEY', 'ABQIAAAAhBhxrOjhRdye1GSCNC0nzhTdfI-5C_lp0IJP5qTDBY2oNKHwQBQDxIGmTARUzKcc7t7CFZe-9K2cig');
        break;

	case 'STAGE':
		define('GOOGLEAPIKEY', 'ABQIAAAAxxXfsmoseJA94BTXroSPbRRPFVoQF0_9I8JtEltiTnd_ZWYUPhQvGLZhp6n6Lxuzb_G551qCaWpGZQ');
		break;

	default:
	case 'LIVE':
		define('GOOGLEAPIKEY', 'ABQIAAAAhBhxrOjhRdye1GSCNC0nzhRUUvRoC7mC7xQCq3LdfKRzrYFIHhTluFGdNw8VoBtLzrjhS4V_deutwQ');
		break;
}

/*
|--------------------------------------------------------------------------
| Email & Contact
|--------------------------------------------------------------------------
|
| Settings for sending and receiving emails, and providing contact info
|
*/
define('DEFAULT_FROM_EMAIL','no-reply@playerspectrum.com');
define('DEFAULT_FROM_NAME',	'Spectrum');
define('SUPPORT_PHONE',		'1 (855) 546 - 9197');
define('SUPPORT_EMAIL',		'service@playerspectrum.com');
define('ERROR_EMAIL',		'operations_bradley@servillian.ca');//this is where the system sends internal errors, such as CRON errors

switch(SYS_STATE)
{
	case 'DEV':
		define('EMAILING_TEST_MODE', FALSE);
		break;

	default:
	case 'STAGE':	
	case 'LIVE':
		define('EMAILING_TEST_MODE', FALSE);
		break;
}

/*
|--------------------------------------------------------------------------
| Financial
|--------------------------------------------------------------------------
|
| Account Numbers
|
*/
define('SSI_TRUST_BANK',	'002');
define('SSI_TRUST_TRANSIT',	'40287');
define('SSI_TRUST_ACCT',	'0003611');

/*
|--------------------------------------------------------------------------
| Scotia Direct
|--------------------------------------------------------------------------
|
| DEPRECIATED. This service is no longer in use.
| Saved for historical and stability purposes.
|
*/
define('SCOTIA_CUSTOMER',	'SD2852000220');
define('SCOTIA_DATA_CENT',	'00220');

/*
|--------------------------------------------------------------------------
| Beanstream
|--------------------------------------------------------------------------
|
| Beanstream Error Codes for EBT Transfers and values for completing EBT
| transfers
|  Only password and merchant id are different between stage and live
|  everything else is constant
|
*/

switch(SYS_STATE)
{
	case 'DEV':
		define('BEANSTREAM_MERCHANT_ID',	'252740000');
		define('BEANSTREAM_PASSWORD',		'4b298d71'); 
		define('BEANSTREAM_COMPANY_ID',		'ServillianSolutionsSB');
	break;
		
	/*
	manual handling: go to
	https://www.beanstream.com/admin/sDefault.asp
	with username 'admin'
	the other credentails are for live or dev as defined in constants
	
	
 contact info:
				Ben Cameron
				Lead Client Services Consultant
				----------------------------------------------
				Beanstream Internet Commerce
				a subsidiary of LML Payment Systems Inc.

				sales: 888.472.2072
				support: 888.472.0811
				support@beanstream.com
	*/
	default:
	case 'STAGE':	
	case 'LIVE':
		define('BEANSTREAM_MERCHANT_ID',	'250230000');  
		define('BEANSTREAM_PASSWORD',		'2351B16d'); 
		define('BEANSTREAM_COMPANY_ID',		'ServillianSolutions');
	break;
}

define('BEANSTREAM_BATCH_URL',		'https://www.beanstream.com/scripts/batch_upload.asp?');//uploading batch files
define('BEANSTREAM_REPORT_URL',		'https://www.beanstream.com/scripts/report.aspx?');//downloading reports
define('BEANSTREAM_LOGIN',			'admin');
define('BEANSTREAM_VERSION',		'1.1');   
define('BEANSTREAM_RPT_VERSION',	'1.0');  //was 1.6. got the error "Invalid report version "
define('BEANSTREAM_DATE_FMT',		'dmY');  //requres date formats like DDMMYYYY , this is the key for date() php fn
define('BEANSTREAM_FILENAME_MAX',	32);     //max length for a filename accepted by their scripts
define('BEANSTREAM_BATCH_MAX',	250000);     //max amt in Dollars  per batch. this is attached to our acount, not hard coded in documentation
define('BEANSTREAM_FILENAME_PREFIX','bs_eft_');
define('BSERROR_SUCCESS',			1);
define('BSERROR_INSECURE',			2);
define('BSERROR_OUTDATED',			3);
define('BSERROR_LOGIN',				4);
define('BSERROR_AUTH',				5);
define('BSERROR_DISABLED',			6);
define('BSERROR_DATE',				7);
define('BSERROR_SERVERBUSY',		8);
define('BSERROR_FILESIZE',			9);
define('BSERROR_UNKNOWN',			10);
define('BSERROR_FILENOTSENT',		11);
define('BSERROR_ACCOUNT',			12);
define('BSERROR_FILENAME',			13);



/*
|--------------------------------------------------------------------------
| Database Selection
|--------------------------------------------------------------------------
|
| Set the database key to select the correct database to work off of.
|
*/
switch(SYS_STATE)
{
	
	case 'DEV':
		define('DBKEY','dev');
		break;

	default:
	case 'STAGE':
	    define('DBKEY','stage');
	
	break;
	case 'LIVE':
	    define('DBKEY','live');
		break;
}

/*
|--------------------------------------------------------------------------
| File Paths
|--------------------------------------------------------------------------
|
| Saved Filepaths for use in the system
|
*/
define('ORG_LOGO_BASEPATH',	'uploaded/org-assets/logo');
define("DIR_EFT_BATCH",		'uploaded/finance-batches/');
define('PUBDOC_SOURCE', 	"http://endeavor.servilliansolutionsinc.com/public_documents/");

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/
define('FOPEN_READ', 							'rb');
define('FOPEN_READ_WRITE',						'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 		'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 	'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE', 					'ab');
define('FOPEN_READ_WRITE_CREATE', 				'a+b');
define('FOPEN_WRITE_CREATE_STRICT', 			'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',		'x+b');



/* End of file constants.php */
/* Location: ./system/application/config/constants.php */