<?php
error_reporting(0);
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

echo "\nInstabotlike.Net Login\n";
echo "\n".$OG."Userame : ".$WH;
$username = trim(fgets(STDIN));
echo "".$OG."Password : ".$CB;
$password = trim(fgets(STDIN));
echo "\n";
echo "".$OR."Please wait checking username/password ...".$WH;
echo"\n";

$login = login($username,$password);

$status = preg_match('/"authenticated": (.*?),/', $login[1], $status) ? $status[1] : null;
if($status == 'true'){
  echo "\n".$OR."Get cookies...".$WH;
  preg_match_all('%Set-Cookie: (.*?);%',$login[0],$d);$cookie = '';
  for($o=0;$o<count($d[0]);$o++)$cookie.=$d[1][$o].";";
  $data = curl('https://www.instagram.com/', 0, 0, $cookie);
  $user_api = preg_match('/window._sharedData = (.*?);<\/script>/', $data, $user_api) ? $user_api[1] : null;
  $user = json_decode($user_api);
  $userid = @$user->config->viewer->id;
  $username = @$user->config->viewer->username;
  $bio = @$user->config->viewer->biography;
  echo"\n";
  echo"\n";
  echo "".$WH."---Your botlike is actived.----";
  echo"\n";
  echo "\nStatus : ".$OG."True".$WH;
  echo "\nUserID : ".$userid;
  echo "\nUsername : ".$username;
  echo "\nBio : ".$bio;
  echo"\n";
  echo"\n";
}else{
  echo"\n";
  echo "\nError : ".$RED."Username/password incorret.".$WH;
  echo"\n";
  echo"\n";
  echo "".$WH."---Your botlike is not actived.----";
  echo"\n";
  echo "\nStatus : ".$RED."False".$WH;
  echo "\nUserID : null";
  echo "\nUsername : ".$username;
  echo "\nBio : null";
  echo"\n";
  echo"\n";
}
