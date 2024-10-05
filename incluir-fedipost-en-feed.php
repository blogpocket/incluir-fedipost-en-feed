<?php
/*
Plugin Name: Controlar Tipos de Post en Feed General
Description: Permite al administrador seleccionar qué tipos de post personalizados se incluyen en el feed RSS general.
Version: 1.3
Author: A. Cambronero Blogpocket.com
*/

// Evitar acceso directo
if (!defined('ABSPATH')) exit;

// Función para modificar la consulta del feed
function ctpff_incluir_tipos_en_feed_general($query) {
    $post_type = $query->get('post_type');
    if ($query->is_feed() && $query->is_main_query() ) {
           if (is_array($post_type)) {
               // Se modifica la consulta para que admita todos los tipos de datos seleccionados
               $post_types = get_option('ctpff_post_types');
               if (empty($post_types)) {
                $post_types = array('post');
               }
               $query->set('post_type', $post_types);
           } else {
               // NO se modifica la consulta y solo admitirá el tipo de datos en cuestión
               $query->set('post_type', $post_type);
           }
    }
}

add_action('pre_get_posts', 'ctpff_incluir_tipos_en_feed_general');


// Añadir página de opciones al menú de ajustes
function ctpff_add_settings_page() {
    add_options_page(
        'Tipos de Post en Feed',
        'Tipos de Post en Feed',
        'manage_options',
        'ctpff-settings',
        'ctpff_render_settings_page'
    );
}
add_action('admin_menu', 'ctpff_add_settings_page');

// Registrar ajustes
function ctpff_register_settings() {
    register_setting('ctpff_settings_group', 'ctpff_post_types');
}
add_action('admin_init', 'ctpff_register_settings');

// Renderizar la página de ajustes
function ctpff_render_settings_page() {
    ?>
    <div class="wrap">
        <h1>Seleccionar Tipos de Post para el Feed RSS General</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('ctpff_settings_group');
            do_settings_sections('ctpff_settings_group');
            $selected_post_types = get_option('ctpff_post_types', array('post'));
            $args = array('public' => true);
            $post_types = get_post_types($args, 'objects');
            ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Tipos de Post a Incluir:</th>
                    <td>
                        <?php foreach ($post_types as $post_type): ?>
                            <label>
                                <input type="checkbox" name="ctpff_post_types[]" value="<?php echo esc_attr($post_type->name); ?>" <?php checked(in_array($post_type->name, $selected_post_types)); ?>>
                                <?php echo esc_html($post_type->labels->singular_name); ?>
                            </label><br>
                        <?php endforeach; ?>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}
