<?php
$arr = [1,2,3,4,5,6,7,8,9];

foreach($arr as $num){
	if ($num%2 == 0){
    	echo $num;
    }
}

echo "<br>\n";
echo "I used the modulus operator too check if the remainder was 0 when you divide  by two to ensure only even numbers would print";
?> 
