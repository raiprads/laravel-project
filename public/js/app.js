$(document).ready(function(){


//click favorite, wish, or watch button
$(".event-social-button button").click(function(){

	var button_id = this.id;
	var button_array = button_id.split("_");
	var token = $('#token').val();

	console.log(button_array);

	$.ajax({

        type: 'POST',
        url: "/social",
        data: {
        	'_token': token,
        	'action' : button_array[0],
        	'helsinki_event_id': button_array[1]
        },
        dataType: 'json',
        success: function (data) {
            console.log(data);
            if(data.message!=0){
            	alert(data.message);
            	$('#'+button_id).prop('disabled', true);
            }else{
            	alert(data.message);
            }
        },
        error: function (data) {
            console.log('Error:', data);
            document.write(data.responseText);
        }
    });

});


}); //end document ready