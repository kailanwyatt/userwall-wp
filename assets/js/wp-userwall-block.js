// wp-userwall-block.js
const { SelectControl } = wp.components;

(function(blocks, editor, element) {
    var el = element.createElement;
    var RichText = editor.RichText;

    blocks.registerBlockType('userwall-wp/page-render', {
        title: 'UserWall WP',
        icon: 'universal-access-alt',
        category: 'user-wall',
        attributes: {
            content: {
                type: 'array',
                source: 'children',
                selector: 'div',
            },
            displayType: {
                type: 'string',
                default: 'wall-posts',
            },
        },
        edit: function(props) {
            function onChangeDisplayType(newDisplayType) {
                props.setAttributes({ displayType: newDisplayType });
            }
            return [
                el(
                    SelectControl,
                    {
                        label: 'Display Type',
                        value: props.attributes.displayType,
                        options: [
                            { value: 'wall-posts', label: 'Wall Posts' },
                            { value: 'profile', label: 'Profile' },
                            { value: 'single-post', label: 'Single Post' },
                        ],
                        onChange: onChangeDisplayType,
                    }
                ),
            ]
        },
        save: function() {
            return null; // Render in PHP
        }
    });
})(window.wp.blocks, window.wp.editor, window.wp.element);
