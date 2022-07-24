<?php
/*********************************************************
*                                                        *
*   (c) 2017 Loogaru, Durkin Andrey                      *
*                                                        *
* This software is under MIT license, so it's FREE.		 *
* FREE as you can feel FREE to DONATE at http://looga.ru *
* Thank you!!											 *
*                                                        *
*********************************************************/

if (!defined('BOOTSTRAP')) { die('Access denied'); }

fn_register_hooks(
	'get_store_locations_for_shipping_before_select',
	'shippings_get_company_shipping_ids',
	'calculate_cart_taxes_pre',
	'place_suborders',
	'allow_place_order'
);
