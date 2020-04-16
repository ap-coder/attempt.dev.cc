jQuery(document).ready(function($){

	$('thead input[type="checkbox"]').on('change', function(){
		var checked = $(this).prop('checked');
		$('tbody input[type="checkbox"]').prop('checked', checked);
	});
	
	$('form.ajax-save').on('submit', function(e){
	
		e.preventDefault();
		
		var form = $(this);

		var validate = $(this).attr('data-validate');

		var action = $(this).attr('action');

		var two_step = false;

		if( typeof validate !== typeof undefined && validate !== false ){
			action = validate;
			two_step = true;
		}

		var ajaxObj = {
			url : action,
			data : form.serialize(),
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				$(form).find('p.error.small').remove();
			},
			success: function(d){

				if( typeof d.redirect !== 'undefined' ){
					window.location.assign(d.redirect);
				}
				
				if( typeof d.status !== 'undefined'){

					if( d.status == 'fail'){

						for( var prop in d.error ){
						
							if( d.error.hasOwnProperty( prop ) ) {
						
						    $('[name="'+prop+'"]').after('<p class="error small">'+d.error[prop]+'</p>');
						  } 
						}
						if(typeof d.message !== 'undefined'){
							if(d.message.length){
								$(form).find('.message-box').html('<div class="alert alert-warning">'+d.message+'</div>')
							}
						}

					} else {
						
						if(two_step){

							 formSecondStep(form);

						} else {


							if(typeof d.message !== 'undefined'){
								if(d.message.length){								
									$(form).find('.message-box').html('<div class="alert alert-success">'+d.message+'</div>')
									return;
								}
							}


							window.location.reload();

						}
					}

				}

			},
			complete : function(){
				$(form)[0].reset();
				setTimeout(function(){
					$(form).find('.alert').fadeOut(function(){
						$(this).remove();
					})
				},30000);
			}
		};

		$.ajax(ajaxObj);

	});
})

function formSecondStep(form){
	form.get(0).submit();
}