// Import Quill
import Quill from 'quill';
import ToolbarEmoji from 'quill-emoji';
//import Masonry from 'masonry-layout';

//window.userwallWP.Masonry = Masonry;
var editor_theme = 'snow';
var editor_config = [];
class UserWallWPHelper {
    constructor() {
      
    }
    
    doEmbed(content) {
        // Extract the first URL from the content
        const url = this.extractFirstLink(content);
       
        if (url) {
            // Determine the content type of the URL (e.g., regular, image, video, embed)
            const contentType = this.determineContentType(url);

            // Generate the rich preview HTML based on content type
            const richPreviewHTML = this.generateRichPreview(contentType, url);

            // Replace the first URL in the content with the rich preview HTML
            //const updatedContent = content.replace(url, richPreviewHTML);
            const updatedContent = this.convertLinksAndHashtags( content ) + richPreviewHTML;

            return updatedContent;
        }

        // If no URL is found, return the original content
        return this.convertLinksAndHashtags( content );
    }

    convertLinksAndHashtags(content) {
        if (typeof content !== 'string') {
            // Handle cases where content is not a string
            return '';
        }
    
        const userUrl = "https://example.com/u/";
    
        const urlRegex = /https?:\/\/[^\s<>]+/g;
        const hashtagRegex = /#(\w+)/g;
        const userRegex = /@(\w+)/g; // New regex to match @user
    
        // Replace URLs with clickable links, removing any immediately following closing tags
        const contentWithLinks = content.replace(urlRegex, function (url) {
            const nextChar = content[content.indexOf(url) + url.length];
            const isClosingTag = nextChar && nextChar === '>';
            const cleanedUrl = isClosingTag ? url.slice(0, -1) : url;
            return `<a href="${cleanedUrl}" target="_blank">${cleanedUrl}</a>`;
        });
    
        // Replace hashtags with clickable links
        const contentWithHashtags = contentWithLinks.replace(hashtagRegex, function (hashtag) {
            const hashtagUrlWithParam = `${hashtagUrl}?hashtag=${encodeURIComponent(hashtag.substring(1))}`;
            return `<a href="${hashtagUrlWithParam}" target="_blank">${hashtag}</a>`;
        });
    
        // Replace @user with clickable links
        const contentWithUsers = contentWithHashtags.replace(userRegex, function (user) {
            const username = user.substring(1); // Remove "@" symbol
            const userUrlWithUsername = `${userUrl}${username}`;
            return `<a href="${userUrlWithUsername}" target="_blank">${user}</a>`;
        });
    
        return contentWithUsers;
    }

    // Public method that can be accessed from outside the class
    // Extract the first link from the editor content
    extractFirstLink(content) {
        if (typeof content !== 'string') {
            // Handle cases where content is not a string
            return null;
        }
    
        // Split the content by lines
        const lines = content.split('\n');
    
        // Iterate through each line to find the first URL
        for (const line of lines) {
            // Use regular expressions to extract URLs from the line
            const urlRegex = /https?:\/\/[^\s/$.?#].[^\s]*/;
            const match = line.match(urlRegex);
    
            // If a URL is found on a line by itself, return it
            if (match) {
                return match[0];
            }
        }
    
        // If no URL is found on a line by itself, return null
        return null;
    }        
    

    determineContentType(url) {
        if (url.endsWith('.jpg') || url.endsWith('.png') || url.endsWith('.gif')) {
            return 'image';
        } else if (url.endsWith('.mp4') || url.endsWith('.avi')) {
            return 'video';
        }  else if ( url.includes('youtube.com') || url.includes('youtu.be') ) {
            return 'youtube'; // YouTube video URL   
        } else if (url.includes('vimeo.com')) {
            return 'vimeo'; // Vimeo video URL
        } else {
            // Assume it's an embeddable video (e.g., other sources)
            return 'embed';
        }
    }
    
    // Generate the rich preview HTML based on content type
    generateRichPreview(contentType, url) {
        var richPreviewHTML = '';
        switch (contentType) {
            case 'image':
                richPreviewHTML = '<img src="' + url + '" alt="Image Preview"';
                break;
            case 'video':
                richPreviewHTML = '<video controls><source src="' + url + '" type="video/mp4"></video>';
                break;
            case 'embed':
                // Example: Embed an iframe for other sources
                richPreviewHTML = '<iframe src="' + url + '" frameborder="0" allowfullscreen></iframe>';
                break;
            case 'youtube':
                // Extract the YouTube video ID from the URL
                var videoId = this.extractYouTubeVideoId(url);
                if (videoId) {
                    // Create an embedded YouTube video player
                    richPreviewHTML = '<iframe width="560" height="315" src="https://www.youtube.com/embed/' + videoId + '" frameborder="0" allowfullscreen></iframe>';
                } else {
                    // Handle invalid YouTube URL
                    richPreviewHTML = 'Invalid YouTube URL';
                }
                break;
            case 'vimeo':
                // Extract the Vimeo video ID from the URL
                var vimeoId = this.extractVimeoVideoId(url);
                if (vimeoId) {
                    // Create an embedded Vimeo video player
                    richPreviewHTML = '<iframe src="https://player.vimeo.com/video/' + vimeoId + '" frameborder="0" allowfullscreen></iframe>';
                } else {
                    // Handle invalid Vimeo URL
                    richPreviewHTML = 'Invalid Vimeo URL';
                }
                break;
            default:
                // For 'regular' content type, display a simple link
                richPreviewHTML = '<a href="' + url + '" target="_blank">' + url + '</a>';
                break;
        }
    
        return '<div class="userwall-wp-embed">' + richPreviewHTML + '</div>';
    }

    // Extract the Vimeo video ID from the URL
    extractVimeoVideoId(url) {
        var videoId = null;
        var vimeoRegex = /(?:vimeo\.com\/|\/video\/)([0-9]+)/i;
        var match = url.match(vimeoRegex);
        if (match && match[1]) {
            videoId = match[1];
        }
        return videoId;
    }

    // Extract the YouTube video ID from the URL
    extractYouTubeVideoId(url) {
        var videoId = null;
        var youtubeRegex = /(?:youtube\.com\/(?:[^/]+\/.+\/|(?:v|e(?:mbed)?|watch)\?.*v=|.*?\/)?)([a-zA-Z0-9_-]{11})/i;
        var match = url.match(youtubeRegex);
        if (match && match[1]) {
            videoId = match[1];
        }
        return videoId;
    }
  }
  
// Create an instance of the class
window.UserWallWPHelper = new UserWallWPHelper();

function transformPosts( userwall ) {
    return userwall
    .filter(function (thread) {
      return thread.post_id !== ''; // Only keep userwall with non-empty post_id
    })
    .map(function (thread) {
        // Apply the doEmbed method to the post_content
        thread.post_content = window.UserWallWPHelper.doEmbed(thread.post_content);
        return wp.hooks.applyFilters('thread_wp_content_filter', thread);
    });
}

function transformComments( comments ) {
    return comments.map(function (comment) {
        // Apply the doEmbed method to the comment_content
        comment.comment_content = window.UserWallWPHelper.doEmbed(comment.comment_content);
        return wp.hooks.applyFilters('thread_wp_content_comment_filter', comment );
    });
}
function renderPosts(userwall, position = 'top' ) {
    var template_id = 'userwall-wp-feed-template';
    var template = wp.template(template_id);
    var newPosts = transformPosts( userwall );

    var html, renderedHtml;
    jQuery.each(newPosts, function (i, thread) {
        html = template({ thread });
        renderedHtml = jQuery('<div class="tempWrap">' + html + '</div>');
        const quillContainerId = `quill-comment-editor-edit-${thread.post_id}`;
        const quillContainer = renderedHtml.find('#' + quillContainerId)[0];

        // Check if the container exists (only if the thread is loaded via Ajax)
        if (quillContainer) {
            // Initialize Quill editor
            const quill = new Quill(quillContainer, {
                theme: editor_theme, // You can use a different theme if needed
                placeholder: userwallWPObject.reply_placeholder,
            });
        }
        if ( ! jQuery('.userwall-wp-thread[data-postid="' + thread.post_id +'"]').length ) {
            if ( position == 'top' ) {
                jQuery('.userwall-wp-reddit-thread').prepend(renderedHtml.html());
            } else {
                jQuery('.userwall-wp-reddit-thread').append(renderedHtml.html());
            }
            wp.hooks.doAction('userwall_wp_post_rendered', thread);
        }
    });
}

function setReadMore() {
    return;
    // Maximum number of characters to show initially
    var maxLength = 300;
    
    // Iterate over each thread content
    jQuery('.userwall-wp-thread-content').each(function() {
        var $content = jQuery(this);
        var contentText = $content.text();
        
        if (contentText.length > maxLength) {
            // Truncate the text
            var truncatedText = contentText.substring(0, maxLength);
            
            // Check if the truncated text ends with an opening HTML tag
            var lastOpeningTagIndex = truncatedText.lastIndexOf('<');
            var lastClosingTagIndex = truncatedText.lastIndexOf('>');
            
            if (lastOpeningTagIndex > lastClosingTagIndex) {
                // Find the corresponding closing tag
                var tagNameStart = lastOpeningTagIndex + 1;
                var tagNameEnd = truncatedText.indexOf(' ', tagNameStart);
                if (tagNameEnd === -1) {
                    tagNameEnd = truncatedText.indexOf('>', tagNameStart);
                }
                var tagName = truncatedText.substring(tagNameStart, tagNameEnd);
                
                // Create the truncated content by adding the closing tag
                truncatedText = contentText.substring(0, lastOpeningTagIndex) +
                    '</' + tagName + '>';
            }
            
            // Create the "Read More" link
            var readMoreLink = jQuery('<a href="#" class="read-more">Read More</a>');
            
            // Hide the content and add the "Read More" link
            $content.html(truncatedText + '... ').append(readMoreLink);
            
            // Hide or show the full content when the link is clicked
            readMoreLink.on('click', function(e) {
                e.preventDefault();
                $content.toggleClass('expanded');
                if ($content.hasClass('expanded')) {
                    $content.html(contentText + ' ');
                    readMoreLink.text('Read Less');
                } else {
                    $content.html(truncatedText + '... ');
                    readMoreLink.text('Read More');
                }
            });
        }
    });
}
function updateTime() {
    jQuery('.userwall-wp-wall-time[data-time-post]').each(function() {
        var postTime = jQuery(this).data('time-post') * 1000; // Assuming the timestamp is in seconds
        var currentTime = new Date().getTime();
        var diff = currentTime - postTime;

        var seconds = Math.floor(diff / 1000);
        var minutes = Math.floor(seconds / 60);
        var hours = Math.floor(minutes / 60);
        var days = Math.floor(hours / 24);

        var formattedTime;

        if (days >= 1) {
            formattedTime = new Date(postTime).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
        } else if (hours >= 1) {
            formattedTime = hours + ' hr' + (hours > 1 ? 's' : '') + ' ago';
        } else if (minutes >= 1) {
            formattedTime = minutes + ' min' + (minutes > 1 ? 's' : '') + ' ago';
        } else {
            formattedTime = seconds + ' sec' + (seconds !== 1 ? 's' : '') + ' ago';
        }

        jQuery(this).html(formattedTime);
    });
}

jQuery(document).ready(function($) {
    wp.hooks.addAction('userwall_wp_post_rendered', 'renderEditor', function(thread) {
        updateTime();
        setReadMore();
    });
    wp.hooks.addAction('userwall_wp_comment_rendered', 'renderEditor', function(thread) {
        updateTime();
    });
    
    class UserWallWPPlugin {
        constructor() {
            this.previousUrl = null;
            this.loading = false;
            this.threadTmpl = 'userwall-wp-feed-template';
            // Initialize the class
            this.initialize();
            this.initializeTabs();
            //this.initializeReplyForm();
            // Initialize Quill rich text editor for the post form
            this.initializeQuillEditor();
            this.initializeLiveUpdates();
        }

        initialize() {
            var classObj = this;
        
            // Update time every minute
            updateTime();
            setInterval(updateTime, 60000);

            // Attach click event to the form submit button
            jQuery('#userwall-wp-post-form').on('submit', this.submitForm.bind(this));
    
            // Attach click event to the ellipsis icon
            jQuery(document).on('click', '.userwall-wp-ellipsis', this.handleEllipsisClick );
    
            // Add a click event to the document body to close actions when clicking outside
            jQuery('body').click(this.closeActions);
    
            // Prevent clicks within the actions area from closing it
            jQuery('.userwall-wp-thread-actions').click(function(event) {
                event.stopPropagation();
            });

            jQuery( document ).on( 'click', '.userwall-wp-post-type', function( e ) {
                e.preventDefault();
                var obj = jQuery( this );
                var type = obj.data( 'type' );
                switch( type ) {
                    case 'image':
                        jQuery('#image-upload').trigger( 'click' );
                        break;
                }
            });
            // Attach click events to actions
            jQuery(document).on( 'click', '.userwall-wp-action', this.handleActionClick.bind(this));

            jQuery('[data-thread]').each(function () {
                const $div = jQuery(this);

                // Fetch and render posts
                classObj.fetchAndRenderPosts($div);
            });

            jQuery(document).on('click', '.comment-submit-button', function() {
                var $thread = jQuery(this).closest('.userwall-wp-thread');
                const postID = $thread.data('postid');
                const quill = new Quill(`#quill-comment-editor-edit-${postID}`);
                const content = quill.root.innerHTML; // Get Quill editor content
                // Perform an AJAX request to save the changes
                $.ajax({
                    url: userwallWPObject.ajax_url,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: 'userwall_wp_post_comment', // Create this AJAX action in your main plugin file
                        post_id: postID,
                        content: content,
                        nonce: userwallWPObject.nonce
                    },
                    success: function(response) {
                        var template = wp.template('reddit-style-thread-comment-template');
                        var newPosts = transformComments(response.data.comments);
                        var html = template(newPosts);;
                        $thread.find('.userwall-wp-comment-section').prepend(html);
                        // Clear the comment input field
                        quill.root.innerHTML = '';
                    },
                    error: function(error) {
                        console.error('Error saving changes:', error);
                    }
                });
                
            });

            jQuery(document).on('click', '.userwall-wp-comment-count', function() {
                const thread = jQuery(this).closest('.userwall-wp-thread');
                // Get the current post ID from data attribute
                const postID = parseInt( thread.data('postid') );
                
                if ( thread.find('.userwall-wp-comment-section').is(':empty')) {
                   // Perform AJAX request to load comments userwall
                    $.ajax({
                        url: userwallWPObject.ajax_url, // Replace with your AJAX endpoint URL
                        type: 'GET',
                        data: {
                            action: 'userwall_wp_load_comments', // Create this AJAX action in your PHP code
                            post_id: postID, // Send the post ID to the server
                            nonce: userwallWPObject.nonce, // Add nonce for security (make sure to localize this in your main PHP file)
                        },
                        success: function(response) {
                            if ( response.data.comments.length && response.data.comments.length > 0 ) {
                                var template = wp.template('reddit-style-thread-comment-template');
                                var newPosts = transformComments( response.data.comments );
                                jQuery.each( newPosts, function( index, comment ) {
                                    var template = wp.template('reddit-style-thread-comment-template');

                                    var commentData = comment;

                                    // Render the template with the data
                                    var renderedHtml = template(commentData);
                                    
                                    // Convert the HTML string to a jQuery object
                                    var commentHtml = jQuery('<div class="tempWrapper">' + renderedHtml + '</div>' );

                                    var childComments = commentData[0].child_comments;
                                    if ( childComments.length > 0 ) {
                                        template = wp.template('reddit-style-thread-comment-template');
                                        var innerComments = transformComments(childComments);
     
                                        jQuery.each( innerComments, function( index2, inner_comment ) {
                                            var ReplyTemplate = wp.template('reddit-style-thread-comment-template');
                                            var child_html = ReplyTemplate({inner_comment});
                                            commentHtml.find('.userwall-wp-comment-reply-section').append(child_html);
                                        });
                                    }

                                    thread.find('.userwall-wp-comment-section').prepend(commentHtml.html());
                                    wp.hooks.doAction('userwall_wp_comment_rendered', comment);
                                });
                                wp.hooks.doAction('userwall_wp_comment_all_rendered', response.data.comments );
                            }
                        },
                        error: function(error) {
                            // Handle errors here (e.g., display an error message)
                            console.error('Error loading comments userwall:', error);
                        },
                    });
                } else {
                    thread.find('.userwall-wp-comment-section').toggle();
                }
            });

            jQuery( document ).on('click', '.userwall-wp-new-userwall', function( e ) {
                e.preventDefault();
                var $div = jQuery(this).closest('[data-thread]');
                const postType = $div.data('post_type');
                const perPage = $div.data('per_page');

                // Initialize variables to keep track of the highest post ID and the corresponding element
                var highestPostId = -1;
                var $elementWithHighestId = null;
                var $userwall = $div.find('.userwall-wp-thread'); 
                // Iterate through each element to find the highest post ID
                $userwall.each(function() {
                    var postId = parseInt(jQuery(this).data('postid'));
                    
                    // Check if the current post ID is higher than the highest found so far
                    if (postId > highestPostId) {
                        highestPostId = postId;
                        $elementWithHighestId = jQuery(this);
                    }
                });

                jQuery.ajax({
                    url: userwallWPObject.ajax_url, // Replace with your AJAX endpoint URL
                    type: 'POST',
                    dataType: 'json', // Adjust the data type based on your server response
                    data: {
                        action: 'fetch_latest_thread', // Create this AJAX action in your main plugin file
                        post_type: postType,
                        per_page: perPage,
                        nonce: userwallWPObject.nonce,
                        post_id: highestPostId,
                    },
                    success: function (response) {
                        jQuery('.userwall-wp-new-userwall').remove();
                        renderPosts( response.data.userwall );
                    },
                    error: function (error) {
                        console.error('Error fetching data:', error);
                    },
                });
            });
            
            jQuery('#image-upload').change(function() {
                // Clear existing image previews
                jQuery('.image-preview').remove();
                jQuery( '.image-upload-area' ).show();
                // Get selected files
                const files = jQuery(this)[0].files;
                if (files.length > 0) {
                    // Loop through selected files
                    for (let i = 0; i < files.length; i++) {
                        const file = files[i];
                        const reader = new FileReader();
        
                        reader.onload = function(e) {
                            // Create image preview
                            const imagePreview = jQuery('<div class="image-preview"></div>');
                            imagePreview.append(`<img src="${e.target.result}" alt="Image Preview">`);
                            imagePreview.append('<span class="remove-image">Remove</span>');
        
                            // Append image preview to upload area
                            jQuery('.image-upload-area').append(imagePreview);
                        };
        
                        // Read the file as a data URL
                        reader.readAsDataURL(file);
                    }
                }
            });
        
            // Handle image removal from previews
            jQuery('.image-upload-area').on('click', '.remove-image', function() {
                jQuery(this).closest('.image-preview').remove();
                // Clear the corresponding file input
                jQuery('#image-upload').val('');
            });
        
            // Handle form submission with AJAX
            jQuery('#userwall-wp-post-form2').submit(function(event) {
                event.preventDefault();
        
                // Get form data including images
                const formData = new FormData(this);
                formData.append('action', sessionID);

                // Perform AJAX request for form submission
                $.ajax({
                    url: userwallWPObject.ajax_url, // Replace with your AJAX endpoint URL
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        // Handle the AJAX response here
                        console.log('Form submitted successfully');
                    },
                    error: function(error) {
                        // Handle AJAX error here
                        console.error('Error submitting form:', error);
                    }
                });
            });
            var that = this;
            jQuery('[data-thread]').each(function () {
                var $threadContainer = jQuery(this);
                jQuery(window).scroll(function() {
                    if (jQuery(window).scrollTop() + jQuery(window).height() >= $threadContainer.height() - 100) {
                        //that.loadMorePosts( $threadContainer );
                    }
                });
            });

            $(document).on('click', '.userwall-wp-action[data-action="Edit"]', function() {
                const isComment = $(this).closest('.userwall-wp-comment').length ? true : false;
                const $commentDiv = isComment ? $(this).closest('.userwall-wp-comment') : '';
                const $postDiv = $(this).closest('.userwall-wp-thread');
                const $content = isComment ? $commentDiv.find('.userwall-wp-comment-content') : $postDiv.find('.userwall-wp-thread-content');
                const $editForm = isComment ? $commentDiv.find('.comment-thread-edit-form') : $postDiv.find('.edit-form');
        
                $content.hide();
                $editForm.show();
                
                if ( isComment ) {
                     // Initialize Quill editor for editing
                    let quillEditor = new Quill(`#quill-editor-edit-${$postDiv.data('postid')}-${$commentDiv.data('commentid')}`, {
                        theme: editor_theme
                    });
                    // Populate Quill editor with existing content
                    const existingContent = $content.html();
                    quillEditor.root.innerHTML = existingContent;
                } else {
                     // Initialize Quill editor for editing
                    let quillEditor = new Quill(`#quill-editor-edit-${$postDiv.data('postid')}`, {
                        theme: editor_theme
                        // ... (Quill editor configurations)
                    });
                    // Populate Quill editor with existing content
                    const existingContent = $content.html();
                    quillEditor.root.innerHTML = existingContent;
                }
            });
        
            // Function to cancel editing
            $(document).on('click', '.cancel-button', function() {
                const isComment = $(this).closest('.userwall-wp-comment').length ? true : false;
                const $commentDiv = isComment ? $(this).closest('.userwall-wp-comment') : '';
                const $postDiv = $(this).closest('.userwall-wp-thread');
                const $content = isComment ? $commentDiv.find('.userwall-wp-comment-content') : $postDiv.find('.userwall-wp-thread-content');
                const $editForm = isComment ? $commentDiv.find('.comment-thread-edit-form') : $postDiv.find('.edit-form');
        
                $editForm.hide();
                $content.show();
            });
            
            // Function to save changes via AJAX
            $(document).on('click','.save-button', function() {
                const isComment = $(this).closest('.userwall-wp-comment').length ? true : false;
                const $commentDiv = isComment ? $(this).closest('.userwall-wp-comment') : '';
                const $postDiv = $(this).closest('.userwall-wp-thread');
                var content = '';
                const $content = isComment ? $commentDiv.find('.userwall-wp-comment-content') : $postDiv.find('.userwall-wp-thread-content');
                const $editForm = isComment ? $commentDiv.find('.comment-thread-edit-form') : $postDiv.find('.edit-form');
                var commentID = isComment ? $commentDiv.data('commentid') : 0;
                var postID = $postDiv.data('postid');

                if ( isComment ) {
                    const quill = new Quill(`#quill-editor-edit-${$postDiv.data('postid')}-${$commentDiv.data('commentid')}`);
                    content = quill.root.innerHTML; // Get Quill editor content
                } else {
                    const quill = new Quill(`#quill-editor-edit-${postID}`);
                    content = quill.root.innerHTML; // Get Quill editor content
                }

                // Perform an AJAX request to save the changes
                $.ajax({
                    url: userwallWPObject.ajax_url,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: isComment ? 'userwall_wp_update_comment' : 'userwall_wp_update_post', // Create this AJAX action in your main plugin file
                        post_id: postID,
                        comment_id: commentID,
                        content: content,
                        nonce: userwallWPObject.nonce
                    },
                    success: function(response) {
                        if ( isComment ) {
                            // Handle success, e.g., update the post content display
                            const $content = $commentDiv.find('.userwall-wp-comment-content');
                            var template = wp.template('reddit-style-thread-comment-template');
                            var newPosts = transformComments(response.data.comments);

                            var html = template(newPosts);
                            $commentDiv.replaceWith(html);
                        } else {
                            // Handle success, e.g., update the post content display
                            const $content = $postDiv;
                            var template = wp.template('userwall-wp-feed-template');
                            var newPosts = transformPosts(response.data.userwall);

                            var html = template(newPosts);
                            $content.replaceWith(html);
                            // Hide the edit form
                            $editForm.hide();
                            $content.show();

                            const quillContainerId = `quill-comment-editor-edit-${postID}`;
                            
                            const quillContainer = document.getElementById(quillContainerId);

                            // Check if the container exists (only if the thread is loaded via Ajax)
                            if (quillContainer) {
                                // Initialize Quill editor
                                const quill = new Quill(quillContainer, {
                                    theme: editor_theme, // You can use a different theme if needed
                                    placeholder: 'Edit your thread here...',
                                });
                            }
                            
                        }
                    },
                    error: function(error) {
                        console.error('Error saving changes:', error);
                    }
                });
            });

            $(document).on('click', '.userwall-wp-reply-button', function () {
                // Find the closest comment container
                var commentContainer = $(this).closest('.userwall-wp-comment');
                
                var commentId = commentContainer.data('commentid');

                var postContainer = $(this).closest('.userwall-wp-thread');
                var postId =  postContainer.data('postid');

                // Get the dynamic ID for the Quill editor
                var quillEditorId = commentContainer.find('[data-comment-reply]').attr('data-comment-reply');
        
                // Toggle the reply form visibility
                commentContainer.find('.userwall-wp-reply-form').toggle();
                
                // Initialize Quill editor inside the reply form using the dynamic ID
                var quill = new Quill('#' + quillEditorId, {
                    theme: editor_theme, // You can customize the Quill editor's theme
                });
                
                // Handle submit button click
                commentContainer.find('.userwall-wp-reply-submit').on('click', function () {
                    var replyContent = quill.root.innerHTML;
               
                    // Perform an AJAX request to submit the comment
                    $.ajax({
                        type: 'POST',
                        url: userwallWPObject.ajax_url,
                        data: {
                            action: 'thread_wp_comment_reply', // AJAX action hook
                            post_id: postId,
                            commentId: commentId,
                            commentContent: replyContent,
                            nonce: userwallWPObject.nonce, // Nonce value (localized in your template)
                        },
                        success: function (response) {
                            // Handle the AJAX response here (update the comment section, etc.)
                            if (response.success) {
                                // Update your comment section with response.data
                                var template = wp.template('reddit-style-thread-comment-template');
                                var newComments = transformComments(response.data.comments);
                                var html = template(newComments);
                                var commentHtml = jQuery( '<div class="tempWrap">' + html + '</div>' );
                                commentHtml.find('.userwall-wp-reply').remove();
                                commentHtml.find('.userwall-wp-comment-reply-section').remove();
                                commentContainer.find('.userwall-wp-comment-reply-section').prepend(commentHtml.html());
                                // Clear the comment input field
                                quill.root.innerHTML = '';
                                wp.hooks.doAction('userwall_wp_comment_rendered', newComments );
                            } else {
                                console.error('Error:', response.data);
                            }
                        },
                        error: function (error) {
                            console.error('Error:', error);
                        },
                    });
                });
            });
        }

        fetchAndRenderPosts($div) {
            const postType = $div.data('post_type');
            const perPage = $div.data('per_page');
            const page = $div.data('page');
            const objectId = $div.data('object_id');
            // Perform an AJAX request to fetch data based on the attributes
            jQuery.ajax({
                url: userwallWPObject.ajax_url, // Replace with your AJAX endpoint URL
                type: 'GET',
                dataType: 'json', // Adjust the data type based on your server response
                data: {
                    action: 'fetch_data_by_thread', // Create this AJAX action in your main plugin file
                    post_type: postType,
                    per_page: perPage,
                    page: page,
                    object_id: objectId,
                    nonce: userwallWPObject.nonce,
                },
                success: function (response) {
                    // Handle the response here (e.g., render the fetched data)
                    renderPosts( response.userwall, 'bottom' );
                },
                error: function (error) {
                    console.error('Error fetching data:', error);
                },
            });
        }

        loadMorePosts( $container ) {
            if (this.loading) {
                return;
            }
            this.loading = true;
            var $loader = $container.find('.loading-indicator');
            $loader.show();
            
            const postType = $container.data('post_type');
            const perPage = $container.data('per_page');

            $.ajax({
                url: userwallWPObject.ajax_url, // Replace with your AJAX endpoint URL
                type: 'POST',
                data: {
                    action: 'userwall_wp_load_more_posts', // Create this AJAX action in your main plugin file
                    per_page: perPage,
                },
                success: function(response) {
                    if (response.success) {
                        if (response.data.posts.length > 0) {
                            var template = wp.template( 'userwall-wp-feed-template' );
                            var newPosts = response.data.userwall( response.data.userwall );
                            
                            var html = template( newPosts );
                            jQuery('.userwall-wp-new-userwall').remove();
                            $container.find('.userwall-wp-reddit-thread').append( html );
                            page++;
                        } else {
                            // No more posts to load
                            $loader.text('No more posts to load').show();
                        }
                    } else {
                        // Handle AJAX error
                        console.error('Error loading more posts:', response.data.message);
                    }
    
                    this.loading = false;
                    $loader.hide();
                },
                error: function(error) {
                    // Handle AJAX error
                    console.error('Error loading more posts:', error);
                    loading = false;
                    $loader.hide();
                },
            });

            
        }
        
        initializeTabs() {
            // Initialize the first tab as active
            jQuery('.userwall-tab:first-child').addClass('active');
            jQuery('.userwall-tab-content:first-child').addClass('active');
    
            // Switch tabs when clicked
            jQuery('.userwall-tab').click(function() {
                jQuery('.userwall-tab').removeClass('active');
                jQuery('.userwall-tab-content').removeClass('active');
                var tab = jQuery(this).data('tab');
                jQuery(this).addClass('active');
                jQuery('.userwall-tab-content[data-tab="' + tab + '"]').addClass('active');
            });
        }
    
        submitForm(event) {
            event.preventDefault();
    
            // Get form data
            //const formData = jQuery('#userwall-wp-post-form').serialize();
            
            // Get the HTML content from the Quill editor
            const quill = new Quill('.post-quill-editor'); // Initialize Quill (if not already done)
            const content = quill.root.innerHTML;

            var tab = jQuery('.userwall-tab.active').data('tab');
            var form = jQuery('#userwall-wp-post-form')[0];

            const formData = new FormData(form);
            if ( tab == 'image' ) {
                formData.append("content", jQuery('#userwall-wp-post-form').find('[name="image_content"]').val() );
            } else {
                formData.append('content', content );
            }

            formData.append( 'action', 'userwall_wp_save_post' );
            formData.append( 'nonce', userwallWPObject.nonce );
            formData.append( 'post_tab', tab );
            
            // Perform AJAX request to save the post
            jQuery.ajax({
                url: userwallWPObject.ajax_url, // Define the AJAX URL (Make sure to localize this in your main plugin file)
                type: 'POST',
                enctype: 'multipart/form-data',
                processData: false,  // Important!
                contentType: false,
                cache: false,
                data: formData,
                success: function(response) {
                    renderPosts( response.data.userwall );
                    // Reset post form.
                    quill.root.innerHTML = '';

                    wp.hooks.doAction('userwall_wp_after_post_submitted', response, formData );
                },
                error: function(error) {
                    // Handle errors here (e.g., display an error message)
                },
            });
        }
    
        handleEllipsisClick() {
            // Find the parent .userwall-wp-thread
            
            var thread = jQuery(this).closest('.userwall-wp-comment');
            if ( thread.length == 0 ) {
                thread = jQuery(this).closest('.userwall-wp-thread');
            }
            // Find .userwall-wp-thread-actions within the thread
            const actionsArea = thread.find('.userwall-wp-thread-actions');

            // Toggle the visibility of the actions area
            actionsArea.toggle();
    
            // Prevent the click event from propagating to the document body
            event.stopPropagation();
        }
    
        closeActions() {
            // Hide all .userwall-wp-thread-actions elements
            jQuery('.userwall-wp-thread-actions').hide();
        }

        handleActionClick(event) {
            var $thread = jQuery(event.target).closest('.userwall-wp-thread');
            const actionType = jQuery(event.target).data('action');
            if ( actionType == 'Edit' ) {
                return;
            }
            var data = {
                action: 'userwall_wp_posts_action',
                action_type: actionType, // The action type you want to perform
                post_id: $thread.data('postid'), // The post ID associated with the action
                nonce: userwallWPObject.nonce 
            };
    
            // Perform the AJAX request
            jQuery.ajax({
                type: 'POST',
                url: userwallWPObject.ajax_url, // Define the AJAX URL (Make sure to localize this in your main plugin file)
                data: data,
                success: function(response) {
                    // Handle the AJAX response here based on the actionType
                    switch (actionType) {
                        case 'Delete':
                        case 'Block':
                        case 'Report':
                            // Handle Delete action response
                            $thread.remove();
                            break;
                        case 'Embed-post':
                            // Handle Embed Post action response
                            break;
                        case 'Save':
                            // Handle Save action response
                            break;
                        case 'Follow':
                            // Handle Follow action response
                            break;
                        default:
                            // Handle unknown action type
                            break;
                    }
                },
                error: function(error) {
                    // Handle AJAX error here
                }
            });
        }

        initializeReplyForm() {
            // Initialize Quill rich text editor
            const quill = new Quill('[data-comment-reply]', {
              theme: editor_theme,
              modules: {
                toolbar: [
                  ['bold', 'italic', 'strike'], // Text formatting options
                  ['image', 'code-block', 'blockquote'], // Add photos, code block, and quote
                ],
              },
            });
      
            // Show/hide reply form when clicking the Reply button
            jQuery(document).on('click', '.userwall-wp-reply-button', function() {
              const replyForm = jQuery(this).siblings('.userwall-wp-reply-form');
              replyForm.toggle();
            });
      
            // Handle form submission
            jQuery(document).on( 'click', '.userwall-wp-reply-submit2', function() {
              // Get the HTML content from Quill editor
              const replyContent = quill.root.innerHTML;
      
              // Perform AJAX request to save the reply
              jQuery.ajax({
                url: userwallWPObject.ajax_url, // Define the AJAX URL
                type: 'POST',
                data: {
                  action: 'userwall_wp_save_reply', // Create this AJAX action in your main plugin file
                  reply_content: replyContent,
                  nonce: userwallWPObject.nonce,
                },
                success: function(response) {
                  // Handle the response here (e.g., display a success message)
                },
                error: function(error) {
                  // Handle errors here (e.g., display an error message)
                },
              });
            });
      
            // Cancel reply form
            jQuery(document).on('click', '.userwall-wp-reply-cancel', function() {
              const replyForm = jQuery(this).parents('.userwall-wp-reply-form');
              replyForm.hide();
            });
        }

        initializeQuillEditor() {
            const that = this;
            if ( ! jQuery('.post-quill-editor' ).length ) {
                return;
            }
            const quillPostEditor = new Quill('.post-quill-editor', {
              theme: editor_theme,
              modules: {
                toolbar: '#userwall-wp-post-toolbar'
              }
            });

            quillPostEditor.on('text-change', function(delta, oldDelta, source) {
                // Extract the link URL from the editor content
                var content = quillPostEditor.getText();
                var url = that.extractFirstLink(content);
                 
                if (url !== that.previousUrl) {
                    // Only update the rich preview if the URL has changed
                    that.previousUrl = url;

                    if (url) {
                        // Determine the content type of the URL (e.g., regular, image, video, embed)
                        var contentType = that.determineContentType(url);

                        // Generate the rich preview HTML based on content type
                        var richPreviewHTML = that.generateRichPreview(contentType, url);

                        // Display the rich preview in the container
                        jQuery('.rich-preview-container').html(richPreviewHTML);
                    } else {
                        // Clear the rich preview if no URL is found
                        jQuery('.rich-preview-container').html('');
                    }
                }
            });

            jQuery('.post-quill-editor').UserWallWPMention({
                editor: quillPostEditor
            });
        }

        initializeLiveUpdates() {
            // Set up a timer to fetch and update data periodically
            const updateInterval = 30000; // 30 seconds in milliseconds
            let isTabActive = true; // Flag to track tab visibility
        
            // Function to check tab visibility
            const checkTabVisibility = () => {
                if (document.hidden) {
                    // Tab is not visible, user switched to another tab or minimized the browser
                    isTabActive = false;
                } else {
                    // Tab is visible, user returned to it
                    isTabActive = true;
                }
            };
        
            // Attach visibility change event listener
            document.addEventListener('visibilitychange', checkTabVisibility);
        
            // Function to fetch and update data
            const fetchAndUpdateData = () => {
                if (isTabActive) {
                    // Only fetch and update data when the tab is active
                    this.fetchAndUpdateData();
                }
            };
        
            // Initial call to check tab visibility
            checkTabVisibility();
        
            // Start the interval timer
            setInterval(fetchAndUpdateData, updateInterval);
        }        
    
        fetchAndUpdateData() {
            jQuery('[data-thread]').each(function () {
                // Get the data attributes from the current div element
                const $div = jQuery(this);
                const postType = $div.data('post_type');
                const perPage = $div.data('per_page');

                // Initialize variables to keep track of the highest post ID and the corresponding element
                var highestPostId = -1;
                var $elementWithHighestId = null;
                var $userwall = $div.find('.userwall-wp-thread'); 
                // Iterate through each element to find the highest post ID
                $userwall.each(function() {
                    var postId = parseInt(jQuery(this).data('postid'));
                    
                    // Check if the current post ID is higher than the highest found so far
                    if (postId > highestPostId) {
                        highestPostId = postId;
                        $elementWithHighestId = jQuery(this);
                    }
                });
                // Use jQuery's AJAX method to make a GET request to your server
                jQuery.ajax({
                    url: userwallWPObject.ajax_url, // Replace with your AJAX endpoint URL
                    type: 'GET',
                    dataType: 'json', // Adjust the data type based on your server response
                    data: {
                        action: 'fetch_latest_thread_notice', // Create this AJAX action in your main plugin file
                        post_type: postType,
                        per_page: perPage,
                        nonce: userwallWPObject.nonce,
                        post_id: highestPostId,
                    },
                    success: (response) => {
                        var message = response.data.message;
                        
                        if ( response.data.message > 0 ) {
                            // Update your template with the new data
                            if ( ! $div.find('.userwall-wp-new-userwall').length ) {
                                $div.prepend('<div class="userwall-wp-new-userwall">New ' + message + ' posts</div>');
                            } else {
                                $div.find('.userwall-wp-new-userwall').html('New ' + message+ ' posts' );
                            }
                            wp.hooks.doAction('userwall_wp_new_posts_available', response );
                        }
                    },
                    error: (error) => {
                        console.error('Error fetching data:', error);
                    },
                });
            });
        }
    
        // Extract the first link from the editor content
        extractFirstLink(content) {

            // Split the content by lines
            const lines = content.split('\n');
        
            // Iterate through each line to find the first URL
            for (const line of lines) {
                // Use regular expressions to extract URLs from the line
                const urlRegex = /https?:\/\/[^\s/$.?#].[^\s]*/;
                const match = line.match(urlRegex);
                
                // If a URL is found on a line by itself, return it
                if (match) {
                    return match[0];
                }
            }
        
            // If no URL is found on a line by itself, return null
            return null;
        }        
        
    
        determineContentType(url) {
            if (url.endsWith('.jpg') || url.endsWith('.png') || url.endsWith('.gif')) {
                return 'image';
            } else if (url.endsWith('.mp4') || url.endsWith('.avi')) {
                return 'video';
            }  else if ( url.includes('youtube.com') || url.includes('youtu.be') ) {
                return 'youtube'; // YouTube video URL   
            } else if (url.includes('vimeo.com')) {
                return 'vimeo'; // Vimeo video URL
            } else {
                // Assume it's an embeddable video (e.g., other sources)
                return 'embed';
            }
        }
        
        // Generate the rich preview HTML based on content type
        generateRichPreview(contentType, url) {
            var richPreviewHTML = '';
            switch (contentType) {
                case 'image':
                    richPreviewHTML = '<img src="' + url + '" alt="Image Preview">';
                    break;
                case 'video':
                    richPreviewHTML = '<video controls><source src="' + url + '" type="video/mp4"></video>';
                    break;
                case 'embed':
                    // Example: Embed an iframe for other sources
                    richPreviewHTML = '<iframe src="' + url + '" frameborder="0" allowfullscreen></iframe>';
                    break;
                case 'youtube':
                    // Extract the YouTube video ID from the URL
                    var videoId = this.extractYouTubeVideoId(url);
                    if (videoId) {
                        // Create an embedded YouTube video player
                        richPreviewHTML = '<iframe width="560" height="315" src="https://www.youtube.com/embed/' + videoId + '" frameborder="0" allowfullscreen></iframe>';
                    } else {
                        // Handle invalid YouTube URL
                        richPreviewHTML = 'Invalid YouTube URL';
                    }
                    break;
                case 'vimeo':
                    // Extract the Vimeo video ID from the URL
                    var vimeoId = this.extractVimeoVideoId(url);
                    if (vimeoId) {
                        // Create an embedded Vimeo video player
                        richPreviewHTML = '<iframe src="https://player.vimeo.com/video/' + vimeoId + '" frameborder="0" allowfullscreen></iframe>';
                    } else {
                        // Handle invalid Vimeo URL
                        richPreviewHTML = 'Invalid Vimeo URL';
                    }
                    break;
                default:
                    // For 'regular' content type, display a simple link
                    richPreviewHTML = '<a href="' + url + '" target="_blank">' + url + '</a>';
                    break;
            }
        
            return richPreviewHTML;
        }

        // Extract the Vimeo video ID from the URL
        extractVimeoVideoId(url) {
            var videoId = null;
            var vimeoRegex = /(?:vimeo\.com\/|\/video\/)([0-9]+)/i;
            var match = url.match(vimeoRegex);
            if (match && match[1]) {
                videoId = match[1];
            }
            return videoId;
        }

        // Extract the YouTube video ID from the URL
        extractYouTubeVideoId(url) {
            var videoId = null;
            var youtubeRegex = /(?:youtube\.com\/(?:[^/]+\/.+\/|(?:v|e(?:mbed)?|watch)\?.*v=|.*?\/)?)([a-zA-Z0-9_-]{11})/i;
            var match = url.match(youtubeRegex);
            if (match && match[1]) {
                videoId = match[1];
            }
            return videoId;
        }
    }
    
    // Attach the class to the window object so you can access it globally
    window.UserWallWPPlugin = UserWallWPPlugin;
    
    // Create an instance of the UserWallWPPlugin class
    const userwallWP = new UserWallWPPlugin();
    
});
