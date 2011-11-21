<?php
	require_once('includes.php');

	$stock = array();
	for($i = 1; $i <= 8; $i++) {
		$stock[] = 'Item '. $i;
	}


	$t = new QTMTemplate('basic.tpl');

	$t->addKey('_TITLE', 'Template Parser Test');

	if(empty($stock)) {
		$t->startSection();
		$t->insertSection('STOCK_EMPTY', '_STOCK');
	} else {
		$t->startSection();

		foreach(array_chunk($stock, 3) as $i => $row) {
			$t->startSection();

			$t->addKey('_ROW', $i+1);
			foreach($row as $j => $item) {
				$t->startSection();
				$t->addKey('_COL', $j+1);
				$t->addKey('_NAME', $item);
				$t->writeSection('ITEM');
			}

			$t->writeSection('ROW');
		}

		$t->insertSection('STOCK_FILLED', '_STOCK');
	}

	echo $t->writeSection('MAIN');
?>
