<?php
$url = "http://echelon-2013.eventbrite.com/";
header ('HTTP/1.1 301 Moved Permanently');
header("Location: ".$url);
exit();

?>