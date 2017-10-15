var loading,
	result_count=0,
	results,
	tabs=0;
$(document).ready(function(e){
	var fail=false;
	loading=document.getElementById('searching');
	results=document.getElementById('results');
	var url="../ajax/search.php?term="+term;
	if(!window.EventSource){
		$.ajax({
			context:this,
			dataType:'json',
			url:url
		}).success(function(data){
			for(var i=0;i<data.length;i++){
				process(data[i]);
			}
		})
		.fail(function(){
			$(this.parentNode).append("<div class='alert alert-danger' role='alert'>Error getting search results.</div>");
			setTimeout(function(){
				$('#'+ms).hide();
			},3000);
		})
		.done(function(){
			loading.className='hidden';
		});
	}else{
		searcher=new EventSource(url+"&method=stream");
		searcher.onmessage=function(e){
			process(JSON.parse(e.data));
		}
	}
});
function process(jsondata){
	if(jsondata.count>0){
		var keys=Object.keys(jsondata.data[0]);
		$('#location').text("'"+jsondata.name+"'");
		var li='<li class="nav-item"><a class="nav-link';
		if(tabs==0){
			li+=' active';
		}
		li+='" href="#'+jsondata.slug+'" role="tab" data-toggle="tab">'+jsondata.name+' <span class="label label-default">'+jsondata.count+'</label></a></li>';
		$('#results_nav').append(li);
		out='<div role="tabpanel" class="tab-pane';
		if(tabs==0){
			out+=' active';
		}
		out+='" id="'+jsondata.slug+'"><div class="table-responsive"><table class="table table-hover"><thead><tr>';
		for(i=0;i<keys.length;i++){
			if(keys[i]!='View'){
				out+="<th>"+keys[i]+"</th>";
			}
		}
		out+="</tr></thead><tbody>";
		for(i=0;i<jsondata.data.length;i++){
			out+="<tr>";
			for(j=0;j<keys.length;j++){
				if(keys[j]!='View'){
					out+="<td>"+jsondata.data[i][keys[j]]+"</td>";
				}
			}
			out+="</tr>";
		}
		$(results).append(out+"</tbody></table></div></div>");
		tabs++;
		result_count+=jsondata.count;
	}
	if(jsondata.status=='close'){
		searcher.close();
		loading.className='hidden';
		$('#title').html("Search <small class='text-muted'>Showing "+result_count+" results for '"+term+"'</small>");
	}
}