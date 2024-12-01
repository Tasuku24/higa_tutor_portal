<?php
// discard session data
session_start();
session_destroy();
header('Location: login.php');
exit;
