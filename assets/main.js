jQuery(function($) {
	$('#post_webhook_add_new, #discord_webhooks_add_new').on('click', function(e){
		e.preventDefault();
		$('.discord_webhook_settings_section_post').append( $('.wp_discord_post_plus_post_webhook_url_single_section').eq(-1).clone() );
		$('input', $('.wp_discord_post_plus_post_webhook_url_single_section').eq(-1)).val('');
		$('select', $('.wp_discord_post_plus_post_webhook_url_single_section').eq(-1)).val('-1');
		$('.wp_discord_post_plus_post_webhook_url_single_section').eq(-1).css('display', 'none');
		$('.wp_discord_post_plus_post_webhook_url_single_section').eq(-1).css('background', 'yellow');
		$('.wp_discord_post_plus_post_webhook_url_single_section').eq(-1).fadeIn('slow', function(){
			$('.wp_discord_post_plus_post_webhook_url_single_section').eq(-1).css('background', 'transparent');
		});
	});

	$('#discord_webhooks_add_new').on('click', function(e){
		e.preventDefault();
		$('.discord_webhook_settings_section_woocommerce').append( $('.discord_webhook_settings_single_section').eq(-1).clone() );
		$('input', $('.discord_webhook_settings_single_section').eq(-1)).val('');
		$('select', $('.discord_webhook_settings_single_section').eq(-1)).val('-1');
		$('.discord_webhook_settings_single_section').eq(-1).css('display', 'none');
		$('.discord_webhook_settings_single_section').eq(-1).css('background', 'yellow');
		$('.discord_webhook_settings_single_section').eq(-1).fadeIn('slow', function(){
			$('.discord_webhook_settings_single_section').eq(-1).css('background', 'transparent');
		});
	});
});