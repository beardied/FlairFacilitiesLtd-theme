(function(wp){
    wp.blocks.registerBlockType('flairltd/expertise-card', {
        edit: function(props) {
            return wp.element.createElement('div', { style: { border: '1px dashed #666', padding: '10px' } },
                wp.element.createElement(wp.blockEditor.InspectorControls, {},
                    wp.element.createElement(wp.components.PanelBody, { title: 'Block Settings' },
                        wp.element.createElement(wp.components.TextControl, { label: 'Title', value: props.attributes.title, onChange: (v) => props.setAttributes({title: v}) }),
                        wp.element.createElement(wp.components.TextControl, { label: 'Description', value: props.attributes.description, onChange: (v) => props.setAttributes({description: v}) }),
                        wp.element.createElement(wp.components.TextControl, { label: 'Button Text', value: props.attributes.buttonText, onChange: (v) => props.setAttributes({buttonText: v}) }),
                        wp.element.createElement(wp.components.TextControl, { label: 'URL', value: props.attributes.buttonUrl, onChange: (v) => props.setAttributes({buttonUrl: v}) })
                    )
                ),
                'Expertise Card: ' + props.attributes.title
            );
        },
        save: function() { return null; }
    });
})(window.wp);