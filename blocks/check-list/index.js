(function(wp){
    var el = wp.element.createElement;
    var TextControl = wp.components.TextControl;
    var PanelBody = wp.components.PanelBody;
    var InspectorControls = wp.blockEditor.InspectorControls;

    wp.blocks.registerBlockType('flairltd/check-list', {
        edit: function(props) {
            var attr = props.attributes;
            var items = [];
            for (var i = 1; i <= 8; i++) {
                var t = attr['item' + i + 'Title'];
                if (t) items.push(t);
            }
            return el('div', { style: { border: '1px dashed #666', padding: '10px' } },
                el(InspectorControls, {},
                    el(PanelBody, { title: 'Check Items', initialOpen: true },
                        (function() {
                            var controls = [];
                            for (var i = 1; i <= 8; i++) {
                                (function(idx) {
                                    controls.push(
                                        el('div', { key: idx, style: { marginBottom: '12px', paddingBottom: '8px', borderBottom: '1px solid #eee' } },
                                            el('strong', {}, 'Item ' + idx),
                                            el(TextControl, {
                                                label: 'Title',
                                                value: attr['item' + idx + 'Title'] || '',
                                                onChange: function(v) {
                                                    var upd = {};
                                                    upd['item' + idx + 'Title'] = v;
                                                    props.setAttributes(upd);
                                                }
                                            }),
                                            el(TextControl, {
                                                label: 'Description (optional)',
                                                value: attr['item' + idx + 'Description'] || '',
                                                onChange: function(v) {
                                                    var upd = {};
                                                    upd['item' + idx + 'Description'] = v;
                                                    props.setAttributes(upd);
                                                }
                                            })
                                        )
                                    );
                                })(i);
                            }
                            return controls;
                        })()
                    )
                ),
                el('strong', {}, 'Check List (' + items.length + ' items)')
            );
        },
        save: function() { return null; }
    });
})(window.wp);
