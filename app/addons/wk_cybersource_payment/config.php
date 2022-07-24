<?php
/**
 * Cyber Source Secure Acceptance Payment Gateway
 *
 * PHP version 7.1
 *
 * @category   Addon
 * @package    Cs-Cart
 * @author     WebKul software private limited <support@webkul.com>
 * @copyright  2010 webkul.com. All Rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version    GIT: 1.2
 * @filesource http://store.webkul.com
 * @link       Technical Support:  Forum - http://webkul.com/ticket
 */
use Tygh\Registry;

if (!defined('BOOTSTRAP')) {
    die('Access denied');
}

define('HMAC_SHA256', 'sha256');

