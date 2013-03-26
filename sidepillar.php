<div class="span3">
<?php
if(is_home()&&0){
	?>
	<div class="side-pillar-btn" >
		<a href="http://e27.co/echelon-2013-startups/" target="_blank" class="btn btn-success btn-large mar-bot-xxs">Submit Your Startup</a>
		<a href="http://echelon.e27.co/sg2013/about/sponsors/" class="btn btn-success btn-large btn-sponsor" style='font-size:15px'>Sponsorship Opportunities</a>
	</div>
	<?
}
if($_SESSION['current_p']->post_name=='sponsors'){
	include_once(dirname(__FILE__)."/sidetestimonials.php");
}
else{
	?>
	<div class="side-pillar txt-c" id='side_pillar' style='display:none;'>
	<div class="head-pillar"><p>Organized By</p></div>
	<ul style='margin-bottom:25px;'>
		<li >
				<img alt="e27" title="e27" src="http://e27.wpengine.netdna-cdn.com/wp-content/themes/e27sg/img/e27-logo.png">
		</li>
	</ul>
	
	<?php
	$side = false;
	$ptype = "echelon_sponsor";
	$args = array(
		'post_type'=> $ptype,
		'order'    => 'ASC',
		'orderby'	=> 'meta_value',
		'meta_key' 	=> $ptype.'_order',
		'posts_per_page' => -1
	);              
	$the_query = new WP_Query( $args );
	$sponsors = array();
	if($the_query->have_posts() ){
		while ( $the_query->have_posts() ){
			$the_query->the_post();
			$p = get_post( get_the_ID(), OBJECT );
			$image_id = get_post_meta( $p->ID, $ptype.'_image_id', true );
			$type = get_post_meta( $p->ID, $ptype.'_type', true );
			$link = get_post_meta( $p->ID, $ptype.'_link', true );
			$html = get_post_meta( $p->ID, $ptype.'_html', true );
			
			$image_src = wp_get_attachment_url( $image_id );
			$v = array();
			$v['post'] = $p;
			$v['image_src'] = $image_src;
			$v['link'] = $link;
			$v['html'] = $html;		
			if(!is_array($sponsors[$type])){
				$sponsors[$type] = array();
			}
			$sponsors[$type][] = $v;
			$side = true;
		}
	}

	wp_reset_postdata();
	foreach($sponsors as $key=>$value){
	$t = count($sponsors[$key]);
		if($t){
			if(strtolower($key)=='country sponsors'){
				?>
				<div class="head-pillar"><p><?php echo $key; ?></p></div>
				<div id='country_sponsors' >
				<ul class="slides_container" style='height:150px' >
				<?php
				for($i=0; $i<$t; $i++){
					?>
					<li>
					<?php
					if(trim($sponsors[$key][$i]['html'])){
						echo trim($sponsors[$key][$i]['html']);
					}
					else{
						e_view($sponsors[$key][$i]['post']);
						if(trim($sponsors[$key][$i]['link'])){
							?>
							<a target='_blank' href="<?php echo e_clickurl($sponsors[$key][$i]['link'], $sponsors[$key][$i]['post']); ?>"><img src="<?php echo $sponsors[$key][$i]['image_src']; ?>" title="<?php echo htmlentities($sponsors[$key][$i]['post']->post_title); ?>" alt="<?php echo htmlentities($sponsors[$key][$i]['post']->post_title); ?>"></a>
							<?php
						}
						else{
							?>
							<img src="<?php echo $sponsors[$key][$i]['image_src']; ?>" title="<?php echo htmlentities($sponsors[$key][$i]['post']->post_title); ?>" alt="<?php echo htmlentities($sponsors[$key][$i]['post']->post_title); ?>">
							<?php
						}
					}
					?>
					</li>
					<?php
				}
				?>
				</ul>
				</div>
				<?php
			}
			else{
				?>
				<div class="head-pillar"><p><?php echo $key; ?></p></div>
				<ul>
				<?php
				for($i=0; $i<$t; $i++){
					?>
					<li>
					<?php
					if(trim($sponsors[$key][$i]['html'])){
						echo trim($sponsors[$key][$i]['html']);
					}
					else{
						e_view($sponsors[$key][$i]['post']);
						if(trim($sponsors[$key][$i]['link'])){
							?>
							<a target='_blank' href="<?php echo e_clickurl($sponsors[$key][$i]['link'], $sponsors[$key][$i]['post']); ?>"><img src="<?php echo $sponsors[$key][$i]['image_src']; ?>" title="<?php echo htmlentities($sponsors[$key][$i]['post']->post_title); ?>" alt="<?php echo htmlentities($sponsors[$key][$i]['post']->post_title); ?>"></a>
							<?php
						}
						else{
							?>
							<img src="<?php echo $sponsors[$key][$i]['image_src']; ?>" title="<?php echo htmlentities($sponsors[$key][$i]['post']->post_title); ?>" alt="<?php echo htmlentities($sponsors[$key][$i]['post']->post_title); ?>">
							<?php
						}
					}
					?>
					</li>
					<?php
				}
				?>
				</ul>
				<?php
			}
		}
	}


	$ptype = "mediapartner";
	$args = array(
		'post_type'=> $ptype,
		'order'    => 'ASC',
		'orderby'	=> 'meta_value',
		'meta_key' 	=> $ptype.'_order',
		'posts_per_page' => -1
	);              
	$the_query = new WP_Query( $args );
	$mps = array();
	if($the_query->have_posts() ){
		while ( $the_query->have_posts() ){
			$the_query->the_post();
			$p = get_post( get_the_ID(), OBJECT );
			$image_id = get_post_meta( $p->ID, $ptype.'_image_id', true );
			$type = get_post_meta( $p->ID, $ptype.'_type', true );
			$link = get_post_meta( $p->ID, $ptype.'_link', true );
			$html = get_post_meta( $p->ID, $ptype.'_html', true );
			
			$image_src = wp_get_attachment_url( $image_id );
			$v = array();
			$v['post'] = $p;
			$v['image_src'] = $image_src;
			$v['link'] = $link;
			$v['html'] = $html;
			
			if(!is_array($mps[$type])){
				$mps[$type] = array();
			}
			$mps[$type][] = $v;
		}
	}

	wp_reset_postdata();
	foreach($mps as $key=>$value){
	$t = count($mps[$key]);
		if($t){
			
			if(strtolower($key)=='supporting partners'){
				?>
				<div class="head-pillar"><p><?php echo $key; ?></p></div>
				<div id='strategic_partners' >
				<ul class="slides_container" style='height:150px' >
				<?php
				for($i=0; $i<$t; $i++){
					?>
					<li>
					<?php
					if(trim($mps[$key][$i]['html'])){
						echo trim($mps[$key][$i]['html']);
					}
					else{
						e_view($mps[$key][$i]['post']);
						if(trim($mps[$key][$i]['link'])){
							?>
							<a target='_blank' href="<?php echo e_clickurl($mps[$key][$i]['link'], $mps[$key][$i]['post']); ?>"><img src="<?php echo $mps[$key][$i]['image_src']; ?>" title="<?php echo htmlentities($mps[$key][$i]['post']->post_title); ?>" alt="<?php echo htmlentities($mps[$key][$i]['post']->post_title); ?>"></a>
							<?php
						}
						else{
							?>
							<img src="<?php echo $mps[$key][$i]['image_src']; ?>" title="<?php echo htmlentities($mps[$key][$i]['post']->post_title); ?>" alt="<?php echo htmlentities($mps[$key][$i]['post']->post_title); ?>">
							<?php
						}
					}
					?>
					</li>
					<?php
				}
				?>
				</ul>
				</div>
				<?php
			}
			else if(strtolower($key)=="community & media partners"){
				?>
				<div class="head-pillar"><p><?php echo $key; ?></p></div>
				<div id='cm_partners' >
					<div class="slides_container">
						<?php
						$n=0;
						for($i=0; $i<$t; $i++){
							if($n%4==0){
								if($n!=0){
									?></ul><ul style='height:370px' ><?php
								}
								else{
									?><ul style='height:370px' ><?php
								}
							}
							?>
							<li>
							<?php
							if(trim($mps[$key][$i]['html'])){
								echo trim($mps[$key][$i]['html']);
							}
							else{
								e_view($mps[$key][$i]['post']);
								if(trim($mps[$key][$i]['link'])){
									?>
									<a target='_blank' href="<?php echo e_clickurl($mps[$key][$i]['link'], $mps[$key][$i]['post']); ?>"><img src="<?php echo $mps[$key][$i]['image_src']; ?>" title="<?php echo htmlentities($mps[$key][$i]['post']->post_title); ?>" alt="<?php echo htmlentities($mps[$key][$i]['post']->post_title); ?>"></a>
									<?php
								}
								else{
									?>
									<img src="<?php echo $mps[$key][$i]['image_src']; ?>" title="<?php echo htmlentities($mps[$key][$i]['post']->post_title); ?>" alt="<?php echo htmlentities($mps[$key][$i]['post']->post_title); ?>">
									<?php
								}
							}
							?>
							</li>
							<?php
							$n++;
						}
						?>
						</ul>
					</div>
				</div>
				<?php
			}
			else{
				?>
				<div class="head-pillar"><p><?php echo $key; ?></p></div>
				<ul>
				<?php
				for($i=0; $i<$t; $i++){
					?>
					<li>
					<?php
					if(trim($mps[$key][$i]['html'])){
						echo trim($mps[$key][$i]['html']);
					}
					else{
						e_view($mps[$key][$i]['post']);
						if(trim($mps[$key][$i]['link'])){
							?>
							<a target='_blank' href="<?php echo e_clickurl($mps[$key][$i]['link'], $mps[$key][$i]['post']); ?>"><img src="<?php echo $mps[$key][$i]['image_src']; ?>" title="<?php echo htmlentities($mps[$key][$i]['post']->post_title); ?>" alt="<?php echo htmlentities($mps[$key][$i]['post']->post_title); ?>"></a>
							<?php
						}
						else{
							?>
							<img src="<?php echo $mps[$key][$i]['image_src']; ?>" title="<?php echo htmlentities($mps[$key][$i]['post']->post_title); ?>" alt="<?php echo htmlentities($mps[$key][$i]['post']->post_title); ?>">
							<?php
						}
					}
					?>
					</li>
					<?php
				}
				?>
				</ul>
				<?php
			}
		}
	}
	?></div><?php
	if($side){
		?>
		<script>
			jQuery("#side_pillar").show();
		</script>
		<?php		
	}
}
?>
</div>