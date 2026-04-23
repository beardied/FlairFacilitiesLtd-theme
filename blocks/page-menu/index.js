(function(wp){
    var el = wp.element.createElement;
    var TextControl = wp.components.TextControl;
    var PanelBody = wp.components.PanelBody;
    var InspectorControls = wp.blockEditor.InspectorControls;
    var ToggleControl = wp.components.ToggleControl;

    var brandColors = [
        { label: 'White (#ffffff)', value: '#ffffff' },
        { label: 'Off White (#f8fafc)', value: '#f8fafc' },
        { label: 'Light Gray (#e2e8f0)', value: '#e2e8f0' },
        { label: 'Dark Navy (#0a1628)', value: '#0a1628' },
        { label: 'Primary Blue (#1e3a8a)', value: '#1e3a8a' },
        { label: 'Bright Blue (#2563eb)', value: '#2563eb' },
        { label: 'Accent Red (#dc2626)', value: '#dc2626' },
        { label: 'Orange (#ea580c)', value: '#ea580c' },
    ];

    wp.blocks.registerBlockType('flairltd/page-menu', {
        edit: function(props) {
            var attr = props.attributes;

            return el('div', { style: { border: '1px dashed #666', padding: '16px', background: '#f8fafc' } },
                el(InspectorControls, {},
                    el(PanelBody, { title: 'Background', initialOpen: false },
                        el('div', { style: { marginBottom: '12px' } },
                            el('label', { style: { display: 'block', marginBottom: '4px', fontWeight: '600' } }, 'Background Colour'),
                            el('select', {
                                value: attr.bgColor,
                                onChange: function(e) { props.setAttributes({ bgColor: e.target.value }); },
                                style: { width: '100%' }
                            }, brandColors.map(function(c) {
                                return el('option', { value: c.value, key: c.value }, c.label);
                            }))
                        ),
                        el(ToggleControl, {
                            label: 'Use Gradient',
                            checked: attr.bgGradient,
                            onChange: function(v) { props.setAttributes({ bgGradient: v }); }
                        }),
                        attr.bgGradient ? el('div', { style: { marginBottom: '12px' } },
                            el('label', { style: { display: 'block', marginBottom: '4px', fontWeight: '600' } }, 'Gradient End Colour'),
                            el('select', {
                                value: attr.bgColor2,
                                onChange: function(e) { props.setAttributes({ bgColor2: e.target.value }); },
                                style: { width: '100%' }
                            }, brandColors.map(function(c) {
                                return el('option', { value: c.value, key: c.value }, c.label);
                            }))
                        ) : null,
                        el(ToggleControl, {
                            label: 'Animate on Scroll',
                            checked: attr.animate,
                            onChange: function(v) { props.setAttributes({ animate: v }); }
                        })
                    )
                ),
                el(TextControl, {
                    label: 'Section Title',
                    value: attr.title,
                    onChange: function(v) { props.setAttributes({ title: v }); },
                    style: { marginBottom: '12px' }
                }),
                el('p', { style: { fontSize: '13px', color: '#64748b', margin: 0 } },
                    'This block displays the parent page and sibling pages (other children of the same parent). It updates automatically based on the page hierarchy.'
                )
            );
        },
        save: function() { return null; }
    });
})(window.wp);
