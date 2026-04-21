(function(wp){
    var el = wp.element.createElement;
    var TextControl = wp.components.TextControl;
    var RangeControl = wp.components.RangeControl;
    var PanelBody = wp.components.PanelBody;
    var InspectorControls = wp.blockEditor.InspectorControls;
    var MediaUpload = wp.blockEditor.MediaUpload;
    var SelectControl = wp.components.SelectControl;

    var brandColors = [
        { label: 'Primary Blue (#1e3a8a)', value: '#1e3a8a' },
        { label: 'Bright Blue (#2563eb)', value: '#2563eb' },
        { label: 'Accent Red (#dc2626)', value: '#dc2626' },
        { label: 'Orange (#ea580c)', value: '#ea580c' },
        { label: 'Dark Navy (#0a1628)', value: '#0a1628' },
    ];

    wp.blocks.registerBlockType('flairltd/expertise-card', {
        edit: function(props) {
            var attr = props.attributes;
            return el('div', { style: { border: '1px dashed #666', padding: '10px' } },
                el(InspectorControls, {},
                    el(PanelBody, { title: 'Background Image', initialOpen: true },
                        el('div', { style: { marginBottom: '12px' } },
                            el(MediaUpload, {
                                onSelect: function(media) { props.setAttributes({ backgroundImage: media.url }); },
                                allowedTypes: ['image'],
                                render: function(obj) {
                                    return el('div', {},
                                        attr.backgroundImage ? el('img', { src: attr.backgroundImage, style: { maxWidth: '100%', maxHeight: '80px', display: 'block', marginBottom: '8px', borderRadius: '4px' } }) : null,
                                        el('button', { type: 'button', className: 'components-button is-secondary', onClick: obj.open }, attr.backgroundImage ? 'Change Image' : 'Select Image')
                                    );
                                }
                            })
                        ),
                        attr.backgroundImage ? el('button', { type: 'button', className: 'components-button is-link is-destructive', style: { marginBottom: '12px' }, onClick: function() { props.setAttributes({ backgroundImage: '' }); } }, 'Remove Image') : null,
                        el(SelectControl, { label: 'Overlay Colour', value: attr.overlayColor, options: brandColors, onChange: function(v) { props.setAttributes({overlayColor: v}); } }),
                        el(RangeControl, { label: 'Overlay Opacity (%)', value: attr.overlayOpacity, min: 0, max: 100, onChange: function(v) { props.setAttributes({overlayOpacity: v}); } })
                    ),
                    el(PanelBody, { title: 'Card Content' },
                        el(TextControl, { label: 'Title', value: attr.title, onChange: function(v) { props.setAttributes({title: v}); } }),
                        el(TextControl, { label: 'Description', value: attr.description, onChange: function(v) { props.setAttributes({description: v}); } }),
                        el(TextControl, { label: 'Button Text', value: attr.buttonText, onChange: function(v) { props.setAttributes({buttonText: v}); } }),
                        el(TextControl, { label: 'URL', value: attr.buttonUrl, onChange: function(v) { props.setAttributes({buttonUrl: v}); } })
                    )
                ),
                'Expertise Card: ' + attr.title
            );
        },
        save: function() { return null; }
    });
})(window.wp);
