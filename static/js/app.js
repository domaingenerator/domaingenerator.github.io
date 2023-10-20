	/*
	*
	* ---------------------------
	* | Domain Name Generator   |
	* ---------------------------
	*
	* @Author: A.I Raju
	* @License: MIT
	* @Copyright: 2023
	*
	*/
	
	
	$(document).on("submit", ".generator-form", function(e){
		e.preventDefault();
		var msg = $(".message");
		var btn = $(".gd");
		
		msg.html('Generating...');
		
		btn.attr('disabled', 'disable');
		
		$.ajax({
			type: 'POST',
			url: 'ajax.php',
			dataType: 'JSON',
			data: $(this).serialize(),
			
			error: function(xhr, status, message)
			{
				msg.html( '<div class="alert alert-danger rounded-1 p-2"><i class="bi bi-exclamation-triangle-fill me-1"></i> '+message+' </div>' );
				btn.removeAttr('disabled');
				send_event({"error":"http_error"}, 'error');
			},
			
			success: function(data)
			{
				if( data.status === 'success' )
				{
					msg.html( '<div class="alert alert-success rounded-1 p-2"><i class="bi bi-check-circle-fill me-1"></i> '+data.message+' </div>' );
					btn.removeAttr('disabled');
					$("#gd").html(data.data);
					send_event({"success":"domain_generate_success"}, 'success');
				}else{
					
					msg.html( '<div class="alert alert-danger rounded-1 p-2"><i class="bi bi-exclamation-triangle-fill me-1"></i> '+data.message+' </div>' );
					btn.removeAttr('disabled');
					send_event({"error":"domain_generate_error"}, 'error');
				}
								
			}
		})
		
	})

	/* Google Analytics Event Handler */
	function send_event( events, eventName = 'user_activity' )
	{
		if( typeof gtag == 'function' ){
			if(typeof events == 'object' || !Array.isArray( events ) || events !== null )
			{
				gtag( 'event', eventName, events );
			}
		}
	}
	
	$(".event").on("click", function(){
		
		var tagName = $(this)[0].tagName;
		var tagValue = $(this)[0].textContent.trim();
		
		send_event({tagName:tagValue}, tagName);
		
	})
