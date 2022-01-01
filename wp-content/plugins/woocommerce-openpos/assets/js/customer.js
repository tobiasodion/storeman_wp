(function($) {


    $(document).ready(function(){
        let client_time_offset = new Date().getTimezoneOffset();
        $.ajax({
            url : data_url,
            type: 'get',
            dataType: 'json',
            data: {action:'op_customer_table_order', key:verify_key, client_time_offset:client_time_offset},
            success: function(response){
                $('#op-message').html(response['message']);
                $('#op-customer-confirm-btn').hide();
                if(response.status == 1)
                {
                    let data = response['data'];

                    var template = ejs.compile(data_template['template'], {});
                    if(data_template['lang'])
                    {
                        data['lang'] = data_template['lang'];
                    }
                    var html = template(data);
                    $('#op-message').html(html);
                    $('#op-customer-confirm-btn').show();
                    var url = data['url'];
                    $('#op-customer-confirm-btn').attr('href',url);
                }else{

                }
                
            },
            error: function(){
               
            }
        });

    });



}(jQuery));