$(document).ready(function(){


//click favorite, wish, or watch button
$(".event-social-button button").click(function(){

	var button_id = this.id;
	var button_array = button_id.split("_");
	var token = $('#token').val();

	console.log(button_array);

	$.ajax({

        type: 'POST',
        url: "/bookmark",
        data: {
        	'_token': token,
        	'action' : button_array[0],
        	'listing_id': button_array[1]
        },
        dataType: 'json',
        success: function (data) {
            console.log(data);
            if(data.message!=0){
                $('#bookmark-buttons').hide();
            	$('#bookmark-message').html('<div class="alert alert-success" role="alert">'+data.message+'</div>');
            	$('#'+button_id).prop('disabled', true);
            }else{
            	$('#bookmark-buttons').hide();
                $('#bookmark-message').html('<div class="alert alert-info" role="alert">Please login.</div>');
            }

            setTimeout(function() {
                $('#bookmark-buttons').show();
                $('#bookmark-message').html('');
            }, 3000);
        },
        error: function (data) {
            console.log('Error:', data.error);
            $('#bookmark-message').html('<div class="alert alert-danger" role="alert">'+data.error+'</div>');
            //document.write(data.responseText);
        }
    });

});


}); //end document ready