$=jQuery;

$(function(){
	$('.set-def-title').click(function(e){
		e.preventDefault();
		var def=$(this).data('def');
		$(this).prev().prev().val(def);
	});
	
	if(!$('input[name="create_map"]').prop('checked')){
		$('#bpp-sitemap-form input[type="checkbox"]').not('input[name="create_map"]').prop('disabled',true);
	}
	
	$('input[name="create_map"]').change(function(){
		if($(this).prop('checked'))
			$('#bpp-sitemap-form input[type="checkbox"]').not('input[name="create_map"]').prop('disabled',false);
		else
			$('#bpp-sitemap-form input[type="checkbox"]').not('input[name="create_map"]').prop('disabled',true);
	});
	
	$('#set-map-def').click(function(e){
		e.preventDefault();
		$('#bpp-sitemap-form input[type="checkbox"]').prop('disabled',false).prop('checked',true);
		var element = document.getElementById('sitemap_update');
		element.value = 86400;
	});
	
	$('#show-placeholders').click(function(e){
		e.preventDefault();
		$('#placeholders').slideToggle('slow',function(){
			if (!$('#placeholders').is(':hidden'))
				$('#show-placeholders').text('Hide Placeholders');
			else
				$('#show-placeholders').text('Show Placeholders');
		});
	});
	
})