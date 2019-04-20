jQuery(function($) {
	$('#post_webhook_add_new, #discord_webhooks_add_new').on('click', function(e){
		e.preventDefault();

		var index = parseInt( $('.wp_discord_post_plus_post_webhook_url_single_section').eq(-1).attr('data-index') ) + 1;

		$('.discord_webhook_settings_section_post').append($('.wp_discord_post_plus_post_webhook_url_single_section').eq(-1).clone());
		$('.wp_discord_post_plus_post_webhook_url_single_section').eq(-1).attr('data-index', index)

		$('input', $('.wp_discord_post_plus_post_webhook_url_single_section').eq(-1)).val('');
		$('input', $('.wp_discord_post_plus_post_webhook_url_single_section').eq(-1)).eq(0).attr('name', 'wp_discord_post_plus_post_webhook_url[' + index + '][chatroom]');
		$('input', $('.wp_discord_post_plus_post_webhook_url_single_section').eq(-1)).eq(1).attr('name', 'wp_discord_post_plus_post_webhook_url[' + index + '][webhook]');

		$('select', $('.wp_discord_post_plus_post_webhook_url_single_section').eq(-1)).val('-1');
		$('select', $('.wp_discord_post_plus_post_webhook_url_single_section').eq(-1)).attr('name', 'wp_discord_post_plus_post_webhook_url[' + index + '][category]');



		$('.wp_discord_post_plus_post_webhook_url_single_section').eq(-1).css('display', 'none');
		$('.wp_discord_post_plus_post_webhook_url_single_section').eq(-1).css('background', 'yellow');
		$('.wp_discord_post_plus_post_webhook_url_single_section').eq(-1).fadeIn('slow', function(){
			$('.wp_discord_post_plus_post_webhook_url_single_section').eq(-1).css('background', 'transparent');
		});
	});

	$('#discord_webhooks_add_new').on('click', function(e){
		e.preventDefault();

		var index = parseInt( $('.discord_webhook_settings_single_section').eq(-1).attr('data-index') ) + 1;


		$('.discord_webhook_settings_section_woocommerce').append($('.discord_webhook_settings_single_section').eq(-1).clone());
		$('.discord_webhook_settings_single_section').eq(-1).attr('data-index', index);

		$('input', $('.discord_webhook_settings_single_section').eq(-1)).val('');
		$('input', $('.discord_webhook_settings_single_section').eq(-1)).eq(0).attr('name', 'wp_discord_post_plus_settings_webhooks_input[' + index + '][chatroom]');
		$('input', $('.discord_webhook_settings_single_section').eq(-1)).eq(1).attr('name', 'wp_discord_post_plus_settings_webhooks_input[' + index + '][webhook]');


		$('select', $('.discord_webhook_settings_single_section').eq(-1)).val('-1');
		$('select', $('.discord_webhook_settings_single_section').eq(-1)).attr('name', 'wp_discord_post_plus_settings_webhooks_input[' + index + '][category]');


		$('.discord_webhook_settings_single_section').eq(-1).css('display', 'none');
		$('.discord_webhook_settings_single_section').eq(-1).css('background', 'yellow');
		$('.discord_webhook_settings_single_section').eq(-1).fadeIn('slow', function(){
			$('.discord_webhook_settings_single_section').eq(-1).css('background', 'transparent');
		});
	});

	var frame; 

	$('.upload-button').on('click', function (event) {
		event.preventDefault();
    
		if ( frame ) {
			frame.open();
			return;
		}

		// Create a new media frame
		frame = wp.media({
			title: 'Select an Avatar',
			button: {
			  text: 'Select'
			},
			multiple: false  // Set to true to allow multiple files to be selected
		});
		
		frame.on('select', function () {
			var attachment = frame.state().get('selection').first().toJSON();
			$('input[name=wp_discord_post_plus_avatar_url]').val( attachment.url );
		});

		frame.open();
	});
});