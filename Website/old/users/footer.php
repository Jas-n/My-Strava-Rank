					</div>
				</div>
			</div>
		</div>
		<div class="hidden" id="loading">
			<svg class="loading_svg" width="40" height="40" viewbox="0 0 40 40">
			  <polygon points="0 0 0 40 40 40 40 0" class="rect" />
			</svg>
		</div>
		<?php if($page->contents['help'] && $page->contents['help']['content']){ ?>
			<div class="modal fade" id="help_modal" tabindex="-1" role="dialog" aria-labelledby="help_modal" aria-hidden="true">
				<div class="modal-dialog modal-lg" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
								<span class="sr-only">Close</span>
							</button>
							<h4 class="modal-title"><?=$page->contents['help']['title']?></h4>
						</div>
						<div class="modal-body">
							<?=$page->contents['help']['content']?>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</div>
			</div>
		<?php }
		$app->get_foot_js();?>
	</body>
</html>