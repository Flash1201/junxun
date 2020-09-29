<?php
include __DIR__ . "/api.php";

$type = @$_GET['type'];
new NEInfo($type);