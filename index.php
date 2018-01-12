<?php 

require('./config/init.php');
require('./app/controller/Bulletin.php');

$page = new Bulletin();

$page->show();
