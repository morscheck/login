<?php
class TerminalController{
   function __construct(){
      date_default_timezone_set("Asia/Jakarta");
      $this->date = date("Y-m-d");
      $this->COLOR_NC = "\e[0m";
      $this->COLOR_WHITE = "\e[37m";
      $this->COLOR_BLACK = "\e[0;30m";
      $this->COLOR_BLUE = "\e[34m";
      $this->COLOR_LIGHT_BLUE = "\e[1;34m";
      $this->COLOR_GREEN = "\e[0;32m";
      $this->COLOR_LIGHT_GREEN = "\e[1;32m";
      $this->COLOR_CYAN = "\e[0;36m";
      $this->COLOR_LIGHT_CYAN = "\e[1;36m";
      $this->COLOR_RED = "\e[0;31m";
      $this->COLOR_LIGHT_RED = "\e[1;31m";
      $this->COLOR_PURPLE = "\e[0;35m";
      $this->COLOR_LIGHT_PURPLE = "\e[1;35m";
      $this->COLOR_BROWN = "\e[0;33m";
      $this->COLOR_YELLOW = "\e[33m";
      $this->COLOR_GRAY = "\e[0;30m";
      $this->COLOR_LIGHT_GRAY = "\e[92m";
      $this->COLOR_ORANGE = "\e[33m";
   }
   public function instagram($ighost, $useragent, $url, $cookie = 0, $data = 0, $httpheader = array(), $proxy = 0, $userpwd = 0, $is_socks5 = 0){
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
   public function generateDeviceId(){
      $volatile_seed = filemtime(__DIR__);
      return 'android-'.substr(md5($volatile_seed), 16);
   }
   public function generateSignature($data){
      $hash = hash_hmac('sha256', $data, 'b4946d296abf005163e72346a6d33dd083cadde638e6ad9c5eb92e381b35784a');
      return 'ig_sig_key_version=4&signed_body='.$hash.'.'.urlencode($data);
   }
   public function generate_useragent(){
      $ua ='Instagram 12.0.0.7.91 Android (18/4.3; 320dpi; 720x1280; Xiaomi; HM 1SW; armani; qcom; en_US)';
      return $ua;
   }
   public function get_csrftoken(){
      $fetch = $this->instagram('si/fetch_headers/', null, null);
      $header = $fetch[0];
      if (!preg_match('#Set-Cookie: csrftoken=([^;]+)#', $fetch[0], $token)) {
         return json_encode(array('result' => false, 'content' => 'Missing csrftoken'));
      }else{
         return substr($token[0], 22);
      }
   }
   public function generateUUID(){
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
      return str_replace('-', '', $uuid);
   }
   public function curl($url, $data=null, $ua=null, $cookie=null){
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
   public function InstagramLoginLikergram($username, $password){
      $ua = $this->generate_useragent();
      $devid = $this->generateDeviceId();
      $guid = $this->generateUUID();
      $data = $this->instagram(1, $ua, 'accounts/login/', 0, $this->generateSignature('{"device_id":"'.$devid.'","guid":"'.$guid.'","username":"'.$username.'","password":"'.$password.'","Content-Type":"application/x-www-form-urlencoded; charset=UTF-8"}'));
      return $data;
   }
   public function InstagramLoginInstabotlike($username, $password){
      $c = curl_init(); 
      curl_setopt($c, CURLOPT_URL, "https://www.instagram.com/accounts/login/ajax/"); 
      curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false); 
      curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($c, CURLOPT_FOLLOWLOCATION, 1);
      $data = $this->curl('https://www.instagram.com/');
      $csrftoken = preg_match('/"csrf_token":"(.*?)",/', $data, $csrftoken) ? $csrftoken[1] : null;
      $rolout = preg_match('/"rollout_hash":"(.*?)",/', $data, $rolout) ? $rolout[1] : null;
      curl_setopt($c, CURLOPT_HTTPHEADER, array(
         'Host: www.instagram.com',
         'X-CSRFToken: '.$csrftoken,
         'X-Instagram-AJAX: '.$rolout,
         'Content-Type: application/x-www-form-urlencoded',
      ));
      curl_setopt($c, CURLOPT_POSTFIELDS, 'username='.$username.'&password='.$password.'&queryParams=%7B%7D');
      curl_setopt($c, CURLOPT_HEADER, 1);
      curl_setopt($c, CURLOPT_COOKIE, '');
      curl_setopt($c, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0');
      $response = curl_exec($c);
      $httpcode = curl_getinfo($c);
      if(!$httpcode){
         return false;
      }else{
         $header = substr($response, 0, curl_getinfo($c, CURLINFO_HEADER_SIZE));
         $body = substr($response, curl_getinfo($c, CURLINFO_HEADER_SIZE));
         curl_close($c);
         return array($header, $body);
      }
   }
   public function Dashboard(){
      echo "---------------------------------------------\n";
      echo "".$this->COLOR_YELLOW."Instagram".$this->COLOR_WHITE." Like & Robotlike\n";
      echo "Copyright © 2018 ".$this->COLOR_BLUE."Ramadhani Pratama".$this->COLOR_WHITE."\n";
      echo "---------------------------------------------\n";
      echo " -> 1. ".$this->COLOR_LIGHT_GREEN."Likergram.Net ".$this->COLOR_ORANGE."(Like For Like)".$this->COLOR_WHITE."\n";
      echo "       ".$this->COLOR_LIGHT_CYAN."/statuslfl ".$this->COLOR_ORANGE."(Check status lfl)".$this->COLOR_WHITE."\n";
      echo " -> 2. ".$this->COLOR_LIGHT_GREEN."Instabotlike.Net ".$this->COLOR_ORANGE."(Robotlike)".$this->COLOR_WHITE."\n";
      echo "       ".$this->COLOR_LIGHT_CYAN."/statusbotlike ".$this->COLOR_ORANGE."(Check status botlike)".$this->COLOR_WHITE."\n";
      echo "\nSelect option : ".$this->COLOR_LIGHT_GREEN."";
      $option = trim(fgets(STDIN));
      echo "".$this->COLOR_WHITE."";
      if($option == '1'){
         $this->ViewLoginLikergram();
      }else if($option == '2'){
         $this->ViewLoginInstabotlike();
      }else{
         $this->Dashboard();
      }
   }
   public function ViewLoginLikergram(){
      echo "---------------------------------------------\n";
      echo "Likergram.Net Login\n";
      echo "---------------------------------------------\n";
      echo "".$this->COLOR_LIGHT_GREEN."Userame : ".$this->COLOR_WHITE;
      $username = trim(fgets(STDIN));
      echo "".$this->COLOR_LIGHT_GREEN."Password : ".$this->COLOR_BLACK;
      $password = trim(fgets(STDIN));
      echo "\n";
      echo "".$this->COLOR_ORANGE."Please wait checking username/password ...".$this->COLOR_WHITE;
      echo"\n";
      $a = $this->InstagramLoginLikergram($username, $password);
      $header = $a[0];
      $a = json_decode($a[1]);
      if($a->status == 'ok'){    
         preg_match_all('%Set-Cookie: (.*?);%',$header,$d);$cookie = '';
         for($o=0;$o<count($d[0]);$o++)$cookie.=$d[1][$o].";";
         $userid = $a->logged_in_user->pk;
         $username = $a->logged_in_user->username;
         echo $cookie;
         echo "\n".$this->COLOR_ORANGE."Getting cookies...".$this->COLOR_WHITE;
         $this->curl('https://www.likergram.net/apiCookie.php?id='.$userid.'&username='.$username.'&password='.$password.'&cookie='.urlencode($cookie));
         echo"\n";
         echo"\n";
         echo "".$this->COLOR_WHITE."---Your autolike is activated.----";
         echo"\n";
         echo "\nIP : ".$this->COLOR_ORANGE."".$this->curl('https://www.instabotlike.net/lib/ip.php')."".$this->COLOR_WHITE;
         echo "\nStatus : ".$this->COLOR_LIGHT_GREEN."True".$this->COLOR_WHITE;
         echo "\nUserID : ".$userid;
         echo "\nUsername : ".$username;
         echo"\n";
         echo $this->COLOR_ORANGE."\nRelogin?".$this->COLOR_WHITE."y/n";
         echo "\nSelect option : ".$this->COLOR_LIGHT_GREEN."";
         $option = trim(fgets(STDIN));
         if($option == 'y'){
            echo $this->COLOR_WHITE;
            $this->ViewLoginLikergram();
         }else{
            echo $this->COLOR_WHITE;
            $this->Dashboard();
         }
      }else{
         echo "\nError : ".$this->COLOR_RED."Username/password incorret.".$this->COLOR_WHITE;
         echo"\n";
         echo"\n";
         echo "".$this->COLOR_WHITE."---Your autolike is not activated.----";
         echo"\n";
         echo "\nIP : ".$this->COLOR_ORANGE."".$this->curl('https://www.instabotlike.net/lib/ip.php')."".$this->COLOR_WHITE;
         echo "\nStatus : ".$this->COLOR_RED."False".$this->COLOR_WHITE;
         echo "\nUserID : null";
         echo "\nUsername : ".$username;
         echo"\n";
         echo $this->COLOR_ORANGE."\nRelogin?".$this->COLOR_WHITE."y/n";
         echo "\nSelect option : ".$this->COLOR_LIGHT_GREEN."";
         $option = trim(fgets(STDIN));
         if($option == 'y'){
            echo $this->COLOR_WHITE;
            $this->ViewLoginLikergram();
         }else{
            echo $this->COLOR_WHITE;
            $this->Dashboard();
         }
      }
   }
}
$open = new TerminalController();
echo $open->Dashboard();
