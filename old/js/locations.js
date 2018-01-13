$(document).ready(function(e){
	$('form').each(function(i1,e1){
		form_name=$(e1).find('.form_name').val();
		var locationsField=document.getElementsByClassName('ajax_location');
		var locationIdField=document.getElementsByClassName('ajax_location_id');
		if(locationsField.length){
			$(locationsField).autocomplete({
				messages:{},
				position:{
					my:"left bottom",
       			 	at:"left top",
				},
				response:function(event,ui){
					$(event.target.parentNode).find('.fa-refresh').removeClass('fa-spin');
				},
				search:function(event,ui){
					$(event.target.parentNode).find('.fa-refresh').addClass('fa-spin');
				},
				select: function( event, ui ) {
					$(locationsField).val(ui.item.town+", "+ui.item.county);
					$(locationIdField).val(ui.item.id);
					return false;
				},
				source:"../ajax/locations.php?type=town"
			})
			.data("ui-autocomplete")._renderItem=function(ul,item){
				return $("<li>").append("<a>"+item.town+", "+item.county+"</a>").appendTo(ul);
			};
		}
	});
});