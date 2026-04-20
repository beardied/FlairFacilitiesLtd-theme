(function(wp){
    var el = wp.element.createElement;
    var TextControl = wp.components.TextControl;
    var PanelBody = wp.components.PanelBody;
    var InspectorControls = wp.blockEditor.InspectorControls;

    wp.blocks.registerBlockType('flairltd/about-image', {
        edit: function(props) {
            var attr = props.attributes;
            return el('div', { style: { border: '1px dashed #666', padding: '10px' } },
                el(InspectorControls, {},
                    el(PanelBody, { title: 'Image', initialOpen: true },
                        el(TextControl, { label: 'Image URL', value: attr.imageUrl, onChange: function(v) { props.setAttributes({imageUrl: v}); } }),
                        el(TextControl, { label: 'Image Alt', value: attr.imageAlt, onChange: function(v) { props.setAttributes({imageAlt: v}); } })
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
