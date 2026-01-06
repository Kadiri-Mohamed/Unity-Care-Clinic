<?php
require_once '../autoload.php';

Session::destroy();
header('Location: ../routes/router.php?route=logout');

