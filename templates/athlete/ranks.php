<div class="full">
	<h2>Ranks</h2>
	<div class="cols-md-2">
		<?php if($athlete->ranks){
			foreach($athlete->ranks as $name=>$ranks){ ?>
				<div class="rank">
					<h3><?=$name?></h3>
					<table class="ranks table table-sm">
						<thead>
							<tr>
								<th>Rank</th>
								<th>Name</th>
								<th>Distance</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach($ranks as $i=>$rank){
								$is_me=$athlete->hex_id==$rank['hex_id']; ?>
								<tr<?=$is_me?' class="me"':''?>>
									<td><?=number_format($rank['rank'])?></td>
									<td><?=(!$is_me?'<a href="/athletes/'.$rank['hex_id'].'" alt="'.$rank['name'].'">':'').$rank['name'].(!$is_me?'</a>':'')?></a></td>
									<td><?=number_format($rank['count'])?></td>
								</tr>
							<?php } ?>
						</tbody>
					</table>
				</div>
			<?php }
		} ?>
	</div>
</div>