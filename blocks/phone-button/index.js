(function(wp){
    var el = wp.element.createElement;
    var PanelBody = wp.components.PanelBody;
    var TextControl = wp.components.TextControl;
    var ToggleControl = wp.components.ToggleControl;
    var RangeControl = wp.components.RangeControl;
    var InspectorControls = wp.blockEditor.InspectorControls;
    var BlockControls = wp.blockEditor.BlockControls;
    var AlignmentToolbar = wp.blockEditor.AlignmentToolbar;

    var colors = window.flairltdCustomizerColors || {};
    var brandColors = [
        { label: 'Accent Red (' + (colors.accent || '#dc2626') + ')', value: colors.accent || '#dc2626' },
        { label: 'Primary Blue (' + (colors.primary || '#1e3a8a') + ')', value: colors.primary || '#1e3a8a' },
        { label: 'Bright Blue (' + (colors.bright || '#2563eb') + ')', value: colors.bright || '#2563eb' },
        { label: 'Orange (' + (colors.orange || '#ea580c') + ')', value: colors.orange || '#ea580c' },
        { label: 'Dark Navy (' + (colors.dark || '#0a1628') + ')', value: colors.dark || '#0a1628' },
        { label: 'White (#ffffff)', value: '#ffffff' },
        { label: 'Black (#000000)', value: '#000000' },
    ];
    var textColors = [
        { label: 'White (#ffffff)', value: '#ffffff' },
        { label: 'Black (#000000)', value: '#000000' },
        { label: 'Accent Red (' + (colors.accent || '#dc2626') + ')', value: colors.accent || '#dc2626' },
        { label: 'Primary Blue (' + (colors.primary || '#1e3a8a') + ')', value: colors.primary || '#1e3a8a' },
        { label: 'Bright Blue (' + (colors.bright || '#2563eb') + ')', value: colors.bright || '#2563eb' },
        { label: 'Orange (' + (colors.orange || '#ea580c') + ')', value: colors.orange || '#ea580c' },
        { label: 'Dark Navy (' + (colors.dark || '#0a1628') + ')', value: colors.dark || '#0a1628' },
    ];

    wp.blocks.registerBlockType('flairltd/phone-button', {
        edit: function(props) {
            var attr = props.attributes;
            var phone = (window.flairltdCustomizerColors && window.flairltdCustomizerColors.phone) || '020 7998 9005';
            var displayText = attr.showPhoneNumber ? attr.buttonText + ' ' + phone : attr.buttonText;
            var btnStyle = {
                display: 'inline-flex',
                alignItems: 'center',
                justifyContent: 'center',
                gap: '8px',
                backgroundColor: attr.backgroundColor,
                color: attr.textColor,
                borderRadius: attr.borderRadius + 'px',
                fontSize: attr.fontSize + 'px',
                fontWeight: 600,
                textDecoration: 'none',
                padding: attr.paddingY + 'px ' + attr.paddingX + 'px',
                width: attr.fullWidth ? '100%' : 'auto',
            };

            var alignClass = attr.align ? 'align' + attr.align : '';
            var wrapperStyle = { border: '1px dashed #666', padding: '16px', background: '#f8fafc' };
            if ( attr.align === 'center' ) { wrapperStyle.textAlign = 'center'; }
            if ( attr.align === 'right' ) { wrapperStyle.textAlign = 'right'; }

            return el('div', { className: alignClass, style: wrapperStyle },
                el(BlockControls, {},
                    el(AlignmentToolbar, {
                        value: attr.align,
                        onChange: function(v) { props.setAttributes({ align: v }); }
                    })
                ),
                el(InspectorControls, {},
                    el(PanelBody, { title: 'Button Text', initialOpen: true },
                        el(TextControl, {
                            label: 'Button Text',
                            value: attr.buttonText,
                            onChange: function(v) { props.setAttributes({ buttonText: v }); }
                        }),
                        el(ToggleControl, {
                            label: 'Show Phone Number',
                            checked: attr.showPhoneNumber,
                            onChange: function(v) { props.setAttributes({ showPhoneNumber: v }); }
                        }),
                        el('p', { style: { fontSize: '12px', color: '#666', marginTop: '0' } }, 'Phone: ' + phone)
                    ),
                    el(PanelBody, { title: 'Colours' },
                        el(wp.components.SelectControl, { label: 'Background Colour', value: attr.backgroundColor, options: brandColors, onChange: function(v) { props.setAttributes({ backgroundColor: v }); } }),
                        el(wp.components.SelectControl, { label: 'Text Colour', value: attr.textColor, options: textColors, onChange: function(v) { props.setAttributes({ textColor: v }); } })
                    ),
                    el(PanelBody, { title: 'Style' },
                        el(RangeControl, { label: 'Border Radius (px)', value: attr.borderRadius, onChange: function(v) { props.setAttributes({ borderRadius: v }); }, min: 0, max: 50, step: 1 }),
                        el(RangeControl, { label: 'Font Size (px)', value: attr.fontSize, onChange: function(v) { props.setAttributes({ fontSize: v }); }, min: 12, max: 32, step: 1 }),
                        el(RangeControl, { label: 'Padding Vertical (px)', value: attr.paddingY, onChange: function(v) { props.setAttributes({ paddingY: v }); }, min: 4, max: 40, step: 2 }),
                        el(RangeControl, { label: 'Padding Horizontal (px)', value: attr.paddingX, onChange: function(v) { props.setAttributes({ paddingX: v }); }, min: 8, max: 60, step: 2 }),
                        el(ToggleControl, { label: 'Full Width', checked: attr.fullWidth, onChange: function(v) { props.setAttributes({ fullWidth: v }); } })
                    )
                ),
                el('a', {
                    href: '#',
                    style: btnStyle,
                    onClick: function(e) { e.preventDefault(); }
                },
                    el('svg', { xmlns: 'http://www.w3.org/2000/svg', viewBox: '0 0 24 24', fill: 'none', stroke: 'currentColor', strokeWidth: '2', strokeLinecap: 'round', strokeLinejoin: 'round', width: '18', height: '18' },
                        el('path', { d: 'M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z' })
                    ),
                    displayText
                )
            );
        },
        save: function() { return null; }
    });
})(window.wp);
