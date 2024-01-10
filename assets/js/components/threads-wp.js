// Import Quill
import Quill from 'quill';
import ToolbarEmoji from 'quill-emoji';

class ThreadWPHelper {
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
    
        const hashtagUrl = "https://example.com/hashtag-page";
    
        const urlRegex = /https?:\/\/[^\s<>]+/g;
    
        // Regular expression to match hashtags in the content
        const hashtagRegex = /#(\w+)/g;
    
        // Replace URLs with clickable links, removing any immediately following closing tags
        const contentWithLinks = content.replace(urlRegex, function (url) {
            // Check if there is a closing tag immediately after the URL
            const nextChar = content[content.indexOf(url) + url.length];
            const isClosingTag = nextChar && nextChar === '>';
    
            // Remove the closing tag if present
            const cleanedUrl = isClosingTag ? url.slice(0, -1) : url;
    
            return `<a href="${cleanedUrl}" target="_blank">${cleanedUrl}</a>`;
        });
    
        // Replace hashtags with clickable links
        const contentWithHashtags = contentWithLinks.replace(hashtagRegex, function (hashtag) {
            const hashtagUrlWithParam = `${hashtagUrl}?hashtag=${encodeURIComponent(hashtag.substring(1))}`;
            return `<a href="${hashtagUrlWithParam}" target="_blank">${hashtag}</a>`;
        });
    
        return contentWithHashtags;
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
    
        return '<div class="thread-wp-embed">' + richPreviewHTML + '</div>';
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
window.ThreadWPHelper = new ThreadWPHelper();

jQuery(document).ready(function($) {
    class ThreadsWPPlugin {
        constructor() {
            this.previousUrl = null;
            this.loading = false;
            this.threadTmpl = 'thread-wp-feed-template';
            // Initialize the class
            this.initialize();
            this.initializeTabs();
            this.initializeReplyForm();
            // Initialize Quill rich text editor for the post form
            this.initializeQuillEditor();
            this.initializeLiveUpdates();
        }
    
        initialize() {
            var classObj = this;
            // Attach click event to the form submit button
            jQuery('#threads-wp-post-form').on('submit', this.submitForm.bind(this));
    
            // Attach click event to the ellipsis icon
            jQuery(document).on('click', '.threads-wp-ellipsis', this.handleEllipsisClick );
    
            // Add a click event to the document body to close actions when clicking outside
            jQuery('body').click(this.closeActions);
    
            // Prevent clicks within the actions area from closing it
            jQuery('.threads-wp-thread-actions').click(function(event) {
                event.stopPropagation();
            });

            // Attach click events to actions
            jQuery(document).on( 'click', '.threads-wp-action', this.handleActionClick.bind(this));

            jQuery('[data-thread]').each(function () {
                const $div = jQuery(this);

                // Fetch and render posts
                classObj.fetchAndRenderPosts($div);
            });

            jQuery( document ).on('click', '.threads-wp-new-threads', function( e ) {
                e.preventDefault();
                var $div = jQuery(this).closest('[data-thread]');
                const postType = $div.data('post_type');
                const perPage = $div.data('per_page');

                // Initialize variables to keep track of the highest post ID and the corresponding element
                var highestPostId = -1;
                var $elementWithHighestId = null;
                var $threads = $div.find('.threads-wp-thread'); 
                // Iterate through each element to find the highest post ID
                $threads.each(function() {
                    var postId = parseInt(jQuery(this).data('postid'));
                    
                    // Check if the current post ID is higher than the highest found so far
                    if (postId > highestPostId) {
                        highestPostId = postId;
                        $elementWithHighestId = jQuery(this);
                    }
                });

                jQuery.ajax({
                    url: threadsWPObject.ajax_url, // Replace with your AJAX endpoint URL
                    type: 'POST',
                    dataType: 'json', // Adjust the data type based on your server response
                    data: {
                        action: 'fetch_latest_thread', // Create this AJAX action in your main plugin file
                        post_type: postType,
                        per_page: perPage,
                        nonce: threadsWPObject.nonce,
                        post_id: highestPostId,
                    },
                    success: function (response) {
                        var template = wp.template( 'thread-wp-feed-template' );
                        var newThreads = response.data.threads.map(function(thread) {
                            // Apply the doEmbed method to the post_content
                            thread.post_content = window.ThreadWPHelper.doEmbed(thread.post_content);
                            return thread;
                        });
                        
                        var html = template( newThreads );
                        jQuery('.threads-wp-new-threads').remove();
                        $div.find('.threads-wp-reddit-thread').prepend( html );
                    },
                    error: function (error) {
                        console.error('Error fetching data:', error);
                    },
                });
            });
            
            jQuery('#image-upload').change(function() {
                // Clear existing image previews
                jQuery('.image-preview').remove();
        
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
            jQuery('#threads-wp-post-form2').submit(function(event) {
                event.preventDefault();
        
                // Get form data including images
                const formData = new FormData(this);
                formData.append('action', sessionID);

                // Perform AJAX request for form submission
                $.ajax({
                    url: threadsWPObject.ajax_url, // Replace with your AJAX endpoint URL
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
                        that.loadMorePosts( $threadContainer );
                    }
                });
            });

            $(document).on('click', '.threads-wp-action[data-action="Edit"]', function() {
                const isComment = $(this).closest('.threads-wp-comment').length ? true : false;
                const $commentDiv = isComment ? $(this).closest('.threads-wp-comment') : '';
                const $postDiv = $(this).closest('.threads-wp-thread');
                const $content = isComment ? $commentDiv.find('.threads-wp-comment-content') : $postDiv.find('.threads-wp-thread-content');
                const $editForm = isComment ? $commentDiv.find('.comment-thread-edit-form') : $postDiv.find('.edit-form');
        
                $content.hide();
                $editForm.show();
                
                if ( isComment ) {
                    console.log( `#quill-editor-edit-${$postDiv.data('postid')}-${$commentDiv.data('commentid')}` );
                     // Initialize Quill editor for editing
                    let quillEditor = new Quill(`#quill-editor-edit-${$postDiv.data('postid')}-${$commentDiv.data('commentid')}`, {
                        theme: 'snow'
                        // ... (Quill editor configurations)
                    });
                    // Populate Quill editor with existing content
                    const existingContent = $content.html();
                    quillEditor.root.innerHTML = existingContent;
                } else {
                     // Initialize Quill editor for editing
                    let quillEditor = new Quill(`#quill-editor-edit-${$postDiv.data('postid')}`, {
                        theme: 'snow'
                        // ... (Quill editor configurations)
                    });
                    // Populate Quill editor with existing content
                    const existingContent = $content.html();
                    quillEditor.root.innerHTML = existingContent;
                }
            });
        
            // Function to cancel editing
            $(document).on('click', '.cancel-button', function() {
                const isComment = $(this).closest('.threads-wp-comment').length ? true : false;
                const $commentDiv = isComment ? $(this).closest('.threads-wp-comment') : '';
                const $postDiv = $(this).closest('.threads-wp-thread');
                const $content = isComment ? $commentDiv.find('.threads-wp-comment-content') : $postDiv.find('.threads-wp-thread-content');
                const $editForm = isComment ? $commentDiv.find('.comment-thread-edit-form') : $postDiv.find('.edit-form');
        
                $editForm.hide();
                $content.show();
            });
        
            // Function to save changes via AJAX
            $(document).on('click','.save-button', function() {
                const isComment = $(this).closest('.threads-wp-comment').length ? true : false;
                const $commentDiv = isComment ? $(this).closest('.threads-wp-comment') : '';
                const $postDiv = $(this).closest('.threads-wp-thread');
                var content = '';
                const $content = isComment ? $commentDiv.find('.threads-wp-comment-content') : $postDiv.find('.threads-wp-thread-content');
                const $editForm = isComment ? $commentDiv.find('.comment-thread-edit-form') : $postDiv.find('.edit-form');
                var commentID = isComment ? $commentDiv.data('commentid') : 0;
                var postID = $postDiv.data('postid');

                if ( isComment ) {
                    console.log( $postDiv );
                    console.log( $commentDiv );
                    const quill = new Quill(`#quill-editor-edit-${$postDiv.data('postid')}-${$commentDiv.data('commentid')}`);
                    content = quill.root.innerHTML; // Get Quill editor content
                } else {
                    //const content = $editForm.find('.post-quill-editor-edit').html();
                    const quill = new Quill(`#quill-editor-edit-${postID}`);
                    content = quill.root.innerHTML; // Get Quill editor content
                }

                // Perform an AJAX request to save the changes
                $.ajax({
                    url: threadsWPObject.ajax_url,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: isComment ? 'threads_wp_update_comment' : 'threads_wp_update_post', // Create this AJAX action in your main plugin file
                        post_id: postID,
                        comment_id: commentID,
                        content: content,
                        nonce: threadsWPObject.nonce
                    },
                    success: function(response) {
                        if ( isComment ) {
                            // Handle success, e.g., update the post content display
                            const $content = $commentDiv.find('.threads-wp-comment-content');
                            var template = wp.template('reddit-style-thread-comment-template');
                            var newThreads = response.data.comments.map(function (thread) {
                                // Apply the doEmbed method to the post_content
                                thread.comment_content = window.ThreadWPHelper.doEmbed(thread.comment_content);
                                return thread;
                            });

                            var html = template(newThreads);
                            console.log( $commentDiv );
                            console.log( html );
                            $commentDiv.replaceWith(html);
                        } else {
                            // Handle success, e.g., update the post content display
                            const $content = $postDiv;
                            var template = wp.template('thread-wp-feed-template');
                            var newThreads = response.data.threads.map(function (thread) {
                                // Apply the doEmbed method to the post_content
                                thread.post_content = window.ThreadWPHelper.doEmbed(thread.post_content);
                                return thread;
                            });

                            var html = template(newThreads);
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
                                    theme: 'snow', // You can use a different theme if needed
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

            $('.threads-wp-reply-button').on('click', function () {
                // Find the closest comment container
                var commentContainer = $(this).closest('.threads-wp-comment');
                
                // Get the dynamic ID for the Quill editor
                var quillEditorId = commentContainer.find('[data-comment-reply]').attr('data-comment-reply');
        
                // Toggle the reply form visibility
                commentContainer.find('.threads-wp-reply-form').toggle();
        
                // Initialize Quill editor inside the reply form using the dynamic ID
                var quill = new Quill('#' + quillEditorId, {
                    theme: 'snow', // You can customize the Quill editor's theme
                });
        
                // Handle submit button click
                commentContainer.find('.threads-wp-reply-submit').on('click', function () {
                    var replyContent = quill.root.innerHTML;
        
                    // Perform an AJAX request to submit the comment
                    $.ajax({
                        type: 'POST',
                        url: ajaxurl, // WordPress AJAX URL
                        data: {
                            action: 'thread_wp_comment_reply', // AJAX action hook
                            postId: postId, // Replace with the actual post ID
                            commentContent: replyContent,
                            nonce: threadWpCommentReply.nonce, // Nonce value (localized in your template)
                        },
                        success: function (response) {
                            // Handle the AJAX response here (update the comment section, etc.)
                            if (response.success) {
                                // Update your comment section with response.data
                                $('.threads-wp-comment-reply-section').html(response.data);
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
    
            // Perform an AJAX request to fetch data based on the attributes
            jQuery.ajax({
                url: threadsWPObject.ajax_url, // Replace with your AJAX endpoint URL
                type: 'GET',
                dataType: 'json', // Adjust the data type based on your server response
                data: {
                    action: 'fetch_data_by_thread', // Create this AJAX action in your main plugin file
                    post_type: postType,
                    per_page: perPage,
                    nonce: threadsWPObject.nonce,
                },
                success: function (response) {
                    // Handle the response here (e.g., render the fetched data)
                    var template = wp.template('thread-wp-feed-template');
                    var newThreads = response.map(function (thread) {
                        // Apply the doEmbed method to the post_content
                        thread.post_content = window.ThreadWPHelper.doEmbed(thread.post_content);
                        return thread;
                    });
    
                    var html = template(newThreads);
                    $div.find('.threads-wp-reddit-thread').prepend(html);
                    jQuery.each( $div.find('.threads-wp-reddit-thread .threads-wp-thread'), function() {
                        var post_id = jQuery(this).data('postid');
                        const quillContainerId = `quill-comment-editor-edit-${post_id}`;
                        const quillContainer = document.getElementById(quillContainerId);

                        // Check if the container exists (only if the thread is loaded via Ajax)
                        if (quillContainer) {
                        // Initialize Quill editor
                        const quill = new Quill(quillContainer, {
                            theme: 'snow', // You can use a different theme if needed
                            placeholder: 'Edit your thread here...',
                        });
                        
                        // You can set the initial content if needed
                        // quill.root.innerHTML = thread.post_content;
                        }
                    });
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
                url: threadsWPObject.ajax_url, // Replace with your AJAX endpoint URL
                type: 'POST',
                data: {
                    action: 'threads_wp_load_more_posts', // Create this AJAX action in your main plugin file
                    per_page: perPage,
                },
                success: function(response) {
                    if (response.success) {
                        if (response.data.posts.length > 0) {
                            var template = wp.template( 'thread-wp-feed-template' );
                            var newThreads = response.data.threads.map(function(thread) {
                                // Apply the doEmbed method to the post_content
                                thread.post_content = window.ThreadWPHelper.doEmbed(thread.post_content);
                                return thread;
                            });
                            
                            var html = template( newThreads );
                            jQuery('.threads-wp-new-threads').remove();
                            $container.find('.threads-wp-reddit-thread').append( html );
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

            jQuery(document).on('click', '.comment-submit-button', function() {
                var $thread = jQuery(this).closest('.threads-wp-thread');
                const postID = $thread.data('postid');
                //const content = $editForm.find('.post-quill-editor-edit').html();
                const quill = new Quill(`#quill-comment-editor-edit-${postID}`);
                const content = quill.root.innerHTML; // Get Quill editor content
                // Perform an AJAX request to save the changes
                $.ajax({
                    url: threadsWPObject.ajax_url,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: 'threads_wp_post_comment', // Create this AJAX action in your main plugin file
                        post_id: postID,
                        content: content,
                        nonce: threadsWPObject.nonce
                    },
                    success: function(response) {
                        var template = wp.template('reddit-style-thread-comment-template');
                        var newThreads = response.data.comments.map(function (thread) {
                            // Apply the doEmbed method to the post_content
                            thread.comment_content = window.ThreadWPHelper.doEmbed(thread.comment_content);
                            return thread;
                        });
                        var html = template(newThreads);;
                        $thread.find('.threads-wp-comment-section').prepend(html);
                        // Clear the comment input field
                        quill.root.innerHTML = '';
                    },
                    error: function(error) {
                        console.error('Error saving changes:', error);
                    }
                });
                
            });

            jQuery(document).on('click', '.threads-wp-comment-count', function() {
                const thread = jQuery(this).closest('.threads-wp-thread');
                // Get the current post ID from data attribute
                const postID = parseInt( thread.data('postid') );
                
                if ( thread.find('.threads-wp-comment-section').is(':empty')) {
                   // Perform AJAX request to load comments threads
                    $.ajax({
                        url: threadsWPObject.ajax_url, // Replace with your AJAX endpoint URL
                        type: 'GET',
                        data: {
                            action: 'threads_wp_load_comments', // Create this AJAX action in your PHP code
                            post_id: postID, // Send the post ID to the server
                            nonce: threadsWPObject.nonce, // Add nonce for security (make sure to localize this in your main PHP file)
                        },
                        success: function(response) {
                            if ( response.data.comments.length && response.data.comments.length > 0 ) {
                                var template = wp.template('reddit-style-thread-comment-template');
                                var newThreads = response.data.comments.map(function (thread) {
                                    // Apply the doEmbed method to the post_content
                                    thread.comment_content = window.ThreadWPHelper.doEmbed(thread.comment_content);
                                    return thread;
                                });
                                var html = template(newThreads[0]);
                                thread.find('.threads-wp-comment-section').prepend(html);
                            }
                        },
                        error: function(error) {
                            // Handle errors here (e.g., display an error message)
                            console.error('Error loading comments threads:', error);
                        },
                    });
                  } else {
                    thread.find('.threads-wp-comment-section').toggle();
                  }

                
            });
        }
        
        initializeTabs() {
            // Initialize the first tab as active
            jQuery('.threads-tab:first-child').addClass('active');
            jQuery('.threads-tab-content:first-child').addClass('active');
    
            // Switch tabs when clicked
            jQuery('.threads-tab').click(function() {
                jQuery('.threads-tab').removeClass('active');
                jQuery('.threads-tab-content').removeClass('active');
                var tab = jQuery(this).data('tab');
                jQuery(this).addClass('active');
                jQuery('.threads-tab-content[data-tab="' + tab + '"]').addClass('active');
            });
        }
    
        submitForm(event) {
            event.preventDefault();
    
            // Get form data
            const formData = jQuery('#threads-wp-post-form').serialize();
            
            // Get the HTML content from the Quill editor
            const quill = new Quill('.post-quill-editor'); // Initialize Quill (if not already done)
            const content = quill.root.innerHTML;

            // Perform AJAX request to save the post
            jQuery.ajax({
                url: threadsWPObject.ajax_url, // Define the AJAX URL (Make sure to localize this in your main plugin file)
                type: 'POST',
                data: {
                    action: 'threads_wp_save_post', // Create this AJAX action in your main plugin file
                    form_data: formData,
                    content: content,
                    nonce: threadsWPObject.nonce,
                },
                success: function(response) {
                    var template = wp.template( 'thread-wp-feed-template' );
                    var newThreads = response.data.threads.map(function(thread) {
                        // Apply the doEmbed method to the post_content
                        thread.post_content = window.ThreadWPHelper.doEmbed(thread.post_content);
                        return thread;
                    });
                    
                    var html = template( newThreads );
                    jQuery('[data-thread="' + response.data.post_type + '"] .threads-wp-reddit-thread').prepend( html );
                    // Reset post form.
                    
                    // Handle the response here (e.g., display a success message)
                },
                error: function(error) {
                    // Handle errors here (e.g., display an error message)
                },
            });
        }
    
        handleEllipsisClick() {
            // Find the parent .threads-wp-thread
            
            var thread = jQuery(this).closest('.threads-wp-comment');
            if ( thread.length == 0 ) {
                thread = jQuery(this).closest('.threads-wp-thread');
            }
            // Find .threads-wp-thread-actions within the thread
            const actionsArea = thread.find('.threads-wp-thread-actions');

            // Toggle the visibility of the actions area
            actionsArea.toggle();
    
            // Prevent the click event from propagating to the document body
            event.stopPropagation();
        }
    
        closeActions() {
            // Hide all .threads-wp-thread-actions elements
            jQuery('.threads-wp-thread-actions').hide();
        }

        handleActionClick(event) {
            var $thread = jQuery(event.target).closest('.threads-wp-thread');
            console.log( $thread );
            const actionType = jQuery(event.target).data('action');
            if ( actionType == 'Edit' ) {
                return;
            }
            var data = {
                action: 'threads_wp_posts_action',
                action_type: actionType, // The action type you want to perform
                post_id: $thread.data('postid'), // The post ID associated with the action
                nonce: threadsWPObject.nonce 
            };
    
            // Perform the AJAX request
            jQuery.ajax({
                type: 'POST',
                url: threadsWPObject.ajax_url, // Define the AJAX URL (Make sure to localize this in your main plugin file)
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
              theme: 'snow',
              modules: {
                toolbar: [
                  ['bold', 'italic', 'strike'], // Text formatting options
                  ['image', 'code-block', 'blockquote'], // Add photos, code block, and quote
                ],
              },
            });
      
            // Show/hide reply form when clicking the Reply button
            jQuery('.threads-wp-reply-button').click(function() {
              const replyForm = jQuery(this).siblings('.threads-wp-reply-form');
              replyForm.toggle();
            });
      
            // Handle form submission
            jQuery('.threads-wp-reply-submit').click(function() {
              // Get the HTML content from Quill editor
              const replyContent = quill.root.innerHTML;
      
              // Perform AJAX request to save the reply
              jQuery.ajax({
                url: threadsWPObject.ajax_url, // Define the AJAX URL
                type: 'POST',
                data: {
                  action: 'threads_wp_save_reply', // Create this AJAX action in your main plugin file
                  reply_content: replyContent,
                  nonce: threadsWPObject.nonce,
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
            jQuery('.threads-wp-reply-cancel').click(function() {
              const replyForm = jQuery(this).parents('.threads-wp-reply-form');
              replyForm.hide();
            });
        }

        initializeQuillEditor() {
            const that = this;
            const quillPostEditor = new Quill('.post-quill-editor', {
              theme: 'snow',
              modules: {
                toolbar: '#toolbar'
              }
            });

            quillPostEditor.on('text-change', function(delta, oldDelta, source) {
                // Extract the link URL from the editor content
                //var content = jQuery('.post-quill-editor .ql-editor').text();
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
                var $threads = $div.find('.threads-wp-thread'); 
                // Iterate through each element to find the highest post ID
                $threads.each(function() {
                    var postId = parseInt(jQuery(this).data('postid'));
                    
                    // Check if the current post ID is higher than the highest found so far
                    if (postId > highestPostId) {
                        highestPostId = postId;
                        $elementWithHighestId = jQuery(this);
                    }
                });
                // Use jQuery's AJAX method to make a GET request to your server
                jQuery.ajax({
                    url: threadsWPObject.ajax_url, // Replace with your AJAX endpoint URL
                    type: 'GET',
                    dataType: 'json', // Adjust the data type based on your server response
                    data: {
                        action: 'fetch_latest_thread_notice', // Create this AJAX action in your main plugin file
                        post_type: postType,
                        per_page: perPage,
                        nonce: threadsWPObject.nonce,
                        post_id: highestPostId,
                    },
                    success: (response) => {
                        var message = response.data.message;
                        
                        if ( response.data.message > 0 ) {
                            // Update your template with the new data
                            if ( ! $div.find('.threads-wp-new-threads').length ) {
                                $div.prepend('<div class="threads-wp-new-threads">New ' + message + ' posts</div>');
                            } else {
                                $div.find('.threads-wp-new-threads').html('New ' + message+ ' posts' );
                            }
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
    window.ThreadsWPPlugin = ThreadsWPPlugin;
    
    // Create an instance of the ThreadsWPPlugin class
    const threadsWP = new ThreadsWPPlugin();
    
});
