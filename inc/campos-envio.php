<?php
// ============================================
// CAMPOS DE TIPO DE ENVÍO (Dokan + Admin)
// ============================================

/**
 * Agrega campo de tipo de envío en el panel de Dokan (vendedores)
 */
add_action('dokan_product_edit_after_shipping', 'islabeya_shipping_field_vendor', 20);
add_action('dokan_new_product_after_shipping', 'islabeya_shipping_field_vendor', 20);

function islabeya_shipping_field_vendor() {
    global $post;
    
    $product_id = isset($post->ID) ? $post->ID : 0;
    $current_value = get_post_meta($product_id, '_shipping_type', true);
    if (empty($current_value)) $current_value = 'gratuito';
    ?>
    
    <div class="dokan-form-group" style="margin-top:20px;padding-top:20px;border-top:1px solid #eee;font-family:'Fuente Poppins',sans-serif;">
        <label class="dokan-control-label" style="font-size:16px;font-weight:500;color:#333;margin-bottom:15px;font-family:'Fuente Poppins',sans-serif; display: flex; align-items: center;">
            Tipo de Entrega
        </label>
        
        <div class="shipping-options-container">
            <label style="display:flex;align-items:center;padding:15px;border:2px solid #e0e0e0;border-radius:8px;background:#fff;cursor:pointer;margin-bottom:12px;transition:all 0.3s;font-family:'Fuente Poppins',sans-serif;margin: 5px !important;width: 80%;<?php echo ($current_value=='recogida')?'border-color:#ff6b35;box-shadow:0 2px 10px rgba(255,107,53,0.1);':'';?>">
                <input type="radio" name="shipping_type" value="recogida" <?php checked($current_value,'recogida');?> style="margin-right:12px;">
                <div style="display: flex; align-items: center;">
                    <div>
                        <div style="font-size:16px;font-weight:600;color:#ff6b35;margin-bottom:3px;font-family:'Fuente Poppins',sans-serif;">Recogida en el Local</div>
                        <div style="font-size:13px;color:#666;font-family:'Fuente Poppins',sans-serif;">El cliente recoge el producto en su tienda física</div>
                    </div>
                </div>
            </label>
            
            <label style="display:flex;align-items:center;padding:15px;border:2px solid #e0e0e0;border-radius:8px;background:#fff;cursor:pointer;transition:all 0.3s;font-family:'Fuente Poppins',sans-serif;margin: 5px !important;width: 80%;<?php echo ($current_value=='gratuito')?'border-color:#00b894;box-shadow:0 2px 10px rgba(0,184,148,0.1);':'';?>">
                <input type="radio" name="shipping_type" value="gratuito" <?php checked($current_value,'gratuito');?> style="margin-right:12px;">
                <div style="display: flex; align-items: center;">
                    <div>
                        <div style="font-size:16px;font-weight:600;color:#00b894;margin-bottom:3px;font-family:'Fuente Poppins',sans-serif;">Envío Gratuito</div>
                        <div style="font-size:13px;color:#666;font-family:'Fuente Poppins',sans-serif;">Envío sin costo adicional para el cliente</div>
                    </div>
                </div>
            </label>
            
            <label style="display:flex;align-items:center;padding:15px;border:2px solid #e0e0e0;border-radius:8px;background:#fff;cursor:pointer;transition:all 0.3s;font-family:'Fuente Poppins',sans-serif;margin: 5px !important;width: 80%;<?php echo ($current_value=='gratis-provincia')?'border-color:#9b59b6;box-shadow:0 2px 10px rgba(155,89,182,0.1);':'';?>">
                <input type="radio" name="shipping_type" value="gratis-provincia" <?php checked($current_value,'gratis-provincia');?> style="margin-right:12px;">
                <div style="display: flex; align-items: center;">
                    <div>
                        <div style="font-size:16px;font-weight:600;color:#9b59b6;margin-bottom:3px;font-family:'Fuente Poppins',sans-serif;">Envío Gratis por Provincia</div>
                        <div style="font-size:13px;color:#666;font-family:'Fuente Poppins',sans-serif;">Envío gratis solo a provincias específicas (usa la taxonomía Provincia)</div>
                    </div>
                </div>
            </label>
        </div>
        
        <?php if ($current_value == 'gratis-provincia'): ?>
        <div class="dokan-form-help" style="margin-top:15px;padding:15px;background:#f8f9fa;border-radius:8px;border-left:4px solid #9b59b6;">
            <p style="margin:0;font-size:13px;color:#6c3483;font-family:'Fuente Poppins',sans-serif;">
                <strong>⚠️ Importante:</strong> Para configurar las provincias permitidas, usa los checkboxes de la taxonomía <strong>Provincia</strong> en el panel lateral derecho.
            </p>
        </div>
        <?php endif; ?>
        
        <p class="dokan-form-help" style="margin-top:10px;font-size:13px;color:#666;font-family:'Fuente Poppins',sans-serif;">
            Esta información se mostrará a los clientes en la página del producto
        </p>
    </div>
    <?php
}

/**
 * Agrega campo de tipo de envío en el panel de administración de WooCommerce
 */
add_action('woocommerce_product_options_shipping', 'islabeya_shipping_field_admin', 20);

function islabeya_shipping_field_admin() {
    global $post;
    
    $product_id = $post->ID;
    $current_value = get_post_meta($product_id, '_shipping_type', true);
    if (empty($current_value)) $current_value = 'gratuito';
    ?>
    
    <div class="options_group" style="padding:15px 0;border-top:1px solid #eee;font-family:'Fuente Poppins',sans-serif;">
        <h4 style="margin:0 0 15px 0;color:#333;font-family:'Fuente Poppins',sans-serif; display: flex; align-items: center;">
            Tipo de Entrega IslaBeya
        </h4>
        
        <p class="form-field">
            <div style="display:flex;flex-direction:column;gap:10px;">
                <label style="margin: 5px !important;width: 80%;display:flex;align-items:center;padding:12px 15px;border:2px solid <?php echo ($current_value=='recogida')?'#ff6b35':'#ddd';?>;border-radius:6px;background:<?php echo ($current_value=='recogida')?'#fff5f0':'#fff';?>;cursor:pointer;font-family:'Fuente Poppins',sans-serif;">
                    <input type="radio" name="shipping_type" value="recogida" <?php checked($current_value,'recogida');?> style="margin-right:10px;">
                    <span style="font-size:14px;font-weight:600;color:#ff6b35;font-family:'Fuente Poppins',sans-serif;">Recogida en el Local</span>
                </label>
                
                <label style="margin: 5px !important;width: 80%;display:flex;align-items:center;padding:12px 15px;border:2px solid <?php echo ($current_value=='gratuito')?'#00b894':'#ddd';?>;border-radius:6px;background:<?php echo ($current_value=='gratuito')?'#f0f9f4':'#fff';?>;cursor:pointer;font-family:'Fuente Poppins',sans-serif;">
                    <input type="radio" name="shipping_type" value="gratuito" <?php checked($current_value,'gratuito');?> style="margin-right:10px;">
                    <span style="font-size:14px;font-weight:600;color:#00b894;font-family:'Fuente Poppins',sans-serif;">Envío Gratuito</span>
                </label>
                
                <label style="margin: 5px !important;width: 80%;display:flex;align-items:center;padding:12px 15px;border:2px solid <?php echo ($current_value=='gratis-provincia')?'#9b59b6':'#ddd';?>;border-radius:6px;background:<?php echo ($current_value=='gratis-provincia')?'#f5f0ff':'#fff';?>;cursor:pointer;font-family:'Fuente Poppins',sans-serif;">
                    <input type="radio" name="shipping_type" value="gratis-provincia" <?php checked($current_value,'gratis-provincia');?> style="margin-right:10px;">
                    <span style="font-size:14px;font-weight:600;color:#9b59b6;font-family:'Fuente Poppins',sans-serif;">Envío Gratis por Provincia</span>
                </label>
            </div>
        </p>
        
        <?php if ($current_value == 'gratis-provincia'): ?>
        <div style="margin-top:15px;padding:15px;background:#f8f9fa;border-radius:6px;border-left:4px solid #9b59b6;">
            <p style="margin:0;font-size:13px;color:#6c3483;font-family:'Fuente Poppins',sans-serif;">
                <strong>⚠️ Importante:</strong> Para configurar las provincias permitidas, usa los checkboxes de la taxonomía <strong>Provincia</strong> en el panel lateral derecho.
            </p>
        </div>
        <?php endif; ?>
    </div>
    <?php
}