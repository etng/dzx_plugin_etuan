function reduce_trade_amount(id)
{
    var input = $('trade_amount_'+ id) ;
    var block_input = $('block_trade_amount_'+ id) ;
    var new_amount = parseInt(input.value)-1;
    if(new_amount<=0)
    {
        new_amount = 1;
    }
    input.value = new_amount;
    if(block_input)
    {
        block_input.value=input.value
    }
}
function increase_trade_amount(id)
{
    var input = $('trade_amount_'+ id) ;
    var new_amount = parseInt(input.value)+1;
    var block_input = $('block_trade_amount_'+ id) ;
    if(new_amount>99)
    {
        new_amount = 99;
    }
    input.value = new_amount;
        if(block_input)
    {
        block_input.value=input.value
    }
}
function set_trade_amount(id)
{
    var input = $('trade_amount_'+ id) ;
    var block_input = $('block_trade_amount_'+ id) ;
    var new_amount = parseInt(input.value);
    if(new_amount>99)
    {
        new_amount = 99;
    }else if(new_amount<=0)
    {
        new_amount = 1;
    }
    input.value = new_amount;
        if(block_input)
    {
        block_input.value=input.value
    }
}
function addTradeCart(tu_id, tp_id)
{
    var amount = parseInt($('trade_amount_' + tp_id).value);
    var url = 'plugin.php?id=etuan:cart&op=add&tuan_id='+tu_id+"&product_id="+tp_id+"&quantity="+amount+"&inajax=1";
    ajaxget(url, 'cart_dummy','cart_dummy', '', '', function(){
        console.log('get done:' .  url);
        showDialog('商品已成功加入购物车！', 'notice', '购物车', '', 1, '', '<a href="plugin.php?id=etuan:cart">点击这里查看购物车</a>');
    });

    return false;
}