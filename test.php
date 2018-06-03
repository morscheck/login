<?php
form();
function form(){
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
  echo "\n -> 1. ".$OG."Likergram.net ".$OR."(Like For Like)".$WH;
  echo "\n -> 2. ".$OG."Instabotlike.net ".$OR."(Robotlike)".$WH."\n";
  echo "Select option : ".$OG."";
  $option = trim(fgets(STDIN));
  echo "".$WH."";
  echo "YOu choose ".$option."\n\n";
  form();
}
?>
