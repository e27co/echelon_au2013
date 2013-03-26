<?php
get_header();
?>


    <div class="container add-top">
      <div class="row add-bot-med">
        <div class="span9 highlights">
          
			<?php
			while ( have_posts() ){
				the_post();
				$p = get_post( get_the_ID(), OBJECT );
				?><h2>
				
				<!--social-->
				<?php
				$perm = get_permalink();
				$perm = urlencode($perm);
				$mailto = "?subject=".urlencode("Echelon 2013 :Driving Asia'a Tech Industry Forward. June 4, 5th | organized by e27")."&body=".urlencode("\n\n\n\nMore details : http://echelon.e27.co/sg2013/ | Registration : http://echelon-2013.eventbrite.com/ | Startup Applications : http://e27.co/echelon-2013-startups/");
				?>
				<div style='margin-bottom: 5px; position: relative; height: 20px; margin-top: 35px;'>
					<div class="juiz_sps_links  juiz_sps_displayed_both" style='margin:0px; position:absolute; top:-20px; '>
						<p class="screen-reader-text juiz_sps_maybe_hidden_text">Share the post "<?php the_title(); ?>"</p>
						<ul class="juiz_sps_links_list juiz_sps_hide_name" style='line-height:20px'>
							<li class="juiz_sps_item juiz_sps_link_facebook" style='font-size:11px;'>
								Share this Marketplace opportunity with your startup friends. &nbsp;&nbsp;&nbsp;
							</li>
							<li class="juiz_sps_item juiz_sps_link_facebook">
								<a target="_blank" title="Share this article on Facebook" rel="nofollow" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $perm; ?>">
								<span class="juiz_sps_icon"></span>
								<span class="juis_sps_network_name">facebook</span>
								</a>
							</li>
							<li class="juiz_sps_item juiz_sps_link_twitter">
								<a target="_blank" title="Share this article on Twitter" rel="nofollow" href="https://twitter.com/intent/tweet?source=webclient&amp;original_referer=<?php echo $perm; ?>&amp;text=<?php echo urlencode($p->post_title); ?>&amp;url=<?php echo $perm; ?>&amp;related=e27co&amp;via=e27co">
								<span class="juiz_sps_icon"></span><span class="juis_sps_network_name">twitter</span></a>
							</li>
							<li class="juiz_sps_item juiz_sps_link_google">
								<a target="_blank" title="Share this article on Google+" rel="nofollow" href="https://plus.google.com/share?url=<?php echo $perm; ?>"><span class="juiz_sps_icon"></span><span class="juis_sps_network_name">google</span></a>
							</li>
							<li class="juiz_sps_item juiz_sps_link_linkedin">
								<a target="_blank" title="Share this article on LinkedIn" rel="nofollow" href="http://www.linkedin.com/shareArticle?mini=true&amp;ro=true&amp;trk=JuizSocialPostSharer&amp;title=<?php echo urlencode($p->post_title); ?>&amp;url=<?php echo $perm; ?>"><span class="juiz_sps_icon"></span><span class="juis_sps_network_name">linkedin</span></a>
							</li>
							<li class="juiz_sps_item juiz_sps_link_mail">
								<a target="_blank" title="Share this article with a friend (email)" rel="nofollow" href="mailto:<?php echo $mailto; ?>"><span class="juiz_sps_icon"></span><span class="juis_sps_network_name">mail</span></a>
							</li>
						</ul>
					</div>
				</div>
				
				<?php echo $p->post_title;?></h2><?php
			}

			$ptype = "echelon_speaker";
			$args = array(
				'post_type'=> $ptype,
				'order'    => 'ASC',
				'orderby'	=> 'meta_value',
				'meta_key' 	=> $ptype.'_order',
				'posts_per_page' => -1
			);              
			$the_query = new WP_Query( $args );
			$i=0;
			$spid = $p->ID;
			if($spid){
				$aspeakers = array();
				$thespeaker = array();
				if($the_query->have_posts() ){
					while ( $the_query->have_posts() ){
						$the_query->the_post();
						$p = get_post( get_the_ID(), OBJECT );
						$image_id = get_post_meta( $p->ID, $ptype.'_image_id', true );
						$image_src = wp_get_attachment_url( $image_id );
						$image_id2 = get_post_meta( $p->ID, $ptype.'_image_id2', true );
						$image_src2 = wp_get_attachment_url( $image_id2 );
						if(trim($image_src2)==""){
							$image_src2 = $image_src;
						}
						$designation = get_post_meta( $p->ID, $ptype.'_designation', true );
						$fb = get_post_meta( $p->ID, $ptype.'_fb', true );
						$tw = get_post_meta( $p->ID, $ptype.'_tw', true );
						$in = get_post_meta( $p->ID, $ptype.'_in', true );
						
						$s = array();
						$s['p'] = $p;
						$s['designation'] = $designation;
						$s['fb'] = $fb;
						$s['tw'] = $tw;
						$s['in'] = $in;
						$s['image_src'] = $image_src2;
						
						if($p->ID==$spid){
							$thespeaker = $s;
						}
						$aspeakers[] = $s;
					}
				}
				?>
				<script>
				jQuery(".highlights h2").hide();
				</script>
				<div class="row-fluid speaker-details">
					<div class="span4 add-top">
					  <ul>
						<?php
						foreach($aspeakers as $value){
							?><li <?php if($value['p']->ID==$spid){ echo "class='active'"; } ?> ><a href="<?php echo get_permalink( $value['p']->ID ) ; ?>"><?php echo $value['p']->post_title; ?></a></li><?php
						}
						?>
					  </ul>          
					</div>
					<div class="span8 wrapper-speakers add-top">
					  <div class="row-fluid pos-abs">              
						<div class="span3">
						  <img class="rounded" alt="speaker1" style='cursor:pointer; height:128px; width:128px' src="<?php echo $thespeaker['image_src']; ?>">
						</div>
						<div class="span9 crew-indiv">
							<em><?php echo $thespeaker['p']->post_title; ?></em><br><?php echo $thespeaker['designation']; ?>
							<div class="social add-top-xxs">
							  <?php
							  if($thespeaker['tw']){
								?><a class="twitter" href="<?php echo $thespeaker['tw']; ?>">twitter</a><?php
							  }
							  if($thespeaker['fb']){
								?><a class="facebook" href="<?php echo $thespeaker['fb']; ?>">facebook</a><?php
							  }
							  if($thespeaker['in']){
								?><a class="linkedin" href="<?php echo $thespeaker['in']; ?>">linkedin</a><?php
							  }
							  
							  ?>
							</div>  
						</div>
					  </div>
					  <div class="row-fluid add-top">
						<?php echo nl2br($thespeaker['p']->post_content); ?>
					  </div>
					</div>
				</div>
				<?php
			}
			wp_reset_postdata();
		  
		  ?>
		</div>
		<?php
		include_once(dirname(__FILE__)."/sidepillar.php");
		?>
	 

      
    <!-- /container -->
   
</div>

 <!-- /container -->
<?php
get_footer();
?>