<?php
	function left($str, $length) {
     return substr($str, 0, $length);
    }
    
    function right($str, $length) {
         return substr($str, -$length);
    }
        
    function sege_sede($tempo_b)
    {
        if(!isset($tempo_b)){
            echo "Impossibile calcolare, il tempo è null";
            return 0;
        }
        if(count(explode(':',$tempo_b)) != 2){
            echo $tempo_b." non va bene<br/>";
        }
        list($minuti,$resto) = explode(':',$tempo_b);
        list($secondi,$centesimi) = explode('.',$resto);
        while($secondi>59)
        {
        	$minuti ++;
            $secondi -= 60;
        }
        return left((int)$centesimi.'00',2) + (int)$secondi*100 + (int)$minuti*60*100;
    }
    function sede_sege($cent)
    {
    	$minuti = intval ($cent / 60 / 100);
    	$secondi = intval (($cent - $minuti*60*100) / 100);
        $centesimi = $cent - $minuti*60*100 - $secondi*100;
        $str = "";
        if($minuti != 0){
            $str .= right('00'.$minuti,2).':';
        }
        $str .= right('00'.$secondi,2).'.';
        return $str.right('00'.$centesimi,2);
    }
    
?>