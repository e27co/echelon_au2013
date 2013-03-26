<?php
include_once(dirname(__FILE__)."/../../../wp-admin/includes/plugin.php");
define('MAGPIE_OUTPUT_ENCODING', "UTF-8");
define('MAGPIE_CACHE_ON', true);
include_once(dirname(__FILE__)."/magpie_0.72/rss_fetch.php");

function htmlentitiesx($str){
	return htmlentities($str, ENT_COMPAT, "UTF-8");
}
class Echelon{
	public function hide_editor(){
		?>
		<style>
		#postdivrich { display:none; }
		</style>
		<?php
	}
	/** Frontend methods ******************************************************/
	/**
	 * Register the custom post type
	 */
	public function init($slug, $label, $supports=null){
		register_post_type( $slug, array( 'public' => true, 'label' => $label, 'supports' => $supports ) );
	}
}




class E_Carousel extends Echelon {
	
	var $slug;
	var $label;
	var $title;
		
	public function __construct() {
		$this->slug = "e_carousel";
		$this->label = "Echelon Carousel";
		$this->title = "Name";
		
		add_action( 'init', $this->init($this->slug, $this->label) );	
		if ( is_admin() ) {
			add_action( 'admin_init', array( &$this, 'admin_init' ) );
		}
	}

	
	/** Admin methods ******************************************************/
	
	
	/**
	 * Initialize the admin, adding actions to properly display and handle 
	 * the carousel custom post type add/edit page
	 */
	public function admin_init() {
		global $pagenow, $post;
		
		if ( $pagenow == 'post-new.php' || $pagenow == 'post.php' || $pagenow == 'edit.php' ) {
			
			add_action( 'add_meta_boxes', array( &$this, 'meta_boxes' ) );
			add_filter( 'enter_title_here', array( &$this, 'enter_title_here' ), 1, 2 );
			add_action( 'save_post', array( &$this, 'meta_boxes_save' ), 1, 2 );
			
			
			if(get_post_type($_GET['post']) == $this->slug||$_GET['post_type']==$this->slug){
				add_action( 'admin_head', array( &$this, 'hide_editor' ));
			}
			add_filter( 'manage_edit-'.$this->slug."_columns", array( &$this, 'columns' ) ) ;
			add_action( 'manage_'.$this->slug.'_posts_custom_column', array( &$this, 'manage_columns' ), 10, 2 );
			
			
			add_filter('manage_edit-'.$this->slug.'_sortable_columns',array( &$this,  'order_column_register_sortable'));



		}
	}

	/**
	* make column sortable
	*/
	function order_column_register_sortable($columns){
	  $columns['order'] = 'order';
	  return $columns;
	}

	/**
	 * Save meta boxes
	 * 
	 * Runs when a post is saved and does an action which the write panel save scripts can hook into.
	 */
	public function meta_boxes_save( $post_id, $post ) {
		if ( empty( $post_id ) || empty( $post ) || empty( $_POST ) ) return;
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
		if ( is_int( wp_is_post_revision( $post ) ) ) return;
		if ( is_int( wp_is_post_autosave( $post ) ) ) return;
		if ( ! current_user_can( 'edit_post', $post_id ) ) return;
		if ( $post->post_type != $this->slug ) return;
			
		$this->process_meta( $post_id, $post );
	}
	
	
	
	
	
	/**
	 * Set a more appropriate placeholder text for the New carousel title field
	 */
	public function enter_title_here( $text, $post ) {
		if ( $post->post_type == $this->slug ) return __( $this->title );
		return $text;
	}
	
	
	
	/**
	 * Add and remove meta boxes from the edit page
	 */
	public function meta_boxes() {
		add_meta_box( $this->slug."-metabox", __( $this->label ), array( &$this, 'meta_box' ), $this->slug, 'normal', 'high' );
	}
	
	/**
	 * Function for processing and storing all carousel data.
	 */
	private function process_meta( $post_id, $post ) {
		update_post_meta( $post_id, $this->slug.'_image_id', $_POST['upload_image_id'] );
		update_post_meta( $post_id, $this->slug.'_alt_tag', $_POST['alt_tag'] );
		update_post_meta( $post_id, $this->slug.'_title_tag', $_POST['title_tag'] );
		update_post_meta( $post_id, $this->slug.'_order', $_POST['order'] );
	}
	
	//columns in list view
	function columns( $columns ) {
		$columns = array(
			'cb' => '<input type="checkbox" />',
			'title' => __( $this->label ),
			'alt_tag' => __( 'Alt Tag' ),
			'title_tag' => __( 'Title Tag' ),
			'image' => __( 'Image' ),
			'order' => __( 'Order' ),
			'date' => __( 'Date' )
		);

		return $columns;
	}
	
	function manage_columns( $column, $post_id ){
		global $post;
		
		$image_id = get_post_meta( $post_id, $this->slug.'_image_id', true );
		$image_src = wp_get_attachment_url( $image_id );
		$alt = get_post_meta( $post_id, $this->slug.'_alt_tag', true );
		$title = get_post_meta( $post_id, $this->slug.'_title_tag', true );
		$order = get_post_meta( $post_id, $this->slug.'_order', true );
		
		
		if($column=='title_tag'){
			echo $title;
		}
		else if($column=='alt_tag'){
			echo $alt;
		}
		else if($column=='image'){
			?><img id="carousel_image" src="<?php echo $image_src ?>" style="max-width:100px;" /><?php
		}
		else if($column=='order'){
			echo $order;
		}
		
		
	}
	
	/**
	 * Display the image meta box
	 */
	public function meta_box() {
		global $post;
		
		$image_src = '';
		
		$image_id = get_post_meta( $post->ID, $this->slug.'_image_id', true );
		$image_src = wp_get_attachment_url( $image_id );
		$alt = get_post_meta( $post->ID, $this->slug.'_alt_tag', true );
		$title = get_post_meta( $post->ID, $this->slug.'_title_tag', true );
		$order = get_post_meta( $post->ID, $this->slug.'_order', true );
		
		?>
		<style>
		#<?php echo $this->slug; ?> td{
			vertical-align:top;
		}
		</style>
		<table id='<?php echo $this->slug; ?>' style='width:100%'>
		<tr>
			<td>Alt Tag</td>
			<td><textarea name="alt_tag" style='width:90%; height:100px'><?php echo htmlentitiesx($alt); ?></textarea></td>
		</tr>
		<tr>
			<td>Title Tag</td>
			<td><textarea name="title_tag" style='width:90%; height:100px'><?php echo htmlentitiesx($title); ?></textarea></td>
		</tr>
		<tr>
			<td>Order</td>
			<td><input type='text' name='order' value="<?php echo $order*1; ?>" /> * must be an integer value</td>
		</tr>
		<tr>
			<td>Image <br /> (490px x 300px)</td>
			<td>
			<img id="carousel_image" src="<?php echo $image_src ?>" style="max-width:100%;" />
			<input type="hidden" name="upload_image_id" id="upload_image_id" value="<?php echo $image_id; ?>" />
			<p>
				<input type='button' class='button' value="<?php _e( 'Set Image' ) ?>"  id="set-image" />
				<input type='button' class='button' style="<?php echo ( ! $image_id ? 'display:none;' : '' ); ?>" value="<?php _e( 'Remove Image' ) ?>"  id="remove-image" />
			</p>
			</td>
		</tr>
		</table>

		<!--- script below -->
		
		<script type="text/javascript">
		jQuery(document).ready(function($) {
			
			// save the send_to_editor handler function
			window.send_to_editor_default = window.send_to_editor;
	
			$('#set-image').click(function(){
				
				// replace the default send_to_editor handler function with our own
				window.send_to_editor = window.attach_image;
				tb_show('', 'media-upload.php?post_id=<?php echo $post->ID ?>&amp;type=image&amp;TB_iframe=true');
				
				return false;
			});
			
			$('#remove-image').click(function() {
				
				$('#upload_image_id').val('');
				$('img').attr('src', '');
				$(this).hide();
				
				return false;
			});
			
			// handler function which is invoked after the user selects an image from the gallery popup.
			// this function displays the image and sets the id so it can be persisted to the post meta
			window.attach_image = function(html) {
				
				// turn the returned image html into a hidden image element so we can easily pull the relevant attributes we need
				$('body').append('<div id="temp_image">' + html + '</div>');
					
				var img = $('#temp_image').find('img');
				
				imgurl   = img.attr('src');
				imgclass = img.attr('class');
				imgid    = parseInt(imgclass.replace(/\D/g, ''), 10);
	
				$('#upload_image_id').val(imgid);
				$('#remove-image').show();
	
				$('img#carousel_image').attr('src', imgurl);
				try{tb_remove();}catch(e){};
				$('#temp_image').remove();
				
				// restore the send_to_editor handler function
				window.send_to_editor = window.send_to_editor_default;
				
			}
	
		});
		</script>
		<?php
	}
}



class E_Youtube extends Echelon {
	
	var $slug;
	var $label;
	var $title;
		
	public function __construct() {
		$this->slug = "e_youtube";
		$this->label = "Echelon Youtubes";
		$this->title = "Name";
		
		add_action( 'init', $this->init($this->slug, $this->label) );	
		if ( is_admin() ) {
			add_action( 'admin_init', array( &$this, 'admin_init' ) );
		}
	}

	
	/** Admin methods ******************************************************/
	
	
	/**
	 * Initialize the admin, adding actions to properly display and handle 
	 * the carousel custom post type add/edit page
	 */
	public function admin_init() {
		global $pagenow, $post;
		
		if ( $pagenow == 'post-new.php' || $pagenow == 'post.php' || $pagenow == 'edit.php' ) {
			
			add_action( 'add_meta_boxes', array( &$this, 'meta_boxes' ) );
			add_filter( 'enter_title_here', array( &$this, 'enter_title_here' ), 1, 2 );
			add_action( 'save_post', array( &$this, 'meta_boxes_save' ), 1, 2 );
			
			
			if(get_post_type($_GET['post']) == $this->slug||$_GET['post_type']==$this->slug){
				add_action( 'admin_head', array( &$this, 'hide_editor' ));
			}
			add_filter( 'manage_edit-'.$this->slug."_columns", array( &$this, 'columns' ) ) ;
			add_action( 'manage_'.$this->slug.'_posts_custom_column', array( &$this, 'manage_columns' ), 10, 2 );
		}
	}


	/**
	 * Save meta boxes
	 * 
	 * Runs when a post is saved and does an action which the write panel save scripts can hook into.
	 */
	public function meta_boxes_save( $post_id, $post ) {
		if ( empty( $post_id ) || empty( $post ) || empty( $_POST ) ) return;
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
		if ( is_int( wp_is_post_revision( $post ) ) ) return;
		if ( is_int( wp_is_post_autosave( $post ) ) ) return;
		if ( ! current_user_can( 'edit_post', $post_id ) ) return;
		if ( $post->post_type != $this->slug ) return;
			
		$this->process_meta( $post_id, $post );
	}
	
	
	
	
	
	/**
	 * Set a more appropriate placeholder text for the New carousel title field
	 */
	public function enter_title_here( $text, $post ) {
		if ( $post->post_type == $this->slug ) return __( $this->title );
		return $text;
	}
	
	
	
	/**
	 * Add and remove meta boxes from the edit page
	 */
	public function meta_boxes() {
		add_meta_box( $this->slug."-metabox", __( $this->label ), array( &$this, 'meta_box' ), $this->slug, 'normal', 'high' );
	}
	
	/**
	 * Function for processing and storing all carousel data.
	 */
	private function process_meta( $post_id, $post ) {
		update_post_meta( $post_id, $this->slug.'_youtube_link', $_POST['youtube_link'] );
	}
	
	//columns in list view
	function columns( $columns ) {
		$columns = array(
			'cb' => '<input type="checkbox" />',
			'title' => __( $this->label ),
			'youtube_link' => __( 'Youtube Link' ),
			'date' => __( 'Date' )
		);

		return $columns;
	}
	
	function manage_columns( $column, $post_id ){
		global $post;
		
		$youtube_link = get_post_meta( $post_id, $this->slug.'_youtube_link', true );
		
		
		if($column=='youtube_link'){
			echo $youtube_link;
		}
	}
	
	/**
	 * Display the image meta box
	 */
	public function meta_box() {
		global $post;
		
		$image_src = '';
		
		$youtube_link = get_post_meta( $post->ID, $this->slug.'_youtube_link', true );
		?>
		<style>
		#<?php echo $this->slug; ?> td{
			vertical-align:top;
		}
		</style>
		<table id='<?php echo $this->slug; ?>' style='width:100%'>
		<tr>
			<td>Youtube Link</td>
			<td><input type="text" name="youtube_link" value="<?php echo htmlentitiesx($youtube_link); ?>" style='width:100%' /></td>
		</tr>
		</table>
		<?php
	}
}

class E_Speaker extends Echelon {
	
	var $slug;
	var $label;
	var $title;
		
	public function __construct() {
		$this->slug = "echelon_speaker";
		$this->label = "Echelon Speakers";
		$this->title = "Speaker Name";
		
		add_action( 'init', $this->init($this->slug, $this->label) );	
		if ( is_admin() ) {
			add_action( 'admin_init', array( &$this, 'admin_init' ) );
		}
	}

	
	/** Admin methods ******************************************************/
	
	
	/**
	 * Initialize the admin, adding actions to properly display and handle 
	 * the carousel custom post type add/edit page
	 */
	public function admin_init() {
		global $pagenow, $post;
		
		if ( $pagenow == 'post-new.php' || $pagenow == 'post.php' || $pagenow == 'edit.php' ) {
			
			add_action( 'add_meta_boxes', array( &$this, 'meta_boxes' ) );
			add_filter( 'enter_title_here', array( &$this, 'enter_title_here' ), 1, 2 );
			add_action( 'save_post', array( &$this, 'meta_boxes_save' ), 1, 2 );
			
			add_filter( 'manage_edit-'.$this->slug."_columns", array( &$this, 'columns' ) ) ;
			add_action( 'manage_'.$this->slug.'_posts_custom_column', array( &$this, 'manage_columns' ), 10, 2 );
			
			add_filter('manage_edit-'.$this->slug.'_sortable_columns',array( &$this,  'order_column_register_sortable'));
			
		}
	}
	
	/**
	* make column sortable
	*/
	function order_column_register_sortable($columns){
	  $columns['order'] = 'order';
	  return $columns;
	}


	/**
	 * Save meta boxes
	 * 
	 * Runs when a post is saved and does an action which the write panel save scripts can hook into.
	 */
	public function meta_boxes_save( $post_id, $post ) {
		if ( empty( $post_id ) || empty( $post ) || empty( $_POST ) ) return;
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
		if ( is_int( wp_is_post_revision( $post ) ) ) return;
		if ( is_int( wp_is_post_autosave( $post ) ) ) return;
		if ( ! current_user_can( 'edit_post', $post_id ) ) return;
		if ( $post->post_type != $this->slug ) return;
			
		$this->process_meta( $post_id, $post );
	}
	
	
	
	
	
	/**
	 * Set a more appropriate placeholder text for the New carousel title field
	 */
	public function enter_title_here( $text, $post ) {
		if ( $post->post_type == $this->slug ) return __( $this->title );
		return $text;
	}
	
	
	
	/**
	 * Add and remove meta boxes from the edit page
	 */
	public function meta_boxes() {
		add_meta_box( $this->slug."-metabox", __( "Speaker Details" ), array( &$this, 'meta_box' ), $this->slug, 'side', 'high' );
	}
	
	/**
	 * Function for processing and storing all carousel data.
	 */
	private function process_meta( $post_id, $post ) {
		update_post_meta( $post_id, $this->slug.'_image_id', $_POST['upload_image_id'] );
		update_post_meta( $post_id, $this->slug.'_image_id2', $_POST['upload_image_id2'] );
		update_post_meta( $post_id, $this->slug.'_designation', $_POST['designation'] );
		update_post_meta( $post_id, $this->slug.'_order', $_POST['order'] );
		update_post_meta( $post_id, $this->slug.'_frontpage', $_POST['frontpage'] );
		update_post_meta( $post_id, $this->slug.'_fb', $_POST['fb'] );
		update_post_meta( $post_id, $this->slug.'_tw', $_POST['tw'] );
		update_post_meta( $post_id, $this->slug.'_in', $_POST['in'] );
		update_post_meta( $post_id, $this->slug.'_exclude', $_POST['exclude'] );
		update_post_meta( $post_id, $this->slug.'_satellites', json_encode($_POST['satellites']) );
		update_post_meta( $post_id, $this->slug.'_url', $_POST['url'] );
	}
	
	//columns in list view
	function columns( $columns ) {
		$columns = array(
			'cb' => '<input type="checkbox" />',
			'title' => __( $this->label ),
			'image' => __( 'Profile Image' ),
			'designation' => __( 'Designation' ),
			'order' => __( 'Order' ),
			'frontpage' => __( 'Frontpage' ),
			'date' => __( 'Date' )
		);

		return $columns;
	}
	
	function manage_columns( $column, $post_id ){
		global $post;
		
		$image_id = get_post_meta( $post_id, $this->slug.'_image_id', true );
		$image_src = wp_get_attachment_url( $image_id );
		$designation = get_post_meta( $post_id, $this->slug.'_designation', true );
		$fb = get_post_meta( $post_id, $this->slug.'_fb', true );
		$tw = get_post_meta( $post_id, $this->slug.'_tw', true );
		$in = get_post_meta( $post_id, $this->slug.'_in', true );
		$order = get_post_meta( $post_id, $this->slug.'_order', true );
		$frontpage = get_post_meta( $post_id, $this->slug.'_frontpage', true );
		$url = get_post_meta( $post_id, $this->slug.'_url', true );
		//$exclude = get_post_meta( $post_id, $this->slug.'_exclude', true );
		//$satellites = get_post_meta( $post_id, $this->slug.'_satellites', true );
		
		if($column=='image'){
			?><img src="<?php echo $image_src ?>" style="max-width:100px;" /><?php
		}
		else if($column=='designation'){
			echo $designation;
		}
		else if($column=='order'){
			echo $order;
		}
		else if($column=='frontpage'){
			echo $frontpage;
		}
		
		
	}
	
	/**
	 * Display the image meta box
	 */
	public function meta_box() {
		global $post;
		
		$image_src = '';
		$image_src2 = '';		
		$image_id = get_post_meta( $post->ID, $this->slug.'_image_id', true );
		$image_src = wp_get_attachment_url( $image_id );	
		$image_id2 = get_post_meta( $post->ID, $this->slug.'_image_id2', true );
		$image_src2 = wp_get_attachment_url( $image_id2 );
		
		$designation = get_post_meta( $post->ID, $this->slug.'_designation', true );
		$order = get_post_meta( $post->ID, $this->slug.'_order', true );
		$frontpage = get_post_meta( $post->ID, $this->slug.'_frontpage', true );
		$fb = get_post_meta( $post->ID, $this->slug.'_fb', true );
		$tw = get_post_meta( $post->ID, $this->slug.'_tw', true );
		$in = get_post_meta( $post->ID, $this->slug.'_in', true );
		$exclude = get_post_meta( $post->ID, $this->slug.'_exclude', true );
		$satellites = get_post_meta( $post->ID, $this->slug.'_satellites', true );
		$url = get_post_meta( $post->ID, $this->slug.'_url', true );
		$satellites = json_decode($satellites);
		$order *= 1;
		?>
		<style>
		#<?php echo $this->slug; ?> td{
			vertical-align:top;
		}
		</style>
		<table id='<?php echo $this->slug; ?>' style='width:100%'>
		<tr>
			<td colspan=2>
			<b>Primary Image (175px x 175px)<br /><br /></b>
			<img id="speaker_image" src="<?php echo $image_src ?>" style="max-width:100%;" />
			<input type="hidden" name="upload_image_id" id="upload_image_id" value="<?php echo $image_id; ?>" />
			<p>
				<input type='button' class='button' value="<?php _e( 'Set Image' ) ?>"  id="set-image" />
				<input type='button' class='button' style="<?php echo ( ! $image_id ? 'display:none;' : '' ); ?>" value="<?php _e( 'Remove Image' ) ?>"  id="remove-image" />
			</p>
			</td>
		</tr>
		<tr>
			<td colspan=2>
			<b>Hover Image (175px x 175px)<br /><br /></b>
			<img id="speaker_image2" src="<?php echo $image_src2 ?>" style="max-width:100%;" />
			<input type="hidden" name="upload_image_id2" id="upload_image_id2" value="<?php echo $image_id2; ?>" />
			<p>
				<input type='button' class='button' value="<?php _e( 'Set Image' ) ?>"  id="set-image2" />
				<input type='button' class='button' style="<?php echo ( ! $image_id2 ? 'display:none;' : '' ); ?>" value="<?php _e( 'Remove Image' ) ?>"  id="remove-image2" />
			</p>
			</td>
		</tr>
		<tr>
			<td>
			<b>Designation (e.g. Founder - e27)<br /><br /></b>
			<input type="text" name="designation" value="<?php echo htmlentitiesx($designation); ?>" style='width:100%' />
			<br /><br />
			<b>Order<br /><br /></b>
			<input type="text" name="order" value="<?php echo htmlentitiesx($order); ?>" style='width:100%' /><br /><br />
			<b>Facebook<br /><br /></b>
			<input type="text" name="fb" value="<?php echo htmlentitiesx($fb); ?>" style='width:100%' /><br /><br />
			<b>Twitter<br /><br /></b>
			<input type="text" name="tw" value="<?php echo htmlentitiesx($tw); ?>" style='width:100%' /><br /><br />
			<b>Linked In<br /><br /></b>
			<input type="text" name="in" value="<?php echo htmlentitiesx($in); ?>" style='width:100%' />
			<br /><br />
			<b>External URL<br /><br /></b>
			<input type="text" name="url" value="<?php echo htmlentitiesx($url); ?>" style='width:100%' />
			<br /><br />
			<b>Show on Frontpage<br /><br /></b>
			<select name='frontpage' id='frontpagex'>
				<option value='Yes'>Yes</option>
				<option value='No'>No</option>
			</select>
			<br /><br />
			<b>Exclude from Speakers Page<br /><br /></b>
			<select name='exclude' id='excludex'>
				<option value='No'>No</option>
				<option value='Yes'>Yes</option>
			</select>
			<br /><br />
			
			<?php
			$ptype = "echelon_satellite";
			$args = array(
				'post_type'=> $ptype,
				'order'    => 'ASC',
				'orderby'	=> 'meta_value',
				'meta_key' 	=> $ptype.'_order',
				'posts_per_page' => -1
			);              
			$the_query = new WP_Query( $args );
			$i=0;
			$arr = array();
			$the = array();
			if($the_query->have_posts() ){
				while ( $the_query->have_posts() ){
					$the_query->the_post();
					$p = get_post( get_the_ID(), OBJECT );
					$arr[] = $p;
				}
			}
			$t = count($arr);
			if($t){
				?><b>is a Speaker for the following Satellite(s)<br /><br /></b><?php
				?><select name='satellites[]' multiple=true style='width:100%; height:100px'><?php
				for($i=0; $i<$t; $i++){
					if(in_array($arr[$i]->ID, $satellites)){
						?><option selected value='<?php echo $arr[$i]->ID; ?>'><?php echo $arr[$i]->post_title; ?></option><?php
					}
					else{
						?><option value='<?php echo $arr[$i]->ID; ?>'><?php echo $arr[$i]->post_title; ?></option><?php
					}
				}
				?></select><?php
			}
			?>
			
			
			
			<script>
			jQuery("#frontpagex").val("<?php echo htmlentitiesx($frontpage); ?>");
			jQuery("#excludex").val("<?php echo htmlentitiesx($exclude); ?>");
			</script>
			
			</td>
		</tr>
		</table>

		<!--- script below -->
		
		<script type="text/javascript">
		jQuery(document).ready(function($) {
			
			
			var imageidtoupdate = "";
			var upload_image_id = "";
			// save the send_to_editor handler function
			window.send_to_editor_default = window.send_to_editor;
	
			$('#set-image').click(function(){
				
				// replace the default send_to_editor handler function with our own
				window.send_to_editor = window.attach_image;
				tb_show('', 'media-upload.php?post_id=<?php echo $post->ID ?>&amp;type=image&amp;TB_iframe=true');
				
				imageidtoupdate = "speaker_image";
				upload_image_id = "upload_image_id";
				return false;
			});
			
			$('#remove-image').click(function() {
				
				$('#upload_image_id').val('');
				$('#speaker_image').attr('src', '');
				$(this).hide();
				
				return false;
			});
			
			$('#set-image2').click(function(){
				
				// replace the default send_to_editor handler function with our own
				window.send_to_editor = window.attach_image;
				tb_show('', 'media-upload.php?post_id=<?php echo $post->ID ?>&amp;type=image&amp;TB_iframe=true');
				
				imageidtoupdate = "speaker_image2";
				upload_image_id = "upload_image_id2";
				return false;
			});
			
			$('#remove-image2').click(function() {
				
				$('#upload_image_id2').val('');
				$('#speaker_image2').attr('src', '');
				$(this).hide();
				
				return false;
			});
			
			// handler function which is invoked after the user selects an image from the gallery popup.
			// this function displays the image and sets the id so it can be persisted to the post meta
			window.attach_image = function(html) {
				
				// turn the returned image html into a hidden image element so we can easily pull the relevant attributes we need
				$('body').append('<div id="temp_image">' + html + '</div>');
					
				var img = $('#temp_image').find('img');
				
				imgurl   = img.attr('src');
				imgclass = img.attr('class');
				imgid    = parseInt(imgclass.replace(/\D/g, ''), 10);
	
				$('#'+upload_image_id).val(imgid);
				$('#remove-image').show();
	
				$('img#'+imageidtoupdate).attr('src', imgurl);
				try{tb_remove();}catch(e){};
				$('#temp_image').remove();
				
				// restore the send_to_editor handler function
				window.send_to_editor = window.send_to_editor_default;
				
			}
	
		});
		</script>
		<?php
	}
}

class E_Startup extends Echelon {
	
	var $slug;
	var $label;
	var $title;
		
	public function __construct() {
		$this->slug = "echelon_startup";
		$this->label = "Echelon Startups";
		$this->title = "Startup Name";
		
		/*
		$args = array(
			'labels' => $labels,
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true,
			'query_var' => true,
			'rewrite' => true,
			'capability_type' => 'post',
			'hierarchical' => false,
			'menu_position' => null,
			'supports' => array('title','thumbnail','excerpt')
		  ); 
		*/
		$supports = array('title');
		$supports = null;
		add_action( 'init', $this->init($this->slug, $this->label, $supports) );	
		if ( is_admin() ) {
			add_action( 'admin_init', array( &$this, 'admin_init' ) );
		}
	}

	
	/** Admin methods ******************************************************/
	
	
	/**
	 * Initialize the admin, adding actions to properly display and handle 
	 * the carousel custom post type add/edit page
	 */
	public function admin_init() {
		global $pagenow, $post;
		
		if ( $pagenow == 'post-new.php' || $pagenow == 'post.php' || $pagenow == 'edit.php' ) {
			
			add_action( 'add_meta_boxes', array( &$this, 'meta_boxes' ) );
			add_filter( 'enter_title_here', array( &$this, 'enter_title_here' ), 1, 2 );
			add_action( 'save_post', array( &$this, 'meta_boxes_save' ), 1, 2 );
			
			add_filter( 'manage_edit-'.$this->slug."_columns", array( &$this, 'columns' ) ) ;
			add_action( 'manage_'.$this->slug.'_posts_custom_column', array( &$this, 'manage_columns' ), 10, 2 );
			
			add_filter('manage_edit-'.$this->slug.'_sortable_columns',array( &$this,  'order_column_register_sortable'));
			
			
			if(get_post_type($_GET['post']) == $this->slug||$_GET['post_type']==$this->slug){
				add_action( 'admin_head', array( &$this, 'hide_editor' ));
			}
			
			
			
		}
	}
	
	/**
	* make column sortable
	*/
	function order_column_register_sortable($columns){
	  $columns['order'] = 'order';
	  return $columns;
	}


	/**
	 * Save meta boxes
	 * 
	 * Runs when a post is saved and does an action which the write panel save scripts can hook into.
	 */
	public function meta_boxes_save( $post_id, $post ) {
		if ( empty( $post_id ) || empty( $post ) || empty( $_POST ) ) return;
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
		if ( is_int( wp_is_post_revision( $post ) ) ) return;
		if ( is_int( wp_is_post_autosave( $post ) ) ) return;
		if ( ! current_user_can( 'edit_post', $post_id ) ) return;
		if ( $post->post_type != $this->slug ) return;
			
		$this->process_meta( $post_id, $post );
	}
	
	
	
	
	
	/**
	 * Set a more appropriate placeholder text for the New carousel title field
	 */
	public function enter_title_here( $text, $post ) {
		if ( $post->post_type == $this->slug ) return __( $this->title );
		return $text;
	}
	
	
	
	/**
	 * Add and remove meta boxes from the edit page
	 */
	public function meta_boxes() {
		add_meta_box( $this->slug."-metabox", __( "Startup Details" ), array( &$this, 'meta_box' ), $this->slug, 'normal', 'high' );
	}
	
	/**
	 * Function for processing and storing all carousel data.
	 */
	private function process_meta( $post_id, $post ) {
		update_post_meta( $post_id, $this->slug.'_image_id', $_POST['upload_image_id'] );
		update_post_meta( $post_id, $this->slug.'_order', $_POST['order'] );
		update_post_meta( $post_id, $this->slug.'_country', $_POST['country'] );
		update_post_meta( $post_id, $this->slug.'_excerpt', $_POST['excerpt'] );
		update_post_meta( $post_id, $this->slug.'_url', $_POST['url'] );
	}
	
	//columns in list view
	function columns( $columns ) {
		$columns = array(
			'cb' => '<input type="checkbox" />',
			'title' => __( $this->label ),
			'image' => __( 'Image' ),
			'order' => __( 'Order' ),
			'country' => __( 'Country' ),
			'date' => __( 'Date' )
		);

		return $columns;
	}
	
	function manage_columns( $column, $post_id ){
		global $post;
		
		$image_id = get_post_meta( $post_id, $this->slug.'_image_id', true );
		$image_src = wp_get_attachment_url( $image_id );
		$order = get_post_meta( $post_id, $this->slug.'_order', true );
		$country = get_post_meta( $post_id, $this->slug.'_country', true );
		
		if($column=='image'){
			?><img id="carousel_image" src="<?php echo $image_src ?>" style="max-width:100px;" /><?php
		}
		else if($column=='order'){
			echo $order;
		}
		else if($column=='country'){
			echo $country;
		}
		
		
	}
	
	/**
	 * Display the image meta box
	 */
	public function meta_box() {
		global $post;
		
		$image_src = '';
		
		$image_id = get_post_meta( $post->ID, $this->slug.'_image_id', true );
		$image_src = wp_get_attachment_url( $image_id );
		$order = get_post_meta( $post->ID, $this->slug.'_order', true );
		$country = get_post_meta( $post->ID, $this->slug.'_country', true );
		$excerpt = get_post_meta( $post->ID, $this->slug.'_excerpt', true );
		$url = get_post_meta( $post->ID, $this->slug.'_url', true );
		$order *= 1;
		?>
		<style>
		#<?php echo $this->slug; ?> td{
			vertical-align:top;
		}
		</style>
		<table id='<?php echo $this->slug; ?>' style='width:100%'>
		<tr>
			<td colspan=2>
			<b>Image (220x80)<br /><br /></b>
			<img id="speaker_image" src="<?php echo $image_src ?>" style="max-width:100%;" />
			<input type="hidden" name="upload_image_id" id="upload_image_id" value="<?php echo $image_id; ?>" />
			<p>
				<input type='button' class='button' value="<?php _e( 'Set Image' ) ?>"  id="set-image" />
				<input type='button' class='button' style="<?php echo ( ! $image_id ? 'display:none;' : '' ); ?>" value="<?php _e( 'Remove Image' ) ?>"  id="remove-image" />
			</p>
			</td>
		</tr>
		<tr>
			<td>
			<b>Order<br /><br /></b>
			<input type="text" name="order" value="<?php echo htmlentitiesx($order); ?>" style='width:100%' /><br /><br />
			<b>Country<br /><br /></b>
			<input type="text" name="country" value="<?php echo htmlentitiesx($country); ?>" style='width:100%' /><br /><br />
			<b>Website URL<br /><br /></b>
			<input type="text" name="url" value="<?php echo htmlentitiesx($url); ?>" style='width:100%' /><br /><br />
			<b>Excerpt<br /><br /></b>
			<textarea name="excerpt" style='width:100%; height:70px'><?php echo htmlentitiesx($excerpt); ?></textarea><br /><br />
			</td>
		</tr>
		</table>

		<!--- script below -->
		
		<script type="text/javascript">
		jQuery(document).ready(function($) {
			
			// save the send_to_editor handler function
			window.send_to_editor_default = window.send_to_editor;
	
			$('#set-image').click(function(){
				
				// replace the default send_to_editor handler function with our own
				window.send_to_editor = window.attach_image;
				tb_show('', 'media-upload.php?post_id=<?php echo $post->ID ?>&amp;type=image&amp;TB_iframe=true');
				
				return false;
			});
			
			$('#remove-image').click(function() {
				
				$('#upload_image_id').val('');
				$('img').attr('src', '');
				$(this).hide();
				
				return false;
			});
			
			// handler function which is invoked after the user selects an image from the gallery popup.
			// this function displays the image and sets the id so it can be persisted to the post meta
			window.attach_image = function(html) {
				
				// turn the returned image html into a hidden image element so we can easily pull the relevant attributes we need
				$('body').append('<div id="temp_image">' + html + '</div>');
					
				var img = $('#temp_image').find('img');
				
				imgurl   = img.attr('src');
				imgclass = img.attr('class');
				imgid    = parseInt(imgclass.replace(/\D/g, ''), 10);
	
				$('#upload_image_id').val(imgid);
				$('#remove-image').show();
	
				$('img#speaker_image').attr('src', imgurl);
				try{tb_remove();}catch(e){};
				$('#temp_image').remove();
				
				// restore the send_to_editor handler function
				window.send_to_editor = window.send_to_editor_default;
				
			}
	
		});
		</script>
		<?php
	}
}


class E_Staff extends Echelon {
	
	var $slug;
	var $label;
	var $title;
		
	public function __construct() {
		$this->slug = "echelon_staff";
		$this->label = "Echelon Crew";
		$this->title = "Staff Name";
		
		add_action( 'init', $this->init($this->slug, $this->label) );	
		if ( is_admin() ) {
			add_action( 'admin_init', array( &$this, 'admin_init' ) );
		}
	}

	
	/** Admin methods ******************************************************/
	
	
	/**
	 * Initialize the admin, adding actions to properly display and handle 
	 * the carousel custom post type add/edit page
	 */
	public function admin_init() {
		global $pagenow, $post;
		
		if ( $pagenow == 'post-new.php' || $pagenow == 'post.php' || $pagenow == 'edit.php' ) {
			
			add_action( 'add_meta_boxes', array( &$this, 'meta_boxes' ) );
			add_filter( 'enter_title_here', array( &$this, 'enter_title_here' ), 1, 2 );
			add_action( 'save_post', array( &$this, 'meta_boxes_save' ), 1, 2 );
			
			add_filter( 'manage_edit-'.$this->slug."_columns", array( &$this, 'columns' ) ) ;
			add_action( 'manage_'.$this->slug.'_posts_custom_column', array( &$this, 'manage_columns' ), 10, 2 );
			
			add_filter('manage_edit-'.$this->slug.'_sortable_columns',array( &$this,  'order_column_register_sortable'));
			
		}
	}
	
	/**
	* make column sortable
	*/
	function order_column_register_sortable($columns){
	  $columns['order'] = 'order';
	  return $columns;
	}


	/**
	 * Save meta boxes
	 * 
	 * Runs when a post is saved and does an action which the write panel save scripts can hook into.
	 */
	public function meta_boxes_save( $post_id, $post ) {
		if ( empty( $post_id ) || empty( $post ) || empty( $_POST ) ) return;
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
		if ( is_int( wp_is_post_revision( $post ) ) ) return;
		if ( is_int( wp_is_post_autosave( $post ) ) ) return;
		if ( ! current_user_can( 'edit_post', $post_id ) ) return;
		if ( $post->post_type != $this->slug ) return;
			
		$this->process_meta( $post_id, $post );
	}
	
	
	
	
	
	/**
	 * Set a more appropriate placeholder text for the New carousel title field
	 */
	public function enter_title_here( $text, $post ) {
		if ( $post->post_type == $this->slug ) return __( $this->title );
		return $text;
	}
	
	
	
	/**
	 * Add and remove meta boxes from the edit page
	 */
	public function meta_boxes() {
		add_meta_box( $this->slug."-metabox", __( "Speaker Details" ), array( &$this, 'meta_box' ), $this->slug, 'side', 'high' );
	}
	
	/**
	 * Function for processing and storing all carousel data.
	 */
	private function process_meta( $post_id, $post ) {
		update_post_meta( $post_id, $this->slug.'_image_id', $_POST['upload_image_id'] );
		update_post_meta( $post_id, $this->slug.'_designation', $_POST['designation'] );
		update_post_meta( $post_id, $this->slug.'_order', $_POST['order'] );
		update_post_meta( $post_id, $this->slug.'_frontpage', $_POST['frontpage'] );
		update_post_meta( $post_id, $this->slug.'_fb', $_POST['fb'] );
		update_post_meta( $post_id, $this->slug.'_tw', $_POST['tw'] );
		update_post_meta( $post_id, $this->slug.'_in', $_POST['in'] );
	}
	
	//columns in list view
	function columns( $columns ) {
		$columns = array(
			'cb' => '<input type="checkbox" />',
			'title' => __( $this->label ),
			'image' => __( 'Profile Image' ),
			'designation' => __( 'Designation' ),
			'order' => __( 'Order' ),
			'frontpage' => __( 'Frontpage' ),
			'date' => __( 'Date' )
		);

		return $columns;
	}
	
	function manage_columns( $column, $post_id ){
		global $post;
		
		$image_id = get_post_meta( $post_id, $this->slug.'_image_id', true );
		$image_src = wp_get_attachment_url( $image_id );
		$designation = get_post_meta( $post_id, $this->slug.'_designation', true );
		$fb = get_post_meta( $post_id, $this->slug.'_fb', true );
		$tw = get_post_meta( $post_id, $this->slug.'_tw', true );
		$in = get_post_meta( $post_id, $this->slug.'_in', true );
		$order = get_post_meta( $post_id, $this->slug.'_order', true );
		$frontpage = get_post_meta( $post_id, $this->slug.'_frontpage', true );
		
		if($column=='image'){
			?><img id="carousel_image" src="<?php echo $image_src ?>" style="max-width:100px;" /><?php
		}
		else if($column=='designation'){
			echo $designation;
		}
		else if($column=='order'){
			echo $order;
		}
		else if($column=='frontpage'){
			echo $frontpage;
		}
		
		
	}
	
	/**
	 * Display the image meta box
	 */
	public function meta_box() {
		global $post;
		
		$image_src = '';
		
		$image_id = get_post_meta( $post->ID, $this->slug.'_image_id', true );
		$image_src = wp_get_attachment_url( $image_id );
		$designation = get_post_meta( $post->ID, $this->slug.'_designation', true );
		$order = get_post_meta( $post->ID, $this->slug.'_order', true );
		$frontpage = get_post_meta( $post->ID, $this->slug.'_frontpage', true );
		$fb = get_post_meta( $post->ID, $this->slug.'_fb', true );
		$tw = get_post_meta( $post->ID, $this->slug.'_tw', true );
		$in = get_post_meta( $post->ID, $this->slug.'_in', true );
		$order *= 1;
		?>
		<style>
		#<?php echo $this->slug; ?> td{
			vertical-align:top;
		}
		</style>
		<table id='<?php echo $this->slug; ?>' style='width:100%'>
		<tr>
			<td colspan=2>
			<b>Profile Image (175px x 175px)<br /><br /></b>
			<img id="speaker_image" src="<?php echo $image_src ?>" style="max-width:100%;" />
			<input type="hidden" name="upload_image_id" id="upload_image_id" value="<?php echo $image_id; ?>" />
			<p>
				<input type='button' class='button' value="<?php _e( 'Set Image' ) ?>"  id="set-image" />
				<input type='button' class='button' style="<?php echo ( ! $image_id ? 'display:none;' : '' ); ?>" value="<?php _e( 'Remove Image' ) ?>"  id="remove-image" />
			</p>
			</td>
		</tr>
		<tr>
			<td>
			<b>Designation (e.g. Media & Events Manage)<br /><br /></b>
			<input type="text" name="designation" value="<?php echo htmlentitiesx($designation); ?>" style='width:100%' />
			<br /><br />
			<b>Order<br /><br /></b>
			<input type="text" name="order" value="<?php echo htmlentitiesx($order); ?>" style='width:100%' /><br /><br />
			<b>Facebook<br /><br /></b>
			<input type="text" name="fb" value="<?php echo htmlentitiesx($fb); ?>" style='width:100%' /><br /><br />
			<b>Twitter<br /><br /></b>
			<input type="text" name="tw" value="<?php echo htmlentitiesx($tw); ?>" style='width:100%' /><br /><br />
			<b>Linked In<br /><br /></b>
			<input type="text" name="in" value="<?php echo htmlentitiesx($in); ?>" style='width:100%' />
			<br /><br />
			<b>Frontpage<br /><br /></b>
			<select name='frontpage' id='frontpagex'>
				<option value='Yes'>Yes</option>
				<option value='No'>No</option>
			</select>
			<script>
			jQuery("#frontpagex").val("<?php echo htmlentitiesx($frontpage); ?>");
			</script>
			
			</td>
		</tr>
		</table>

		<!--- script below -->
		
		<script type="text/javascript">
		jQuery(document).ready(function($) {
			
			// save the send_to_editor handler function
			window.send_to_editor_default = window.send_to_editor;
	
			$('#set-image').click(function(){
				
				// replace the default send_to_editor handler function with our own
				window.send_to_editor = window.attach_image;
				tb_show('', 'media-upload.php?post_id=<?php echo $post->ID ?>&amp;type=image&amp;TB_iframe=true');
				
				return false;
			});
			
			$('#remove-image').click(function() {
				
				$('#upload_image_id').val('');
				$('img').attr('src', '');
				$(this).hide();
				
				return false;
			});
			
			// handler function which is invoked after the user selects an image from the gallery popup.
			// this function displays the image and sets the id so it can be persisted to the post meta
			window.attach_image = function(html) {
				
				// turn the returned image html into a hidden image element so we can easily pull the relevant attributes we need
				$('body').append('<div id="temp_image">' + html + '</div>');
					
				var img = $('#temp_image').find('img');
				
				imgurl   = img.attr('src');
				imgclass = img.attr('class');
				imgid    = parseInt(imgclass.replace(/\D/g, ''), 10);
	
				$('#upload_image_id').val(imgid);
				$('#remove-image').show();
	
				$('img#speaker_image').attr('src', imgurl);
				try{tb_remove();}catch(e){};
				$('#temp_image').remove();
				
				// restore the send_to_editor handler function
				window.send_to_editor = window.send_to_editor_default;
				
			}
	
		});
		</script>
		<?php
	}
}


class E_Quote extends Echelon {
	
	var $slug;
	var $label;
	var $title;
		
	public function __construct() {
		$this->slug = "e_quote";
		$this->label = "Echelon Quotes";
		$this->title = "&lt;Quote from Name&gt; ~ &lt;Designation&gt;";
		
		add_action( 'init', $this->init($this->slug, $this->label) );	
		if ( is_admin() ) {
			add_action( 'admin_init', array( &$this, 'admin_init' ) );
		}
	}

	
	/** Admin methods ******************************************************/
	
	
	/**
	 * Initialize the admin, adding actions to properly display and handle 
	 * the carousel custom post type add/edit page
	 */
	public function admin_init() {
		global $pagenow, $post;
		
		if ( $pagenow == 'post-new.php' || $pagenow == 'post.php' || $pagenow == 'edit.php' ) {
			
			add_action( 'add_meta_boxes', array( &$this, 'meta_boxes' ) );
			add_filter( 'enter_title_here', array( &$this, 'enter_title_here' ), 1, 2 );
			add_action( 'save_post', array( &$this, 'meta_boxes_save' ), 1, 2 );
			
			add_filter( 'manage_edit-'.$this->slug."_columns", array( &$this, 'columns' ) ) ;
			add_action( 'manage_'.$this->slug.'_posts_custom_column', array( &$this, 'manage_columns' ), 10, 2 );
			
			add_filter('manage_edit-'.$this->slug.'_sortable_columns',array( &$this,  'order_column_register_sortable'));
			
		}
	}
	
	/**
	* make column sortable
	*/
	function order_column_register_sortable($columns){
	 // $columns['order'] = 'order';
	  return $columns;
	}


	/**
	 * Save meta boxes
	 * 
	 * Runs when a post is saved and does an action which the write panel save scripts can hook into.
	 */
	public function meta_boxes_save( $post_id, $post ) {
		if ( empty( $post_id ) || empty( $post ) || empty( $_POST ) ) return;
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
		if ( is_int( wp_is_post_revision( $post ) ) ) return;
		if ( is_int( wp_is_post_autosave( $post ) ) ) return;
		if ( ! current_user_can( 'edit_post', $post_id ) ) return;
		if ( $post->post_type != $this->slug ) return;
			
		$this->process_meta( $post_id, $post );
	}
	
	
	
	
	
	/**
	 * Set a more appropriate placeholder text for the New carousel title field
	 */
	public function enter_title_here( $text, $post ) {
		if ( $post->post_type == $this->slug ) return __( $this->title );
		return $text;
	}
	
	
	
	/**
	 * Add and remove meta boxes from the edit page
	 */
	public function meta_boxes() {
		add_meta_box( $this->slug."-metabox", __( "Image" ), array( &$this, 'meta_box' ), $this->slug, 'side', 'high' );
	}
	
	/**
	 * Function for processing and storing all carousel data.
	 */
	private function process_meta( $post_id, $post ) {
		update_post_meta( $post_id, $this->slug.'_image_id', $_POST['upload_image_id'] );
	}
	
	//columns in list view
	function columns( $columns ) {
		$columns = array(
			'cb' => '<input type="checkbox" />',
			'title' => __( $this->label ),
			'image' => __( 'Image' ),
			'date' => __( 'Date' )
		);

		return $columns;
	}
	
	function manage_columns( $column, $post_id ){
		global $post;
		
		$image_id = get_post_meta( $post_id, $this->slug.'_image_id', true );
		$image_src = wp_get_attachment_url( $image_id );
		
		if($column=='image'){
			?><img id="carousel_image" src="<?php echo $image_src ?>" style="max-width:100px;" /><?php
		}
		
		
	}
	
	/**
	 * Display the image meta box
	 */
	public function meta_box() {
		global $post;
		
		$image_src = '';
		
		$image_id = get_post_meta( $post->ID, $this->slug.'_image_id', true );
		$image_src = wp_get_attachment_url( $image_id );
		?>
		<style>
		#<?php echo $this->slug; ?> td{
			vertical-align:top;
		}
		</style>
		<table id='<?php echo $this->slug; ?>' style='width:100%'>
		<tr>
			<td colspan=2>
			<b>Image (175px x 175px)<br /><br /></b>
			<img id="speaker_image" src="<?php echo $image_src ?>" style="max-width:100%;" />
			<input type="hidden" name="upload_image_id" id="upload_image_id" value="<?php echo $image_id; ?>" />
			<p>
				<input type='button' class='button' value="<?php _e( 'Set Image' ) ?>"  id="set-image" />
				<input type='button' class='button' style="<?php echo ( ! $image_id ? 'display:none;' : '' ); ?>" value="<?php _e( 'Remove Image' ) ?>"  id="remove-image" />
			</p>
			</td>
		</tr>
		</table>

		<!--- script below -->
		
		<script type="text/javascript">
		jQuery(document).ready(function($) {
			
			// save the send_to_editor handler function
			window.send_to_editor_default = window.send_to_editor;
	
			$('#set-image').click(function(){
				
				// replace the default send_to_editor handler function with our own
				window.send_to_editor = window.attach_image;
				tb_show('', 'media-upload.php?post_id=<?php echo $post->ID ?>&amp;type=image&amp;TB_iframe=true');
				
				return false;
			});
			
			$('#remove-image').click(function() {
				
				$('#upload_image_id').val('');
				$('img').attr('src', '');
				$(this).hide();
				
				return false;
			});
			
			// handler function which is invoked after the user selects an image from the gallery popup.
			// this function displays the image and sets the id so it can be persisted to the post meta
			window.attach_image = function(html) {
				
				// turn the returned image html into a hidden image element so we can easily pull the relevant attributes we need
				$('body').append('<div id="temp_image">' + html + '</div>');
					
				var img = $('#temp_image').find('img');
				
				imgurl   = img.attr('src');
				imgclass = img.attr('class');
				imgid    = parseInt(imgclass.replace(/\D/g, ''), 10);
	
				$('#upload_image_id').val(imgid);
				$('#remove-image').show();
	
				$('img#speaker_image').attr('src', imgurl);
				try{tb_remove();}catch(e){};
				$('#temp_image').remove();
				
				// restore the send_to_editor handler function
				window.send_to_editor = window.send_to_editor_default;
				
			}
	
		});
		</script>
		<?php
	}
}


class E_Sponsor extends Echelon {
	
	var $slug;
	var $label;
	var $title;
		
	public function __construct() {
		$this->slug = "echelon_sponsor";
		$this->label = "Echelon Sponsors";
		$this->title = "Sponsor Name";
		
		add_action( 'init', $this->init($this->slug, $this->label) );	
		if ( is_admin() ) {
			add_action( 'admin_init', array( &$this, 'admin_init' ) );
		}
	}

	
	/** Admin methods ******************************************************/
	
	
	/**
	 * Initialize the admin, adding actions to properly display and handle 
	 * the carousel custom post type add/edit page
	 */
	public function admin_init() {
		global $pagenow, $post;
		
		if ( $pagenow == 'post-new.php' || $pagenow == 'post.php' || $pagenow == 'edit.php' ) {
			
			add_action( 'add_meta_boxes', array( &$this, 'meta_boxes' ) );
			add_filter( 'enter_title_here', array( &$this, 'enter_title_here' ), 1, 2 );
			add_action( 'save_post', array( &$this, 'meta_boxes_save' ), 1, 2 );
			
			add_filter( 'manage_edit-'.$this->slug."_columns", array( &$this, 'columns' ) ) ;
			add_action( 'manage_'.$this->slug.'_posts_custom_column', array( &$this, 'manage_columns' ), 10, 2 );
			
			add_filter('manage_edit-'.$this->slug.'_sortable_columns',array( &$this,  'order_column_register_sortable'));
			
		}
	}
	
	/**
	* make column sortable
	*/
	function order_column_register_sortable($columns){
	  $columns['order'] = 'order';
	  $columns['type'] = 'type';
	  $columns['clicks'] = 'clicks';
	  $columns['views'] = 'views';
	  return $columns;
	}


	/**
	 * Save meta boxes
	 * 
	 * Runs when a post is saved and does an action which the write panel save scripts can hook into.
	 */
	public function meta_boxes_save( $post_id, $post ) {
		if ( empty( $post_id ) || empty( $post ) || empty( $_POST ) ) return;
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
		if ( is_int( wp_is_post_revision( $post ) ) ) return;
		if ( is_int( wp_is_post_autosave( $post ) ) ) return;
		if ( ! current_user_can( 'edit_post', $post_id ) ) return;
		if ( $post->post_type != $this->slug ) return;
			
		$this->process_meta( $post_id, $post );
	}
	
	
	
	
	
	/**
	 * Set a more appropriate placeholder text for the New carousel title field
	 */
	public function enter_title_here( $text, $post ) {
		if ( $post->post_type == $this->slug ) return __( $this->title );
		return $text;
	}
	
	
	
	/**
	 * Add and remove meta boxes from the edit page
	 */
	public function meta_boxes() {
		add_meta_box( $this->slug."-metabox", __( "Sponsor Details" ), array( &$this, 'meta_box' ), $this->slug, 'side', 'high' );
	}
	
	/**
	 * Function for processing and storing all carousel data.
	 */
	private function process_meta( $post_id, $post ) {
		update_post_meta( $post_id, $this->slug.'_image_id', $_POST['upload_image_id'] );
		update_post_meta( $post_id, $this->slug.'_type', $_POST['type'] );
		update_post_meta( $post_id, $this->slug.'_order', $_POST['order'] );
		update_post_meta( $post_id, $this->slug.'_html', $_POST['html'] );
		update_post_meta( $post_id, $this->slug.'_link', $_POST['link'] );
		update_post_meta( $post_id, $this->slug.'_sidebar', $_POST['sidebar'] );
		update_post_meta( $post_id, $this->slug.'_exclude', $_POST['exclude'] );
		update_post_meta( $post_id, $this->slug.'_satellites', json_encode($_POST['satellites']) );
	}
	
	//columns in list view
	function columns( $columns ) {
		$columns = array(
			'cb' => '<input type="checkbox" />',
			'title' => __( $this->label ),
			'image' => __( 'Profile Image' ),
			'type' => __( 'Type' ),
			'order' => __( 'Order' ),
			'views' => __( 'Views' ),
			'clicks' => __( 'Clicks' ),
			'date' => __( 'Date' )
		);

		return $columns;
	}
	
	function manage_columns( $column, $post_id ){
		global $post;
		
		$image_id = get_post_meta( $post_id, $this->slug.'_image_id', true );
		$image_src = wp_get_attachment_url( $image_id );
		$type = get_post_meta( $post_id, $this->slug.'_type', true );
		$order = get_post_meta( $post_id, $this->slug.'_order', true );
		$html = get_post_meta( $post_id, $this->slug.'_html', true );
		$views = get_post_meta( $post_id, $this->slug.'_views', true );
		$clicks = get_post_meta( $post_id, $this->slug.'_clicks', true );
		
		$views *= 1;
		$clicks *= 1;
		$views += 0;
		$clicks += 0;
		
		if($column=='image'){
			?><img id="carousel_image" src="<?php echo $image_src ?>" style="max-width:100px;" /><?php
		}
		else if($column=='type'){
			echo $type;
		}
		else if($column=='html'){
			echo $html;
		}
		else if($column=='order'){
			echo $order;
		}
		else if($column=='views'){
			echo $views;
		}
		else if($column=='clicks'){
			echo $clicks;
		}
		
		
	}
	
	/**
	 * Display the image meta box
	 */
	public function meta_box() {
		global $post;
		
		$image_src = '';
		
		$image_id = get_post_meta( $post->ID, $this->slug.'_image_id', true );
		$image_src = wp_get_attachment_url( $image_id );
		$type = get_post_meta( $post->ID, $this->slug.'_type', true );
		$order = get_post_meta( $post->ID, $this->slug.'_order', true );
		$html = get_post_meta( $post->ID, $this->slug.'_html', true );
		$link = get_post_meta( $post->ID, $this->slug.'_link', true );
		$views = get_post_meta( $post->ID, $this->slug.'_views', true );
		$clicks = get_post_meta( $post->ID, $this->slug.'_clicks', true );
		
		$sidebar = get_post_meta( $post->ID, $this->slug.'_sidebar', true );
		$exclude = get_post_meta( $post->ID, $this->slug.'_exclude', true );
		$satellites = get_post_meta( $post->ID, $this->slug.'_satellites', true );
		$satellites = json_decode($satellites);
		
		$order *= 1;
		$views *= 1;
		$clicks *= 1;
		?>
		<style>
		#<?php echo $this->slug; ?> td{
			vertical-align:top;
		}
		</style>
		<table id='<?php echo $this->slug; ?>' style='width:100%'>
		<tr>
			<td colspan=2>
			<b>Logo (175px x 175px)<br /><br /></b>
			<img id="<?php echo $this->slug; ?>_image" src="<?php echo $image_src ?>" style="max-width:100%;" />
			<input type="hidden" name="upload_image_id" id="upload_image_id" value="<?php echo $image_id; ?>" />
			<p>
				<input type='button' class='button' value="<?php _e( 'Set Image' ) ?>"  id="set-image" />
				<input type='button' class='button' style="<?php echo ( ! $image_id ? 'display:none;' : '' ); ?>" value="<?php _e( 'Remove Image' ) ?>"  id="remove-image" />
			</p>
			<br />
			<b>URL Link<br /></b>
			<input type="text" name="link" value="<?php echo htmlentitiesx($link); ?>" style='width:100%' />
			<br /><br />
			<b>Views: <?php echo $views; ?> Clicks: <?php echo $clicks; ?> <br /></b>
			<br /><br />
			</td>
		</tr>
		<tr>
			<td>
			<br /><br />
			<b>Category<br /></b>
			<select name='type' id='xtype'>
				<option value='Strategic Partners'>Strategic Partners</option>
				<option value='Premier Sponsors'>Premier Sponsors</option>
				<option value='Supporting Sponsors'>Supporting Sponsors</option>
				<option value='Regular Sponsors'>Regular Sponsors</option>
				<option value='Tech Sponsors'>Tech Sponsors</option>
				<option value='Launchpad Prize Sponsors'>Launchpad Prize Sponsors</option>
				<option value='Supporting Organizations'>Supporting Organizations</option>
				<option value='Afterevent Sponsors'>Afterevent Sponsors</option>
				<option value='Chill Out Sponsors'>Chill Out Sponsors</option>
				<option value='Country Sponsors'>Country Sponsors</option>
			</select>
			<script>
			jQuery("#xtype").val("<?php echo $type; ?>");
			</script>
			<br /><br />
			<b>Order<br /></b>
			<input type="text" name="order" value="<?php echo htmlentitiesx($order); ?>" style='width:100%' />
			
			</td>
		</tr>
		<tr>
			<td>
			<br /><br />
			----- Or -----
			<br /><br />
			<b>HTML<br /></b>
			<textarea name='html' style='width:100%; height:200px'><?php echo htmlentitiesx($html);  ?></textarea>
			
			</td>
		</tr>
		<tr>
			<td>
			<br /><br />
			<b>Show on Sidebar<br /><br /></b>
			<select name='sidebar' id='sidebarx'>
				<option value='Yes'>Yes</option>
				<option value='No'>No</option>
			</select>
			<br /><br />
			<b>Exclude from Sponsors Page<br /><br /></b>
			<select name='exclude' id='excludex'>
				<option value='No'>No</option>
				<option value='Yes'>Yes</option>
			</select>
			<br /><br />
			
			<?php
			$ptype = "echelon_satellite";
			$args = array(
				'post_type'=> $ptype,
				'order'    => 'ASC',
				'orderby'	=> 'meta_value',
				'meta_key' 	=> $ptype.'_order',
				'posts_per_page' => -1
			);              
			$the_query = new WP_Query( $args );
			$i=0;
			$arr = array();
			$the = array();
			if($the_query->have_posts() ){
				while ( $the_query->have_posts() ){
					$the_query->the_post();
					$p = get_post( get_the_ID(), OBJECT );
					$arr[] = $p;
				}
			}
			$t = count($arr);
			if($t){
				?><b>is a Sponsor for the following Satellite(s)<br /><br /></b><?php
				?><select name='satellites[]' multiple=true style='width:100%; height:100px'><?php
				for($i=0; $i<$t; $i++){
					if(in_array($arr[$i]->ID, $satellites)){
						?><option selected value='<?php echo $arr[$i]->ID; ?>'><?php echo $arr[$i]->post_title; ?></option><?php
					}
					else{
						?><option value='<?php echo $arr[$i]->ID; ?>'><?php echo $arr[$i]->post_title; ?></option><?php
					}
				}
				?></select><?php
			}
			?>
			
			
			
			<script>
			jQuery("#sidebarx").val("<?php echo htmlentitiesx($sidebar); ?>");
			jQuery("#excludex").val("<?php echo htmlentitiesx($exclude); ?>");
			</script>
			</td>
		</td>
		</table>

		<!--- script below -->
		
		<script type="text/javascript">
		jQuery(document).ready(function($) {
			
			// save the send_to_editor handler function
			window.send_to_editor_default = window.send_to_editor;
	
			$('#set-image').click(function(){
				
				// replace the default send_to_editor handler function with our own
				window.send_to_editor = window.attach_image;
				tb_show('', 'media-upload.php?post_id=<?php echo $post->ID ?>&amp;type=image&amp;TB_iframe=true');
				
				return false;
			});
			
			$('#remove-image').click(function() {
				
				$('#upload_image_id').val('');
				$('img').attr('src', '');
				$(this).hide();
				
				return false;
			});
			
			// handler function which is invoked after the user selects an image from the gallery popup.
			// this function displays the image and sets the id so it can be persisted to the post meta
			window.attach_image = function(html) {
				
				// turn the returned image html into a hidden image element so we can easily pull the relevant attributes we need
				$('body').append('<div id="temp_image">' + html + '</div>');
					
				var img = $('#temp_image').find('img');
				
				imgurl   = img.attr('src');
				imgclass = img.attr('class');
				imgid    = parseInt(imgclass.replace(/\D/g, ''), 10);
	
				$('#upload_image_id').val(imgid);
				$('#remove-image').show();
	
				$('img#<?php echo $this->slug; ?>_image').attr('src', imgurl);
				try{tb_remove();}catch(e){};
				$('#temp_image').remove();
				
				// restore the send_to_editor handler function
				window.send_to_editor = window.send_to_editor_default;
				
			}
	
		});
		</script>
		<?php
	}
}


class E_MediaPartner extends Echelon {
	
	var $slug;
	var $label;
	var $title;
		
	public function __construct() {
		$this->slug = "mediapartner";
		$this->label = "Echelon Media Partners";
		$this->title = "Media Partner Name";
		
		add_action( 'init', $this->init($this->slug, $this->label) );	
		if ( is_admin() ) {
			add_action( 'admin_init', array( &$this, 'admin_init' ) );
		}
	}

	
	/** Admin methods ******************************************************/
	
	
	/**
	 * Initialize the admin, adding actions to properly display and handle 
	 * the carousel custom post type add/edit page
	 */
	public function admin_init() {
		global $pagenow, $post;
		
		if ( $pagenow == 'post-new.php' || $pagenow == 'post.php' || $pagenow == 'edit.php' ) {
			
			add_action( 'add_meta_boxes', array( &$this, 'meta_boxes' ) );
			add_filter( 'enter_title_here', array( &$this, 'enter_title_here' ), 1, 2 );
			add_action( 'save_post', array( &$this, 'meta_boxes_save' ), 1, 2 );
			
			add_filter( 'manage_edit-'.$this->slug."_columns", array( &$this, 'columns' ) ) ;
			add_action( 'manage_'.$this->slug.'_posts_custom_column', array( &$this, 'manage_columns' ), 10, 2 );
			
			add_filter('manage_edit-'.$this->slug.'_sortable_columns',array( &$this,  'order_column_register_sortable'));
			
		}
	}
	
	/**
	* make column sortable
	*/
	function order_column_register_sortable($columns){
	  $columns['order'] = 'order';
	  $columns['type'] = 'type';
	  $columns['clicks'] = 'clicks';
	  $columns['views'] = 'views';
	  return $columns;
	}


	/**
	 * Save meta boxes
	 * 
	 * Runs when a post is saved and does an action which the write panel save scripts can hook into.
	 */
	public function meta_boxes_save( $post_id, $post ) {
		if ( empty( $post_id ) || empty( $post ) || empty( $_POST ) ) return;
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
		if ( is_int( wp_is_post_revision( $post ) ) ) return;
		if ( is_int( wp_is_post_autosave( $post ) ) ) return;
		if ( ! current_user_can( 'edit_post', $post_id ) ) return;
		if ( $post->post_type != $this->slug ) return;
			
		$this->process_meta( $post_id, $post );
	}
	
	
	
	
	
	/**
	 * Set a more appropriate placeholder text for the New carousel title field
	 */
	public function enter_title_here( $text, $post ) {
		if ( $post->post_type == $this->slug ) return __( $this->title );
		return $text;
	}
	
	
	
	/**
	 * Add and remove meta boxes from the edit page
	 */
	public function meta_boxes() {
		add_meta_box( $this->slug."-metabox", __( "Media Partner Details" ), array( &$this, 'meta_box' ), $this->slug, 'side', 'high' );
	}
	
	/**
	 * Function for processing and storing all carousel data.
	 */
	private function process_meta( $post_id, $post ) {
		update_post_meta( $post_id, $this->slug.'_image_id', $_POST['upload_image_id'] );
		update_post_meta( $post_id, $this->slug.'_type', $_POST['type'] );
		update_post_meta( $post_id, $this->slug.'_order', $_POST['order'] );
		update_post_meta( $post_id, $this->slug.'_html', $_POST['html'] );
		update_post_meta( $post_id, $this->slug.'_link', $_POST['link'] );
	}
	
	//columns in list view
	function columns( $columns ) {
		$columns = array(
			'cb' => '<input type="checkbox" />',
			'title' => __( $this->label ),
			'image' => __( 'Profile Image' ),
			'type' => __( 'Type' ),
			'order' => __( 'Order' ),
			'views' => __( 'Views' ),
			'clicks' => __( 'Clicks'),			
			'date' => __( 'Date' )
		);

		return $columns;
	}
	
	function manage_columns( $column, $post_id ){
		global $post;
		
		$image_id = get_post_meta( $post_id, $this->slug.'_image_id', true );
		$image_src = wp_get_attachment_url( $image_id );
		$type = get_post_meta( $post_id, $this->slug.'_type', true );
		$order = get_post_meta( $post_id, $this->slug.'_order', true );
		$html = get_post_meta( $post_id, $this->slug.'_html', true );
		$views = get_post_meta( $post_id, $this->slug.'_views', true );
		$clicks = get_post_meta( $post_id, $this->slug.'_clicks', true );
		
		if($column=='image'){
			?><img id="carousel_image" src="<?php echo $image_src ?>" style="max-width:100px;" /><?php
		}
		else if($column=='type'){
			echo $type;
		}
		else if($column=='html'){
			echo $html;
		}
		else if($column=='order'){
			echo $order;
		}
		else if($column=='views'){
			echo $views;
		}
		else if($column=='clicks'){
			echo $clicks;
		}
		
		
	}
	
	/**
	 * Display the image meta box
	 */
	public function meta_box() {
		global $post;
		
		$image_src = '';
		
		$image_id = get_post_meta( $post->ID, $this->slug.'_image_id', true );
		$image_src = wp_get_attachment_url( $image_id );
		$type = get_post_meta( $post->ID, $this->slug.'_type', true );
		$order = get_post_meta( $post->ID, $this->slug.'_order', true );
		$html = get_post_meta( $post->ID, $this->slug.'_html', true );
		$link = get_post_meta( $post->ID, $this->slug.'_link', true );
		$views = get_post_meta( $post->ID, $this->slug.'_views', true );
		$clicks = get_post_meta( $post->ID, $this->slug.'_clicks', true );
		
		$order *= 1;
		$views *= 1;
		$clicks *= 1;
		?>
		<style>
		#<?php echo $this->slug; ?> td{
			vertical-align:top;
		}
		</style>
		<table id='<?php echo $this->slug; ?>' style='width:100%'>
		<tr>
			<td colspan=2>
			<b>Logo (175px x 175px)<br /><br /></b>
			<img id="<?php echo $this->slug; ?>_image" src="<?php echo $image_src ?>" style="max-width:100%;" />
			<input type="hidden" name="upload_image_id" id="upload_image_id" value="<?php echo $image_id; ?>" />
			<p>
				<input type='button' class='button' value="<?php _e( 'Set Image' ) ?>"  id="set-image" />
				<input type='button' class='button' style="<?php echo ( ! $image_id ? 'display:none;' : '' ); ?>" value="<?php _e( 'Remove Image' ) ?>"  id="remove-image" />
			</p>
			<br />
			<b>URL Link<br /></b>
			<input type="text" name="link" value="<?php echo htmlentitiesx($link); ?>" style='width:100%' />
			</td>
		</tr>
		<tr>
			<td>
			<br /><br />
			<b>Type<br /></b>
			<select name='type' id='xtype'>
				<option value='Premier Media Partner'>Premier Media Partner</option>
				<option value='Community & Media Partners'>Community & Media Partners</option>
				<option value='Supporting Partners'>Supporting Partners</option>
			</select>
			<script>
			jQuery("#xtype").val("<?php echo $type; ?>");
			</script>
			<br /><br />
			<b>Order<br /></b>
			<input type="text" name="order" value="<?php echo htmlentitiesx($order); ?>" style='width:100%' />
			
			</td>
		</tr>
		<tr>
			<td>
			<br /><br />
			----- Or -----
			<br /><br />
			<b>HTML<br /></b>
			<textarea name='html' style='width:100%; height:200px'><?php echo htmlentitiesx($html);  ?></textarea>
			
			</td>
		</tr>
		</table>

		<!--- script below -->
		
		<script type="text/javascript">
		jQuery(document).ready(function($) {
			
			// save the send_to_editor handler function
			window.send_to_editor_default = window.send_to_editor;
	
			$('#set-image').click(function(){
				
				// replace the default send_to_editor handler function with our own
				window.send_to_editor = window.attach_image;
				tb_show('', 'media-upload.php?post_id=<?php echo $post->ID ?>&amp;type=image&amp;TB_iframe=true');
				
				return false;
			});
			
			$('#remove-image').click(function() {
				
				$('#upload_image_id').val('');
				$('img').attr('src', '');
				$(this).hide();
				
				return false;
			});
			
			// handler function which is invoked after the user selects an image from the gallery popup.
			// this function displays the image and sets the id so it can be persisted to the post meta
			window.attach_image = function(html) {
				
				// turn the returned image html into a hidden image element so we can easily pull the relevant attributes we need
				$('body').append('<div id="temp_image">' + html + '</div>');
					
				var img = $('#temp_image').find('img');
				
				imgurl   = img.attr('src');
				imgclass = img.attr('class');
				imgid    = parseInt(imgclass.replace(/\D/g, ''), 10);
	
				$('#upload_image_id').val(imgid);
				$('#remove-image').show();
	
				$('img#<?php echo $this->slug; ?>_image').attr('src', imgurl);
				try{tb_remove();}catch(e){};
				$('#temp_image').remove();
				
				// restore the send_to_editor handler function
				window.send_to_editor = window.send_to_editor_default;
				
			}
	
		});
		</script>
		<?php
	}
}


class E_Satellite extends Echelon {
	
	var $slug;
	var $label;
	var $title;
		
	public function __construct() {
		$this->slug = "echelon_satellite";
		$this->label = "Echelon Satellites";
		$this->title = "City";
		
		add_action( 'init', $this->init($this->slug, $this->label) );	
		if ( is_admin() ) {
			add_action( 'admin_init', array( &$this, 'admin_init' ) );
		}
	}

	
	/** Admin methods ******************************************************/
	
	
	/**
	 * Initialize the admin, adding actions to properly display and handle 
	 * the carousel custom post type add/edit page
	 */
	public function admin_init() {
		global $pagenow, $post;
		
		if ( $pagenow == 'post-new.php' || $pagenow == 'post.php' || $pagenow == 'edit.php' ) {
			
			add_action( 'add_meta_boxes', array( &$this, 'meta_boxes' ) );
			add_filter( 'enter_title_here', array( &$this, 'enter_title_here' ), 1, 2 );
			add_action( 'save_post', array( &$this, 'meta_boxes_save' ), 1, 2 );
			
			add_filter( 'manage_edit-'.$this->slug."_columns", array( &$this, 'columns' ) ) ;
			add_action( 'manage_'.$this->slug.'_posts_custom_column', array( &$this, 'manage_columns' ), 10, 2 );
			
			add_filter('manage_edit-'.$this->slug.'_sortable_columns',array( &$this,  'order_column_register_sortable'));
			
		}
	}
	
	/**
	* make column sortable
	*/
	function order_column_register_sortable($columns){
	 // $columns['order'] = 'order';
	  return $columns;
	}


	/**
	 * Save meta boxes
	 * 
	 * Runs when a post is saved and does an action which the write panel save scripts can hook into.
	 */
	public function meta_boxes_save( $post_id, $post ) {
		if ( empty( $post_id ) || empty( $post ) || empty( $_POST ) ) return;
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
		if ( is_int( wp_is_post_revision( $post ) ) ) return;
		if ( is_int( wp_is_post_autosave( $post ) ) ) return;
		if ( ! current_user_can( 'edit_post', $post_id ) ) return;
		if ( $post->post_type != $this->slug ) return;
			
		$this->process_meta( $post_id, $post );
	}
	
	
	
	
	
	/**
	 * Set a more appropriate placeholder text for the New carousel title field
	 */
	public function enter_title_here( $text, $post ) {
		if ( $post->post_type == $this->slug ) return __( $this->title );
		return $text;
	}
	
	
	
	/**
	 * Add and remove meta boxes from the edit page
	 */
	public function meta_boxes() {
		add_meta_box( $this->slug."-metabox", __( "Satellite Details" ), array( &$this, 'meta_box' ), $this->slug, 'side', 'high' );
	}
	
	/**
	 * Function for processing and storing all carousel data.
	 */
	private function process_meta( $post_id, $post ) {
		update_post_meta( $post_id, $this->slug.'_image_id', $_POST['upload_image_id'] );
		update_post_meta( $post_id, $this->slug.'_image_id2', $_POST['upload_image_id2'] );
		update_post_meta( $post_id, $this->slug.'_country', $_POST['country'] );
		update_post_meta( $post_id, $this->slug.'_when', $_POST['when'] );
		update_post_meta( $post_id, $this->slug.'_where', $_POST['where'] );
		update_post_meta( $post_id, $this->slug.'_deadline', $_POST['deadline'] );
		update_post_meta( $post_id, $this->slug.'_excerpt', $_POST['excerpt'] );
		update_post_meta( $post_id, $this->slug.'_order', $_POST['order'] );
		update_post_meta( $post_id, $this->slug.'_attendurl', $_POST['attendurl'] );
	}
	
	//columns in list view
	function columns( $columns ) {
		$columns = array(
			'cb' => '<input type="checkbox" />',
			'title' => __( $this->label ),
			//'image' => __( 'Image' ),
			'date' => __( 'Date' )
		);

		return $columns;
	}
	
	function manage_columns( $column, $post_id ){
		global $post;
		
		$image_id = get_post_meta( $post_id, $this->slug.'_image_id', true );
		$image_src = wp_get_attachment_url( $image_id );
		
		if($column=='image'){
			?><img  src="<?php echo $image_src ?>" style="max-width:100px;" /><?php
		}
		
		
	}
	
	/**
	 * Display the image meta box
	 */
	public function meta_box() {
		global $post;
		
		$image_src = '';
		$image_src2 = '';
		$image_id = get_post_meta( $post->ID, $this->slug.'_image_id', true );
		$image_src = wp_get_attachment_url( $image_id );
		$image_id2 = get_post_meta( $post->ID, $this->slug.'_image_id2', true );
		$image_src2 = wp_get_attachment_url( $image_id2 );
		
		$country = get_post_meta( $post->ID, $this->slug.'_country', true );
		$when = get_post_meta( $post->ID, $this->slug.'_when', true );
		$where = get_post_meta( $post->ID, $this->slug.'_where', true );
		$deadline = get_post_meta( $post->ID, $this->slug.'_deadline', true );
		$excerpt = get_post_meta( $post->ID, $this->slug.'_excerpt', true );
		$order = get_post_meta( $post->ID, $this->slug.'_order', true );
		$attendurl = get_post_meta( $post->ID, $this->slug.'_attendurl', true );
		$order *= 1;
		?>
		<style>
		#<?php echo $this->slug; ?> td{
			vertical-align:top;
		}
		</style>
		<table id='<?php echo $this->slug; ?>' style='width:100%'>
		<tr>
			<td colspan=2>
			<b>BG Image (223px x 300px)<br /><br /></b>
			<img id="speaker_image" src="<?php echo $image_src ?>" style="max-width:100%;" />
			<input type="hidden" name="upload_image_id" id="upload_image_id" value="<?php echo $image_id; ?>" />
			<p>
				<input type='button' class='button' value="<?php _e( 'Set Image' ) ?>"  id="set-image" />
				<input type='button' class='button' style="<?php echo ( ! $image_id ? 'display:none;' : '' ); ?>" value="<?php _e( 'Remove Image' ) ?>"  id="remove-image" />
			</p>
			</td>
		</tr>
		<tr>
			<td colspan=2>
			<b>Hover Image (223px x 300px)<br /><br /></b>
			<img id="speaker_image2" src="<?php echo $image_src2 ?>" style="max-width:100%;" />
			<input type="hidden" name="upload_image_id2" id="upload_image_id2" value="<?php echo $image_id2; ?>" />
			<p>
				<input type='button' class='button' value="<?php _e( 'Set Image' ) ?>"  id="set-image2" />
				<input type='button' class='button' style="<?php echo ( ! $image_id2 ? 'display:none;' : '' ); ?>" value="<?php _e( 'Remove Image' ) ?>"  id="remove-image2" />
			</p>
			</td>
		</tr>
		<tr>
			<td>
			<b>Country<br /><br /></b>
			<input type="text" name="country" value="<?php echo htmlentitiesx($country); ?>" style='width:100%' /><br /><br />
			<b>When<br /><br /></b>
			<input type="text" name="when" value="<?php echo htmlentitiesx($when); ?>" style='width:100%' /><br /><br />
			<b>Where<br /><br /></b>
			<input type="text" name="where" value="<?php echo htmlentitiesx($where); ?>" style='width:100%' /><br /><br />
			<b>Deadline<br /><br /></b>
			<input type="text" name="deadline" value="<?php echo htmlentitiesx($deadline); ?>" style='width:100%' /><br /><br />
			<b>Excerpt<br /><br /></b>
			<textarea name="excerpt" style='width:100%; height:70px'><?php echo htmlentitiesx($excerpt); ?></textarea><br /><br />
			<!--
			<b>Attend Button URL<br /><br /></b>
			<input type="text" name="attendurl" value="<?php echo htmlentitiesx($attendurl); ?>" style='width:100%' /><br /><br />
			-->
			<b>Order<br /><br /></b>
			<input type="text" name="order" value="<?php echo htmlentitiesx($order); ?>" style='width:100%' /><br /><br />
			</td>
		</tr>
		</table>

		<!--- script below -->
		
		<script type="text/javascript">
		jQuery(document).ready(function($) {
			
			var imageidtoupdate = "";
			var upload_image_id = "";
			// save the send_to_editor handler function
			window.send_to_editor_default = window.send_to_editor;
	
			$('#set-image').click(function(){
				
				// replace the default send_to_editor handler function with our own
				window.send_to_editor = window.attach_image;
				tb_show('', 'media-upload.php?post_id=<?php echo $post->ID ?>&amp;type=image&amp;TB_iframe=true');
				
				imageidtoupdate = "speaker_image";
				upload_image_id = "upload_image_id";
				return false;
			});
			
			$('#remove-image').click(function() {
				
				$('#upload_image_id').val('');
				$('#speaker_image').attr('src', '');
				$(this).hide();
				
				return false;
			});
			
			$('#set-image2').click(function(){
				
				// replace the default send_to_editor handler function with our own
				window.send_to_editor = window.attach_image;
				tb_show('', 'media-upload.php?post_id=<?php echo $post->ID ?>&amp;type=image&amp;TB_iframe=true');
				
				imageidtoupdate = "speaker_image2";
				upload_image_id = "upload_image_id2";
				return false;
			});
			
			$('#remove-image2').click(function() {
				
				$('#upload_image_id2').val('');
				$('#speaker_image2').attr('src', '');
				$(this).hide();
				
				return false;
			});
			
			// handler function which is invoked after the user selects an image from the gallery popup.
			// this function displays the image and sets the id so it can be persisted to the post meta
			window.attach_image = function(html) {
				
				// turn the returned image html into a hidden image element so we can easily pull the relevant attributes we need
				$('body').append('<div id="temp_image">' + html + '</div>');
					
				var img = $('#temp_image').find('img');
				
				imgurl   = img.attr('src');
				imgclass = img.attr('class');
				imgid    = parseInt(imgclass.replace(/\D/g, ''), 10);
	
				$('#'+upload_image_id).val(imgid);
				$('#remove-image').show();
	
				$('img#'+imageidtoupdate).attr('src', imgurl);
				try{tb_remove();}catch(e){};
				$('#temp_image').remove();
				
				// restore the send_to_editor handler function
				window.send_to_editor = window.send_to_editor_default;
				
			}
	
		});
		</script>
		<?php
	}
}

class E_Settings {
	public function __construct() {
		add_action("admin_menu", $this->setup_theme_admin_menus()); 
	}
	public function setup_theme_admin_menus(){
		add_menu_page('Echelon Frontpage Settings', 'Echelon Frontpage Settings', 'manage_options',   
			'echelon_theme_settings', array( &$this, 'theme_settings_page' ), '', '61');  
		add_action("admin_head",array( &$this, 'myplugin_load_tiny_mce' ));
			
	}
	function myplugin_load_tiny_mce() {
		wp_tiny_mce( false ); // true gives you a stripped down version of the editor
	}
	// We also need to add the handler function for the top level menu  
	function theme_settings_page() {  
		// Check that the user is allowed to update options  
		if (!current_user_can('manage_options')) {  
			wp_die('You do not have sufficient permissions to access this page.');  
		} 
		if (isset($_POST['echelon_options'])) {  
			
			foreach($_POST as $key=>$value){
				$value = esc_attr($value);     
				update_option($key, $value);  
			}
		}  
		?>  
			<div class="wrap">  
				
				<style>
				td{
					vertical-align:top;
				}
				.heading{
					font-size: 1.7em;
					line-height: 100%;
					outline: 0 none;
					padding: 3px 8px;
					width: 100%;
				}
				
				.js .tmce-active .wp-editor-area {
					color: black;
				}
				</style>
				
				
				<form method="POST" action="">  
					<input type='hidden' name='echelon_options' value='1' />
					<?php screen_icon('themes'); ?> <h2>Front Page Settings</h2>
					<table class="form-table" >  
						<tr>
							<th>
								<label for="num_elements" style='font-size:24px; font-weight:bold'>  
									FRONT PAGE TEXT 1
								</label>
							</th>
						</tr>
						<tr valign="top">  
							<td>  
								<input placeholder="Heading Text 1" class='heading' type="text" autocomplete="off" value="<?php echo (stripslashes(html_entity_decode(get_option("echelon_fphead_1")))) ?>" size="30" name="echelon_fphead_1">
								<br /><br />
								<?php
								the_editor(stripslashes(html_entity_decode(get_option("echelon_fptext_1"))), 'echelon_fptext_1');
								?>
							</td>  
						</tr>
						<tr>
							<th>
								<label for="num_elements" style='font-size:24px; font-weight:bold; padding-top:20px;'>  
									FRONT PAGE TEXT 2
								</label>
							</th>
						</tr>
						<tr valign="top">  
							<td>  
								<input placeholder="Heading Text 2" class='heading' type="text" autocomplete="off" value="<?php echo (stripslashes(html_entity_decode(get_option("echelon_fphead_2")))) ?>" size="30" name="echelon_fphead_2">
								<br /><br />
								<?php
								the_editor(stripslashes(html_entity_decode(get_option("echelon_fptext_2"))), 'echelon_fptext_2');
								?>
							</td>  
						</tr>
						
						<tr>
							<th>
								<label for="num_elements" style='font-size:24px; font-weight:bold; padding-top:20px;'>  
									FRONT PAGE ARTICLE 1
								</label>
							</th>
						</tr>
						<tr valign="top">  
							<td>  
								<input placeholder="Heading Article 1" class='heading' type="text" autocomplete="off" value="<?php echo (stripslashes(html_entity_decode(get_option("echelon_fphead_5")))) ?>" size="30" name="echelon_fphead_5">
								<br /><br />
								<?php
								the_editor(stripslashes(html_entity_decode(get_option("echelon_fptext_5"))), 'echelon_fptext_5');
								?>
							</td>  
						</tr>
						
						<tr>
							<th>
								<label for="num_elements" style='font-size:24px; font-weight:bold; padding-top:20px;'>  
									FRONT PAGE ARTICLE 2
								</label>
							</th>
						</tr>
						<tr valign="top">  
							<td>  
								<input placeholder="Heading Article 2" class='heading' type="text" autocomplete="off" value="<?php echo (stripslashes(html_entity_decode(get_option("echelon_fphead_4")))) ?>" size="30" name="echelon_fphead_4">
								<br /><br />
								<?php
								the_editor(stripslashes(html_entity_decode(get_option("echelon_fptext_4"))), 'echelon_fptext_4');
								?>
							</td>  
						</tr>
							<tr>
							<th>
								<label for="num_elements" style='font-size:24px; font-weight:bold; padding-top:20px;'>  
									FOOTER TEXT
								</label>
							</th>
						</tr>
						<tr valign="top">  
							<td>  
								<input placeholder="Footer Heading Text" class='heading' type="text" autocomplete="off" value="<?php echo (stripslashes(html_entity_decode(get_option("echelon_fphead_3")))) ?>" size="30" name="echelon_fphead_3">
								<br /><br />
								<?php
								the_editor(stripslashes(html_entity_decode(get_option("echelon_fptext_3"))), 'echelon_fptext_3');
								?>
							</td>  
						</tr>						
					</table>  
					<br /><br /><br /><br />
					<?php screen_icon('themes'); ?> <h2>Social Settings</h2>  
					
					<table style='width:100%'>
					<tr>
					<td>
						<table class="form-table">  
							<tr valign="top">  
								<th scope="row">  
									<label for="num_elements" >  
										Facebook URL
									</label>   
								</th>  
								<td>  
									<input type='text' class='heading' name="echelon_fb_url" value="<?php echo htmlentitiesx(get_option("echelon_fb_url")) ?>" />
								</td>  
							</tr>
							<tr valign="top">  
								<th scope="row">  
									<label for="num_elements">  
										Twitter URL
									</label>   
								</th>  
								<td>  
									<input type='text' class='heading' name="echelon_tw_url" value="<?php echo htmlentitiesx(get_option("echelon_tw_url")) ?>" />
								</td>  
							</tr>
							<tr valign="top">  
								<th scope="row">  
									<label for="num_elements">  
										GPlus URL
									</label>   
								</th>  
								<td>  
									<input type='text' class='heading' name="echelon_gp_url" value="<?php echo htmlentitiesx(get_option("echelon_gp_url")) ?>" />
								</td>  
							</tr>
							<tr valign="top">  
								<th scope="row">  
									<label for="num_elements">  
										LinkedIn URL
									</label>   
								</th>  
								<td>  
									<input type='text' class='heading' name="echelon_in_url" value="<?php echo htmlentitiesx(get_option("echelon_in_url")) ?>" />
								</td>  
							</tr> 
						</table> 
					</td>
					</tr>
					<tr>
					<td colspan='1' style='text-align:center; padding-top:30px;'>
						<input type='submit' class='button button-primary button-large' value='Save' style='width:300px; height:50px;'  />
					</td>
					</tr>
					</table>
				</form>  
			</div>  
		<?php  
	}  
}



function e_clickurl($link, $p){
	//echo $p->post_type;
	//echo $p->ID;
	
	return "?a=click&u=".urlencode($link)."&post_type=".$p->post_type."&pid=".$p->ID."&_=".time();
	
	//$clicks = get_post_meta( $premier_sponsors[$i]['post']->ID, $ptype.'_clicks', true );
	//$views *= 1;
	//$clicks *= 1;
	//$views += 1;
	//update_post_meta( $premier_sponsors[$i]['post']->ID, $ptype.'_views', $views );
}

function e_view($p){
	$views = get_post_meta( $p->ID, $p->post_type.'_views', true );
	$views *= 1;
	$views += 1;
	update_post_meta( $p->ID, $p->post_type.'_views', $views );
}

if($_GET['a']=='click'){
	$_clicks = get_post_meta( $_GET['pid'], $_GET['post_type'].'_clicks', true );
	$_clicks *= 1;
	$_clicks += 1;
	update_post_meta( $_GET['pid'], $_GET['post_type'].'_clicks', $_clicks );
	
	$url = urldecode($_GET['u']);
	header ('HTTP/1.1 301 Moved Permanently');
	header("Location: ".$url);
	exit();
}

// This theme uses wp_nav_menu() in one location.
register_nav_menu( 'primary', __( 'Primary Menu', 'echelon2013' ) );

// finally instantiate our plugin class and add it to the set of globals (not really needed but it will instantiate the class)
$GLOBALS['E_Carousel'] = new E_Carousel();
$GLOBALS['E_Youtube'] = new E_Youtube();
$GLOBALS['E_Speaker'] = new E_Speaker();
$GLOBALS['E_Startup'] = new E_Startup();
$GLOBALS['E_Staff'] = new E_Staff();
$GLOBALS['E_Quote'] = new E_Quote();
$GLOBALS['E_Sponsor'] = new E_Sponsor();
$GLOBALS['E_MediaPartner'] = new E_MediaPartner();
$GLOBALS['E_Satellite'] = new E_Satellite();
$GLOBALS['E_Settings'] = new E_Settings();




function e_speakers($content){
	ob_start();
	$current_p = $_SESSION['current_p'];
	$current_posttype = $current_p->post_type;
	$current_postid = $current_p->ID;
	
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
	if($_GET['speakerid']){
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
				$exclude = get_post_meta( $p->ID, $ptype.'_exclude', true );
				$frontpage = get_post_meta( $p->ID, $ptype.'_frontpage', true );
				$satellites = get_post_meta( $p->ID, $ptype.'_satellites', true );
				$satellites = json_decode($satellites);
				
				$url = get_post_meta( $p->ID, $ptype.'_url', true );
				$urled = true;
				if(!trim($url)){
					$url = get_permalink( $p->ID );
					$urled = false;
				}
				
				$s = array();
				$s['p'] = $p;
				$s['designation'] = $designation;
				$s['fb'] = $fb;
				$s['tw'] = $tw;
				$s['exclude'] = $exclude;
				$s['frontpage'] = $frontpage;
				$s['satellites'] = $satellites;
				$s['in'] = $in;
				$s['image_src'] = $image_src2;
				$s['url'] = $url;
				
				if($p->ID==$_GET['speakerid']){
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
					?><li <?php if($value['p']->ID==$_GET['speakerid']){ echo "class='active'"; } ?> ><a href="<?php echo $value['url'] ; ?>"><?php echo $value['p']->post_title; ?></a></li><?php
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
	else{
		$therearesomespeakers = false;
		echo '
		<!--
		<h2>Speakers and Judges</h2>
		Renowned for our ability to bring in top notch speakers and judges from around the world including US and Asia. You can be assured that we will delivered the utmost relevant trending Asia content.
		-->
		<div class="row-fluid add-top">';
		if($the_query->have_posts() ){
			$i = 0;
			while ( $the_query->have_posts() ){
				
				$the_query->the_post();
				$p = get_post( get_the_ID(), OBJECT );
				$image_id = get_post_meta( $p->ID, $ptype.'_image_id', true );
				$image_src = wp_get_attachment_url( $image_id );
				$image_id2 = get_post_meta( $p->ID, $ptype.'_image_id2', true );
				$image_src2 = wp_get_attachment_url( $image_id2 );
				$exclude = get_post_meta( $p->ID, $ptype.'_exclude', true );
				
				if(trim($image_src2)==""){
					$image_src2 = $image_src;
				}
				$designation = get_post_meta( $p->ID, $ptype.'_designation', true );
				
				$satellites = get_post_meta( $p->ID, $ptype.'_satellites', true );
				$satellites = json_decode($satellites);
				$url = get_post_meta( $p->ID, $ptype.'_url', true );
				$target = " target='_blank' ";
				
				
				
				if(!trim($url)){
					$url = get_permalink( $p->ID );
					$target = "";
				}
						
						
				$print = false;
				if($current_posttype=="echelon_satellite"){
					if(in_array($current_postid, $satellites)){
						$print = true;
					}
				}
				else if(strtolower(trim($exclude))!="yes"){
					$print = true;
				}
				
				if($print){
					if($i%4==0){
						if($i>0){
							?></div></div><?php
						}
						?><div class="row-fluid"><div class="wrapper-speakers"><?php
					}
					$therearesomespeakers = true;
					?>
					 <div class="span3 txt-c">
						<a href='<?php echo $url ; ?>' <?php echo $target; ?> class='speakerimage'>
						<img style='cursor:pointer; height:128px; width:128px' src="<?php echo $image_src?>" title="<?php echo htmlentitiesx($p->post_title) ?>" alt="<?php echo htmlentitiesx($p->post_title) ?>" class="rounded primaryimg"/>
						<img style='display:none; cursor:pointer; height:128px; width:128px' src="<?php echo $image_src2?>" title="<?php echo htmlentitiesx($p->post_title) ?>" alt="<?php echo htmlentitiesx($p->post_title) ?>" class="rounded alternateimg"/>
						</a>
						<p><a href='<?php echo $url ; ?>'style='color:black' class='speakerlink' ><em><?php echo htmlentitiesx($p->post_title) ?></em></a><br/><?php echo $designation;?></p>
					  </div>
					<?php
					$i++;
				}
			}
			?></div></div>
			<script>
			jQuery(".speakerimage").hover(
				function(){
					jQuery(this).find(".primaryimg").hide();
					jQuery(this).find(".alternateimg").show();
				},
				function(){
					jQuery(this).find(".primaryimg").show();
					jQuery(this).find(".alternateimg").hide();
				}
			);
			
			/*
			jQuery(".speakerlink").hover(
				function(){
					jQuery(this).parent().find(".speakerimage").find(".primaryimg").hide();
					jQuery(this).parent().find(".speakerimage").find(".alternateimg").show();
				},
				function(){
					jQuery(this).parent().find(".speakerimage").find(".primaryimg").show();
					jQuery(this).parent().find(".speakerimage").find(".alternateimg").hide();
				}
			);
			*/
			</script>
			<?php
		}
		echo "</div>";
	}
	wp_reset_postdata();
	$speakers = ob_get_contents();
	ob_end_clean();
	
	if($_GET['speakerid']){
		return $speakers;
	}
	else{
		if($therearesomespeakers){
			$content = str_replace("[[ep_speakers]]", $speakers, $content);
		}
		else{
			$content = str_replace("[[ep_speakers]]", "", $content);
		}
		return $content;
	}
	
}


function e_quotes($content){
	ob_start();
	?>
	<div class="par-comment">
		<div class="row-fluid comment-wrapper" style='position:relative'>
		  <style>
			#quotes1{
				width:650px;
			}
			#quotes1 .slides_container .div {
				width:600px;
				height:130px;
				display:block;
			}
			#quotes1 .slides_container{
				paddin-top:10px;
				width:600px;
			}
			
			</style>
			<div class="green-quote">quote</div>
			<div id='quotes1'>
			<div class="slides_container">
				<?php
					$ptype = "e_quote";
					$args = array(
						'post_type'=> $ptype,
						'order'    => 'ASC',
						'orderby'	=> 'rand',
						'posts_per_page' => -1
					);              
					$the_query = new WP_Query( $args );
					if($the_query->have_posts() ){
						while ( $the_query->have_posts() ){
							$the_query->the_post();
							$p = get_post( get_the_ID(), OBJECT );
							$image_id = get_post_meta( $p->ID, $ptype.'_image_id', true );
							$image_src = wp_get_attachment_url( $image_id );
							?> 
							
								<div class='div'>
									<table style='width:100%; height:150px'>
									<tr>
									<td style='vertical-align:top'>
										<div class="client-badge"><img src="<?php echo $image_src; ?>" style='height:70px; width:70px' /></div>
									</td>
									<td style='vertical-align:middle'>
										<div class="sayings" style='margin-top:0px; float:left; width:480px;'><?php echo $p->post_content; echo " </p> <p align='right'>- "; echo $p->post_title; ?></div>
									</td>
									</tr>
									</table>
								</div>
							
							<?php
						}
					}
					wp_reset_postdata();
					
					?>
			</div>
			</div>
			
		</div>
	  </div>
	<?php
	$quotes = ob_get_contents();
	ob_end_clean();
	
	$content = str_replace("[[ep_quote]]", $quotes, $content);
	return $content;
}

function e_news($content){
	global $e_rss;
	ob_start();
	?>
	<div class="row-fluid fourth-lvl">
		<h2>News & Update</h2>
		<div class="row-fluid">
		  <div class="span6 nu-box">
			<h3><?php echo $e_rss->items[0]['title']; ?></h3>
			<p>
			<?php echo preg_replace("/<p>The post <a href=\".*<\/p>/", "", $e_rss->items[0]['description']); ?>
			</p>            
			<a href="<?php echo $e_rss->items[0]['link']; ?>" class="readmore">Read more</a>              
		  </div>
		  <div class="span6 nu-box">
			<h3><?php echo $e_rss->items[1]['title']; ?></h3>
			<p>
			<?php echo preg_replace("/<p>The post <a href=\".*<\/p>/", "", $e_rss->items[0]['description']); ?>
			</p>            
			<a href="<?php echo $e_rss->items[1]['link']; ?>" class="readmore">Read more</a>              
		  </div>
		</div>
	  </div>
	<?php
	
	$news = ob_get_contents();
	ob_end_clean();
	
	$content = str_replace("[[ep_news-update]]", $news, $content);
	return $content;
}


function e_crew($content){
	ob_start();
	$ptype = "echelon_staff";
	$args = array(
		'post_type'=> $ptype,
		'order'    => 'ASC',
		'orderby'	=> 'meta_value',
		'meta_key' 	=> $ptype.'_order',
		'posts_per_page' => -1
	);              
	$the_query = new WP_Query( $args );
	$i=0;
	$astaff = array();
	$thestaff = array();
	if($the_query->have_posts() ){
		while ( $the_query->have_posts() ){
			$the_query->the_post();
			$p = get_post( get_the_ID(), OBJECT );
			$image_id = get_post_meta( $p->ID, $ptype.'_image_id', true );
			$designation = get_post_meta( $p->ID, $ptype.'_designation', true );
			$fb = get_post_meta( $p->ID, $ptype.'_fb', true );
			$tw = get_post_meta( $p->ID, $ptype.'_tw', true );
			$in = get_post_meta( $p->ID, $ptype.'_in', true );
			$image_src = wp_get_attachment_url( $image_id );
			
			$s = array();
			$s['p'] = $p;
			$s['designation'] = $designation;
			$s['fb'] = $fb;
			$s['tw'] = $tw;
			$s['in'] = $in;
			$s['image_src'] = $image_src;
			
			if($p->ID==$_GET['staffid']){
				$thestaff = $s;
			}
			$astaff[] = $s;
		}
	}
	$t = count($astaff);
	$leftt = ceil($t/2);
	?>
	<h2 class="add-top">e27 Crew</h2>
	  <div class="row-fluid">
		<p>Renowned for our ability to bring in top notch speakers and judges from around the world including US and Asia.
		You can be assured that we will delivered the utmost relevant trending Asia content.</p>

		<div class="row-fluid wrapper-crew add-top">
			
		  <div class="span6">
			 <?php
			  for($i=0; $i<$t; $i+=2){
				?>
				<div class="row-fluid add-bot pos-abs">
				  <div class="span6">
					<img class="rounded" alt="Mohan Belani" src="<?php echo $astaff[$i]['image_src']; ?>">              
				  </div>
				  <div class="span6 crew-indiv">
					<em><?php echo $astaff[$i]['p']->post_title; ?></em><br/><?php echo $astaff[$i]['designation']; ?>
					<div class="social add-top-xxs">
					  <?php
					  if($astaff[$i]['tw']){
						?><a href="<?php echo $astaff[$i]['tw']; ?>" class="twitter">twitter</a><?php
					  }
					  if($astaff[$i]['fb']){
						?><a href="<?php echo $astaff[$i]['fb']; ?>" class="facebook">facebook</a><?php
					  }
					  if($astaff[$i]['in']){
						?><a href="<?php echo $astaff[$i]['in']; ?>" class="linkedin">linkedin</a><?php
					  }
					  
					  ?>
					</div>  
				  </div>
				</div>
				<?php
			  }
			  ?>
		  </div>
		  <!-- --> 
		  <div class="span6">
			<?php
			  for($i=1; $i<$t; $i+=2){
				?>
				<div class="row-fluid add-bot pos-abs">
				  <div class="span6">
					<img class="rounded" alt="Mohan Belani" src="<?php echo $astaff[$i]['image_src']; ?>">              
				  </div>
				  <div class="span6 crew-indiv">
					<em><?php echo $astaff[$i]['p']->post_title; ?></em><br/><?php echo $astaff[$i]['designation']; ?>
					<div class="social add-top-xxs">
					  <?php
					  if($astaff[$i]['tw']){
						?><a href="<?php echo $astaff[$i]['tw']; ?>" class="twitter">twitter</a><?php
					  }
					  if($astaff[$i]['fb']){
						?><a href="<?php echo $astaff[$i]['fb']; ?>" class="facebook">facebook</a><?php
					  }
					  if($astaff[$i]['in']){
						?><a href="<?php echo $astaff[$i]['in']; ?>" class="linkedin">linkedin</a><?php
					  }
					  
					  ?>
					</div>  
				  </div>
				</div>
				<?php
			  }
			  ?>
			
		  </div>
		</div>  
	  </div>
	<?php
	$crew = ob_get_contents();
	ob_end_clean();
	
	$content = str_replace("[[ep_crew]]", $crew, $content);
	return $content;
}

function e_startups($content){
	ob_start();
	$ptype = "echelon_startup";
	$args = array(
		'post_type'=> $ptype,
		'order'    => 'ASC',
		'orderby'	=> 'meta_value',
		'meta_key' 	=> $ptype.'_order',
		'posts_per_page' => -1
	);              
	$the_query = new WP_Query( $args );
	$i=0;
	$astartups = array();
	$thestartup = array();
	$countries = array();
	if($the_query->have_posts() ){
		while ( $the_query->have_posts() ){
			$the_query->the_post();
			$p = get_post( get_the_ID(), OBJECT );
			$image_id = get_post_meta( $p->ID, $ptype.'_image_id', true );
			$country = get_post_meta( $p->ID, $ptype.'_country', true );
			$excerpt = get_post_meta( $p->ID, $ptype.'_excerpt', true );
			$url = get_post_meta( $p->ID, $ptype.'_url', true );
			$image_src = wp_get_attachment_url( $image_id );
			
			
			if(!in_array(strtoupper($country), $countries)){
				$countries[] = strtoupper($country);
			}
			$s = array();
			$s['p'] = $p;
			$s['country'] = strtoupper($country);
			$s['image_src'] = $image_src;
			$s['excerpt'] = $excerpt;
			$s['url'] = $url;
			
			if($p->ID==$_GET['startupid']){
				$thestartup = $s;
			}
			$astartups[] = $s;
		}
	}
	
	?>
	<script>
		jQuery(function(){
			jQuery(".country-startup-btn").click(function(){
				jQuery(".country-startup-btn").removeClass("selected");
				jQuery(this).addClass("selected");
				jQuery(".startup").hide();
				if(jQuery(this).html()!='ALL COUNTRIES'){
					jQuery("."+jQuery(this).attr('name')).show();
				}
				else{
					jQuery(".startup").show();
				}
			});
		});
	</script>
	<div class="row-fluid add-top">
		<!--<h2>Startup Marketplace</h2>
		<p>After going through a grueling judging process, including at the regional satellite events, Echelon 2012 is proud to announce the following startups at our Startup Marketplace:</p> -->
		<div class="country-startup add-top-small">
		  <!--
		  <a class="country-startup-btn selected" href="">all country</a>
		  <a class="country-startup-btn" href="">australia</a>
		  <a class="country-startup-btn" href="">japan</a>
		  <a class="country-startup-btn" href="">korea</a>
		  <a class="country-startup-btn" href="">indonesia</a>
		  <a class="country-startup-btn" href="">malaysia</a>
		  <a class="country-startup-btn" href="">philippines</a>
		  <a class="country-startup-btn" href="">taiwan</a>
		  <a class="country-startup-btn" href="">thailand</a>
		  <a class="country-startup-btn" href="">vietnam</a>
		  -->
		  <a class="country-startup-btn selected">ALL COUNTRIES</a>
		  <?php
		  $ct = count($countries);
		  for($i=0; $i<$ct; $i++){
			?>
			 <a class="country-startup-btn" name='<?php echo "c_".md5($countries[$i]); ?>' ><?php echo $countries[$i]; ?></a>
			<?php
		  }
		  ?>
		</div>
		<div class="row-fluid startups-list add-top">
		  
		  <?php
		  $t = count($astartups);
		  for($i=0; $i<$t; $i++){
			  ?>
				<div class="span4 startup <?php echo "c_".md5($astartups[$i]['country']); ?>">
				  <div class="inner-top">
					<a target='_blank' href="<?php echo $astartups[$i]['url']; ?>"><img style='max-width:220px; margin:auto;' src="<?php echo $astartups[$i]['image_src']; ?>"></a>
					<h2><?php echo $astartups[$i]['p']->post_title; ?></h2>
					<p><?php echo $astartups[$i]['country']; ?></p>
				  </div>
				  <p><?php if(strlen($astartups[$i]['excerpt'])>150) { echo substr($astartups[$i]['excerpt'], 0, 150)."..."; } else { echo $astartups[$i]['excerpt']; } ; ?></p>
				</div>
			  <?php
		  }
		  ?>
		  
		</div>
	  </div>
	<?php
	$startups = ob_get_contents();
	ob_end_clean();
	
	$content = str_replace("[[ep_startups]]", $startups, $content);
	return $content;
}

function e_satellites($content){
	ob_start();
	$ptype = "echelon_satellite";
	$args = array(
		'post_type'=> $ptype,
		'order'    => 'ASC',
		'orderby'	=> 'meta_value',
		'meta_key' 	=> $ptype.'_order',
		'posts_per_page' => -1
	);              
	$the_query = new WP_Query( $args );
	$i=0;
	$arr = array();
	$the = array();
	if($the_query->have_posts() ){
		while ( $the_query->have_posts() ){
			$the_query->the_post();
			$p = get_post( get_the_ID(), OBJECT );
			$image_id = get_post_meta( $p->ID, $ptype.'_image_id', true );
			$image_src = wp_get_attachment_url( $image_id );
			
			$image_id2 = get_post_meta( $p->ID, $ptype.'_image_id2', true );
			$image_src2 = wp_get_attachment_url( $image_id2 );
			if(!trim($image_src2)){
				$image_src2 = $image_src;
			}
			$country = get_post_meta( $p->ID, $ptype.'_country', true );
			$when = get_post_meta( $p->ID, $ptype.'_when', true );
			$where = get_post_meta( $p->ID, $ptype.'_where', true );
			$deadline = get_post_meta( $p->ID, $ptype.'_deadline', true );
			$excerpt = get_post_meta( $p->ID, $ptype.'_excerpt', true );
			/*$attendurl = get_post_meta( $p->ID, $ptype.'_attendurl', true );*/

			$s = array();
			$s['p'] = $p;
			$s['country'] = $country;
			$s['when'] = $when;
			$s['where'] = $where;
			$s['deadline'] = $deadline;
			$s['excerpt'] = $excerpt;
			$s['attendurl'] = $attendurl;
			$s['bg'] = $image_src;
			$s['bg2'] = $image_src2;
			
			if($p->ID==$_GET['satelliteid']){
				$the = $s;
			}
			$arr[] = $s;
		}
	}
	
	  $t = count($arr);
	  for($i=0; $i<$t; $i++){
		  if($i==0){
			?>
			<style>
			.wrapper-satellite .hover{
				/*border:5px solid yellow;*/
				box-shadow: 0 3px 9px #2F2F2E;
			}
			</style>
			
			<div class="row-fluid add-top"><div class="wrapper-satellite"><?php
		  }
		  if($i>0&&$i%3==0){
			?></div></div><div class="row-fluid add-top"><div class="wrapper-satellite"><?php
		  }
		/*
		 ?>
			<div class="span4 country">
				<div class="country-banner">
				  <p><?php echo $arr[$i]['p']->post_title; ?></p>
				  <p class="cntry-big"><?php echo $arr[$i]['country']; ?></p>
				</div>
				<span class="shade-small"></span>
				<div class="country-det">
				  <p><?php echo $arr[$i]['when']; ?> at <a><?php echo $arr[$i]['where']; ?></a></p>
				  <p class="satellite-caption"><?php echo $arr[$i]['excerpt']; ?></p>
				  <a class="btn btn-success btn-large" href="<?php echo get_permalink( $arr[$i]['p']->ID ) ; ?>">Attend Satellite</a>
				  <!--<p class="date">Deadline: <?php echo $arr[$i]['deadline']; ?></p>-->
				</div>
			  </div>
		  <?php
		 */
		 ?>
		 <div name='<?php echo $arr[$i]['bg']; ?>|<?php echo $arr[$i]['bg2']; ?>' class="span4 country sattelitediv" style='background:url(<?php echo $arr[$i]['bg']; ?>); height:300px; cursor:pointer' onclick='self.location="<?php echo get_permalink( $arr[$i]['p']->ID ) ; ?>"'>
			<img src='<?php echo $arr[$i]['bg2']; ?>' style='display:none' />
			<h2><?php echo $arr[$i]['country']; ?></h2>			
			<div class="country-det pod">
			  <p><?php echo $arr[$i]['when']; ?><a class="loc" style='text-decoration:none' ><?php echo $arr[$i]['where']; ?></a></p>
			  <!--<a href="<?php echo get_permalink( $arr[$i]['p']->ID ) ; ?>" class="btn btn-success btn-large">Attend Satellite</a>-->
			</div>
		  </div>
		 <?php
	  }
	  if($t){
		?></div></div>
		
		<script>
		jQuery(".sattelitediv").hover(
			function () {
				name = $(this).attr("name");
				x = name.split("|");
				//alert(x[1]);
				$(this).css("background-image", "url('"+x[1]+"')");
				$(this).addClass("hover");
			},
			function () {
				name = $(this).attr("name");
				x = name.split("|");
				//alert(x[0]);
				$(this).css("background-image", "url('"+x[0]+"')");
				$(this).removeClass("hover");
			}
		);
		</script>
		<?php
	  }
	$str = ob_get_contents();
	ob_end_clean();
	
	$content = str_replace("[[ep_satellites]]", $str, $content);
	return $content;
}

function e_sat_sponsors($content){
	$current_p = $_SESSION['current_p'];
	ob_start();
	if($current_p->post_type=='echelon_satellite'){
		?>
		<div class="row-fluid satellite-sponsors">
		<h2><?php echo $current_p->post_title; ?> Satellite Sponsors</h2>
		<?php
		
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
				$satellites = get_post_meta( $p->ID, $ptype.'_satellites', true );
				$satellites = json_decode($satellites);
				
				$image_src = wp_get_attachment_url( $image_id );
				$v = array();
				$v['post'] = $p;
				$v['image_src'] = $image_src;
				$v['link'] = $link;
				$v['html'] = $html;		
				if(in_array($current_p->ID, $satellites)){
					if(!is_array($sponsors[$type])){
						$sponsors[$type] = array();
					}
					$sponsors[$type][] = $v;
				}
			}
		}
		$thereisasponsor = false;
		foreach($sponsors as $category => $asponsors){
			$t = count($asponsors);
			for($i=0; $i<$t; $i++){
				if(trim($asponsors[$i]['link'])){
					?>
					<a href="<?php echo e_clickurl($asponsors[$i]['link'], $asponsors[$i]['post']); ?>">
					<img src='<?php echo $asponsors[$i]['image_src']?>' >
					</a>
					<?php
				}
				else{
					?>
					<img src='<?php echo $asponsors[$i]['image_src']?>' >
					<?php
				}
				$thereisasponsor = true;
			}
		}
		?>
			<div class='wanttobeasponsor' ><a>Want to be a sponsor? Click</a></div>
		</div>
		<?php
	}
	$str = ob_get_contents();
	ob_end_clean();
	if($thereisasponsor){
		$content = str_replace("[[ep_sat_sponsors]]", $str, $content);
		return $content;
	}
	else{
		$content = str_replace("[[ep_sat_sponsors]]", "", $content);
		return $content;
	}
}

function set_current_p($content){
	$_SESSION['current_p'] = get_post( get_the_ID(), OBJECT );
	return $content;
}

function e_sponsors($content){
	$current_p = $_SESSION['current_p'];
	ob_start();
	if(1){
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
				$satellites = get_post_meta( $p->ID, $ptype.'_satellites', true );
				$exclude = get_post_meta( $p->ID, $ptype.'_exclude', true );
				$satellites = json_decode($satellites);
				
				$image_src = wp_get_attachment_url( $image_id );
				$v = array();
				$v['post'] = $p;
				$v['image_src'] = $image_src;
				$v['link'] = $link;
				$v['html'] = $html;		
				if(strtolower($exclude)!="yes"){
					if(!is_array($sponsors[$type])){
						$sponsors[$type] = array();
					}
					$sponsors[$type][] = $v;
				}
			}
		}
		
		$thereisasponsor = trie;
		foreach($sponsors as $category => $asponsors){
			?><h2 class="add-top mar-bot"><?php echo $category; ?></h2><?php
			$t = count($asponsors);
			for($i=0; $i<$t; $i++){
				if(trim($asponsors[$i]['link'])){
					?>				
					<div class="row-fluid txt-c">
						<div class="span4"><a href="<?php echo e_clickurl($asponsors[$i]['link'], $asponsors[$i]['post']); ?>">
						<img src="<?php echo $asponsors[$i]['image_src']?>" alt="<?php echo $asponsors[$i]['post']->post_title?>"></a></div>
						<div class="span8 txt-l"><p class="sponsor-det">
							<a href="<?php echo e_clickurl($asponsors[$i]['link'], $asponsors[$i]['post']); ?>"><?php echo $asponsors[$i]['post']->post_title; ?></a> 
							<?php echo $asponsors[$i]['post']->post_content; ?>
							</p>
						</div> 
					</div>
					<?php
				}
				else{
					?>
					<div class="row-fluid txt-c">
						<div class="span4">
						<img src="<?php echo $asponsors[$i]['image_src']?>" alt="<?php echo $asponsors[$i]['post']->post_title?>"></div>
						<div class="span8 txt-l"><p class="sponsor-det">
							<?php echo $asponsors[$i]['post']->post_title; ?>
							<?php echo $asponsors[$i]['post']->post_content; ?>
							</p>
						</div> 
					</div>
					<?php
				}
				$thereisasponsor = true;
			}
		}
	}
	$str = ob_get_contents();
	ob_end_clean();
	if($thereisasponsor){
		$content = str_replace("[[ep_sponsors]]", $str, $content);
		return $content;
	}
	else{
		$content = str_replace("[[ep_sponsors]]", "", $content);
		return $content;
	}
}


//rss
$url = "http://e27.sg/tag/echelon-2013/feed/";
$e_rss = @fetch_rss( $url );

//plugins
add_action("the_content", "set_current_p");
add_action("the_content", "e_speakers");
add_action("the_content", "e_quotes");
add_action("the_content", "e_news");
add_action("the_content", "e_crew");
add_action("the_content", "e_startups");
add_action("the_content", "e_satellites");
add_action("the_content", "e_sat_sponsors");
add_action("the_content", "e_sponsors");




?>