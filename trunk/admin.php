<?php

class PremiseGAEcommerce_Admin_Boxes extends Premise_Admin_Boxes {

	function __construct() {
		$menu_options = array(
			'submenu' => array(
				'parent_slug' => 'premise-member',
				'page_title' => 'Google Analytics Ecommerce Settings',
				'menu_title' => 'Google Analytics Ecommerce',
				'capability' => 'manage_options',
			)
		);

		$default_settings = array(
			'enabled' => 0,
			'account_id' => 'UA-XXXXX-X',
		);

		$this->create('premise-ga-ecommerce', $menu_options, array(), 'premise-ga-ecommerce-settings', $default_settings);

		add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_css'));
	}

	function enqueue_admin_css() {
		$screen = get_current_screen();

		if (! $screen || 'member-access_page_premise-ga-ecommerce' != $screen->id)
			return;

		wp_enqueue_style( 'premise-admin', PREMISE_RESOURCES_URL . 'premise-admin.css', array( 'thickbox' ), PREMISE_VERSION );
	}

	function metaboxes() {
		add_meta_box('premise-ga-ecommerce-settings', 'Google Analytics Ecommerce Settings', array($this, 'settings'), $this->pagehook, 'main');
	}

	function settings() {
		?>

			<p>
				<label for="<?php echo $this->get_field_id('enabled'); ?>"><?php _e('Enable'); ?> <input type="checkbox" name="<?php echo $this->get_field_name('enabled'); ?>" id="<?php echo $this->get_field_id('enabled'); ?>" value="1" <?php checked($this->get_field_value('enabled'), 1); ?>>
				</label>
			</p>

			<p>
				<label for="<?php echo $this->get_field_id('account_id'); ?>"><?php _e('Account ID'); ?></label><br>
				<input type="text" name="<?php echo $this->get_field_name('account_id'); ?>" id="<?php echo $this->get_field_id('account_id'); ?>" value="<?php echo $this->get_field_value('account_id'); ?>">
			</p>

		<?php
	}
}

new PremiseGAEcommerce_Admin_Boxes;
