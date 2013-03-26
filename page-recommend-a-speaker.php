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
	?></title>
	<meta property="og:image" content="<?php echo get_template_directory_uri(); ?>/img/og_image.jpg" />
	<link rel="shortcut icon" href="<?php echo get_template_directory_uri(); ?>/favicon.ico" />
	<link rel="profile" href="http://gmpg.org/xfn/11" />
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
	<?php // Loads HTML5 JavaScript file to add support for HTML5 elements in older IE versions. ?>
	<!--[if lt IE 9]>
	<script src="<?php echo get_template_directory_uri(); ?>/js/html5.js" type="text/javascript"></script>
	<![endif]-->
	 <!-- Le styles -->
	 <link href="/wp-includes/js/tinymce/themes/advanced/skins/wp_theme/content.css?_=<?php echo time(); ?>" rel="stylesheet">
    <link href="<?php echo get_template_directory_uri(); ?>/themes/css/bootstrap.css?_=<?php echo time(); ?>" rel="stylesheet">
    <link href="<?php echo get_template_directory_uri(); ?>/themes/css/component.css?_=<?php echo time(); ?>" rel="stylesheet">
    <link href="<?php echo get_template_directory_uri(); ?>/themes/css/carousel-pagination.css?_=<?php echo time(); ?>" rel="stylesheet">

    <script src="<?php echo get_template_directory_uri(); ?>/js/plugins/jquery-1.5.1.js"></script>
    <script src="<?php echo get_template_directory_uri(); ?>/js/plugins/slides.min.jquery.js"></script>

	<?php wp_head(); ?>
	</head>
<body style='margin:0px; background:#2f2f2e'>
<center>
<table cellpadding=0 cellpadding=0>
<tr>
	<td>
		<a href='<?php bloginfo("home") ?>' ><img style='cursor:pointer; border:0px' src='<?php echo get_template_directory_uri(); ?>/img/bannerx.jpg?_=<?php echo time(); ?>'></a>
	</td>
</tr>
<tr>
	<td style='background:#e8eef7; text-align:center'>
		<center>
		<iframe style='border:0px; width:704px; height: 2500px' src='https://docs.google.com/spreadsheet/viewform?formkey=dDN6SE0tTE9qRjNTZzNXdV9NcXViMkE6MA'></iframe>
		</center>
	</td>
</tr>
</table>
</center>
</body>
</html>