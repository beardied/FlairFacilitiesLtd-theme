(function(wp){
    wp.blocks.registerBlockType('flairltd/service-block', {
        edit: function(props) {
            return wp.element.createElement('div', { style: { border: '1px dashed #666', padding: '10px' } },
                wp.element.createElement(wp.blockEditor.InspectorControls, {},
                    wp.element.createElement(wp.components.PanelBody, { title: 'Block Settings' },
                        wp.element.createElement(wp.components.TextControl, { label: 'Icon', value: props.attributes.icon, onChange: (v) => props.setAttributes({icon: v}) }),
                        wp.element.createElement(wp.components.TextControl, { label: 'Title', value: props.attributes.title, onChange: (v) => props.setAttributes({title: v}) }),
                        wp.element.createElement(wp.components.TextControl, { label: 'Description', value: props.attributes.description, onChange: (v) => props.setAttributes({description: v}) }),
                        wp.element.createElement(wp.components.TextControl, { label: 'Button Text', value: props.attributes.buttonText, onChange: (v) => props.setAttributes({buttonText: v}) })
                    )
                ),
                'Service Block: ' + props.attributes.title
            );
        },
        save: function() { return null; }
    });
})(window.wp);