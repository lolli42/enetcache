<?php
/***************************************************************
 * Copyright notice
 *
 * (c) 2011 Michael Knabe <mk@e-netconsulting.de>
 * All rights reserved
 *
 * This script is part of the TYPO3 project. The TYPO3 project is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * This is a TCA column definition for a standalone table that has
 * no references to other tables. It is a copy of some commerce definition
 */
return array(
	'columns' => array(
		'cust_deliveryaddress' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:commerce/locallang_db.xml:tx_commerce_orders.cust_deliveryaddress',
			'config' => array(
				'type' => 'user',
				'userFunc' => 'user_orderedit_func->delivery_adress',
			)
		),
		'order_id' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:commerce/locallang_db.xml:tx_commerce_orders.order_id',
			'config' => array(
				'type' => 'none',
				'pass_content' => 1,
			)
		),
		'crdate' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:commerce/locallang_db.xml:tx_commerce_orders.crdate',
			'config' => array(
				'type' => 'none',
				'format' => 'date',
				'eval' => 'date',
			)
		),
		'cust_invoice' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:commerce/locallang_db.xml:tx_commerce_orders.cust_invoice',
			'config' => array(
				'type' => 'user',
				'userFunc' => 'user_orderedit_func->invoice_adress',
			)
		),
		'sum_price_net' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:commerce/locallang_db.xml:tx_commerce_orders.sum_price_net',
			'config' => array(
				'type' => 'user',
				'userFunc' => 'user_orderedit_func->order_articles',
			)
		),
		'sum_price_gross' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:commerce/locallang_db.xml:tx_commerce_orders.sum_price_gross',
			'config' => array(
				'type' => 'user',
				'userFunc' => 'user_orderedit_func->sum_price_gross_format',
			)
		),
		'payment_ref_id' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:commerce/locallang_db.xml:tx_commerce_orders.payment_ref_id',
			'config' => array(
				'type' => 'none',
				'pass_content' => 1,
			),
		),
		'comment' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:commerce/locallang_db.xml:tx_commerce_orders.comment',
			'config' => array(
				'type' => 'text',
				'cols' => '30',
				'rows' => '5',
			),
		),
		'internalcomment' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:commerce/locallang_db.xml:tx_commerce_orders.internalcomment',
			'config' => array(
				'type' => 'text',
				'cols' => '30',
				'rows' => '5',
			),
		),
		'pricefromnet' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:commerce/locallang_db.xml:tx_commerce_orders.pricefromnet',
			'config' => array(
				'type' => 'select',
				'items' => Array(
					Array('LLL:EXT:commerce/locallang_be.xml:no',0),
					Array('LLL:EXT:commerce/locallang_be.xml:yes',1)
				)
			),
		),
	),
);
?>