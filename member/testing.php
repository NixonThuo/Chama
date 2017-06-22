<?php
//echo "Today is " . date("Y-m-d") . "<br>";
//echo "Today is " . date("Y.m.d") . "<br>";
//echo "Today is " . date("Y-m-d") . "<br>";
//echo "Today is " . date("l");
?>
<?php
echo "Today is " . date("Y-m-d") . "<br>";
$today=date("Y-m-d");
$date=date_create($today);
$due_date=date_add($date,date_interval_create_from_date_string("10 days"));
$due_date= date_format($date,"Y-m-d");
echo $due_date;
echo "<br>";
?> 
<?php 
$num1=6;
$num2=4;
echo ($num1-$num2);
?>