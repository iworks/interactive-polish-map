(function(blocks, blockEditor, element, components, __) {
    var el = element.createElement;
    var useBlockProps = blockEditor.useBlockProps;
    var SelectControl = components.SelectControl;
    var TextControl = components.TextControl;

    blocks.registerBlockType('interactive-polish-map/map', {
        title: 'Interactive Polish Map',
        icon: 'location-alt',
        category: 'widgets',

        attributes: {
            style: {
                type: 'number',
                default: 400,
            },
            menu: {
                type: 'string',
                default: 'hide'
            }
        },

        example: {
            attributes: {
                style: 400,
                menu: 'hide',
            },
        },
        edit: function(props) {
            var style = props.attributes.style;
            var menu = props.attributes.menu;

            function setstyle(value) {
                props.setAttributes({
                    style: Number( value )
                });
            }

            function setMenu(value) {
                props.setAttributes({
                    menu: value === undefined ? 'hide' : value,
                });
            }

            return el(
                'div',
                useBlockProps(),
                el(
                    TextControl, {
                        label: __('Map Width', 'interactive-polish-map'),
                        value: style,
                        type: 'number',
                        onChange: (value) => {
                            setstyle(value);
                        }
                    }
                ),
                el(
                    SelectControl, {
                        label: __('Choose Districts List Position', 'interactive-polish-map'),
                        value: menu,
                        options: [{
                            value: 'hide',
                            label: __('Hidden', 'interactive-polish-map')
                        }, {
                            value: 'left',
                            label: __('On left', 'interactive-polish-map')
                        }, {
                            value: 'right',
                            label: __('On right', 'interactive-polish-map')
                        }, {
                            value: 'before',
                            label: __('Before', 'interactive-polish-map')
                        }, {
                            value: 'after',
                            label: __('After', 'interactive-polish-map')
                        }, {
                            value: 'after-two-columns',
                            label: __('After two columns', 'interactive-polish-map')
                        }, {
                            value: 'bottom-three-columns',
                            label: __('After three columns', 'interactive-polish-map')
                        }],
                        onChange: (menu) => {
                            setMenu(menu);
                        }
                    }
                )
            );
        },

        save: function(props) {
            var blockProps = useBlockProps.save();
            return el(
                'div',
                blockProps,
                '[interactive-polish-map style="'+props.attributes.style+'" menu="'+props.attributes.menu+'"]'
            );
        },
    });
})(window.wp.blocks, window.wp.blockEditor, window.wp.element, window.wp.components, wp.i18n.__);

