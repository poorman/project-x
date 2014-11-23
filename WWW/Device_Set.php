<?php session_start();
require_once 'Mobile_Detect.php';
$detect = new Mobile_Detect;
$device = array();
// Any mobile device (phones or tablets).
$device['mobile'] = $detect->isMobile();
// Any tablet device.
$device['tablet'] = $detect->isTablet();
// Exclude tablets.
//if( $detect->isMobile() && !$detect->isTablet() ){	}
 
// Check for a specific platform with the help of the magic methods:
$device['isios'] =  $detect->isiOS();
$device['isandroid'] = $detect->isAndroidOS();
// Alternative method is() for checking specific properties.
// WARNING: this method is in BETA, some keyword properties will change in the future.
$device['chrome'] =  $detect->is('Chrome');
$device['IOS'] =  $detect->is('iOS');
$device['UC'] =  $detect->is('UC Browser');
// [...]
 
// Batch mode using setUserAgent():
$userAgents = array(
'Mozilla/5.0 (Linux; Android 4.0.4; Desire HD Build/IMM76D) AppleWebKit/535.19 (KHTML, like Gecko) Chrome/18.0.1025.166 Mobile Safari/535.19',
'BlackBerry7100i/4.1.0 Profile/MIDP-2.0 Configuration/CLDC-1.1 VendorID/103',
// [...]
);
foreach($userAgents as $userAgent){
 
  $detect->setUserAgent($userAgent);
  $isMobile = $detect->isMobile();
  $isTablet = $detect->isTablet();
  // Use the force however you want.
 
}
 
// Get the version() of components.
// WARNING: this method is in BETA, some keyword properties will change in the future.
$device['ipad'] =  $detect->version('iPad'); // 4.3 (float)
$device['iphone'] =  $detect->version('iPhone'); // 3.1 (float)
$device['android'] =  $detect->version('Android'); // 2.1 (float)
$device['opera_mini'] =  $detect->version('Opera Mini'); // 5.0 (float)

$_SESSION['device'] = $device;