<?php
require_once '../autoload.php';
Session::init();
Session::destroy();
header('Location: ../routes/router.php?route=logout');

