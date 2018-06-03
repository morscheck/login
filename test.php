<?php

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
      echo "\n";
      echo "\Likergram.Net Login\n";
      echo "\n".$OG."Userame : ".$WH;
      $username = trim(fgets(STDIN));
      echo "".$OG."Password : ".$CB;
      $password = trim(fgets(STDIN));
      echo "\n";
  }else if($option == '2'){
      echo "\n";
      echo "\nInstabotlike.Net Login\n";
      echo "\n".$OG."Userame : ".$WH;
      $username = trim(fgets(STDIN));
      echo "".$OG."Password : ".$CB;
      $password = trim(fgets(STDIN));
      echo "\n";
  }else{
    index();
  }
}
?>
