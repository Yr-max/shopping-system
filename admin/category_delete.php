<?php

require '../config/config.php';

$stmt = $db->prepare("DELETE FROM categories WHERE id=".$_GET['id']);
$stmt->execute();
header('location:category.php');

?>