(function(wp){
    var el = wp.element.createElement;
    var TextControl = wp.components.TextControl;
    var PanelBody = wp.components.PanelBody;
    var InspectorControls = wp.blockEditor.InspectorControls;
    var MediaUpload = wp.blockEditor.MediaUpload;
    var SelectControl = wp.components.SelectControl;
    var ToggleControl = wp.components.ToggleControl;

    var brandColors = [
        { label: 'Primary Blue (#1e3a8a)', value: '#1e3a8a' },
        { label: 'Bright Blue (#2563eb)', value: '#2563eb' },
        { label: 'Accent Red (#dc2626)', value: '#dc2626' },
        { label: 'Orange (#ea580c)', value: '#ea580c' },
        { label: 'Dark Navy (#0a1628)', value: '#0a1628' },
    ];

    wp.blocks.registerBlockType('flairltd/about-image', {
        edit: function(props) {
            var attr = props.attributes;
            return el('div', { style: { border: '1px dashed #666', padding: '10px' } },
                el(InspectorControls, {},
                    el(PanelBody, { title: 'Image', initialOpen: true },
                        el('div', { style: { marginBottom: '12px' } },
                            el(MediaUpload, {
                                onSelect: function(media) { props.setAttributes({imageUrl: media.url, imageAlt: media.alt || ''}); },
                                allowedTypes: ['image'],
                                render: function(obj) {
                                    return el('div', {},
                                        attr.imageUrl ? el('img', { src: attr.imageUrl, style: { maxWidth: '100%', maxHeight: '80px', display: 'block', marginBottom: '8px', borderRadius: '4px' } }) : null,
                                        el('button', { type: 'button', className: 'components-button is-secondary', onClick: obj.open }, attr.imageUrl ? 'Change Image' : 'Select Image')
                                    );
                                }
                            })
                        ),
                        attr.imageUrl ? el('button', { type: 'button', className: 'components-button is-link is-destructive', style: { marginBottom: '12px' }, onClick: function() { props.setAttributes({imageUrl: '', imageAlt: ''}); } }, 'Remove Image') : null,
                        el(TextControl, { label: 'Image Alt', value: attr.imageAlt, onChange: function(v) { props.setAttributes({imageAlt: v}); } })
                    ),
                    el(PanelBody, { title: 'Stats Overlay Background' },
                        el(SelectControl, { label: 'Background Colour', value: attr.statBgColor, options: brandColors, onChange: function(v) { props.setAttributes({statBgColor: v}); } }),
                        el(ToggleControl, { label: 'Use Gradient', checked: attr.statBgGradient, onChange: function(v) { props.setAttributes({statBgGradient: v}); } }),
                        attr.statBgGradient ? el(SelectControl, { label: 'Gradient Second Colour', value: attr.statBgColor2, options: brandColors, onChange: function(v) { props.setAttributes({statBgColor2: v}); } }) : null,
                        el(ToggleControl, { label: 'Animate on Scroll', checked: attr.animate, onChange: function(v) { props.setAttributes({animate: v}); } })
                    ),
                    el(PanelBody, { title: 'Stat 1' },
                        el(TextControl, { label: 'Number', value: attr.stat1Number, onChange: function(v) { props.setAttributes({stat1Number: v}); } }),
                        el(TextControl, { label: 'Suffix', value: attr.stat1Suffix, onChange: function(v) { props.setAttributes({stat1Suffix: v}); } }),
                        el(TextControl, { label: 'Label', value: attr.stat1Label, onChange: function(v) { props.setAttributes({stat1Label: v}); } })
                    ),
                    el(PanelBody, { title: 'Stat 2' },
                        el(TextControl, { label: 'Number', value: attr.stat2Number, onChange: function(v) { props.setAttributes({stat2Number: v}); } }),
                        el(TextControl, { label: 'Suffix', value: attr.stat2Suffix, onChange: function(v) { props.setAttributes({stat2Suffix: v}); } }),
                        el(TextControl, { label: 'Label', value: attr.stat2Label, onChange: function(v) { props.setAttributes({stat2Label: v}); } })
                    )
                ),
                el('strong', {}, 'About Image: ' + (attr.imageUrl || 'default'))
            );
        },
        save: function() { return null; }
    });
})(window.wp);
