var map;
function initMap(){
	var map=document.getElementById('athlete_map');
	$(map).height($(map).width());
	map = new google.maps.Map(map,{
		center: {lat: -34.397, lng: 150.644},
		zoom: 8
	});
}