var msr={
	page:1,
	init:function(){
		msr.load_sections();
		$('.add-rank').click(function(){
			window.location=this.dataset.link;
		});
	},
	load_sections:function(){
		$('body').on('click','.js-load',function(e){
			e.stopPropagation();
			var is_nav=!!$(e.target).parents('nav').length;
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
				'/t_'+what+'.php',
				{
					id:target_id,
					page:msr.page
				},
				function(){
					$('main').get(0).className=what;
				}
			);
		});
	}
};
msr.init();