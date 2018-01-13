<nav class="navbar navbar-fixed-top navbar-default navbar-dark primary_bg">
	<div class="navbar-header">
		<button class="navbar-toggler hidden-sm-up" type="button" data-toggle="collapse" data-target="#mainnav">
			&#9776;
		</button>
	</div>
	<div class="collapse navbar-toggleable-xs" id="mainnav">
		<ul class="nav navbar-nav">
			<li class="nav-item active"><a class="nav-link" href="/">Home</a></li>
			<li class="nav-item"><a class="nav-link" href="/login"><?=is_logged_in()?"Account":"Login"?></a></li>
			<?php if(REGISTRATION_ALLOWED){ ?>
				<li class="nav-item"><a class="nav-link" href="/register">Register</a></li>
			<?php } ?>
		</ul>
	</div>
</nav>