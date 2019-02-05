var bootstrap={
	table:{
		classes:'table table-hover table-striped'
	},
	theme:[
		'primary',
		'secondary',
		'success',
		'danger',
		'warning',
		'info',
		'light',
		'dark'
	],
	alert:function(type,content,args){
		'use strict';
		var html='';
		var data=[];
		if(bootstrap.theme.indexOf(type)===-1){
			type='primary';
		}
		if(args){
			if(args.data){
				for(key in args.data){
					data.push('data-'+key+'="'+args.data[key]+'"');
				}
			}
		}
		html+='<div class="alert alert-'+type+(args && args.class?' '+args.class:'')+(args && args.dismissible?' alert-dismissible fade show':'')+'"'+(args && args.data?' '+data.join(' '):'')+(args && args.id?' id="'+args.id+'"':'')+' role="alert">';
			if(args && args.dismissible){
				html+='<button type="button" class="close" data-dismiss="alert" aria-label="Close">'+
					'<span aria-hidden="true">&times;</span>'+
				'</button>';
			}
			html+=content+
		'</div>';
		return html;
	},
	breadcrumb:function(links){
		if(!links.length){
			return false;
		}
		var html=`<ol class="breadcrumb">`;
			for(var i=0;i<links.length;i++){
				if(i+1!==links.length){
					if(!links[i].id){
						links[i].id=0;
					}
					html+='<li class="breadcrumb-item" data-id="'+links[i].id+'" data-load="'+links[i].load+'">'+links[i].name+'</li>';
				}else{
					html+='<li class="breadcrumb-item active">'+links[i].name+'</li>';
				}
			}
		html+=`</ol>`;
		return html;
	},
	pagination:function(count,link,page){
		'use strict';
		var out		='';
		var pages	=Math.ceil(count/server.ITEMS_PER_PAGE);
		page		=Number(page);
		if(pages>1 || page>1){
			out+='<ul class="pagination">';
				if(pages<=10){
					for(var i=1;i<=pages;i++){
						out+='<li class="page-item'+(page==i || (i==1 && !page)?' active':'')+'">'+bootstrap.pagination_link(i,link)+'</li>';
					}
				}else{
					var toout=0;
					// First Page
					out+='<li class="page-item'+(page==1 || !page?' active':'')+'">'+bootstrap.pagination_link(1,link)+'</li>';
					// Page= 1-4
					if(page<5){
						if(page<4){
							toout=6;
						}else{
							toout=7;
						}
						for(i=2;i<toout;i++){
							out+='<li class="page-item'+(page==i?' active':'')+'">'+bootstrap.pagination_link(i,link)+'</li>';
						}
						out+='<li class="page-item disabled"><a class="page-link"><span aria-hidden="true">&hellip;</span></a></li>';
					}
					// Page>=5
					else if(page<pages-5){
						out+='<li class="page-item disabled"><a class="page-link"><span aria-hidden="true">&hellip;</span></a></li>';
						for(i=page-2;i<page;i++){
							out+='<li class="page-item'+(page==i?' active':'')+'">'+bootstrap.pagination_link(i,link)+'</li>';
						}
						out+='<li class="page-item active">'+bootstrap.pagination_link(page,link)+'</li>';
						for(i=page+1;i<=page+2;i++){
							out+='<li class="page-item'+(page==i?' active':'')+'">'+bootstrap.pagination_link(i,link)+'</li>';
						}
						out+='<li class="page-item disabled"><a class="page-link"><span aria-hidden="true">&hellip;</span></a></li>';
					}
					// If page last 5
					else if(page>pages-4){
						out+='<li class="page-item disabled"><a class="page-link"><span aria-hidden="true">&hellip;</span></a></li>';
						if(pages-page==3){
							toout=5;
						}else{
							toout=4;
						}
						for(i=pages-toout;i<pages;i++){
							out+='<li class="page-item'+(page==i?' active':'')+'">'+bootstrap.pagination_link(i,link)+'</li>';
						}
					}else{
						out+='<li class="page-item disabled"><a class="page-link"><span aria-hidden="true">&hellip;</span></a></li>';
						for(i=page-2;i<page;i++){
							out+='<li class="page-item'+(page==i?' active':'')+'">'+bootstrap.pagination_link(i,link)+'</li>';
						}
						out+='<li class="page-item active">'+bootstrap.pagination_link(page,link)+'</li>';
						for(i=page+1;i<=page+2;i++){
							out+='<li class="page-item'+(page==i?' active':'')+'">'+bootstrap.pagination_link(i,link)+'</li>';
						}
						out+='<li class="page-item disabled"><a class="page-link"><span aria-hidden="true">&hellip;</span></a></li>';
					}
					// Last Page
					out+='<li class="page-item'+(page==pages?' active':'')+'">'+bootstrap.pagination_link(pages,link)+'</li>';
				}
			out+='</ul>';
		}
		return out;
	},
	pagination_link:function(page,link){
		return '<a class="page-link" href="'+link+'?page='+page+'">'+page+'</a>';
	}
};