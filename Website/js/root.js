var msr = {
	page: 1,
	init: function () {
		msr.load_sections();
		msr.scrollroad();
		$('.add-rank').click(function () {
			window.location = this.dataset.link;
		});
		window.onpopstate = function (e) {
			var data = {};
			var what = location.pathname.substr(1);
			if (what.indexOf('/')) {
				var split = what.split('/');
				data.id = split[1];
				what = split[0];
			}
			$('main').load(
				'/t_' + what + '.php',
				data,
				function () {
					var title = '';
					if (what != 'index') {
						title = php.ucwords(what) + ' | ';
					}
					$('main').get(0).className = what;
					$('title').text(title + 'My Strava Rank');
				}
			);
		};
	},
	load_sections: function () {
		$('body').on('click', '.js-load', function (e) {
			e.stopPropagation();
			var is_nav = !!$(e.target).parents('nav').length;
			var target = $(e.target).hasClass('js-load') ? $(e.target).get(0) : $(e.target).parents('.js-load').get(0);
			var target_id = target.dataset ? target.dataset.id : false;
			var from = php.strpos(target.className, 'js-load');
			var to = php.strpos(target.className, ' ', from + 8);
			var what = false;
			if (to) {
				what = target.className.substring(from + 8, to);
			} else {
				what = target.className.substring(from + 8);
			}
			$('.navbar-nav a').removeClass('active');
			if ($('.navbar-nav a.' + what)) {
				$('.navbar-nav a.' + what).addClass('active');
			}
			var data = {
				id: target_id,
				page: msr.page
			};
			$('main').load(
				'/t_' + what + '.php',
				data,
				function () {
					var title = '';
					if (what != 'index') {
						title = php.ucwords(what) + ' | ';
					}
					$('main').get(0).className = what;
					$('title').text(title + 'My Strava Rank');
					window.history.pushState(data, what, what + (target_id ? '/' + target_id : ''));
				}
			);
		});
	},
	scrollroad: function () {
		var svg = document.querySelector('#svg-road');
		if (svg) {
			var navheight = document.querySelector('.navbar').getBoundingClientRect().height;
			svg.setAttribute('height', window.innerHeight - navheight);
			var path = document.querySelector('#svg-road-path');
			var pathLength = 1150;
			path.style.strokeDasharray = pathLength + ' ' + pathLength;
			path.style.strokeDashoffset = pathLength;
			path.getBoundingClientRect();
			window.addEventListener("scroll", function (e) {
				var scrollPercentage = (document.documentElement.scrollTop + document.body.scrollTop) / (document.documentElement.scrollHeight - document.documentElement.clientHeight);
				var drawLength = pathLength * scrollPercentage;
				path.style.strokeDashoffset = pathLength - drawLength;
				if (scrollPercentage >= 0.99) {
					path.style.strokeDasharray = "none";
				} else {
					path.style.strokeDasharray = pathLength + ' ' + pathLength;
				}
			});
		}
	}
};
msr.init();