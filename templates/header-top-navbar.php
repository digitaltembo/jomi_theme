<?php
global $user;
?>
<?php if(is_front_page()) : ?>
	<video id="video" autoplay="" loop="" class='background hidden-xs' poster="/wp-content/themes/jomi/assets/img/vid_poster.png">
		<source id="video-source" src="http://cdn.jomi.com/wp-content/themes/jomi/assets/video/background.mp4" type="video/mp4">
		<source id="video-source-webm" src="http://cdn.jomi.com/wp-content/themes/jomi/assets/video/background.webm" type="video/webm">
	</video>
	<img src='http://cdn.jomi.com/wp-content/themes/jomi/assets/img/background_mobile.png' class='background visible-xs'/>
	<div class='blackbox background'></div>

<?php endif; ?>

  <nav class="navbar navbar-default site-header" role="navigation" style="background-color: rgba(0,0,0,0); border-color: rgba(0,0,0,0);">
  <div class="container-fluid container">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <div class="logo"><a href="/"><img src="/wp-content/themes/jomi/assets/img/logo.png" alt="Journal of Medical Insight"></a></div>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
	 
	 <form class="navbar-form navbar-left" role="search">
        <div class="form-group">

        <form class="navbar-form navbar-left" role="search">
	    <input placeholder="Search Articles" type="text" name="login" size="30" class="border search" id="search-field"/>
	    <span class="glyphicon glyphicon-search search-icon"></span>
	    </form>

	    </div>
      </form>

      <ul class="nav navbar-nav navbar-right">
 
	      <?php if(!is_user_logged_in()): ?>
		        <li class="dropdown hidden-xs">
					<a class="dropdown-toggle border" href="#" data-toggle="dropdown" id="login-btn">Sign&nbsp;in</a>
						<div class="dropdown-menu pull-right" style="padding: 15px; z-index: 5;">
							<div id="login-form">
								<form name="loginform" id="loginform" action="">
									<p class="login-username">
										<label for="user_login">Username</label>
										<input type="text" name="log" id="user_login" class="input" value="" size="20">
									</p>
									<p class="login-password">
										<label for="user_pass">Password</label>
										<input type="password" name="pwd" id="user_pass" class="input" value="" size="20">
									</p>
									<p class="login-remember"><label><input name="rememberme" type="checkbox" id="rememberme" value="forever"> Remember Me</label></p>

									<p class="login-submit">
										<input type="submit" name="submit" id="submit" class="btn btn-default" value="Log In" onblur="this.style.color='white'">
										<input type="hidden" name="redirect_to" value="/">
									</p>
									<p>
									<a href="/register" class="register">Register</a>
									</p>

									<!--p class="login-register" style="width:45%">
										<a href="/register"><btn type="register" name="register" id="register" class="btn btn-default" value="Register"/></a>
									</p-->

								</form>
							</div>
					</div>
				</li>
				<?php else: ?>
				<li><?php wp_loginout(); ?></li>
			<?php endif; ?>
			<li><a href='/login/' class=" active visible-xs">Sign in</a></li>
			<li><a href='/subscribers/' class="<?php 	if( is_page( 'subscribers') ) echo " active"; ?>">Subscribers</a></li>
	        <li><a href="/pricing/" class="<?php 		if( is_page( 'pricing') ) echo " active"; ?>">Pricing</a></li>
	        <li><a href="/contact/" class="<?php 		if( is_page( 'contact') ) echo " active"; ?>">Contact</a></li>
	        <li><a href="/about/" class="<?php 			if( is_page( 'about') ) echo " active"; ?>">About</a></li>

     </ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>
</div>




<script>
	if ($(window).width() < 768) {
		$('#video-source').attr("src", "");
		$('#video-source-webm').attr("src", "");
	}
	/* VIDEO LOAD CONDITIONALS */
	$(window).resize(function() {
		if ($(window).width() < 768 && $('#video-source').attr("src") != '') {
			$('#video-source').attr('src', '');
			$('#video-source-webm').attr('src', '');
			$('#video').load();
		} else if ($(window).width() >= 768 && $('#video-source').attr("src") == ''){
			$('#video-source').attr('src', 'http://cdn.jomi.com/wp-content/themes/jomi/assets/video/background.mp4');
			$('#video-source-webm').attr('src', 'http://cdn.jomi.com/wp-content/themes/jomi/assets/video/background.webm');
			$('#video').load();
		}
	});


	/* SIGNUP & LOGIN */
	$(function() {
		$('#loginform').on('submit', function(e) {
			e.preventDefault();

			var login = $('#login-form input[name="log"]').val();
			if(login === '') {
				console.log('no username specified');
				return;
			}
			var pass = $('#login-form input[name="pwd"]').val();
			if(pass === '') {
				console.log('no password specified');
				return;
			}
			console.log('user: ' + login);
			console.log('pass: ' + pass);

			var dataString = 'log='+ login + '&pwd=' + pass;
			//alert (dataString);return false;
			$.ajax({
			  type: "POST",
			  url: "/wp-login.php",
			  data: dataString,
			  success: function(data) {
			  	console.log(String(data));
			    if(String(data).indexOf("login_error") > 0) {
			    	// login error occured
			    	console.log('whoops')
			    } else {
			    	console.log('success');
			    	window.location.reload();
			    }
			  }
			});
		});

		// Setup drop down menu
		$('.dropdown-toggle').dropdown();

		// Fix input element click problem
		$('.dropdown input, .dropdown label').click(function(e) {
			e.stopPropagation();
		});
		$('#search-field').keydown(function(event){
			if(event.which == 13)
			{
				event.preventDefault();
				window.location.href = "/?s="+$(this).val();
			}
		});
	});

</script>