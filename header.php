<!DOCTYPE html>
<html lang="en">
	<head>
	<meta name="google-site-verification" content="DlzD62DJzM7eYqX0Q4QNWG9an2temBP2QWj1dhd5Ezo" />
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width" />
	<title><?php
	/*
	 * Print the <title> tag based on what is being viewed.
	 */
	global $page, $paged;

	wp_title( '|', true, 'right' );

	// Add the blog name.
	//bloginfo( 'name' );

	/*
	// Add the blog description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		echo " | $site_description";

	// Add a page number if necessary:
	if ( $paged >= 2 || $page >= 2 )
		echo ' | ' . sprintf( __( 'Page %s', '' ), max( $paged, $page ) );

	*/
		
	?></title>
	
	<meta property="og:image" content="<?php echo get_template_directory_uri(); ?>/img/og_image.jpg" />
	
	
	
	<link rel="shortcut icon" href="<?php //echo get_template_directory_uri(); ?>/wp-content/themes/echelon2013/favicon.ico" />
	<link rel="profile" href="http://gmpg.org/xfn/11" />
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
	<?php // Loads HTML5 JavaScript file to add support for HTML5 elements in older IE versions. ?>
	<!--[if lt IE 9]>
	<script src="<?php echo get_template_directory_uri(); ?>/js/html5.js" type="text/javascript"></script>
	<![endif]-->
	
	<?php
	
	?>
	 <!-- Le styles -->
	 <link href="/wp-includes/js/tinymce/themes/advanced/skins/wp_theme/content.css?_=<?php echo time(); ?>" rel="stylesheet">
    <link href="<?php echo get_template_directory_uri(); ?>/themes/css/bootstrap.css?_=<?php echo time(); ?>" rel="stylesheet">
    <link href="<?php echo get_template_directory_uri(); ?>/themes/css/component.css?_=<?php echo time(); ?>" rel="stylesheet">
    <link href="<?php echo get_template_directory_uri(); ?>/themes/css/carousel-pagination.css?_=<?php echo time(); ?>" rel="stylesheet">

    <script src="<?php echo get_template_directory_uri(); ?>/js/plugins/jquery-1.5.1.js"></script>
    <script src="<?php echo get_template_directory_uri(); ?>/js/plugins/slides.min.jquery.js"></script>
	<script src="<?php echo get_template_directory_uri(); ?>/js/plugins/persisten-header.js"></script>
	<script src="<?php echo get_template_directory_uri(); ?>/js/functions.js?_=<?php echo time(); ?>"></script>
	
	 
	
	
    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->


	<style>
	.navbar .nav .current-menu-item > a,
	.navbar .nav .current-menu-item > a:hover,
	.navbar .nav .current-menu-item > a:focus,
	.current-menu-parent{
	background-color:none;
	color:#2F2F2E;
	text-shadow:none !important;
	} 
	
	
	.client-badge img{
		border-radius: 50%;
	}
	.sayings{
		color:white;
	}
	.sayings *{
		color:white;
	}
	.video-wrapper {
		text-indent: 0px;;
	}
	a:link, a:visited, a:hover{
		cursor:pointer;
	}
	a:link img, a:visited img, a:hover img{
		cursor:pointer;
	}
	img{
		max-width:
	}

	</style>
	<?php wp_head(); ?>
	</head>
	
  <body>
  <div id="fb-root"></div>
	<script>(function(d, s, id) {
	  var js, fjs = d.getElementsByTagName(s)[0];
	  if (d.getElementById(id)) return;
	  js = d.createElement(s); js.id = id;
	  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
	  fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));</script>
	<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
	<script type="text/javascript">
	  (function() {
	    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
	    po.src = 'https://apis.google.com/js/plusone.js';
	    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
	  })();
	</script>
    <div class="wrapper">
      <header class="header">
        <div class="inner">
          <div class="branding">
            <h1 class="logo">
              <a href="<?php echo home_url('/'); ?>" title="Echelon 2013" alt="Echelon" >Echelon</a>
            </h1>
            <span class="year">2013</span>
            <div class="date">4-5 June 2013</div>
            <p>Singapore</p>
          </div>
          <div class="slogan">
            <em>Driving Asia's<br/>Tech Industry Forward</em>
          </div>
          <div class="banner-wrapper">
            <div class="banner-1">Banner 1</div>
            <span class="social-top">
            <ul class="clearfix">
              <li><a href="<?php echo get_option("echelon_fb_url"); ?>" target="_blank" class="facebook"></a></li> 
              <li><a href="<?php echo get_option("echelon_tw_url"); ?>" target="_blank" class="twitter">twitter</a></li>
              <li><a href="<?php echo get_option("echelon_gp_url"); ?>" target="_blank" class="google">google</a></li>
              <li class="last"><a href="<?php echo get_option("echelon_in_url"); ?>" target="_blank" class="linkedin">linkedin</a></li>
            </ul>          
          </span>
          </div>
        </div>
      </header>
    
			<div class="persist-area" style="position:relative; z-index:9999">
      <div class="navbar persist-header">
        <div class="navbar-inner">
          <div class="container">
            <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </a>
            <a class="register" target='_blank' href="<?php echo site_url('/register'); ?>">Project name</a>

			<?php
			/*
			<div class="nav-collapse collapse">
              <ul class="nav">
                <li><a href="about.html">About</a></li>
                <li><a href="agenda.html">Agenda</a></li>
                <li><a href="#speaker">Speakers</a></li>
                <li><a href="#satellites">Satellites</a></li>
                <li><a href="#startups">Startups</a></li>
                <li><a href="#venue">Venue</a></li>
                <li><a href="#partner">Partner</a></li>
                <li><a href="#contact">Contact</a></li>	
              </ul>
			 </div>
			*/
			?>
			  <?php

				$defaults = array(
					'theme_location'  => '',
					'menu'            => '',
					'container'       => 'div',
					'container_class' => 'nav-collapse collapse',
					'container_id'    => '',
					'menu_class'      => 'nav',
					'menu_id'         => '',
					'echo'            => true,
					'fallback_cb'     => 'wp_page_menu',
					'before'          => '',
					'after'           => '',
					'link_before'     => '',
					'link_after'      => '',
					'items_wrap'      => '<ul id="%1$s" class="%2$s">%3$s</ul>',
					'depth'           => 0,
					'walker'          => ''
				);

				wp_nav_menu( $defaults );
				
				
				?>
            <!--/.nav-collapse -->
          </div>
        </div>
      </div>
</div>
     