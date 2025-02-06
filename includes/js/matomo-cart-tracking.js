jQuery(document).ready(function ($) {
    if (typeof tracktasticCartData !== 'undefined' && tracktasticCartData.items.length > 0) {
        _paq = _paq || [];
        tracktasticCartData.items.forEach(function (item) {
            _paq.push(['addEcommerceItem',
                item.sku,
                item.name,
                item.category,
                item.price,
                item.quantity
            ]);
        });
        _paq.push(['trackEcommerceCartUpdate', tracktasticCartData.cartTotal]);
    }
});