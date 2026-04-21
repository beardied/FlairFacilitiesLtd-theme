(function(wp){
    var el = wp.element.createElement;
    var TextControl = wp.components.TextControl;
    var RangeControl = wp.components.RangeControl;
    var PanelBody = wp.components.PanelBody;
    var InspectorControls = wp.blockEditor.InspectorControls;
    var MediaUpload = wp.blockEditor.MediaUpload;

    wp.blocks.registerBlockType('flairltd/hero', {
        edit: function(props) {
            var attr = props.attributes;
            return el('div', { style: { border: '1px dashed #666', padding: '10px', background: '#0a1628', color: '#fff' } },
                el(InspectorControls, {},
                    el(PanelBody, { title: 'Background', initialOpen: true },
                        el('div', { style: { marginBottom: '12px' } },
                            el(MediaUpload, {
                                onSelect: function(media) {
                                    props.setAttributes({ backgroundImage: media.url });
                                },
                                allowedTypes: ['image'],
                                render: function(obj) {
                                    return el('div', {},
                                        attr.backgroundImage ? el('img', { src: attr.backgroundImage, style: { maxWidth: '100%', maxHeight: '120px', display: 'block', marginBottom: '8px', borderRadius: '4px' } }) : null,
                                        el('button', { type: 'button', className: 'components-button is-secondary', onClick: obj.open }, attr.backgroundImage ? 'Change Background Image' : 'Select Background Image')
                                    );
                                }
                            })
                        ),
                        attr.backgroundImage ? el('button', { type: 'button', className: 'components-button is-link is-destructive', style: { marginBottom: '12px' }, onClick: function() { props.setAttributes({ backgroundImage: '' }); } }, 'Remove Image') : null,
                        el(TextControl, { label: 'Overlay Colour', value: attr.overlayColor, onChange: function(v) { props.setAttributes({overlayColor: v}); } }),
                        el(RangeControl, { label: 'Overlay Opacity (%)', value: attr.overlayOpacity, min: 0, max: 100, onChange: function(v) { props.setAttributes({overlayOpacity: v}); } })
                    ),
                    el(PanelBody, { title: 'Hero Content' },
                        el(TextControl, { label: 'Headline', value: attr.headline, onChange: function(v) { props.setAttributes({headline: v}); } }),
                        el(TextControl, { label: 'Description', value: attr.description, onChange: function(v) { props.setAttributes({description: v}); } })
                    ),
                    el(PanelBody, { title: 'Badges' },
                        el(TextControl, { label: 'Badge 1', value: attr.badge1, onChange: function(v) { props.setAttributes({badge1: v}); } }),
                        el(TextControl, { label: 'Badge 2', value: attr.badge2, onChange: function(v) { props.setAttributes({badge2: v}); } }),
                        el(TextControl, { label: 'Badge 3', value: attr.badge3, onChange: function(v) { props.setAttributes({badge3: v}); } })
                    ),
                    el(PanelBody, { title: 'Main Buttons' },
                        el(TextControl, { label: 'Primary Button Text', value: attr.primaryBtnText, onChange: function(v) { props.setAttributes({primaryBtnText: v}); } }),
                        el(TextControl, { label: 'Primary Button URL', value: attr.primaryBtnUrl, onChange: function(v) { props.setAttributes({primaryBtnUrl: v}); } }),
                        el(TextControl, { label: 'Secondary Button Text', value: attr.secondaryBtnText, onChange: function(v) { props.setAttributes({secondaryBtnText: v}); } }),
                        el(TextControl, { label: 'Secondary Button URL', value: attr.secondaryBtnUrl, onChange: function(v) { props.setAttributes({secondaryBtnUrl: v}); } })
                    ),
                    el(PanelBody, { title: 'Side Card' },
                        el(TextControl, { label: 'Card Title', value: attr.cardTitle, onChange: function(v) { props.setAttributes({cardTitle: v}); } }),
                        el(TextControl, { label: 'Card Description', value: attr.cardDescription, onChange: function(v) { props.setAttributes({cardDescription: v}); } }),
                        el(TextControl, { label: 'Card Button 1 Text', value: attr.cardBtn1Text, onChange: function(v) { props.setAttributes({cardBtn1Text: v}); } }),
                        el(TextControl, { label: 'Card Button 1 URL', value: attr.cardBtn1Url, onChange: function(v) { props.setAttributes({cardBtn1Url: v}); } }),
                        el(TextControl, { label: 'Card Button 2 Text', value: attr.cardBtn2Text, onChange: function(v) { props.setAttributes({cardBtn2Text: v}); } }),
                        el(TextControl, { label: 'Card Button 2 URL', value: attr.cardBtn2Url, onChange: function(v) { props.setAttributes({cardBtn2Url: v}); } })
                    )
                ),
                el('strong', {}, 'Hero: ' + attr.headline)
            );
        },
        save: function() { return null; }
    });
})(window.wp);
