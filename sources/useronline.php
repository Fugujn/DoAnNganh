<?php

$time = time();
$time_check = $time - 600; // set thời gian kiểm tra là 10 phút
$ip = $_SERVER['REMOTE_ADDR'];

$tbl_name = "user_online"; // tên của table đã tạo bên trên

// Now we are checking if the ip was logged in the database. Depending of the value in minutes in the locktime variable. 
$day             =    date('d'); 
$month             =    date('n'); 
$year             =    date('Y'); 
$daystart         =    mktime(0,0,0,$month,$day,$year); 
$monthstart         =  mktime(0,0,0,$month,1,$year); 
//tinh thang
$weekday         =    date('w'); 
$weekday--; 
if ($weekday < 0)    $weekday = 7; 
$weekday         =    $weekday * 24*60*60; 
$weekstart         =    $daystart - $weekday; 

$sql = "SELECT * FROM $tbl_name WHERE session='$session'";
$result = $d->query($sql);
$count = mysql_num_rows($result);

if ($count == "0") {
    //Them moi nguoi onlie
    $sql1 = "INSERT INTO $tbl_name(session, time,	ip )VALUES('$session', '$time','$ip')";
    $result1 = $d->query($sql1);
} else {
    //Cap nhat lai thoi gian
    $sql2 = "UPDATE $tbl_name SET time='$time' WHERE session = '$session'";
    $result2 = $d->query($sql2);
}

// nếu quá 10 phút mà ko thấy session này làm việc thì tiến hành xóa
$sql4 = "DELETE FROM $tbl_name WHERE time<$time_check";
$result4 = $d->query($sql4);


//Lay tong so nguoi online
$sql3 = "SELECT * FROM $tbl_name";
$result3 = $d->query($sql3);
$count_user_online = mysql_num_rows($result3);

//lay so nguoi theo thang
// $query =    "SELECT COUNT(*) AS weekrec FROM $tbl_name WHERE tm>='".$weekstart."'"; 
// $weekrec = mysql_fetch_assoc($d->query($query)); 
// $week_visitors     =    $weekrec['weekrec']; 
?>