<?php class statistics{
	private $total_users;
	public function __construct(){
		global $jobs,$users;
		$this->users=$users->statistics();
	}
	public function get_orphans(){
	}
	public function get_totals(){
		global $companies,$jobs,$users;
		$out.='<div class="col-sm-6">
			<div class="card">
				<div class="card-header">Totals</div>
				<ul class="list-group list-group-flush">
					<li class="list-group-item">
						<strong>Users:</strong> '.$this->users['total'].'
					</li>
				</ul>
			</div>
		</div>';
		echo $out;
	}
	public function get_users(){
		global $db,$users;
		$roles=$users->get_roles();
		$out.='<div class="col-sm-6">
			<div class="card">
				<div class="card-header">Users<a class="btn btn-primary btn-sm  pull-xs-right" href="./users" title="View Users">View</a></div>
				<table class="table table-hover table-striped">
					<thead>
						<tr>
							<th></th>
							<th>Count</th>
							<th>Percent</th>
						</tr>
					</thead>
					<tbody>';
						foreach($roles['roles'] as $role){
							$count=$db->result_count("FROM `users` WHERE `role_id`=?",$role['id']);
							$out.='<tr>
								<th>'.$role['role'].'</th>
								<td>'.$count.'</td>
								<td>'.($count==0?'':number_format($count/$this->users['total']*100,2)."%").'</td>
							</tr>';
						}
						$out.='<tr>
							<th>Total</th>
							<td>'.$this->users['total'].'</td>
							<td></td>
						</tr>
						<tr>
							<th>Can Access</th>
							<td>'.$this->users['has-access'].'</td>
							<td>'.number_format($this->users['has-access']/$this->users['total']*100,2).'%</td>
						</tr>
						<tr>
							<th title="Users who have logged in within the last '.MONTH_LENGTH.' days">Active</th>
							<td>'.$this->users['active'].'</td>
							<td>'.number_format($this->users['active']/$this->users['total']*100,2).'%</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>';
		echo $out;
	}
}