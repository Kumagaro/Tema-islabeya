<?php
/**
 * Template: ubicacion-modal.php
 * Descripción: Plantilla del modal para seleccionar ubicación
 * Variables disponibles:
 * - $provinces: Array de provincias
 * - $all_municipalities: Array de todos los municipios agrupados
 * - $current_province: ID de la provincia actual
 * - $title: Título del modal
 */

if (!defined('ABSPATH')) exit;
?>

<div id="ubicacion-selector-modal" class="ubicacion-modal" style="display: none;" aria-hidden="true"
     data-all-municipalities='<?php echo json_encode($all_municipalities); ?>'>
    
    <div class="ubicacion-modal-content" role="dialog" aria-labelledby="ubicacion-modal-title">
        
        <button type="button" id="btn-close-ubicacion-modal" class="ubicacion-modal-close" 
                style="position:absolute; top:6px; right:15px; background:none; border:none; font-size:24px; cursor:pointer; color:#000; z-index:10;">
            &times;
        </button>
        
        <div class="ubicacion-modal-header">
            <h3 id="ubicacion-modal-title"><?php echo esc_html($title); ?></h3>
        </div>
        
        <div class="ubicacion-modal-body">
            <form id="ubicacion-selector-form" method="post" class="ubicacion-form">
                
                <div class="ubicacion-form-group">
                    <label for="ubicacion-province-select" class="ubicacion-label">
                        Provincia <span class="required">*</span>
                    </label>
                    <select name="province" id="ubicacion-province-select" class="ubicacion-select" required>
                        <option value="">-- Selecciona una provincia --</option>
                        <?php foreach ($provinces as $province): ?>
                            <option value="<?php echo esc_attr($province->term_id); ?>" 
                                    <?php selected($current_province, $province->term_id); ?>>
                                <?php echo esc_html($province->name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="ubicacion-form-group">
                    <label for="ubicacion-municipality-select" class="ubicacion-label">
                        Municipio <span class="required">*</span>
                    </label>
                    <select name="municipality" id="ubicacion-municipality-select" class="ubicacion-select" 
                            required <?php echo !$current_province ? 'disabled' : ''; ?>>
                        <option value="">-- Selecciona un municipio --</option>
                    </select>
                </div>
                
                <div class="ubicacion-form-group ubication-form-submit">
                    <button type="submit" class="ubicacion-submit-btn" id="btn-confirm-ubicacion">
                        <span class="btn-text">Confirmar ubicación</span>
                        <span class="btn-loader" style="display: none;">
                            <span class="spinner"></span> Guardando...
                        </span>
                    </button>
                </div>
            </form>
            
            <div id="ubicacion-message" class="ubicacion-message" style="display: none;" role="alert"></div>
        </div>
    </div>
</div>