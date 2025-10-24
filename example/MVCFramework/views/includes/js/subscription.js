$(document).ready(function () {
    function calculateTotal() {
        var numberOfCostitems = $('#numberOfCostitems').val();
        var $result = $('#result');
    
        var total = 0;
        for (let i = 1; i <= numberOfCostitems; i++) {
            $price = "price_" + i;
            $qty = "qty_" + i;
            total += (parseInt(document.getElementById($price).value) * parseInt(document.getElementById($qty).value)|0);            
        }
        $result.text('0');
        $result.text(total);
        $('#total').val(total);
    }
        
    $('.qty').on("change", calculateTotal);
    calculateTotal();
});
