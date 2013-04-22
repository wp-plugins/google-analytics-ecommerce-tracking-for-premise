<?php
/*
Plugin Name: GA Ecommerce Tracking for Premise
Plugin URI: http://www.eugenoprea.com/wordpress-plugins/premise-google-analytics-ecommerce/?utm_source=wp_admin&utm_medium=plugin&utm_campaign=premise_ga_ecommerce-tracking
Description: Send Ecommerce data from Premise to Google Analytics. It passes item and order information to Google Analytics reports when Ecommerce is activated.
Version: 1.0.1
Author: Eugen Oprea
Author URI: http://www.eugenoprea.com/?utm_source=wp_admin&utm_medium=plugin&utm_campaign=premise_ga_ecommerce-tracking
License: GPLv2 or later
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

class PremiseGAEcommerce {

	private $options;

	function __construct() {
		$this->options = (array) get_option('premise-ga-ecommerce-settings');

		if  (isset($this->options['enabled']) && $this->options['enabled']) {
			add_action('premise_membership_create_order', array($this, 'get_order_data'), 10, 3);
			add_action('premise_checkout_complete_after', array($this, 'print_javascript'), 10);
		}

		add_action('init', array($this, 'admin_init'));
	}

	function admin_init() {
		if (is_admin())
			require dirname(__FILE__) . '/admin.php';
	}

	function get_order_data($member_id, $order_details, $renewal) {
		$this->order_data = array(
			'trans_id' => $order_details['_acp_order_time'],
			'store_name' => get_bloginfo('name'),
			'total' => $order_details['_acp_order_price'] ? $order_details['_acp_order_price'] : 0,
		);

		$this->product = get_post($order_details['_acp_order_product_id']);
	}

	function print_javascript() {
		?>
		<!-- Premise Google Analytics Ecommerce -->
		<script type="text/javascript">

		  var _gaq = _gaq || [];
		  _gaq.push(['_setAccount', '<?php echo trim($this->options['account_id']); ?>']);
		  _gaq.push(['_trackPageview']);
		  _gaq.push(['_addTrans',
			  '<?php echo $this->order_data['trans_id']; ?>',
			  '<?php echo $this->order_data['store_name']; ?>',
			  <?php echo $this->order_data['total']; ?>
		  ]);

		  _gaq.push(['_addItem',
			  '<?php echo $this->order_data['trans_id']; ?>',
			  '<?php echo $this->product->ID; ?>',
			  '<?php echo $this->product->post_title; ?>',
			  '<?php _e('Premise Product'); ?>',
			  <?php echo $this->order_data['total']; ?>,
			  1
		  ]);
		  _gaq.push(['_trackTrans']);

		  (function() {
			var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		  })();

		</script>
		<!-- /Premise Google Analytics Ecommerce -->
		<?php
	}
}

new PremiseGAEcommerce;
