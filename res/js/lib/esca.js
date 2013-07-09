bkLib.onDomLoaded(function() {
	$('.esca').each(function() {
		new nicEditor({iconsPath : '".$this->locations['resources']."/img/nicEditIcons.gif"."'}).panelInstance(this.id);
	});
});
