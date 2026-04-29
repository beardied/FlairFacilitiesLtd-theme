(function(wp){
    var el = wp.element.createElement;
    var useState = wp.element.useState;
    var PanelBody = wp.components.PanelBody;
    var InspectorControls = wp.blockEditor.InspectorControls;
    var MediaUpload = wp.blockEditor.MediaUpload;
    var RangeControl = wp.components.RangeControl;
    var ToggleControl = wp.components.ToggleControl;
    var ColorPicker = wp.components.ColorPicker;
    var BaseControl = wp.components.BaseControl;
    var Button = wp.components.Button;

    wp.blocks.registerBlockType('flairltd/logo-marquee', {
        edit: function(props) {
            var attr = props.attributes;
            var logos = attr.logos || [];

            function addLogo() {
                props.setAttributes({ logos: logos.concat([{ id: 0, url: '', alt: '' }]) });
            }

            function updateLogo(index, updates) {
                var newLogos = logos.slice();
                newLogos[index] = Object.assign({}, newLogos[index], updates);
                props.setAttributes({ logos: newLogos });
            }

            function removeLogo(index) {
                var newLogos = logos.slice();
                newLogos.splice(index, 1);
                props.setAttributes({ logos: newLogos });
            }

            function moveLogo(index, direction) {
                var newLogos = logos.slice();
                var swapIndex = index + direction;
                if (swapIndex < 0 || swapIndex >= newLogos.length) return;
                var temp = newLogos[index];
                newLogos[index] = newLogos[swapIndex];
                newLogos[swapIndex] = temp;
                props.setAttributes({ logos: newLogos });
            }

            return el('div', { style: { border: '1px dashed #666', padding: '16px' } },
                el(InspectorControls, {},
                    el(PanelBody, { title: 'Background', initialOpen: true },
                        el(BaseControl, { label: 'Background Colour' },
                            el(ColorPicker, {
                                color: attr.bgColor,
                                onChangeComplete: function(color) { props.setAttributes({ bgColor: color.hex }); },
                                disableAlpha: false
                            })
                        )
                    ),
                    el(PanelBody, { title: 'Scroll Settings' },
                        el(RangeControl, {
                            label: 'Scroll Speed (seconds per loop)',
                            value: attr.scrollSpeed,
                            onChange: function(v) { props.setAttributes({ scrollSpeed: v }); },
                            min: 5,
                            max: 120,
                            step: 1
                        }),
                        el(ToggleControl, {
                            label: 'Pause on Hover',
                            checked: attr.pauseOnHover,
                            onChange: function(v) { props.setAttributes({ pauseOnHover: v }); }
                        })
                    ),
                    el(PanelBody, { title: 'Logo Size & Spacing' },
                        el(RangeControl, {
                            label: 'Logo Max Width (px)',
                            value: attr.logoMaxWidth,
                            onChange: function(v) { props.setAttributes({ logoMaxWidth: v }); },
                            min: 50,
                            max: 400,
                            step: 5
                        }),
                        el(RangeControl, {
                            label: 'Logo Max Height (px)',
                            value: attr.logoMaxHeight,
                            onChange: function(v) { props.setAttributes({ logoMaxHeight: v }); },
                            min: 30,
                            max: 200,
                            step: 5
                        }),
                        el(RangeControl, {
                            label: 'Gap Between Logos (px)',
                            value: attr.gap,
                            onChange: function(v) { props.setAttributes({ gap: v }); },
                            min: 20,
                            max: 200,
                            step: 5
                        })
                    ),
                    el(PanelBody, { title: 'Padding' },
                        el(RangeControl, {
                            label: 'Top Padding (px)',
                            value: attr.paddingTop,
                            onChange: function(v) { props.setAttributes({ paddingTop: v }); },
                            min: 0,
                            max: 200,
                            step: 5
                        }),
                        el(RangeControl, {
                            label: 'Bottom Padding (px)',
                            value: attr.paddingBottom,
                            onChange: function(v) { props.setAttributes({ paddingBottom: v }); },
                            min: 0,
                            max: 200,
                            step: 5
                        })
                    )
                ),

                el('h4', { style: { marginTop: 0, marginBottom: '12px' } }, 'Logo Marquee'),

                logos.length === 0 ? el('p', { style: { color: '#666' } }, 'No logos added. Click "Add Logo" to get started.') : null,

                el('div', { style: { display: 'flex', flexDirection: 'column', gap: '10px', marginBottom: '12px' } },
                    logos.map(function(logo, index) {
                        return el('div', { key: index, style: { display: 'flex', alignItems: 'center', gap: '8px', padding: '8px', background: '#f0f0f0', borderRadius: '4px' } },
                            el('div', { style: { flex: '0 0 auto' } },
                                el(MediaUpload, {
                                    onSelect: function(media) { updateLogo(index, { id: media.id, url: media.url, alt: media.alt || '' }); },
                                    allowedTypes: ['image'],
                                    render: function(obj) {
                                        return el('div', {},
                                            logo.url ? el('img', { src: logo.url, style: { maxWidth: '80px', maxHeight: '40px', display: 'block', marginBottom: '4px', objectFit: 'contain' } }) : null,
                                            el(Button, { isSecondary: true, onClick: obj.open, isSmall: true }, logo.url ? 'Change' : 'Select')
                                        );
                                    }
                                })
                            ),
                            el('div', { style: { flex: '1 1 auto', fontSize: '12px', color: '#444', overflow: 'hidden', textOverflow: 'ellipsis', whiteSpace: 'nowrap' } },
                                logo.alt || 'No alt text'
                            ),
                            el('div', { style: { display: 'flex', gap: '4px', flex: '0 0 auto' } },
                                el(Button, { isSmall: true, disabled: index === 0, onClick: function() { moveLogo(index, -1); } }, '↑'),
                                el(Button, { isSmall: true, disabled: index === logos.length - 1, onClick: function() { moveLogo(index, 1); } }, '↓'),
                                el(Button, { isSmall: true, isDestructive: true, onClick: function() { removeLogo(index); } }, '×')
                            )
                        );
                    })
                ),

                el(Button, { isPrimary: true, onClick: addLogo }, 'Add Logo')
            );
        },
        save: function() { return null; }
    });
})(window.wp);
