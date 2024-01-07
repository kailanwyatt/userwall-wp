// wp-threads-block.js
import { registerBlockType } from '@wordpress/blocks';
//const { registerBlockType } = wp.blocks;
const { TextControl } = wp.components;

registerBlockType('wp-threads/thread-post', {
    title: 'Thread Post',
    icon: 'shield',
    category: 'common',
    keywords: ['thread', 'post'],
    attributes: {
        content: {
            type: 'string',
            default: '',
        },
    },

    edit: function(props) {
        return (
            <div>
                <TextControl
                    label="Thread Post Content"
                    value={props.attributes.content}
                    onChange={content => props.setAttributes({ content })}
                />
            </div>
        );
    },

    save: function(props) {
        return <div>{props.attributes.content}</div>;
    },
});
