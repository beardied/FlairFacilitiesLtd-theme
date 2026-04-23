(function(wp){
    var el = wp.element.createElement;
    var TextControl = wp.components.TextControl;
    var TextareaControl = wp.components.TextareaControl;
    var PanelBody = wp.components.PanelBody;
    var InspectorControls = wp.blockEditor.InspectorControls;
    var Button = wp.components.Button;

    wp.blocks.registerBlockType('flairltd/faq-section', {
        edit: function(props) {
            var attr = props.attributes;
            var items = attr.items || [];

            function updateTitle(value) {
                props.setAttributes({ title: value });
            }

            function updateItem(index, field, value) {
                var newItems = items.slice();
                newItems[index] = Object.assign({}, newItems[index], { [field]: value });
                props.setAttributes({ items: newItems });
            }

            function addItem() {
                props.setAttributes({
                    items: items.concat([{ question: '', answer: '' }])
                });
            }

            function removeItem(index) {
                var newItems = items.slice();
                newItems.splice(index, 1);
                props.setAttributes({ items: newItems });
            }

            function moveItem(index, direction) {
                var newItems = items.slice();
                var swapIndex = index + direction;
                if (swapIndex < 0 || swapIndex >= newItems.length) return;
                var temp = newItems[index];
                newItems[index] = newItems[swapIndex];
                newItems[swapIndex] = temp;
                props.setAttributes({ items: newItems });
            }

            return el('div', { style: { border: '1px dashed #666', padding: '16px', background: '#f8fafc' } },
                el(TextControl, {
                    label: 'Section Title',
                    value: attr.title,
                    onChange: updateTitle,
                    style: { marginBottom: '16px' }
                }),
                el('h3', { style: { margin: '0 0 16px', fontSize: '14px', textTransform: 'uppercase', letterSpacing: '0.5px', color: '#0a1628' } }, 'FAQ Items (' + items.length + ')'),
                items.map(function(item, index) {
                    return el('div', {
                        key: index,
                        style: {
                            background: '#fff',
                            border: '1px solid #e2e8f0',
                            borderRadius: '6px',
                            padding: '12px',
                            marginBottom: '10px'
                        }
                    },
                        el('div', { style: { display: 'flex', gap: '8px', marginBottom: '8px' } },
                            el(Button, {
                                isSmall: true,
                                disabled: index === 0,
                                onClick: function() { moveItem(index, -1); }
                            }, '↑'),
                            el(Button, {
                                isSmall: true,
                                disabled: index === items.length - 1,
                                onClick: function() { moveItem(index, 1); }
                            }, '↓'),
                            el('span', { style: { flex: 1, fontSize: '12px', color: '#64748b', lineHeight: '26px' } }, 'Item ' + (index + 1)),
                            el(Button, {
                                isSmall: true,
                                isDestructive: true,
                                onClick: function() { removeItem(index); }
                            }, 'Remove')
                        ),
                        el(TextControl, {
                            label: 'Question',
                            value: item.question,
                            onChange: function(v) { updateItem(index, 'question', v); }
                        }),
                        el(TextareaControl, {
                            label: 'Answer',
                            value: item.answer,
                            rows: 3,
                            onChange: function(v) { updateItem(index, 'answer', v); }
                        })
                    );
                }),
                el(Button, {
                    isSecondary: true,
                    onClick: addItem,
                    style: { marginTop: '8px' }
                }, '+ Add FAQ Item')
            );
        },
        save: function() { return null; }
    });
})(window.wp);
