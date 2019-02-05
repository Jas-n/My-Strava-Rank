var msr={
	id:0,
	is_app:false,
	loaded:[],
	user:0,
	init:function(){
		if(msr.is_app){
			this.app_init();
		}
		this.watch_links();
	},
	app_init:function(){
		console.log(cordova.file);
	},
	ajax:function(file,method,callback,data){
		$.ajax({
			dataType:'json',
			type:"POST",
			url:'https://api.mystravarank.com/ajax.php',
			data:{
				class:file,
				data:data,
				method:method,
				key:'b3de695507ba629509ef810d00ca6006'
			},
			success:function(json){
				callback(json);
			},
			error: function (json) {
				console.log(json.responseText);
			}
		});
	},
	load_partial:function(partial,id){
		if(id){
			msr.id=id;
			$('main').attr('data-id',msr.id);
		}else{
			msr.id=0;
			$('main').attr('data-id',msr.id);
		}
		$('main').load('partials/'+partial+'.html',function(){
			$('.breadcrumb').remove();
			$('main').attr('id',partial);
			$('.hero').attr('src','images/'+partial+'.jpg');
			if(msr.loaded.indexOf(partial)===-1){
				$('head').append('<link rel="stylesheet" href="css/'+partial+'.css">');
				$('body').append('<script src="js/'+partial+'.js"></script>');
				msr.loaded.push(partial);
			}
			if($('footer .col[data-load="'+partial+'"]').length){
				$('footer .col').removeClass('active');
				$('footer .col[data-load="'+partial+'"]').addClass('active');
			}
			if(typeof window[partial].init!=='undefined'){
				window[partial].init();
			}
			if(!window[partial].name){
				window[partial].name=partial;
			}
			$('h1').text(window[partial].name);
		});
	},
	ordinal:function(number){
		var j = number % 10,
        k = number % 100;
		if (j == 1 && k != 11) {
			ordinal = "st";
		}else if (j == 2 && k != 12) {
			ordinal = "nd";
		}else if (j == 3 && k != 13) {
			ordinal = "rd";
		}else{
			ordinal = "th";
		}
		return number+ordinal;
	},
	watch_links:function(){
		this.load_partial('home');
		$('body').on('click','[data-load]',function(){
			msr.load_partial($(this).data('load'),$(this).data('id'));
		});
	}
};
if(typeof cordova!=='undefined'){
	msr.is_app=true;
	document.addEventListener('deviceready',msr.init,false);
}else{
	msr.init();
}