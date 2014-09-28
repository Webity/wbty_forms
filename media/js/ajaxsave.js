// Javascript file

function loadWbtyAjax() {
	if(!window.jQuery)
	{
	   var script = document.createElement('script');
	   script.type = "text/javascript";
	   script.src = "//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js";
	   document.getElementsByTagName('head')[0].appendChild(script);
	   setTimeout(function() {loadWbtyAjax();}, 1500);
	} else {
		jQuery(document).on('submit', '.wbty_form form', function(e) {
			e.preventDefault();
			data = jQuery(this).serialize();
			url = window.juri_root + 'index.php?option=com_wbty_forms&task=form.validateForm';
			
			$submitBtn = jQuery(this).find('.submit-container button');
			$submitBtn.prop('disabled', true);
			btnText = $submitBtn.html();
			
			i = 0;
			var loadingAnimation = setInterval(function() {
				i = ++i % 4;
				$submitBtn.html("Sending"+Array(i+1).join("."));
			}, 150);
			
			jQuery.ajax({
				url: url,
				data: data,
                dataType: 'json',
				type: "POST",
				context: this,
				success: function(resp) {
					console.log(resp);				
						
					$submitBtn.prop('disabled', false);
					clearInterval(loadingAnimation);
					$submitBtn.html(btnText);
					
                    if (resp.status) {
                    	jQuery(this).closest('.wbty_form').html(jQuery('.wbty_form .thank-you').html());
                    	if (jQuery('#conversion_code').length > 0) {
	                    	jQuery('#conversion_code').append(jQuery('#conversion_code').val());
	                    }
	                    if (resp.redirect) {
	                    	window.location = resp.redirect;
	                    }
                    } else {
                    	if (typeof Recaptcha != 'undefined') {
                        	Recaptcha.reload();
                    		jQuery('#recaptcha_table input').addClass('invalid').closest('.control-group').find('.control-label').addClass('invalid').after('<span id="failure-notice" style="display: block; color: red;">Please enter the text you see in the box below.</span>');
                        } else {
                            alert('Error submitting');
                        }
                    }
				},
				error: function (x, status, error) {
					console.log(status + '-' + error);
					console.log(x);
					
					alert('There was an error submitting the form. Please try again.');
					
					$submitBtn.prop('disabled', false);
					clearInterval(loadingAnimation);
					$submitBtn.html(btnText);
				}
			});
		});
	}
}

loadWbtyAjax();
