(function(wp){
    var el = wp.element.createElement;
    var PanelBody = wp.components.PanelBody;
    var InspectorControls = wp.blockEditor.InspectorControls;
    var InnerBlocks = wp.blockEditor.InnerBlocks;
    var SelectControl = wp.components.SelectControl;
    var ToggleControl = wp.components.ToggleControl;
    var TextControl = wp.components.TextControl;
    var ColorPicker = wp.components.ColorPicker;
    var BaseControl = wp.components.BaseControl;

    var colors = window.flairltdCustomizerColors || {};
    var brandColors = [
        { label: 'Primary Blue (' + (colors.primary || '#1e3a8a') + ')', value: colors.primary || '#1e3a8a' },
        { label: 'Bright Blue (' + (colors.bright || '#2563eb') + ')', value: colors.bright || '#2563eb' },
        { label: 'Accent Red (' + (colors.accent || '#dc2626') + ')', value: colors.accent || '#dc2626' },
        { label: 'Orange (' + (colors.orange || '#ea580c') + ')', value: colors.orange || '#ea580c' },
        { label: 'Dark Navy (' + (colors.dark || '#0a1628') + ')', value: colors.dark || '#0a1628' },
        { label: 'White (#ffffff)', value: '#ffffff' },
        { label: 'Light Gray (#f8fafc)', value: '#f8fafc' },
    ];

    wp.blocks.registerBlockType('flairltd/cta-section', {
        edit: function(props) {
            var attr = props.attributes;
            var phone = (window.flairltdCustomizerColors && window.flairltdCustomizerColors.phone) || '020 7998 9005';

            return el('div', { style: { border: '1px dashed #666', padding: '16px' } },
                el(InspectorControls, {},
                    el(PanelBody, { title: 'Background', initialOpen: true },
                        el(SelectControl, { label: 'Background Colour', value: attr.bgColor, options: brandColors, onChange: function(v) { props.setAttributes({bgColor: v}); } }),
                        el(ToggleControl, { label: 'Use Gradient', checked: attr.bgGradient, onChange: function(v) { props.setAttributes({bgGradient: v}); } }),
                        attr.bgGradient ? el(SelectControl, { label: 'Gradient Second Colour', value: attr.bgColor2, options: brandColors, onChange: function(v) { props.setAttributes({bgColor2: v}); } }) : null,
                        el(ToggleControl, { label: 'Animated Gradient', checked: attr.animate, onChange: function(v) { props.setAttributes({animate: v}); } })
                    ),
                    el(PanelBody, { title: 'Call Button' },
                        el(ToggleControl, { label: 'Show Button', checked: attr.showButton, onChange: function(v) { props.setAttributes({showButton: v}); } }),
                        attr.showButton ? el(TextControl, { label: 'Button Text', value: attr.buttonText, onChange: function(v) { props.setAttributes({buttonText: v}); } }) : null,
                        attr.showButton ? el(BaseControl, { label: 'Phone Number (from Customizer): ' + phone }, el('span', { style: { color: '#666', fontSize: '12px' } }, 'Edit in Customizer → Contact Info')) : null,
                        attr.showButton ? el(SelectControl, { label: 'Button Background', value: attr.buttonBgColor, options: brandColors, onChange: function(v) { props.setAttributes({buttonBgColor: v}); } }) : null,
                        attr.showButton ? el(ToggleControl, { label: 'Button Gradient', checked: attr.buttonGradient, onChange: function(v) { props.setAttributes({buttonGradient: v}); } }) : null,
                        attr.showButton && attr.buttonGradient ? el(SelectControl, { label: 'Button Gradient Second Colour', value: attr.buttonBgColor2, options: brandColors, onChange: function(v) { props.setAttributes({buttonBgColor2: v}); } }) : null,
                        attr.showButton ? el(BaseControl, { label: 'Button Text Colour' }, el(ColorPicker, { color: attr.buttonTextColor, onChangeComplete: function(c) { props.setAttributes({buttonTextColor: c.hex}); }, disableAlpha: true })) : null
                    )
                ),

                el('h4', { style: { marginTop: 0, marginBottom: '8px' } }, 'CTA Section'),
                el('p', { style: { marginTop: 0, marginBottom: '12px', fontSize: '12px', color: '#666' } }, 'Add content blocks below. The Call Now button will appear underneath.'),

                el(InnerBlocks, {}),

                attr.showButton ? el('div', { style: { marginTop: '16px', textAlign: 'center' } },
                    el('a', { href: '#', style: { display: 'inline-flex', alignItems: 'center', gap: '8px', padding: '12px 24px', borderRadius: '8px', textDecoration: 'none', fontWeight: '600', background: attr.buttonGradient ? 'linear-gradient(135deg, ' + attr.buttonBgColor + ' 0%, ' + attr.buttonBgColor2 + ' 100%)' : attr.buttonBgColor, color: attr.buttonTextColor },
                        el('span', {}, attr.buttonText + ' ' + phone)
                    )
                ) : null
            );
        },
        save: function() { return el(InnerBlocks.Content); }
    });
})(window.wp);
