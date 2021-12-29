<?php

include "phpqrcode.php";
$dynamic_address = $_SERVER['QUERY_STRING'];
$rs=QRcode::png($dynamic_address, false, QR_ECLEVEL_H, 6, 4);


?>