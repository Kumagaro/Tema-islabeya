<?php
// Reemplazar método de envío en FunnelKit con ubicación Dokan
add_action('wfacp_template_before_payment', 'replace_funnelkit_shipping_methods_with_location');

function replace_funnelkit_shipping_methods_with_location() {
    if (!WC()->cart) return;
    
    $cart_items = WC()->cart->get_cart();
    if (empty($cart_items)) return;
    
    // Analizar tipos de envío en el carrito
    $has_recogida = false;
    $has_gratuito = false;
    $vendor_locations = array();
    $recogida_products = array();
    $gratuito_products = array();
    
    foreach ($cart_items as $cart_item) {
        $product_id = $cart_item['product_id'];
        $product = wc_get_product($product_id);
        $shipping_type = get_post_meta($product_id, '_shipping_type', true);
        $quantity = $cart_item['quantity'];
        
        if ($shipping_type == 'recogida') {
            $has_recogida = true;
            $recogida_products[] = array(
                'name' => $product->get_name(),
                'quantity' => $quantity,
                'total' => wc_price($cart_item['line_total'])
            );
            
            $vendor_id = dokan_get_vendor_by_product($product_id, true);
            
            if ($vendor_id && !isset($vendor_locations[$vendor_id])) {
                $tienda_info = obtener_direccion_dokan_checkout($vendor_id);
                
                if ($tienda_info && $tienda_info['has_address']) {
                    $phone_display = '';
                    if (!empty($tienda_info['phone'])) {
                        $phone_display = '<div style="margin-top: 8px; font-size: 14px; color: #555; display: flex; align-items: center;">';
                        $phone_display .= '<img src="' . get_template_directory_uri() . 'assets/icons/Phone Rounded.svg" style="width: 18px; height: 18px; margin-right: 6px;" alt="Teléfono">';
                        $phone_display .= '<strong>Teléfono:</strong> ' . esc_html($tienda_info['phone']);
                        $phone_display .= '</div>';
                    }
                    
                    $email_display = '';
                    if (!empty($tienda_info['email'])) {
                        $email_display = '<div style="font-size: 14px; color: #555; display: flex; align-items: center;">';
                        $email_display .= '<img src="' . get_template_directory_uri() . 'assets/icons/Letter.svg" style="width: 18px; height: 18px; margin-right: 6px;" alt="Email">';
                        $email_display .= '<strong>Email:</strong> ' . esc_html($tienda_info['email']);
                        $email_display .= '</div>';
                    }
                    
                    $vendor_locations[$vendor_id] = array(
                        'store_name' => $tienda_info['store_name'],
                        'address_html' => $tienda_info['html_direccion'],
                        'phone' => $tienda_info['phone'],
                        'email' => $tienda_info['email'],
                        'phone_display' => $phone_display,
                        'email_display' => $email_display
                    );
                }
            }
        } else {
            $has_gratuito = true;
            $gratuito_products[] = array(
                'name' => $product->get_name(),
                'quantity' => $quantity,
                'total' => wc_price($cart_item['line_total'])
            );
        }
    }
    
    if ($has_recogida && $has_gratuito) {
        display_mixed_shipping_with_complete_address($vendor_locations, $recogida_products, $gratuito_products);
    } elseif ($has_recogida) {
        display_recogida_only_with_complete_address($vendor_locations, $recogida_products);
    } else {
        display_gratuito_only_simple();
    }
    
    // Ocultar métodos de envío predeterminados de FunnelKit
    echo '<style>
        .wfacp_shipping_methods,
        .wfacp_shipping_calculator,
        .shipping_method,
        .woocommerce-shipping-methods {
            display: none !important;
        }
    </style>';
}

function display_mixed_shipping_with_complete_address($vendor_locations, $recogida_products, $gratuito_products) {
    ?>
    <div class="islabeya-funnelkit-shipping mixed-shipping" style="
        margin: 20px 0;
        padding: 20px;
        background: linear-gradient(135deg, #fff8e1 0%, #fff 100%);
        border-radius: 10px;
        border: 2px solid #f39c12;
        box-shadow: 0 4px 15px rgba(243, 156, 18, 0.1);
        
    ">
        <div class="mixed-shipping-header" style="display: flex; align-items: center; margin-bottom: 15px; ">
            <div class="mixed-shipping-icon-wrapper" style="width: 50px; height: 50px; background: #f39c12; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/icons/Notes.svg" class="mixed-shipping-icon" style="width: 22px; height: 28px; filter: brightness(0) invert(1);" alt="Método mixto">
            </div>
            <div class="texto-entrega-mixta">
                <h3 style="margin: 0; color: #333; font-size: 18px;">Método de Entrega Mixto</h3>
                <p style="margin: 5px 0 0 0; color: #666; font-size: 14px;">Tu pedido contiene productos con diferentes tipos de entrega</p>
            </div>
        </div>
        
        <div class="shipping-methods-container" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-top: 20px;">
            <!-- Envío Gratuito (escritorio derecha, móvil primero) -->
            <div class="gratuito-section" style="padding: 15px; background: #f0f9f4; border-radius: 8px; border-left: 4px solid #00b894; order: 2;">
                <div style="display: flex; align-items: center; margin-bottom: 10px;">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/img/Delivery.svg" style="width: 20px; height: 20px; margin-right: 8px;" alt="Envío">
                    <span style="font-weight: 600; color: #00b894; font-size: 15px;">Envío Gratuito</span>
                </div>
                <div style="margin-top: 10px;">
                    <div style="font-size: 13px; color: #666; font-weight: 600; margin-bottom: 5px; display: flex; align-items: center;">
                        Productos para enviar (<?php echo count($gratuito_products); ?>):
                    </div>
                    <?php foreach ($gratuito_products as $product): ?>
                    <div style="display: flex; justify-content: space-between; font-size: 12px; color: #555; margin-top: 3px; padding: 3px 0; border-bottom: 1px dashed #eee;">
                        <span><?php echo esc_html($product['name']); ?> × <?php echo $product['quantity']; ?></span>
                        <span style="font-weight: 600;"><?php echo $product['total']; ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div style="margin-top: 10px; font-size: 13px; color: #666;">
                    Se enviarán a tu dirección de entrega
                </div>
            </div>
            
            <!-- Recogida (escritorio izquierda, móvil segundo) -->
            <div class="recogida-section" style="padding: 15px; background: #fff5f0; border-radius: 8px; border-left: 4px solid #ff6b35; order: 1;">
                <div style="display: flex; align-items: center; margin-bottom: 10px;">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/img/Box.svg" style="width: 20px; height: 20px; margin-right: 8px;" alt="Recogida">
                    <span style="font-weight: 600; color: #ff6b35; font-size: 15px;">Recogida en el Local</span>
                </div>
                <div style="margin-top: 10px;">
                    <div style="font-size: 13px; color: #666; font-weight: 600; margin-bottom: 5px; display: flex; align-items: center;">
                        Productos para recoger (<?php echo count($recogida_products); ?>):
                    </div>
                    <?php foreach ($recogida_products as $product): ?>
                    <div style="display: flex; justify-content: space-between; font-size: 12px; color: #555; margin-top: 3px; padding: 3px 0; border-bottom: 1px dashed #eee;">
                        <span><?php echo esc_html($product['name']); ?> × <?php echo $product['quantity']; ?></span>
                        <span style="font-weight: 600;"><?php echo $product['total']; ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <?php if (!empty($vendor_locations)): ?>
                <div style="margin-top: 15px; padding-top: 10px; border-top: 1px dashed #ffd1b9;">
                    <div style="font-size: 13px; color: #666; font-weight: 600; margin-bottom: 8px; display: flex; align-items: center;">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/icons/Point On Map.svg" style="width: 18px; height: 18px; margin-right: 6px;" alt="Ubicación">
                        Ubicaciones para recoger:
                    </div>
                    <?php foreach ($vendor_locations as $vendor_data): ?>
                    <div style="margin-bottom: 15px; padding: 12px; background: white; border-radius: 6px; border: 1px solid #ffd1b9;">
                        <div style="font-weight: 600; color: #ff6b35; font-size: 14px; margin-bottom: 8px; display: flex; align-items: center;">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/icons/Shop.svg" style="width: 18px; height: 18px; margin-right: 6px;" alt="Tienda">
                            <?php echo esc_html($vendor_data['store_name']); ?>
                        </div>
                        <div style="font-size: 13px; color: #555; line-height: 1.5;">
                            <?php echo wp_kses_post($vendor_data['address_html']); ?>
                        </div>
                        <?php if (!empty($vendor_data['phone_display'])): ?>
                        <div style="margin-top: 8px; font-size: 13px; color: #555;">
                            <?php echo wp_kses_post($vendor_data['phone_display']); ?>
                        </div>
                        <?php endif; ?>
                        <?php if (!empty($vendor_data['email_display'])): ?>
                        <div style="font-size: 13px; color: #555;">
                            <?php echo wp_kses_post($vendor_data['email_display']); ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <style>
            @media (max-width: 767px) {
                .shipping-methods-container {
                    grid-template-columns: 1fr !important;
                    gap: 20px !important;
                }
                .gratuito-section { order: 1 !important; }
                .recogida-section { order: 2 !important; }
                .mixed-shipping-icon-wrapper { width: 40px !important; height: 40px !important; margin-right: 12px !important; }
                .mixed-shipping-icon { width: 18px !important; height: 24px !important; }
                .islabeya-funnelkit-shipping.mixed-shipping { padding: 15px !important; margin: 15px 0 !important; }
            }
            @media (min-width: 768px) {
                .shipping-methods-container { grid-template-columns: 1fr 1fr !important; gap: 15px !important; }
                .gratuito-section { order: 2 !important; }
                .recogida-section { order: 1 !important; }
            }
        </style>
    </div>
    <?php
}

function display_recogida_only_with_complete_address($vendor_locations, $recogida_products) {
    ?>
    <div class="islabeya-funnelkit-shipping recogida-only" style="margin: 20px 0; padding: 20px; background: linear-gradient(135deg, #fff5f0 0%, #fff 100%); border-radius: 10px; border: 2px solid #ff6b35; box-shadow: 0 4px 15px rgba(255,107,53,0.1);">
        <div class="recogida-only-header" style="display: flex; align-items: center; margin-bottom: 15px;">
            <div class="recogida-only-icon-wrapper" style="width: 50px; height: 50px; background: #ff6b35; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/img/Box.svg" class="recogida-only-icon" style="width: 22px; height: 22px; filter: brightness(0) invert(1);" alt="Recogida">
            </div>
            <div>
                <h3 style="margin: 0; color: #333; font-size: 18px;">Recogida en el Local</h3>
                <p style="margin: 5px 0 0 0; color: #666; font-size: 14px;">Todos los productos deben recogerse en nuestros locales</p>
            </div>
        </div>
        <div style="margin-top: 15px;">
            <div style="font-size: 14px; color: #666; font-weight: 600; margin-bottom: 8px; display: flex; align-items: center;">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/icons/Bag 2.svg" style="width: 18px; height: 18px; margin-right: 6px;" alt="Productos">
                Productos para recoger (<?php echo count($recogida_products); ?>):
            </div>
            <?php foreach ($recogida_products as $product): ?>
            <div style="display: flex; justify-content: space-between; font-size: 13px; color: #555; margin-top: 6px; padding: 8px 10px; background: #fff9f6; border-radius: 5px; border-left: 3px solid #ff6b35;">
                <span><?php echo esc_html($product['name']); ?> × <?php echo $product['quantity']; ?></span>
                <span style="font-weight: 600; color: #ff6b35;"><?php echo $product['total']; ?></span>
            </div>
            <?php endforeach; ?>
        </div>
        <?php if (!empty($vendor_locations)): ?>
        <div style="margin-top: 20px; padding-top: 15px; border-top: 2px solid #ffd1b9;">
            <div style="font-size: 14px; color: #666; font-weight: 600; margin-bottom: 10px; display: flex; align-items: center;">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/icons/Point On Map.svg" style="width: 20px; height: 20px; margin-right: 8px;" alt="Ubicación">
                Ubicaciones para recoger:
            </div>
            <?php foreach ($vendor_locations as $vendor_data): ?>
            <div style="margin-bottom: 15px; padding: 15px; background: white; border-radius: 8px; border: 1px solid #ffd1b9; box-shadow: 0 2px 8px rgba(255,107,53,0.08);">
                <div style="font-weight: 600; color: #ff6b35; font-size: 15px; margin-bottom: 10px; display: flex; align-items: center;">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/icons/Shop.svg" style="width: 20px; height: 20px; margin-right: 8px;" alt="Tienda">
                    <?php echo esc_html($vendor_data['store_name']); ?>
                </div>
                <div style="font-size: 14px; color: #555; line-height: 1.6; margin-bottom: 8px;">
                    <?php echo wp_kses_post($vendor_data['address_html']); ?>
                </div>
                <?php if (!empty($vendor_data['phone_display'])): ?>
                <div style="margin-top: 8px; font-size: 14px; color: #555;"><?php echo wp_kses_post($vendor_data['phone_display']); ?></div>
                <?php endif; ?>
                <?php if (!empty($vendor_data['email_display'])): ?>
                <div style="font-size: 14px; color: #555;"><?php echo wp_kses_post($vendor_data['email_display']); ?></div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        <style>
            @media (max-width: 767px) {
                .recogida-only-icon-wrapper { width: 40px !important; height: 40px !important; margin-right: 12px !important; }
                .recogida-only-icon { width: 18px !important; height: 18px !important; }
                .islabeya-funnelkit-shipping.recogida-only { padding: 15px !important; margin: 15px 0 !important; }
            }
        </style>
    </div>
    <?php
}

function display_gratuito_only_simple() {
    ?>
    <div class="islabeya-funnelkit-shipping gratuito-only" style="margin: 20px 0; padding: 20px; background: linear-gradient(135deg, #f0f9f4 0%, #fff 100%); border-radius: 10px; border: 2px solid #00b894; box-shadow: 0 4px 15px rgba(0,184,148,0.1);">
        <div class="gratuito-only-header" style="display: flex; align-items: center; margin-bottom: 15px;">
            <div class="gratuito-only-icon-wrapper" style="width: 50px; height: 50px; background: #00b894; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/img/Delivery.svg" class="gratuito-only-icon" style="width: 24px; height: 20px; filter: brightness(0) invert(1);" alt="Envío gratuito">
            </div>
            <div>
                <h3 style="margin: 0; color: #333; font-size: 18px;">Envío Gratuito</h3>
                <p style="margin: 5px 0 0 0; color: #666; font-size: 14px;">Todos los productos se enviarán a tu dirección</p>
            </div>
        </div>
        <div style="margin-top: 15px; padding: 15px; background: white; border-radius: 8px; border: 1px solid #d1f2eb;">
            <div style="display: flex; align-items: center;">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/icons/Check Square.svg" style="width: 20px; height: 20px; margin-right: 10px;" alt="Confirmado">
                <div style="font-size: 15px; color: #555;">
                    <span style="font-weight: 600; color: #00b894;">¡Envío gratuito confirmado!</span>
                    <p style="margin: 5px 0 0 0; color: #666; font-size: 14px;">Los productos se enviarán a la dirección que proporcionaste. No hay costo adicional por envío.</p>
                </div>
            </div>
            <div style="margin-top: 15px; padding-top: 15px; border-top: 1px dashed #d1f2eb;">
                <div style="display: flex; align-items: center; font-size: 14px; color: #666;">
                    <span><strong>Tiempo estimado de entrega:</strong> 35-40 días hábiles</span>
                </div>
            </div>
        </div>
        <style>
            @media (max-width: 767px) {
                .gratuito-only-icon-wrapper {height: 35.28px !important; margin-right: 12px !important; }
                .gratuito-only-icon { width: 20px !important; height: 16px !important; }
                .islabeya-funnelkit-shipping.gratuito-only { padding: 15px !important; margin: 15px 0 !important; }
            }
        </style>
    </div>
    <?php
}