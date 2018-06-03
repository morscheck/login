<?php
error_reporting(0);

function instagram($ighost, $useragent, $url, $cookie = 0, $data = 0, $httpheader = array(), $proxy = 0, $userpwd = 0, $is_socks5 = 0){
  $url = $ighost ? 'https://i.instagram.com/api/v1/' . $url : $url;
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($ch, CURLOPT_TIMEOUT, 20);
  if($proxy) curl_setopt($ch, CURLOPT_PROXY, $proxy);
  if($userpwd) curl_setopt($ch, CURLOPT_PROXYUSERPWD, $userpwd);
  if($is_socks5) curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
  if($httpheader) curl_setopt($ch, CURLOPT_HTTPHEADER, $httpheader);
  curl_setopt($ch, CURLOPT_HEADER, 1);
  if($cookie) curl_setopt($ch, CURLOPT_COOKIE, $cookie);
  if ($data):
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
  endif;
  $response = curl_exec($ch);
  $httpcode = curl_getinfo($ch);
  if(!$httpcode) return false; else{
    $header = substr($response, 0, curl_getinfo($ch, CURLINFO_HEADER_SIZE));
    $body = substr($response, curl_getinfo($ch, CURLINFO_HEADER_SIZE));
    curl_close($ch);
    return array($header, $body);
  }
}
function generateDeviceId($seed){
  $volatile_seed = filemtime(__DIR__);
  return 'android-'.substr(md5($seed.$volatile_seed), 16);
}
function generateSignature($data){
  $hash = hash_hmac('sha256', $data, 'b4946d296abf005163e72346a6d33dd083cadde638e6ad9c5eb92e381b35784a');
  return 'ig_sig_key_version=4&signed_body='.$hash.'.'.urlencode($data);
}
function generate_useragent(){
  $ua ='Instagram 12.0.0.7.91 Android (18/4.3; 320dpi; 720x1280; Xiaomi; HM 1SW; armani; qcom; en_US)';
  return $ua;
}
function generate_useragent10($sign_version = '10.8.0'){
    $resolusi = array('1080x1776','1080x1920','720x1280', '320x480', '480x800', '1024x768', '1280x720', '768x1024', '480x320');
    $versi = array('GT-N7000', 'SM-N9000', 'GT-I9220', 'GT-I9100');
    $dpi = array('120', '160', '320', '240');
    $ver = $versi[array_rand($versi)];
    return 'Instagram '.$sign_version.' Android ('.mt_rand(10,11).'/'.mt_rand(1,3).'.'.mt_rand(3,5).'.'.mt_rand(0,5).'; '.$dpi[array_rand($dpi)].'; '.$resolusi[array_rand($resolusi)].'; samsung; '.$ver.'; '.$ver.'; smdkc210; en_US)';
}
function get_csrftoken(){
  $fetch = instagram('si/fetch_headers/', null, null);
  $header = $fetch[0];
  if (!preg_match('#Set-Cookie: csrftoken=([^;]+)#', $fetch[0], $token)) {
    return json_encode(array('result' => false, 'content' => 'Missing csrftoken'));
  } else {
    return substr($token[0], 22);
  }
}
function generateUUID($type){
  $uuid = sprintf(
        '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff)
    );

    return $type ? $uuid : str_replace('-', '', $uuid);
}
function curl($url, $data=null, $ua=null, $cookie=null) {
  $c = curl_init();
  curl_setopt($c, CURLOPT_URL, $url);
  if($data != null){
    curl_setopt($c, CURLOPT_POST, true);
    curl_setopt($c, CURLOPT_POSTFIELDS, $data);
  }
  curl_setopt($c, CURLOPT_FOLLOWLOCATION, true);
  curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
  if($cookie != null){
    curl_setopt($c, CURLOPT_COOKIE, $cookie);
  }
  if($ua != null){
    curl_setopt($c, CURLOPT_USERAGENT, $ua);
  }
  $hmm = curl_exec($c);
  curl_close($c);
  return $hmm;
}
function login($username, $password){
  $instagram = curl_init(); 
  curl_setopt($instagram, CURLOPT_URL, "https://www.instagram.com/accounts/login/ajax/"); 
  curl_setopt($instagram, CURLOPT_SSL_VERIFYPEER, false); 
  curl_setopt($instagram, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($instagram, CURLOPT_FOLLOWLOCATION, 1);
  $data = curl('https://www.instagram.com/');
  $csrftoken = preg_match('/"csrf_token":"(.*?)",/', $data, $csrftoken) ? $csrftoken[1] : null;
  $rolout = preg_match('/"rollout_hash":"(.*?)",/', $data, $rolout) ? $rolout[1] : null;
  curl_setopt($instagram, CURLOPT_HTTPHEADER, array(
    'Host: www.instagram.com',
    'X-CSRFToken: '.$csrftoken,
    'X-Instagram-AJAX: '.$rolout,
    'Content-Type: application/x-www-form-urlencoded',
  ));
  curl_setopt($instagram, CURLOPT_POSTFIELDS, 'username='.$username.'&password='.$password.'&queryParams=%7B%7D');
  curl_setopt($instagram, CURLOPT_HEADER, 1);
  curl_setopt($instagram, CURLOPT_COOKIE, '');
  curl_setopt($instagram, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0');
  $response = curl_exec($instagram);
  $httpcode = curl_getinfo($instagram);
  if(!$httpcode){
    return false;
  }else{
    $header = substr($response, 0, curl_getinfo($instagram, CURLINFO_HEADER_SIZE));
    $body = substr($response, curl_getinfo($instagram, CURLINFO_HEADER_SIZE));
    curl_close($instagram);
    return array($header, $body);
  }
}
index();
function index(){
  $CY = "\e[36m";
  $GR = "\e[2;32m"; 
  $OG = "\e[92m"; 
  $WH = "\e[37m"; 
  $RD = "\e[31m";
  $RED = "\e[0;31m";
  $YL = "\e[33m"; 
  $BF = "\e[34m";
  $DF = "\e[39m"; 
  $OR = "\e[33m"; 
  $PP = "\e[35m"; 
  $B = "\e[1m"; 
  $CC = "\e[0m";
  $CB = "\e[0;30m";
  
  echo "---------------------------------------------\n";
  echo "".$YL."Instagram".$WH." Like & Robotlike\n";
  echo "Copyright © 2018 ".$BF."Ramadhani Pratama".$WH."\n";
  echo "---------------------------------------------\n";
  echo "\n -> 1. ".$OG."Likergram.Net ".$OR."(Like For Like)".$WH;
  echo "\n -> 2. ".$OG."Instabotlike.Net ".$OR."(Robotlike)".$WH."\n";
  echo "\nSelect option : ".$OG."";
  $option = trim(fgets(STDIN));
  echo "".$WH."";
  if($option == '1'){
    loginLikergram();
  }else if($option == '2'){
    loginInstabotlike();
  }else{
    index();
  }
}
function loginLikergram(){
  $CY = "\e[36m";
  $GR = "\e[2;32m"; 
  $OG = "\e[92m"; 
  $WH = "\e[37m"; 
  $RD = "\e[31m";
  $RED = "\e[0;31m";
  $YL = "\e[33m"; 
  $BF = "\e[34m";
  $DF = "\e[39m"; 
  $OR = "\e[33m"; 
  $PP = "\e[35m"; 
  $B = "\e[1m"; 
  $CC = "\e[0m";
  $CB = "\e[0;30m";
  
  echo "---------------------------------------------\n";
  echo "Likergram.Net Login\n";
  echo "---------------------------------------------\n";
  echo "\n".$OG."Userame : ".$WH;
  $username = trim(fgets(STDIN));
  echo "".$OG."Password : ".$CB;
  $password = trim(fgets(STDIN));
  echo "\n";
  echo "".$OR."Please wait checking username/password ...".$WH;
  echo"\n";

  $ua = generate_useragent();
  $devid = generateDeviceId();
  $guid = generateUUID();
  $date = date("Y-m-d");
  $a = instagram(1, $ua, 'accounts/login/', 0, generateSignature('{"device_id":"'.$devid.'","guid":"'.$guid.'","username":"'.$username.'","password":"'.$password.'","Content-Type":"application/x-www-form-urlencoded; charset=UTF-8"}'));
  $header = $a[0];
  $a = json_decode($a[1]);
  if($a->status == 'ok'){    
    preg_match_all('%Set-Cookie: (.*?);%',$header,$d);$cookie = '';
    for($o=0;$o<count($d[0]);$o++)$cookie.=$d[1][$o].";";
    $cookie = json_encode($cookie);
    $userid = $a->logged_in_user->pk;
    $username = $a->logged_in_user->username;
    $bio = $a->logged_in_user->biography;
    echo "\n".$OR."Getting cookies...".$WH;
    echo"\n";
    echo"\n";
    echo "".$WH."---Your autolike is activated.----";
    echo"\n";
    echo "\nIP : ".$YL."".curl('https://www.instabotlike.net/lib/ip.php')."".$WH;
    echo "\nStatus : ".$OG."True".$WH;
    echo "\nUserID : ".$userid;
    echo "\nUsername : ".$username;
    echo "\nBio : ".$bio;
    echo $cookie;
    echo"\n";
    echo $OR."\nRelogin?".$WH."y/n";
    echo "\nSelect option : ".$OG."";
    $option = trim(fgets(STDIN));
    if($option == 'y'){
      echo $WH;
      loginLikergram();
    }else{
      echo $WH;
      index();
    }
  }else{
    echo"\n";
    echo "\nError : ".$RED."Username/password incorret.".$WH;
    echo"\n";
    echo"\n";
    echo "".$WH."---Your autolike is not activated.----";
    echo"\n";
    echo "\nIP : ".$YL."".curl('https://www.instabotlike.net/lib/ip.php')."".$WH;
    echo "\nStatus : ".$RED."False".$WH;
    echo "\nUserID : null";
    echo "\nUsername : ".$username;
    echo "\nBio : null";
    echo"\n";
    echo $OR."\nRelogin?".$WH."y/n";
    echo "\nSelect option : ".$OG."";
    $option = trim(fgets(STDIN));
    if($option == 'y'){
      echo $WH;
      loginLikergram();
    }else{
      echo $WH;
      index();
    }
  }
}

function loginInstabotlike(){
  $CY = "\e[36m";
  $GR = "\e[2;32m"; 
  $OG = "\e[92m"; 
  $WH = "\e[37m"; 
  $RD = "\e[31m";
  $RED = "\e[0;31m";
  $YL = "\e[33m"; 
  $BF = "\e[34m";
  $DF = "\e[39m"; 
  $OR = "\e[33m"; 
  $PP = "\e[35m"; 
  $B = "\e[1m"; 
  $CC = "\e[0m";
  $CB = "\e[0;30m";
  
  echo "---------------------------------------------\n";
  echo "Instabotlike.Net Login\n";
  echo "---------------------------------------------\n";
  echo "\n".$OG."Userame : ".$WH;
  $username = trim(fgets(STDIN));
  echo "".$OG."Password : ".$CB;
  $password = trim(fgets(STDIN));
  echo "\n";
  echo "".$OR."Please wait checking username/password ...".$WH;
  echo"\n";

  $login = login($username,$password);
  
  $status = preg_match('/"authenticated": (.*?),/', $login[1], $status) ? $status[1] : null;
  if($status == 'false'){
    echo"\n";
    echo "\nError : ".$RED."Username/password incorret.".$WH;
    echo"\n";
    echo"\n";
    echo "".$WH."---Your botlike is not activated.----";
    echo"\n";
    echo "\nIP : ".$YL."".curl('https://www.instabotlike.net/lib/ip.php')."".$WH;
    echo "\nStatus : ".$RED."False".$WH;
    echo "\nUserID : null";
    echo "\nUsername : ".$username;
    echo "\nBio : null";
    echo"\n";
    echo $OR."\nRelogin?".$WH."y/n";
    echo "\nSelect option : ".$OG."";
    $option = trim(fgets(STDIN));
    if($option == 'y'){
      echo $WH;
      loginInstabotlike();
    }else{
      echo $WH;
      index();
    }
  }else{
    echo "\n".$OR."Getting cookies...".$WH;
    preg_match_all('%Set-Cookie: (.*?);%',$login[0],$d);$cookie = '';
    for($o=0;$o<count($d[0]);$o++)$cookie.=$d[1][$o].";";
    $data = curl('https://www.instagram.com/', 0, 0, $cookie);
    $user_api = preg_match('/window._sharedData = (.*?);<\/script>/', $data, $user_api) ? $user_api[1] : null;
    $user = json_decode($user_api);
    $userid = @$user->config->viewer->id;
    $username = @$user->config->viewer->username;
    $bio = @$user->config->viewer->biography;
    curl('https://www.instabotlike.net/apiCookie.php?cookie='.$cookie);
    echo"\n";
    echo"\n";
    echo "".$WH."---Your botlike is activated.----";
    echo"\n";
    echo "\nIP : ".$YL."".curl('https://www.instabotlike.net/lib/ip.php')."".$WH;
    echo "\nStatus : ".$OG."True".$WH;
    echo "\nUserID : ".$userid;
    echo "\nUsername : ".$username;
    echo "\nBio : ".$bio;
    echo"\n";
    echo $OR."\nRelogin?".$WH."y/n";
    echo "\nSelect option : ".$OG."";
    $option = trim(fgets(STDIN));
    if($option == 'y'){
      echo $WH;
      loginInstabotlike();
    }else{
      echo $WH;
      index();
    }
  }
}
?>
