jQuery(document).ready(function($) {
	jQuery('#accordion').dcAccordion({
		eventType: 'hover',
		autoClose: true,
		saveState: false,
		disableLink: false,
		showCount: false,
		menuClose: false,
		hoverDelay   : 50,
		speed: 'slow' // slow, fast
	});
}); 