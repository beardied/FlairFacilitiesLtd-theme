(function(wp){
    wp.blocks.registerBlockType('flairltd/stats-counter', {
        edit: function(props) {
            return wp.element.createElement('div', { style: { border: '1px dashed #666', padding: '10px' } },
                wp.element.createElement(wp.blockEditor.InspectorControls, {},
                    wp.element.createElement(wp.components.PanelBody, { title: 'Block Settings' },
                        wp.element.createElement(wp.components.TextControl, { label: 'Number', value: props.attributes.number, onChange: (v) => props.setAttributes({number: v}) }),
                        wp.element.createElement(wp.components.TextControl, { label: 'Suffix', value: props.attributes.suffix, onChange: (v) => props.setAttributes({suffix: v}) }),
                        wp.element.createElement(wp.components.TextControl, { label: 'Label', value: props.attributes.label, onChange: (v) => props.setAttributes({label: v}) })
                    )
                ),
                'Stat: ' + props.attributes.number + props.attributes.suffix
            );
        },
        save: function() { return null; }
    });
})(window.wp);