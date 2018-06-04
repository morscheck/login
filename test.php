<?php
class TerminalController{
   function __construct(){
      date_default_timezone_set("Asia/Jakarta");
      $this->time = date("h:i");
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
      $this->access_token = @file_get_contents('accessToken.txt');
      $this->api_secret = 'c1e620fa708a1d5696fb991c1bde5662';
      $this->api_key = '3e7c78e35a76a9299309885393b02d97';
      $this->base = 'https://api.facebook.com/restserver.php';
   }
   public function Dashboard(){
      echo "---------------------------------------------\n";
      echo "".$this->COLOR_YELLOW."Facebook".$this->COLOR_WHITE." Robotlike\n";
      echo "Copyright © 2018 ".$this->COLOR_BLUE."Ramadhani Pratama".$this->COLOR_WHITE."\n";
      echo "---------------------------------------------\n";
      echo " -> 1. ".$this->COLOR_LIGHT_GREEN."Robotlike ".$this->COLOR_ORANGE."(Automatic like on timeline)".$this->COLOR_WHITE."\n";
      echo "\nSelect option : ".$this->COLOR_LIGHT_GREEN."";
      $option = trim(fgets(STDIN));
      echo "".$this->COLOR_WHITE."";
      if($option == '1'){
         $this->Robotlike();
      }
   }
   public function Robotlike(){
      echo "\nLimit Feed : ".$this->COLOR_LIGHT_GREEN."";
      $limit = trim(fgets(STDIN));
      echo "".$this->COLOR_WHITE."";
      echo "Delay Second : ".$this->COLOR_LIGHT_GREEN."";
      $delay = trim(fgets(STDIN));
      echo "".$this->COLOR_WHITE."";
      $api = json_decode($this->curl('https://graph.facebook.com/me/home?fields=id&limit='.$limit.'&access_token='.$this->access_token));
      print_r($api);
      $this->Dashboard();
   }

   public function MenuLogin(){
      echo "---------------------------------------------\n";
      echo "".$this->COLOR_YELLOW."Facebook".$this->COLOR_WHITE." Robotlike\n";
      echo "Copyright © 2018 ".$this->COLOR_BLUE."Ramadhani Pratama".$this->COLOR_WHITE."\n";
      echo "---------------------------------------------\n";
      echo "".$this->COLOR_LIGHT_GREEN."Userame : ".$this->COLOR_WHITE;
      $username = trim(fgets(STDIN));
      echo "".$this->COLOR_LIGHT_GREEN."Password : ".$this->COLOR_BLACK;
      $password = trim(fgets(STDIN));
      echo "\n";
      echo "".$this->COLOR_ORANGE."Please wait checking username/password ...".$this->COLOR_WHITE;
      echo"\n";
      $this->CheckToken($username, $password);
   }

   public function CheckToken($username, $password){
      $api = json_decode($this->curl('https://graph.facebook.com/me?access_token='.$this->access_token));
      if(@$api->id){
         echo "".$this->COLOR_ORANGE."Ready!".$this->COLOR_WHITE."\n";
         $this->Dashboard();
      }else{
         echo "".$this->COLOR_ORANGE."Please wait get new access_token ...".$this->COLOR_WHITE."\n";
         $this->Login($username, $password);
      }
   }
   public function Login($username, $password){
      $data = array(
         "api_key" => $this->api_key,
         "email" => $username,
         "format" => "JSON",
         "locale" => "vi_vn",
         "method" => "auth.login",
         "password" => $password,
         "return_ssl_resources" => "0",
         "v" => "1.0"
      );
      $this->SignCreator($data);
      $response = $this->GetToken('GET', false, $data);
      $data = json_decode($response);
      if(!@$data->access_token){
         echo "Failed : ".$this->COLOR_RED."Username/password incorret.".$this->COLOR_WHITE."\n";
         $this->MenuLogin();
      }else{
         $x=$data->access_token."\n";
         $y=fopen('accessToken.txt','w');
         fwrite($y,$x);
         fclose($y);
         echo "Success : ".$this->COLOR_LIGHT_GREEN."Success get cookies.".$this->COLOR_WHITE."\n";
         echo $this->access_token;
         $this->Dashboard();
      }
      
   }
   public function SignCreator(&$data){
      $sig = "";
      foreach($data as $key => $value){
         $sig .= "$key=$value";
      }
      $sig .= $this->api_secret;
      $sig = md5($sig);
      return $data['sig'] = $sig;
   }
   public function UserAgent(){
      $user_agents = array(
         "Mozilla/5.0 (iPhone; CPU iPhone OS 9_2_1 like Mac OS X) AppleWebKit/601.1.46 (KHTML, like Gecko) Mobile/13D15 Safari Line/5.9.5",
         "Mozilla/5.0 (iPhone; CPU iPhone OS 9_0_2 like Mac OS X) AppleWebKit/601.1.46 (KHTML, like Gecko) Mobile/13A452 Safari/601.1.46 Sleipnir/4.2.2m",
         "Mozilla/5.0 (iPhone; CPU iPhone OS 9_3 like Mac OS X) AppleWebKit/601.1.46 (KHTML, like Gecko) Version/9.0 Mobile/13E199 Safari/601.1",
         "Mozilla/5.0 (iPod; CPU iPhone OS 9_2_1 like Mac OS X) AppleWebKit/600.1.4 (KHTML, like Gecko) CriOS/45.0.2454.89 Mobile/13D15 Safari/600.1.4",
         "Mozilla/5.0 (iPhone; CPU iPhone OS 9_3 like Mac OS X) AppleWebKit/601.1.46 (KHTML, like Gecko) Version/9.0 Mobile/13E198 Safari/601.1"
      );
      $useragent = $user_agents[array_rand($user_agents)];
      return $useragent;
   }
   public function GetToken($method = 'GET', $url = false, $data){
      $c = curl_init();
      $opts = array(
      CURLOPT_URL => ($url ? $url : $this->base).($method == 'GET' ? '?'.http_build_query($data) : ''),
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_SSL_VERIFYPEER => false,
      CURLOPT_USERAGENT => $this->UserAgent());
      if($method == 'POST'){
         $opts[CURLOPT_POST] = true;
         $opts[CURLOPT_POSTFIELDS] = $data;
      }
      curl_setopt_array($c, $opts);
      $d = curl_exec($c);
      curl_close($c);
      return $d;
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
}
$open = new TerminalController();
echo $open->MenuLogin();
