<?php
session_start();
session_destroy();
header("Refresh:2; url=..\HTML\index.html");
?>