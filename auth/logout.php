<?php
require_once '../autoload.php';

Session::destroy();
echo "<script>location.href = './login.php';</script>";

