<?php
/**
* Plugin Name: WhatsApp Contact & Request Quote
* Plugin URI: https://github.com/desoem
* Description: Plugin untuk menambahkan tombol WhatsApp floating di semua halaman dan tombol "Request Penawaran" pada halaman produk WooCommerce. Mendukung pengaturan nomor WhatsApp, posisi tombol, dan warna tombol.
* Version: 1.0
* Author: Ridwan Sumantri
* Author URI: https://github.com/desoem
* Text Domain: whatsapp-contact-request-quote
* License: GPL2
* License URI: https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/

// Cegah akses langsung ke file
if (!defined('ABSPATH')) {
    exit;
}

// Tambahkan menu pengaturan di Admin Dashboard
function whatsapp_button_settings_menu() {
    add_options_page(
        'WhatsApp Settings',
        'WhatsApp Settings',
        'manage_options',
        'whatsapp-settings',
        'render_whatsapp_settings_page'
    );
}
add_action('admin_menu', 'whatsapp_button_settings_menu');

// Render halaman pengaturan
function render_whatsapp_settings_page() {
    ?>
    <div class="wrap">
        <h1>WhatsApp Plugin Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('whatsapp-settings-group');
            do_settings_sections('whatsapp-settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

// Registrasi pengaturan
function whatsapp_register_settings() {
    // Pengaturan global
    register_setting('whatsapp-settings-group', 'whatsapp_number');
    register_setting('whatsapp-settings-group', 'whatsapp_position');
    register_setting('whatsapp-settings-group', 'whatsapp_color');

    add_settings_section('whatsapp_settings_section', 'Settings', null, 'whatsapp-settings');
    
    // Fields untuk Floating Button
    add_settings_field('whatsapp_number', 'WhatsApp Number', 'whatsapp_number_field', 'whatsapp-settings', 'whatsapp_settings_section');
    add_settings_field('whatsapp_position', 'Button Position', 'whatsapp_position_field', 'whatsapp-settings', 'whatsapp_settings_section');
    add_settings_field('whatsapp_color', 'Button Color', 'whatsapp_color_field', 'whatsapp-settings', 'whatsapp_settings_section');
}
add_action('admin_init', 'whatsapp_register_settings');

// Callback fields
function whatsapp_number_field() {
    $number = get_option('whatsapp_number', '');
    echo "<input type='text' name='whatsapp_number' value='$number' placeholder='e.g., 1234567890'>";
}

function whatsapp_position_field() {
    $position = get_option('whatsapp_position', 'right');
    echo "<select name='whatsapp_position'>
            <option value='right' " . selected($position, 'right', false) . ">Right</option>
            <option value='left' " . selected($position, 'left', false) . ">Left</option>
          </select>";
}

function whatsapp_color_field() {
    $color = get_option('whatsapp_color', '#25d366');
    echo "<input type='color' name='whatsapp_color' value='$color'>";
}

// Tambahkan tombol Floating Customer Service di semua halaman
function whatsapp_floating_button() {
    $number = get_option('whatsapp_number', '1234567890');
    $position = get_option('whatsapp_position', 'right');
    $color = get_option('whatsapp_color', '#25d366');
    $icon_url = 'https://upload.wikimedia.org/wikipedia/commons/6/6b/WhatsApp.svg'; // Icon resmi WhatsApp

    if (!$number) return;

    $style = "bottom: 20px; " . ($position === 'right' ? "right: 20px;" : "left: 20px;") . " background-color: $color;";
    echo "
    <div id='whatsapp-floating-button' style='$style'>
        <a href='https://wa.me/$number' target='_blank'>
            <img src='$icon_url' alt='WhatsApp' style='width: 25px; height: 25px;'>
        </a>
    </div>
    ";
}
add_action('wp_footer', 'whatsapp_floating_button');

// Tambahkan tombol Request Penawaran di halaman produk WooCommerce
function whatsapp_request_quote_button() {
    if (!is_product()) return; // Tampilkan hanya di halaman produk

    $number = get_option('whatsapp_number', '1234567890');
    if (!$number) return;

    global $product;
    $product_name = $product->get_name();
    $product_url = get_permalink($product->get_id());
    $message = urlencode("Halo, saya tertarik dengan produk berikut: $product_name ($product_url). Bisakah saya mendapatkan penawaran?");
    $whatsapp_link = "https://wa.me/$number?text=$message";

    // URL untuk icon WhatsApp
    $icon_url = 'https://upload.wikimedia.org/wikipedia/commons/6/6b/WhatsApp.svg';

    echo "<a href='$whatsapp_link' target='_blank' class='button alt' style='margin-top: 20px; background-color: #25d366; color: white; text-align: center; display: inline-flex; align-items: center; gap: 8px; padding: 10px 15px; border-radius: 5px;'>
            <img src='$icon_url' alt='WhatsApp' style='width: 20px; height: 20px;'> 
            Minta Penawaran
          </a>";
}
add_action('woocommerce_single_product_summary', 'whatsapp_request_quote_button', 35);


// Tambahkan CSS untuk tombol Floating
function whatsapp_floating_button_styles() {
    echo "
    <style>
    #whatsapp-floating-button {
        position: fixed;
        bottom: 20px;
        z-index: 9999;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    </style>
    ";
}
add_action('wp_head', 'whatsapp_floating_button_styles');
