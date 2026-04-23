(function(wp){
    var el = wp.element.createElement;
    var TextControl = wp.components.TextControl;
    var PanelBody = wp.components.PanelBody;
    var InspectorControls = wp.blockEditor.InspectorControls;
    var SelectControl = wp.components.SelectControl;
    var ToggleControl = wp.components.ToggleControl;

    var brandColors = [
        { label: 'Primary Blue (#1e3a8a)', value: '#1e3a8a' },
        { label: 'Bright Blue (#2563eb)', value: '#2563eb' },
        { label: 'Accent Red (#dc2626)', value: '#dc2626' },
        { label: 'Orange (#ea580c)', value: '#ea580c' },
        { label: 'Dark Navy (#0a1628)', value: '#0a1628' },
    ];

    wp.blocks.registerBlockType('flairltd/stats-counter', {
        edit: function(props) {
            var attr = props.attributes;
            return el('div', { style: { border: '1px dashed #666', padding: '10px' } },
                el(InspectorControls, {},
                    el(PanelBody, { title: 'Block Settings' },
                        el(TextControl, { label: 'Number', value: attr.number, onChange: function(v) { props.setAttributes({number: v}); } }),
                        el(TextControl, { label: 'Suffix', value: attr.suffix, onChange: function(v) { props.setAttributes({suffix: v}); } }),
                        el(TextControl, { label: 'Label', value: attr.label, onChange: function(v) { props.setAttributes({label: v}); } })
                    ),
                    el(PanelBody, { title: 'Background Colour' },
                        el(SelectControl, { label: 'Background Colour', value: attr.bgColor, options: brandColors, onChange: function(v) { props.setAttributes({bgColor: v}); } }),
                        el(ToggleControl, { label: 'Use Gradient', checked: attr.bgGradient, onChange: function(v) { props.setAttributes({bgGradient: v}); } }),
                        attr.bgGradient ? el(SelectControl, { label: 'Gradient Second Colour', value: attr.bgColor2, options: brandColors, onChange: function(v) { props.setAttributes({bgColor2: v}); } }) : null,
                        el(ToggleControl, { label: 'Animate on Scroll', checked: attr.animate, onChange: function(v) { props.setAttributes({animate: v}); } })
                    )
                ),
                'Stat: ' + attr.number + attr.suffix
            );
        },
        save: function() { return null; }
    });
})(window.wp);
