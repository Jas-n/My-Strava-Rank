<?php # 1.8.1 - For Boootstrap 4
namespace Formation\Core;
class bootstrap{
	public $colours;
	public $table;
	public $theme;
	# 25/10/2017 15:04
	public function __construct(){
		$this->colours=(object) array(
			'grays'=>array(
				100=>'#f8f9fa',
				200=>'#e9ecef',
				300=>'#dee2e6',
				400=>'#ced4da',
				500=>'#adb5bd',
				600=>'#868e96',
				700=>'#495057',
				800=>'#343a40',
				900=>'#212529'
			),
			'theme'=>(object) array(
				'primary'	=>'#007bff',
				'secondary'	=>'#868e96',
				'success'	=>'#5dc145',
				'danger'	=>'#dc3545',
				'warning'	=>'#f0ae20',
				'info'		=>'#17a2b8',
				'light'		=>'#f8f9fa',
				'dark'		=>'#343a40'
			)
		);
		$this->table=(object) array(
			'classes'=>'table table-hover table-striped'
		);
		$this->theme=array(
			'primary',
			'secondary',
			'success',
			'danger',
			'warning',
			'info',
			'light',
			'dark'
		);
	}
	# 13/10/2017 13:05
	public function alert($type,$content,$args=NULL,$return=false){
		// To Do: check content for links, adding "alert-link" to class if it doesn't exist
		if(!$type || !in_array($type,$this->theme)){
			$type='primary';
		}
		if($return){
			ob_start();
		}
		if($args['data']){
			ksort($args['data']);
			foreach($args['data'] as $k=>$v){
				$data[]='data-'.$k.'="'.$v.'"';
			}
			$args['data']=' '.implode(' ',$data);
		}?>
		<div class="alert alert-<?=$type.($args['class']?' '.$args['class']:'').($args['dismissible']?' alert-dismissible fade show':'')?>"<?=$args['data']?' '.$args['data']:''?> <?=$args['id']?' id="'.$args['id'].'"':''?> role="alert">
			<?php if($args['dismissible']){ ?>
				<button type="button" class="close" data-dismiss="alert" aria-label="Close">
					<span>Dismiss</span>
				</button>
			<?php }
			echo $content?>
		</div>
		<?php if($return){
			return ob_get_clean();
		}
	}
	# 25/10/2017 15:45
	public function btn($type='primary',$size='sm'){
		if(!in_array($type,array_merge($this->theme,array('white')))){
			$type='primary';
		}
		if(!in_array($size,array('xs','sm','lg'))){
			unset($size);
		}
		return 'btn btn-'.$type.($size?' btn-'.$size:'');
	}
	# 25/10/2017 15:45
	public function list_group(array $items,$args=NULL,$return=false){
		if($return){
			ob_start();
		}?>
		<div class="list-group<?=$args['wrapclass']?' '.$args['wrapclass']:''?>"<?=$args['id']?' id="'.$args['id'].'"':''?>>
			<?php foreach($items as $item){ ?>
				<div class="list-group-item<?=(is_array($item) && $item['class']?' '.$item['class']:'').($args['itemclass']?' '.$args['itemclass']:'')?>">
					<?php if(is_array($item)){
						echo $item['content'];
					}else{
						echo $item;
					} ?>
				</div>
			<?php } ?>
		</div>
		<?php if($return){
			return ob_get_clean();
		}
	}
	# 29/03/2017 14:25
	public static function modal($id,$title,$content,$return=false,$save_text='Save',$close_text='Close',$size='md',$args=NULL){
		if($return){
			ob_start();
		} ?>
		<div class="modal fade" id="<?=$id?>" tabindex="-1" role="dialog" aria-labelledby="<?=$id?>_label" aria-hidden="true">
			<div class="modal-dialog<?=$size && in_array($size,array('sm','lg'))?' modal-'.$size:''?>" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="modal-title" id="<?=$id?>_label"><?=$title?></h4>
						<button type="button" class="close modal_close" data-dismiss="modal" aria-label="<?=$close_text?>">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<?=$content?>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-sm btn-<?=$save_text?'secondary':'primary'?> modal_close" data-dismiss="modal"><?=$close_text?></button>
						<?php if($save_text){ ?>
							<button type="button" class="btn btn-sm btn-primary modal_save"><?=$save_text?></button>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
		<?php if($return){
			return ob_get_clean();
		}
	}
	# 18/01/2017 11:35
	public function progress($value=0,$max=100,$label='',$style='default',$return=false){
		if($return){
			ob_start();
		}?>
		<div class="progress">
			<div aria-valuenow="<?=$value?>" aria-valuemin="0" aria-valuemax="<?=$max?>" class="progress-bar<?=$style!='default'?' bg-'.$style:''?>" role="progressbar" style="width:<?=round($value/$max*100,2)?>%;"><?=$label?></div>
		</div>
		<?php if($return){
			return ob_get_clean();
		}
	}
}