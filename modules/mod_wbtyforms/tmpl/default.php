<?php
/**
 * @package		Joomla.Site
 * @subpackage	mod_footer
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

JHtml::_ ( 'behavior.formvalidation' );
if($item) : ?>
    <div class="wbty_form">
	<?php if ($form) { ?>
		<form method="post" class="form-validate form-horizontal">
        
        	<?php if ($params->get('display_legend')) : ?>
        		<legend><?php echo $item->name; ?></legend>
            <?php endif; ?>
            
            <div class="page page1 active" data-page="1">
			
            	<?php
                $i = $f = 1;
				$multiPage = false;
                foreach ($form->getFieldset('fields') as $field) {
					
					// Check if we need to create the next page
					if ($field->__get('pagebreak')) {
						$f++;
						$multiPage = true;
						echo '<div class="clear"></div></div><div class="page page'.$f.'" data-page="'.$f.'">';
					} else {
						$class = $field->__get('class');
						echo '<div class="control-group group'.$i.''. ($class ? ' '.$class : '') . ' ' . str_replace('calendar', 'calendar_type', strtolower($field->__get('type'))) .'">'.str_replace('<label','<label class="control-label"',$field->__get('label')).'<div class="controls">'.$field->__get('input').'</div></div>';
						$i++;
					}
                }
                ?>
                
                <div class="control-group submit-group">
                    <div class="controls">
                      <input type="submit" value="<?php echo ($item->submit_text ? $item->submit_text : 'Submit'); ?>" />
                    </div>
                </div>
                <div class="clear"></div>
            
            </div>
            
            <?php if($multiPage === true) : ?>
                <div class="control-group pager-group">
                    <div class="controls">
                    	<a class="prevPage pager button disabled" data-direction="prev">Previous</a>
                        <a class="nextPage pager button" data-direction="next">Next</a>
					</div>
                </div>
            <?php endif; ?>
        </form>
        <?php
		ob_start();
		?>
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
						url = '<?php echo JRoute::_('index.php?option=com_wbty_forms&task=form.validateForm&id='.$params->get('form')); ?>';
						
						jQuery.ajax({
							url: url,
							data: data,
							type: "POST",
                			dataType: 'json',
							context: this,
							success: function(resp) {
								console.log(resp);
								if (resp.html) {
		                    		jQuery(this).closest('.wbty_form').html(resp.html);
		                    	} else {
			                    	jQuery(this).closest('.wbty_form').html(jQuery('.wbty_form .thank-you').html());
			                    }
							}
						});
					});
                    <?php if ($multiPage === true) : ?>
                    jQuery(document).on('click', '.pager', function(e) {
                    	e.preventDefault();
                    	var $activePage = jQuery('div.active');
                        var activeNo = parseInt($activePage.data('page'));
                        
                        if (jQuery(this).data('direction') == 'prev') {
	                        var $nextPage = jQuery('div.page' + (activeNo - 1));
							var $nextNextPage = jQuery('div.page' + (activeNo - 2));
                        } else {
                        	// run validation before going to next page
							document.formvalidator.isValid(jQuery(this).closest('form').get(0));

							if ($activePage.find('label.invalid').length > 0) {
								return false;
							}
                        	var $nextPage = jQuery('div.page' + (activeNo + 1));
                            var $nextNextPage = jQuery('div.page' + (activeNo + 2));
                        }
                        
                        if ($nextPage.length) {
	                      	$activePage.fadeOut().removeClass('active');
    	                    $nextPage.fadeIn().addClass('active');

    	                    $nextPage.find('.invalid').removeClass('invalid');
                        } 
                        
                        if (!$nextNextPage.length && $nextPage.length) {
                        	jQuery('.pager').removeClass('disabled');
                        	if ($nextPage.data('page') > 1) {
                            	jQuery('.nextPage').addClass('disabled');
                            } else {
                            	jQuery('.prevPage').addClass('disabled');
                            }
                        }
                    });
                    <?php endif; ?>
				}
			}
			
            loadWbtyAjax();
		<?php 
		$script = ob_get_contents();
		ob_end_clean();
		JFactory::getDocument()->addScriptDeclaration($script);
		?>
        <div style="display:none;" class="thank-you">
	        <?php //echo $item->thank_you_message; ?>
        </div>
    <?php } else {
		echo $item->thank_you_message;
	} ?>
    </div>
    
    
    
<?php endif; ?>