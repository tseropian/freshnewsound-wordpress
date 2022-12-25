function cleanArchivesInit() {
	// Define the togglers and the items to be resized
	var monthTitles = document.getElementsByClassName('monthtitle');
	var monthPostLists = document.getElementsByClassName('postspermonth');

	// Create the accordian, but since IE sucks, don't let it use the fade in/out
	var IEversion = 0;
	if (navigator.appVersion.indexOf('MSIE') != -1) {
		tempVersion = navigator.appVersion.split('MSIE')
		IEversion = parseFloat(tempVersion[1]);
	}
	if (IEversion != 0) {
		var archiveAccordion = new fx.Accordion(monthTitles, monthPostLists, {opacity: false, duration: 500});
	}
	else {
		var archiveAccordion = new fx.Accordion(monthTitles, monthPostLists, {opacity: true, duration: 500});
	}

	// If an anchor was provided, open it, otherwise open the latest month's posts
	function cleanArchivesCheckHash() {
		var found = false;
		monthTitles.each(function(span, i) {
			if (window.location.href.indexOf(span.title) > 0) {
				archiveAccordion.showThisHideOpen(monthPostLists[i]);
				found = true;
			}
		});
		return found;
	}
	if (!cleanArchivesCheckHash()) archiveAccordion.showThisHideOpen(monthPostLists[0]);
}