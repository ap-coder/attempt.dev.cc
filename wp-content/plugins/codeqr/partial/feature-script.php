

<script type="text/javascript">

	(function($){
	    $.fn.serializeObject = function(){

	        var self = this,
	            json = {},
	            push_counters = {},
	            patterns = {
	                "validate": /^[a-zA-Z][a-zA-Z0-9_]*(?:\[(?:\d*|[a-zA-Z0-9_]+)\])*$/,
	                "key":      /[a-zA-Z0-9_]+|(?=\[\])/g,
	                "push":     /^$/,
	                "fixed":    /^\d+$/,
	                "named":    /^[a-zA-Z0-9_]+$/
	            };


	        this.build = function(base, key, value){
	            base[key] = value;
	            return base;
	        };

	        this.push_counter = function(key){
	            if(push_counters[key] === undefined){
	                push_counters[key] = 0;
	            }
	            return push_counters[key]++;
	        };

	        $.each($(this).serializeArray(), function(){

	            // skip invalid keys
	            if(!patterns.validate.test(this.name)){
	                return;
	            }

	            var k,
	                keys = this.name.match(patterns.key),
	                merge = this.value,
	                reverse_key = this.name;

	            while((k = keys.pop()) !== undefined){

	                // adjust reverse_key
	                reverse_key = reverse_key.replace(new RegExp("\\[" + k + "\\]$"), '');

	                // push
	                if(k.match(patterns.push)){
	                    merge = self.build([], self.push_counter(reverse_key), merge);
	                }

	                // fixed
	                else if(k.match(patterns.fixed)){
	                    merge = self.build([], k, merge);
	                }

	                // named
	                else if(k.match(patterns.named)){
	                    merge = self.build({}, k, merge);
	                }
	            }

	            json = $.extend(true, json, merge);
	        });

	        return json;
	    };
	})(jQuery);

	jQuery(document).ready(function($){

		var $products = $('#product-list li');

		$('#documents').keyup(function() {
		  var re = new RegExp($(this).val(), "i"); // "i" means it's case-insensitive
		  $products.show().filter(function() {
		      return !re.test($(this).text());
		  }).hide();
		});

		$('#create').on('click', function(){

			var feature_name = $('#feature_name').val();

			if( feature_name.length < 1 ){
				alert('Please give this feature a name.');
				return;
			}

			$('.no-lists').remove();
			var $form = $('#qr-feature-form');//.find('input, select');
			var template = $($form).serializeObject();

			// if there are empty values;
			if( template.option_name.length < 1 || template.option_value.length < 1){
				alert('Please make sure that the option name, value, type, and if applicable min/max character has a value.');
				return;
			}

			/*if( template.option_type == 'number' || template.option_type == 'text' ){
				if( template.min_char.length < 1 || template.max_char.length < 1 ){
					alert('Please make sure that the option name, value, type, and if applicable min/max character has a value.');
					return;
				}
			}*/

			var $feature = $('#feature-list-template').html();
			var $template = JSON.stringify(template);

			$feature = $($feature);

			//$($feature).find('.feature_name').find('code').html(template.feature_name);
			$($feature).find('.option_name').find('code').html(template.option_name);
			$($feature).find('.option_value').find('code').html(template.option_value);
			$($feature).find('.option_type').find('code').html(template.option_type);
			$($feature).find('.min-char').find('code').html(template.min_char);
			$($feature).find('.max-char').find('code').html(template.max_char);
			$($feature).find('.template_part').val($template);
			

			$($feature).appendTo('#features-list');
		});

		$('#features-list').on('click','.btn.delete', function(){

			$(this).closest('.feature.client-file').remove();
		});

		$('#product-list').on('click', '.btn.btn-select',function(){
			var label = $(this).closest('li').find('span').text();
			var product_id = $(this).closest('li').data('product_id');
			$('#selected-products').append('<li>\
				<div class="left" style="padding-right: 5px;">\
					<span><i class="fa fa-bars"></i></span>\
				</div>\
				<div class="none">\
					<span class="btn btn-unselect"><i class="fa fa-minus"></i></span>\
					'+label+'<input type="hidden" name="products[]" value="'+product_id+'" />\
				</div>\
			</li>');

			$(this).closest('li').hide();
		});

		$('#selected-products').on('click', '.btn-unselect', function(){
			var product_id = $(this).closest('div.none').find('input').val();
			$('li[data-product_id="'+product_id+'"]').show();
			$(this).closest('li').remove();
		});

		/*$('#feature-list-form').submit(function(e){
  		e.preventDefault(e);
			var feature_name = $('#feature_name').val();
			var feature_code = $('#feature_code').val();
			var description = $('#description').val();
			var selected_products = false;
  		var feat_cats = '';
			if( $('input[type="checkbox"]:checked').length > 0 ){

				var feat_cats = $('input[type="checkbox"]:checked').serializeObject()
				var feat_cats = feat_cats.feature_categories.length == 1 ? feat_cats.feature_categories[0] : feat_cats.feature_categories.join(",");
			}

			// clone the file input field
			var x = $("#feature_qr_upload"),
			y = x.clone();
			y.attr('style','opacity: 0');
			$('#feature_qr_upload').val('');

			if( $('.feature_image').length > 0 ){
			$(this).closest('form').append('<input type="hidden" value="true" name="image">');
			}
			$(this).closest('form').append('<input type="hidden" name="feat_cats" value="'+feat_cats+'" />');
			$(this).closest('form').append('<input type="hidden" name="feature_name" value="'+feature_name+'" />');
			$(this).closest('form').append('<input type="hidden" name="feature_code" value="'+feature_code+'" />');
			$(this).closest('form').append('<input type="hidden" name="description" value="'+description+'" />');
			$(this).closest('form').append(y);
			
			if( $('#selected-products').find('input').length > 0 ){

				var selected_products = $('#selected-products').find('input').serializeObject();
				var selected_products = selected_products.products.join(',');
				$(this).closest('form').append('<input type="hidden" name="products" value="'+selected_products+'" />');
			}

			$('#feature-list-form').submit();
		});*/

		$('.remove_image').click(function(){
			$(this).closest('.feature_image').remove();
		});

		$('#option_type').on('change', function(e){
			var value = e.target.value;
			if( value != 'no-input' ){
				$('.character-limit').show();
			} else {
				$('.character-limit').hide();
			}
		});

	});

</script>