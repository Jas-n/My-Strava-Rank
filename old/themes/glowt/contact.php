<div class="row">
	<div class="col-md-offset-2 col-md-8 page-header title-bar">
		<h1><?=$page_title?></h1>
	</div>
</div>
<div class="row title-shadow"></div>
<div class="row<?=!SHOW_CONTACT_MAP || !COMPANY_LATLNG?' last':''?>">
	<div class="col-md-4 col-md-offset-2">
		<?=$app->get_messages().
		$form->get_form()?>
	</div>
	<div class="col-md-3 col-md-offset-1" id="contact-details">
		<h2>Company Details</h2>
		<p><?=COMPANY_ADDRESS?></p>
		<p>Telephone: <?=COMPANY_TEL?></p>
	</div>
</div>
<?php if(SHOW_CONTACT_MAP && COMPANY_LATLNG){ ?>
	<div class="row last">
		<?php list($lat,$lng)=explode(',',COMPANY_LATLNG);
		$app->add_to_foot("<script src='https://maps.googleapis.com/maps/api/js'></script>");
		$app->add_to_foot('<script>
			var company="'.SITE_NAME.'";
			var lat='.$lat.';
			var lng='.$lng.';
			$(document).ready(function(e){
				var map;
				function offsetCenter(latlng,offsetx,offsety) {
					var scale=Math.pow(2,map.getZoom());
					var nw=new google.maps.LatLng(
						map.getBounds().getNorthEast().lat(),
						map.getBounds().getSouthWest().lng()
					);
					var worldCoordinateCenter = map.getProjection().fromLatLngToPoint(latlng);
					var pixelOffset=new google.maps.Point((offsetx/scale) || 0,(offsety/scale) ||0);
					var worldCoordinateNewCenter = new google.maps.Point(
						worldCoordinateCenter.x - pixelOffset.x,
						worldCoordinateCenter.y + pixelOffset.y
					);
					var newCenter = map.getProjection().fromPointToLatLng(worldCoordinateNewCenter);
					map.setCenter(newCenter);
				}
				google.maps.event.addDomListener(window,"load",initialize);
				function initialize() {
					var myLatlng=new google.maps.LatLng(lat,lng);
					var mapOptions={
						center:myLatlng,
						zoom:15
					}
					map=new google.maps.Map(document.getElementById("map"),mapOptions);
					// offsetCenter(myLatlng,100,0);
					// To add the marker to the map, use the "map" property
					var marker = new google.maps.Marker({
						position: myLatlng,
						map: map,
						title:company
					});
					var infowindow = new google.maps.InfoWindow({
						content:"<img src=\'../images/email_logo.png\'>"
					});
					infowindow.open(map,marker);
				}
			});
		</script>');?>
		<div class="row">
			<div class="contact" id='map'></div>
		</div>
	</div>
<?php } ?>