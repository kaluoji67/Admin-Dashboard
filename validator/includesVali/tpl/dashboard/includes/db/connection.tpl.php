<?php

$pdo = new PDO('mysql:host=localhost;port=3306;dbname=sqlvali_data','root','');
$pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);