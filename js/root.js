var msr={
	page:1,
	init:function(){
		msr.load_sections();
	},
	load_sections:function(){
		$('body').on('click','.js-load',function(e){
			e.stopPropagation();
			var is_nav=!!$(e.target).parents('nav').length;
			console.log(is_nav);
			var target=$(e.target).hasClass('js-load')?$(e.target).get(0):$(e.target).parents('.js-load').get(0);
			var target_id=target.dataset?target.dataset.id:false;
			var from=php.strpos(target.className,'js-load');
			var to=php.strpos(target.className,' ',from+8);
			var what=false;
			if(to){
				what=target.className.substring(from+8,to);
			}else{
				what=target.className.substring(from+8);
			}
			$('main').load(
				'/templates/'+what+'.php',
				{
					id:target_id,
					page:msr.page
				},
				function(){
					if(is_nav){
						$('nav section').removeClass('active');
						$(target).addClass('active');
						$('nav').addClass('has-active');
					}
					$('main').removeClass();
					$('main').addClass(what);
				}
			);
		});
	}
};
msr.init();