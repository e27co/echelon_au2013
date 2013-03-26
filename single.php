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
			?><h2><?php echo $p->post_title;?></h2><?php
			echo "<div class='content'>";
			the_content();
			echo "</div>";
		  }
		  
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