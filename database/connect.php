<?php
    $conn = mysqli_connect('localhost','root','123456','banhngot');
    mysqli_set_charset($conn,'utf8');
    
    // Set timezone to Vietnam (GMT+7)
    mysqli_query($conn, "SET time_zone = '+07:00'");
    
    // Set PHP timezone
    date_default_timezone_set('Asia/Ho_Chi_Minh');
?>