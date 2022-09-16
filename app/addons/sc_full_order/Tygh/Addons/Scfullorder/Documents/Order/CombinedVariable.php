<?php


namespace Tygh\Addons\Scfullorder\Documents\Order;


use Tygh\Registry;
use Tygh\Template\Document\Order\Context;
use Tygh\Template\IVariable;

/**
 * Class BarcodeVariable
 * @package Tygh\Addons\Barcode\Documents\Order
 */
class CombinedVariable implements IVariable
{
    public $child_order_details_info;

    public $companies_name_with_order_number;
    public $companies_name_without_order_number;

    public $shippings_name_with_order_number;
    public $shippings_name_without_order_number;

    public $combine_invoice_id ='';

    public $combine_tracking_number ='';

    /**
     * BarcodeVariable constructor.
     *
     * @param Context $context Instance of order invoice context.
     */
    public function __construct(Context $context)
    {
        $order = $context->getOrder();

        if(!isset($order->data['child_order_details_info'])){
            return false;
        }
        $ch_o_info = $order->data['child_order_details_info'];

        if(!empty($ch_o_info)) {

            $this->child_order_details_info = $ch_o_info;
            $invoice_text = array();
            $shipping_info = array();
            $shipping_info_without = array();
            $companies_info = array();
            $companies_info_without = array();
            $shipping_methods = array();
            $combine_tracking_number = array();

            foreach ($ch_o_info as $K => $suborder) {

                if(!empty($suborder['is_sc_united_ship_order']) && $suborder['is_sc_united_ship_order'] =='Y'){
                    continue;
                }

                $header_id = __('order', array(), $context->getLangCode()) . '&nbsp;#' . $suborder['order_id'];
                $invoice_text[] = $header_id;

                $companies_info[] = $header_id.'  '.$suborder['company'];
                $companies_info_without[] = $suborder['company'];





                foreach ($suborder['product_groups'] as $k => $pr_data) {

                    //foreach ($pr_data['shippings'] as $shipping) {
                    foreach ($pr_data['chosen_shippings'] as $shipping_key => $shipping) {
                        if (!empty($shipping['shipping'])) {
                            $shipping_methods[] = $header_id . '  ' . $shipping['shipping'];

                            $shipping_info_without[] = $shipping['shipping'];
                        }
                    }
                }


                //$shipping_info[] = $header_id.'  '.$suborder['company'];
                //$shipping_info_without[] = $suborder['company'];

                if(!empty($suborder['tracking_number'])){

                    $combine_tracking_number[] = $header_id.'  '.$suborder['tracking_number'];
                }
            }

            $this->shippings_name_with_order_number = implode(', ', $shipping_methods);
            $this->shippings_name_without_order_number = implode(', ', $shipping_info_without);

            $this->companies_name_with_order_number = implode(',</br> ', $companies_info);
            $this->companies_name_without_order_number = implode(', ', $companies_info_without);


            $this->combine_invoice_id = implode("///",$invoice_text);
            if(!empty($combine_tracking_number)){
                $this->combine_tracking_number =implode("///",$combine_tracking_number);
            }
            else{
                $this->combine_tracking_number = false;
            }
        }
        else{
            $order['child_order_details_info'] = array();
        }

        //$this->data['invoice_id_text'] = __('order', array(), $this->context->getLangCode()) . '&nbsp;#' . $this->context->getOrder()->getId();
    }
}