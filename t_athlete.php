<?php require('init.php');
if(basename($_SERVER['SCRIPT_NAME'],'.php')=='t_athlete' && $_POST){
	$athlete=$_POST['id'];
}else{
	$athlete=$_GET['id'];
}
$athlete=new Jasn\MSR\athlete($athlete);
require('t_header.php');
$sections=array(
	'bio',
	'distance',
	'altitude',
	#'clubs',
	'ranks'
);
$bootstrap->progress($athlete->points,$athlete->next_rank_points,'','warning');?>
<!-- SVG background = transparent > background colour to show progress?-->
<svg id="svg-road" height="10" width="100" xmlns="http://www.w3.org/2000/svg">
	<path d="m13.5 -0.5c5.8 2 60.3 21.6 67 63 4.5 27.9-14.4 54.1-31 77-24.1 33.3-36.7 30.7-44 52-12.6 37.1 14.3 78.6 25 95 26.2 40.3 43.4 33.1 58 64 24.9 52.8-6.1 114.5-9 120-14.9 28.7-25.1 24.6-43 57-10.9 19.7-32.3 58.4-23 98 8.7 37.3 35.3 35.4 61 92 10.2 22.5 19 42.6 14 66-4.4 20.7-15.1 23.2-42 60-24.7 33.9-39 54-41 82-0.6 8.4-2.9 40.2 17 61 20.6 21.5 44.8 10.3 60 33 8.5 12.7 12.5 33.4 3 48-4.1 6.3-9.9 10-14 13.5" fill="none" id="svg-road-path" stroke-width="3"></path>
</svg>
<div class="sections">
	<?php foreach($sections as $section){ ?>
		<section class="<?=$section?> scroll-snap">
			<?php include(ROOT.'/templates/athlete/'.$section.'.php'); ?>
		</section>
	<?php } ?>
</div>
<?php require('t_footer.php');