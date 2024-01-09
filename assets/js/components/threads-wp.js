// Import Quill
import Quill from 'quill';
import ToolbarEmoji from 'quill-emoji';

jQuery(document).ready(function($) {
    class ThreadsWPPlugin {
        constructor() {
            // Initialize the class
            this.initialize();
            this.initializeTabs();
            this.initializeReplyForm();
            // Initialize Quill rich text editor for the post form
            this.initializeQuillEditor();
            //this.initializeLiveUpdates();
        }
    
        initialize() {
            // Attach click event to the form submit button
            $('#threads-wp-post-form').on('submit', this.submitForm.bind(this));
    
            // Attach click event to the ellipsis icon
            $(document).on('click', '.threads-wp-ellipsis', this.handleEllipsisClick );
    
            // Add a click event to the document body to close actions when clicking outside
            $('body').click(this.closeActions);
    
            // Prevent clicks within the actions area from closing it
            $('.threads-wp-thread-actions').click(function(event) {
                event.stopPropagation();
            });

            // Attach click events to actions
            $('.threads-wp-action').click(this.handleActionClick.bind(this));
        }
    
        initializeTabs() {
            console.log( 'test' );
            // Initialize the first tab as active
            $('.threads-tab:first-child').addClass('active');
            $('.threads-tab-content:first-child').addClass('active');
    
            // Switch tabs when clicked
            $('.threads-tab').click(function() {
                $('.threads-tab').removeClass('active');
                $('.threads-tab-content').removeClass('active');
                var tab = $(this).data('tab');
                $(this).addClass('active');
                $('.threads-tab-content[data-tab="' + tab + '"]').addClass('active');
            });
        }
    
        submitForm(event) {
            event.preventDefault();
    
            // Get form data
            const formData = $('#threads-wp-post-form').serialize();
            
            // Get the HTML content from the Quill editor
            const quill = new Quill('.post-quill-editor'); // Initialize Quill (if not already done)
            const content = quill.root.innerHTML;

            // Perform AJAX request to save the post
            $.ajax({
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
                    jQuery('[data-thread="' + response.data.post_type + '"] .threads-wp-reddit-thread').append( template( response.data.threads )  );
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
            const thread = $(this).closest('.threads-wp-thread');
    
            // Find .threads-wp-thread-actions within the thread
            const actionsArea = thread.find('.threads-wp-thread-actions');
    
            // Toggle the visibility of the actions area
            actionsArea.toggle();
    
            // Prevent the click event from propagating to the document body
            event.stopPropagation();
        }
    
        closeActions() {
            // Hide all .threads-wp-thread-actions elements
            $('.threads-wp-thread-actions').hide();
        }

        handleActionClick(event) {
            const actionType = $(event.target).data('action');
            // Depending on the actionType, perform the appropriate action
            switch (actionType) {
                case 'edit':
                    // Handle Edit action
                    break;
                case 'delete':
                    // Handle Delete action
                    break;
                case 'block':
                    // Handle Block action
                    break;
                case 'report':
                    // Handle Report action
                    break;
                case 'embed-post':
                    // Handle Embed Post action
                    break;
                case 'save':
                    // Handle Save action
                    break;
                case 'follow':
                    // Handle Follow action
                    break;
                default:
                    // Handle unknown action type
                    break;
            }
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
            $('.threads-wp-reply-button').click(function() {
              const replyForm = $(this).siblings('.threads-wp-reply-form');
              replyForm.toggle();
            });
      
            // Handle form submission
            $('.threads-wp-reply-submit').click(function() {
              // Get the HTML content from Quill editor
              const replyContent = quill.root.innerHTML;
      
              // Perform AJAX request to save the reply
              $.ajax({
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
            $('.threads-wp-reply-cancel').click(function() {
              const replyForm = $(this).parents('.threads-wp-reply-form');
              replyForm.hide();
            });
        }

        initializeQuillEditor() {
            const quillPostEditor = new Quill('.post-quill-editor', {
              theme: 'snow',
              modules: {
                toolbar: '#toolbar'
              }
            });
        }

        initializeLiveUpdates() {
            // Set up a timer to fetch and update data periodically
            const updateInterval = 30000; // 30 seconds in milliseconds
            setInterval(() => {
                this.fetchAndUpdateData();
            }, updateInterval);
        }
    
        fetchAndUpdateData() {
            // Use jQuery's AJAX method to make a GET request to your server
            $.ajax({
                url: threadsWPObject.ajax_url, // Replace with your AJAX endpoint URL
                type: 'GET',
                dataType: 'json', // Adjust the data type based on your server response
                success: (response) => {
                // Update your template with the new data
                const template = wp.template('your_template_name_here');
                const html = template(response);
                $('#your-container').html(html); // Replace #your-container with your actual container ID
                },
                error: (error) => {
                console.error('Error fetching data:', error);
                },
            });
        }
    }
    
    // Create an instance of the ThreadsWPPlugin class
    const threadsWP = new ThreadsWPPlugin();
    
});
