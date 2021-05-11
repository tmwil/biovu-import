<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://vumc.org
 * @since      1.0.0
 *
 * @package    Biovu_Import
 * @subpackage Biovu_Import/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Biovu_Import
 * @subpackage Biovu_Import/admin
 * @author     Travis Wilson <travis.m.wilson@vumc.org>
 */
class Biovu_Import_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Biovu_Import_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Biovu_Import_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/biovu-import-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Biovu_Import_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Biovu_Import_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/biovu-import-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Testing some stuff.
	 *
	 * @since    1.0.0
	 */
	public function test_plugin_setup_menu(){
		add_menu_page( 'BioVU Project Import', 'BioVU Project Import', 'manage_options', 'biovu-project-import', function(){
			$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

			if(isset($_FILES['redcapExport'])) {
				$file = fopen($_FILES['redcapExport']['tmp_name'],"r");
				$keys = array();
				$user_id = get_current_user_id();
				$postInsertCount = 0;
				$taxonomy = 'biovu-research-category';
				while (($data = fgetcsv($file)) !== FALSE) {
					if(empty($keys)) {
						$keys = array_flip($data);
					} else {
						$my_post = array(
							'post_title'    => wp_strip_all_tags($data[$keys['lay_title']]),
							'post_status'   => 'publish',
							'post_author'   => $user_id,
							'post_content'  => wp_strip_all_tags($data[$keys['lay_summary']]),
							'post_type'     => 'biovu-projects'
						);
						$postID = wp_insert_post($my_post);
						if(!empty($postID)) {
							$postInsertCount++;
							update_field('record-id', wp_strip_all_tags($data[0]), $postID);
							if(!empty($data[$keys['biovu_letter_date']])) {
								$letterDate = new \DateTime($data[$keys['biovu_letter_date']]);
								update_field('biovu_letter_date', $letterDate->format('Ymd'), $postID);
							}
							update_field('identify', wp_strip_all_tags($data[$keys['identify']]), $postID);
							update_field('scientific_project_title', wp_strip_all_tags($data[$keys['title']]), $postID);
							update_field('pi_first_name', wp_strip_all_tags($data[$keys['pi_firstname']]), $postID);
							update_field('pi_last_name', wp_strip_all_tags($data[$keys['pi_lastname']]), $postID);
							update_field('irb_number', wp_strip_all_tags($data[$keys['irb_number']]), $postID);
							update_field('keywords', wp_strip_all_tags($data[$keys['project_keywords']]), $postID);
							if(!empty($data[$keys['research_type']])) {
								$researchType = array_map('trim', explode(',', $data[$keys['research_type']]));
								$categories = array();
								foreach($researchType AS $rKey => $rType) {
									if(empty($rType)) {
										unset($researchType[$rKey]);
									} else {
										$cat = get_term_by('name', $rType, $taxonomy);
										if($cat == false) {
											$cat = wp_insert_term($rType, $taxonomy);
											$cat_id = $cat['term_id'] ;
										} else {
											$cat_id = $cat->term_id ;
										}
										$categories[] = $cat_id;
									}
								}
								wp_set_post_terms($postID, $categories, $taxonomy);
							}
						}
					}
				}
				echo '<h3>Import Complete!</h3><p>'.$postInsertCount.' projects added.</p><hr>';
			}
			?>
				<h1>BioVU CSV Project Import</h1>
				<form action="<?php echo $actual_link; ?>" method="post" enctype="multipart/form-data">
					<strong>Select CSV to import:</strong><br>
					<input type="file" name="redcapExport" id="redcapExport"><br><br>
					<input type="submit" value="Import BioVU Projects" name="submit">
				</form>
			<?php
		});
	}

}
