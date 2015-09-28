(function($) {
	$.entwine('ss', function($) {

		$('select.subsite-filter').entwine({

			onchange: function() {
				var subsiteID = $(this).val();
				if(subsiteID !== '') {
					$('.ss-gridfield-items:first .ss-gridfield-item').each( function(index, item) {
						$(item).show();
						if($(item).attr('data-subsite-id') != subsiteID) {
							$(item).hide();
						}
					});
				} else {
					$('.ss-gridfield-items:first .ss-gridfield-item').show();
				}
			}

		});

	});
}(jQuery));