(function(wp){
    var el = wp.element.createElement;
    var TextControl = wp.components.TextControl;
    var ToggleControl = wp.components.ToggleControl;
    var PanelBody = wp.components.PanelBody;
    var InspectorControls = wp.blockEditor.InspectorControls;

    wp.blocks.registerBlockType('flairltd/contact-form', {
        edit: function(props) {
            var attr = props.attributes;
            var defaultEmail = (window.flairltdCustomizerColors && window.flairltdCustomizerColors.email)
                ? window.flairltdCustomizerColors.email
                : 'info@flairfacilities.co.uk';

            return el('div', { style: { border: '1px dashed #666', padding: '16px', background: '#f8fafc' } },
                el(InspectorControls, {},
                    el(PanelBody, { title: 'Form Settings', initialOpen: true },
                        el(TextControl, {
                            label: 'Recipient Email',
                            help: 'Where submissions are sent. Falls back to Customizer contact email if empty.',
                            value: attr.recipientEmail,
                            onChange: function(v) { props.setAttributes({ recipientEmail: v }); },
                            type: 'email'
                        }),
                        el(ToggleControl, {
                            label: 'Show Border',
                            checked: attr.hasBorder !== false,
                            onChange: function(v) { props.setAttributes({ hasBorder: v }); }
                        })
                    )
                ),
                el('h3', { style: { margin: '0 0 8px', fontSize: '16px' } }, 'Contact Form'),
                el('p', { style: { fontSize: '12px', color: '#64748b', margin: 0 } },
                    'Submissions sent to: ' + (attr.recipientEmail || defaultEmail)
                )
            );
        },
        save: function() { return null; }
    });
})(window.wp);
