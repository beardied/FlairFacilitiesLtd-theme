(function(wp){
    wp.blocks.registerBlockType('flairltd/testimonial-block', {
        edit: function(props) {
            return wp.element.createElement('div', { style: { border: '1px dashed #666', padding: '10px' } },
                wp.element.createElement(wp.blockEditor.InspectorControls, {},
                    wp.element.createElement(wp.components.PanelBody, { title: 'Block Settings' },
                        wp.element.createElement(wp.components.TextControl, { label: 'Quote', value: props.attributes.quote, onChange: (v) => props.setAttributes({quote: v}) }),
                        wp.element.createElement(wp.components.TextControl, { label: 'Author', value: props.attributes.author, onChange: (v) => props.setAttributes({author: v}) }),
                        wp.element.createElement(wp.components.RangeControl, { label: 'Rating', value: props.attributes.rating, min: 1, max: 5, onChange: (v) => props.setAttributes({rating: v}) })
                    )
                ),
                'Testimonial: ' + props.attributes.author
            );
        },
        save: function() { return null; }
    });
})(window.wp);