{if $product.free_shipping == "Y"}
    {include "views/products/components/product_label.tpl"
        label_meta    = "ty-product-labels__item--shipping"
        label_text    = __("free_shipping")
        label_mini    = $product_labels_mini
        label_static  = $product_labels_static
        label_rounded = $product_labels_rounded
    }
{/if}