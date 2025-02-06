jQuery(document).ready(function ($) {
    _paq = _paq || [];

    tracktasticOrderData.items.forEach(function (item) {
        _paq.push(['addEcommerceItem',
            item.sku,
            item.name,
            item.category_name,
            item.price,
            item.quantity
        ]);
    });

    _paq.push(['trackEcommerceOrder',
        tracktasticOrderData.order_number,
        tracktasticOrderData.total,
        tracktasticOrderData.subtotal,
        tracktasticOrderData.total_tax,
        tracktasticOrderData.shipping_total,
        false
    ]);
});