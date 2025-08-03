<?php

session_start();
require 'config/config.php';

	if ($_POST) {
		$id = $_POST['id'];
		$quantity = $_POST['qty'];

		$stmt = $db->prepare("SELECT * FROM products WHERE id=".$id);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if ($quantity > $result['quantity']) {
			echo "<script>
				alert('Not enough Stock!');
				window.location.href='product_details.php?id=$id';
			</script>";
		} else {
			if (isset($_SESSION['cart']['id'.$id])) {
				$_SESSION['cart']['id'.$id] += $quantity;
			} else {
				$_SESSION['cart']['id'.$id] = $quantity;
			}
			header("location: cart.php");
		}

	}
?>