
<div class="row-fluid socials add-bot" >
	<div class="span4" >
	  <iframe scrolling="no" height="250" frameborder="0" width="290" src="http://www.facebook.com/connect/connect.php?id=7137316668&amp;connections=10&amp;stream=false&amp;header=false&amp;locale=" title="facebook">&amp;nbsp;</iframe>
	  <!--<div <?php /* onClick="_gaq.push(['_trackEvent', 'Button Clicks', 'Social Links', 'Facebook']);" */ ?> class="fb-like" data-href="http://www.facebook.com/e27" data-send="false" data-width="280" data-show-faces="true" data-font="arial"></div>-->
	</div>
	<div class="span4 add-left">
		<div>
		<a <?php /* onClick="_gaq.push(['_trackEvent', 'Button Clicks', 'Social Links', 'Twitter']);" */ ?> href="https://twitter.com/e27co" class="twitter-follow-button" data-show-count="true">Follow @e27co</a> 
		
		<?php
		$feed = 'http://search.twitter.com/search.json?q=from:e27co';
		$tweets = json_decode(file_get_contents($feed));
		/*
		stdClass Object
		(
			[completed_in] => 0.019
			[max_id] => 3.02276351598E+17
			[max_id_str] => 302276351598141441
			[next_page] => ?page=2&max_id=302276351598141441&q=from%3Ae27co
			[page] => 1
			[query] => from%3Ae27co
			[refresh_url] => ?since_id=302276351598141441&q=from%3Ae27co
			[results] => Array
				(
					[0] => stdClass Object
						(
							[created_at] => Fri, 15 Feb 2013 04:41:12 +0000
							[from_user] => e27co
							[from_user_id] => 15315691
							[from_user_id_str] => 15315691
							[from_user_name] => e27
							[geo] => 
							[id] => 3.02276351598E+17
							[id_str] => 302276351598141441
							[iso_language_code] => en
							[metadata] => stdClass Object
								(
									[result_type] => recent
								)

							[profile_image_url] => http://a0.twimg.com/profile_images/2817545201/83d0f88ad573ddf1a64f0b567a109a46_normal.jpeg
							[profile_image_url_https] => https://si0.twimg.com/profile_images/2817545201/83d0f88ad573ddf1a64f0b567a109a46_normal.jpeg
							[source] => <a href="http://www.hootsuite.com">HootSuite</a>
							[text] => Creative Mixer 5 is here. The theme will be "push", exploring how #entrepreneurs are pushing the boundaries http://t.co/bH19QMon
							[to_user] => 
							[to_user_id] => 0
							[to_user_id_str] => 0
							[to_user_name] => 
						)

					[1] => stdClass Object
						(
							[created_at] => Fri, 15 Feb 2013 04:20:14 +0000
							[from_user] => e27co
							[from_user_id] => 15315691
							[from_user_id_str] => 15315691
							[from_user_name] => e27
							[geo] => 
							[id] => 3.02271073104E+17
							[id_str] => 302271073104326656
							[iso_language_code] => en
							[metadata] => stdClass Object
								(
									[result_type] => recent
								)

							[profile_image_url] => http://a0.twimg.com/profile_images/2817545201/83d0f88ad573ddf1a64f0b567a109a46_normal.jpeg
							[profile_image_url_https] => https://si0.twimg.com/profile_images/2817545201/83d0f88ad573ddf1a64f0b567a109a46_normal.jpeg
							[source] => <a href="http://www.hootsuite.com">HootSuite</a>
							[text] => What's going to be hot in #mobile #ecommerce this year? http://t.co/aZz5AF7H
							[to_user] => 
							[to_user_id] => 0
							[to_user_id_str] => 0
							[to_user_name] => 
						)

		*/
		function convertLinks($str){
			$str = preg_replace("/(http:\/\/[^\s]*)/i", "<a href='$1' target='_blank'>$1</a>", $str);
			$str = preg_replace("/#([^\s]*)/i", "<a href='https://twitter.com/search?q=%23$1&src=hash' target='_blank'>#$1</a>", $str);
			
			return $str;
		}
		for($i=0; $i<2; $i++){
			echo "<div style='padding:20px 0px 0px 0px;'>";
			echo "<div>";
			echo convertLinks($tweets->results[$i]->text);
			echo "</div>";
			echo "<div style='padding-top:4px;'>";
			$hoursago = (time() - strtotime($tweets->results[$i]->created_at))/(60*60);
			if($hoursago<1){
				$hoursago = "less than an hour ago";
			}
			else if($hoursago<2){
				$hoursago = "about an hour ago";
			}
			else{
				$hoursago = floor($hoursago)." hours ago";
			}
			echo "<i>".$hoursago." by </i><a href='http://twitter.com/".$tweets->results[$i]->from_user."' target='_blank'>".$tweets->results[$i]->from_user."</a>";
			echo "</div>";
			echo "</div>";
		}

		?>
		
		</div>
		<!--
		<div class="twitter-feed">
		  <p>Call for startup pitch! hurry up for the life time experience!<br/><em>6 hours ago by <span class="text-success">e27</span></em></p>  
		</div>
		<div class="twitter-feed">
		  <p>Call for startup pitch! hurry up for the life time experience!<br/><em>6 hours ago by <span class="text-success">e27</span></em></p>  
		</div>
		-->
	</div>
	<div class="span4" >
	  <div class="subscribe add-left-med">
		<h3>Subscribe to e27</h3>
		<!--
		<input type="text" placeholder="Your Email"/>
		<a class="btn btn-success subscribe" data-toggle="modal" href="#signupModal">Subscribe</a>
		-->
		
		<form style="display: inline;"  action="http://e27.us1.list-manage2.com/subscribe/post?u=5d6bc43500e46f74ebde550e9&id=304859caf2" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank">
			<!-- <label for="mce-EMAIL">Receive e27 newsletters</label> -->
			<input type="email" value="" name="EMAIL" class="email" id="mce-EMAIL" placeholder="Enter you email:" required>
			<input type="submit" value="Subscribe &gt;" name="subscribe" id="mc-embedded-subscribe" class="btn btn-success subscribe">
			<p class="txt-small">Don't worry! we hate spam too!</p>
		</form>
	  </div>            
	</div>
  </div>
</div>
<div class="footer-extras">
  <div class="container">
	<div class="row">
	  <div class="span3-ftr-cnter  logo-e27"><a href="" alt="e27 Web Innovation Asia" title="e27 Web Innovation Asia">e27 logo</a></div>
	  <div class="span7 about-small">
		<!--
		<h3>Your event organizer, e27</h3>
		<p>Founded in 2007, e27 is a media organization focused on the Asian technology startup industry.
		  We believe in building the community of technology innovators across Asia by reporting on the latest,
		  breaking news relevant to technology startups, technology companies as well as investors on the  while
		  keeping our ears to the ground by connecting with our readers </p>
		-->
		
		<h3><?php echo get_option("echelon_fphead_3"); ?></h3>
		<?php echo stripslashes(html_entity_decode(get_option("echelon_fptext_3"))); ?>
	  </div>
	  <div class="span2 social-footer pull-right">
		<h3>Stay Connected</h3>
		<div class="inner-social-footer">
		  <ul class="social-icons-footer clearfix">
			<li>
			  <span class="twitter"></span>
			  <a href="<?php echo get_option("echelon_tw_url"); ?>" target="_blank">Twitter</a>
			</li>
			<li>
			  <span class="facebook"></span>                  
			  <a href="<?php echo get_option("echelon_fb_url"); ?>" target="_blank">Facebook</a>
			</li>
			<li>
			  <span class="google"></span>
			  <a href="<?php echo get_option("echelon_gp_url"); ?>" target="_blank">Google+</a>
			</li>   
			<li>
			  <span class="linkedin"></span>  
			  <a href="<?php echo get_option("echelon_in_url"); ?>" target="_blank">LinkedIn</a></li>
		  </ul>
		</div>
	  </div>
	</div>      
  </div>
</div>
<footer class="footer">
  <div class="container add-top-small c-text">
	<div class="pull-left">&copy;2013 Echelon | e27 Web Innovation Asia</div>
	<div class="pull-right"><a href="<?php echo site_url('/about'); ?>">About</a> | <a href="<?php echo site_url('/register'); ?>">Register</a></div>  
  </div>    
</footer>

    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->

 
<?php wp_footer(); ?> 
	
  </body>
</html>
