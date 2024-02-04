// Import Quill
import Quill from 'quill';
import ToolbarEmoji from 'quill-emoji';
//import InfiniteScroll from './modules/infiniteScroll';

var editor_theme = 'snow';
var editorModules = {
    toolbar: [userwallWPObject.toolbar]
};
const userLoggedIn = (parseInt( userwallWPObject.user_id ) > 0);
const postOpenType = userwallWPObject.settings.open_posts;
const isSinglePost = userwallWPObject.isSinglePost;

function InfiniteScroll(contentId) {
    let page = 1;
    const content = document.querySelector(contentId);
    const loadingIndicator = document.getElementById('loading');
    const itemsPerPage = 10;
    const threadWrapper = document.querySelector(contentId).closest('[data-thread]');
    let isLoading = false;
    let sentinel; // Sentinel element to detect scroll to the bottom
    let hasMoreResults = true; // Track if more results are available

    function createSentinel() {
        sentinel = document.createElement('div');
        sentinel.classList.add('sentinel');
        content.appendChild(sentinel);
    }

    createSentinel(); // Initialize the sentinel element

    function loadMoreItems() {
        if (isLoading || !hasMoreResults) {
            return;
        }
        isLoading = true;
        loadingIndicator.style.display = 'block';

        // Check if the sentinel element is in the viewport
        const sentinelRect = sentinel.getBoundingClientRect();
        if (sentinelRect.top <= window.innerHeight) {
            const xhr = new XMLHttpRequest();
            xhr.open('POST', userwallWPObject.ajax_url);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

            xhr.onload = function () {
                if (xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        if (response.data.posts.length > 0) {
                            renderPosts(response.data.posts, 'bottom');
                            if ( isSinglePost ) {
                                loadComments(isSinglePost, jQuery(contentId).closest('[data-thread]') )
                                hasMoreResults = false;
                            }
                            page++;
                        } else {
                            // No more posts to load
                            hasMoreResults = false; // Disable further loading
                        }
                    } else {
                        // Handle AJAX error
                        console.error('Error loading more posts:', response.data.message);
                        hasMoreResults = false;
                    }
                } else {
                    // Handle AJAX error
                    console.error('Error loading more posts:', xhr.statusText);
                    hasMoreResults = false;
                }

                isLoading = false;
                loadingIndicator.style.display = 'none';
            };

            xhr.onerror = function () {
                // Handle AJAX error
                console.error('Error loading more posts:', xhr.statusText);
                isLoading = false;
                loadingIndicator.style.display = 'none';
                hasMoreResults = false;
            };

            const data = new URLSearchParams();
            data.append('action', 'userwall_wp_load_more_posts');
            data.append('per_page', itemsPerPage);
            if ( threadWrapper.dataset.post_id && !isNaN(threadWrapper.dataset.post_id) ) {
                data.append( 'post_id', threadWrapper.dataset.post_id );
            }
            
            if ( userwallWPObject.user_wall ) {
                data.append('user_wall', userwallWPObject.user_wall );
            }
            // Get all elements with the data-postid attribute
            const postIdElements = content.querySelectorAll('[data-postid]');
            
            // Initialize a variable to store the lowest postid
            let lowestPostId = Number.MAX_SAFE_INTEGER;

            // Loop through all elements with data-postid attribute
            postIdElements.forEach(function (element) {
                const postid = parseInt(element.getAttribute('data-postid'));
                if (!isNaN(postid) && postid < lowestPostId) {
                    lowestPostId = postid;
                }
            });

            data.append('last_post', lowestPostId);

            xhr.send(data);
        }
    }

    window.addEventListener('scroll', function () {
        if (!isLoading) {
            const contentRect = content.getBoundingClientRect();
            if (contentRect.bottom <= window.innerHeight) {
                loadMoreItems();
            }
        }
    });

    loadMoreItems();
}

document.querySelectorAll('[data-thread]').forEach(function(element) {
    const loadingElement = element.querySelector('#loading');
    
    if (loadingElement) {
        InfiniteScroll('.userwall-wp-inner-thread');
    }
});


function loadComments( postID, thread ) {
    // Perform AJAX request to load comments userwall
    jQuery.ajax({
        url: userwallWPObject.ajax_url, // Replace with your AJAX endpoint URL
        type: 'GET',
        data: {
            action: 'userwall_wp_load_comments', // Create this AJAX action in your PHP code
            post_id: postID, // Send the post ID to the server
            nonce: userwallWPObject.nonce, // Add nonce for security (make sure to localize this in your main PHP file)
        },
        success: function(response) {
            if ( response.data.comments.length && response.data.comments.length > 0 ) {
                var template = wp.template('userwall-wp-thread-comment-template');
                var newPosts = transformComments( response.data.comments );
                jQuery.each( newPosts, function( index, comment ) {
                    var template = wp.template('userwall-wp-thread-comment-template');

                    var commentData = comment;

                    // Render the template with the data
                    var renderedHtml = template(commentData);
                    
                    // Convert the HTML string to a jQuery object
                    var commentHtml = jQuery('<div class="tempWrapper">' + renderedHtml + '</div>' );
                    
                    jQuery.each( comment, function( index, reply ) {
                        if ( reply.child_comments.length > 0 ) {
                            template = wp.template('userwall-wp-thread-comment-template');
                            var innerComments = transformComments(reply.child_comments);

                            jQuery.each( innerComments, function( index2, inner_comment ) {
                                var ReplyTemplate = wp.template('userwall-wp-thread-comment-template');
                                var child_html = ReplyTemplate({inner_comment});
                                commentHtml.find('[data-commentid="' + inner_comment.parent_id + '"] > .userwall-wp-comment-reply-section').append(child_html);
                            });
                        }
                    });

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
}
//import Masonry from 'masonry-layout';

//window.userwallWP.Masonry = Masonry;

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
                //richPreviewHTML = '<iframe src="' + url + '" frameborder="0" allowfullscreen></iframe>';
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
        if ( ! richPreviewHTML ) {
            return '';
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
        return wp.hooks.applyFilters('userwall_wp_content_filter', thread);
    });
}

function transformComments( comments ) {
    return comments.map(function (comment) {
        // Apply the doEmbed method to the comment_content
        comment.comment_content = window.UserWallWPHelper.doEmbed(comment.comment_content);
        comment.comment_content = comment.comment_content.replace(/^<p>\s*<\/p>/, '');
        return wp.hooks.applyFilters('userwall_wp_content_comment_filter', comment );
    });
}
function renderPosts(posts, position = 'top' ) {
    var template_id = 'userwall-wp-feed-template';
    var template = wp.template(template_id);
    var newPosts = transformPosts( posts );

    var html, renderedHtml;
    jQuery.each(newPosts, function (i, thread) {
        html = template({ thread });
        renderedHtml = jQuery('<div class="tempWrap">' + html + '</div>');
        const quillContainerId = `quill-comment-editor-edit-${thread.post_id}`;
        const quillContainer = renderedHtml.find('#' + quillContainerId)[0];
        let quill;
       
        if ( ! jQuery('.userwall-wp-thread[data-postid="' + thread.post_id +'"]').length ) {
            if ( position == 'top' ) {
                jQuery('.userwall-wp-inner-thread').prepend(renderedHtml.html());
            } else {
                jQuery('.userwall-wp-inner-thread').append(renderedHtml.html());
            }
            if ( userLoggedIn ) {
                const quillContainerId = `quill-comment-editor-edit-${thread.post_id}`;
                const quillContainer = jQuery('.userwall-wp-inner-thread').find('#' + quillContainerId)[0];
                // Initialize Quill editor
                quill = new Quill(quillContainer, {
                    theme: editor_theme,
                    placeholder: userwallWPObject.reply_placeholder,
                    modules: editorModules,
                });
            }
            wp.hooks.doAction('userwall_wp_post_rendered', thread);
        }
    });
}

function truncateHtml(html, maxLength) {
    var charCount = 0;
    var output = '';
    var isTruncating = false;
    
    // Convert HTML string to jQuery objects and iterate
    jQuery(html).each(function() {
        var textContent = jQuery(this).text();
        
        if (charCount + textContent.length > maxLength && !isTruncating) {
            // Calculate the number of characters to take from the current element
            var remainingChars = maxLength - charCount;
            var truncatedText = textContent.substr(0, remainingChars);
            
            // Create a clone of the current element and set its text content to the truncated text
            var clonedElement = jQuery(this).clone();
            clonedElement.text(truncatedText);
            output += jQuery('<div>').append(clonedElement).html();
            
            isTruncating = true;
        } else if (!isTruncating) {
            // Add the whole element if we are not yet truncating
            output += jQuery('<div>').append(jQuery(this).clone()).html();
        }
        
        charCount += textContent.length;
        
        // If we started truncating, no need to process further elements
        if (isTruncating) {
            return false; // Stop adding more elements to the output
        }
    });
    
    // If the total character count is less than the maxLength, return false
    if (charCount <= maxLength) {
        return false;
    }
    
    return output;
}


function setReadMore() {
    var maxLength = 100; // Maximum number of characters to display before truncating
    return;
    jQuery('.userwall-wp-thread').each(function() {
        var container = jQuery(this);
        var fullHtml = container.find('.userwall-wp-thread-content').html();
        var truncatedHtml = truncateHtml(fullHtml, maxLength);
        
       
        
        if (truncatedHtml && fullHtml != '<p><br></p>' ) {
            if ( fullHtml.trim() !== truncatedHtml ) {
                console.log( fullHtml.trim() );
                console.log( truncatedHtml );
                console.log( fullHtml.trim() !== truncatedHtml );
                container.find('.userwall-wp-thread-content').data('full-html', fullHtml).html(truncatedHtml);
                container.find('.userwall-wp-thread-content').after('<button class="read-more-btn">Read More</button>'); // Add the Read More button
                container.find('.read-more-btn').show();
            }
        }
    });
    
    jQuery('.userwall-wp-thread').on('click', '.read-more-btn', function() {
        var container = jQuery(this).closest('.userwall-wp-thread');
        var textContent = container.find('.userwall-wp-thread-content');
        var isFullHtmlVisible = textContent.data('is-full-html-visible');
        
        if (isFullHtmlVisible) {
            var fullHtml = textContent.data('full-html');
            var truncatedHtml = truncateHtml(fullHtml, maxLength);
            textContent.html(truncatedHtml);
            jQuery(this).text('Read More');
            textContent.data('is-full-html-visible', false);
        } else {
            var fullHtml = textContent.data('full-html');
            textContent.html(fullHtml);
            jQuery(this).text('Read Less');
            textContent.data('is-full-html-visible', true);
        }
    });

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
            
            jQuery(document).on('click', '.userwall-wp-reaction-count', function() {
                if ( ! userLoggedIn ) {
                    return;
                }
                var button = jQuery(this);
                var countSpan = button.find('.span-count');
                var like_reaction = button.data('like-reaction');
                const isComment = jQuery(this).closest('.userwall-wp-comment').length ? true : false;
                const $commentDiv = isComment ? jQuery(this).closest('.userwall-wp-comment') : '';
                const $threadDiv = jQuery(this).closest('.userwall-wp-thread');
                var postID = $threadDiv.data('postid');
                var commentID = isComment ? $commentDiv.data('commentid') : 0;

                $.ajax({
                    url: userwallWPObject.ajax_url,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: 'userwall_wp_post_like', // Create this AJAX action in your main plugin file
                        post_id: postID,
                        comment_id: commentID,
                        like_reaction: like_reaction,
                        nonce: userwallWPObject.nonce
                    },
                    success: function(response) {
                        countSpan.text( parseInt( response.data.total) );
                
                        // Animate the button on click
                        if ( like_reaction == 'like' ) {
                            button.addClass('userwall-wp-liked');
                            button.data('like-reaction', 'like' );
                        } else {
                            button.removeClass('userwall-wp-liked');
                            button.data('like-reaction', 'remove' );
                        }
                    }
                });
            });

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

            jQuery(document).on("click", ".userwall-wp-post-body", function(event) {
                var $thread = jQuery(this).closest('.userwall-wp-thread');
                var link    = $thread.data('permalink');
                // Check if goToPage is true and the clicked element is not a link
                if (link && postOpenType && !$(event.target).is("a")) {

                  window.location.href = link;
                }
            });

            // Attach click events to actions
            jQuery(document).on( 'click', '.userwall-wp-action', this.handleActionClick.bind(this));

            jQuery('[data-thread]').each(function () {
                const $div = jQuery(this);

                // Fetch and render posts
                //classObj.fetchAndRenderPosts($div);
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
                        var template = wp.template('userwall-wp-thread-comment-template');
                        var newPosts = transformComments(response.data.comments);
                        var html = template(newPosts);;
                        $thread.find('.userwall-wp-comment-section').prepend(html);

                        $thread.find('.userwall-wp-comment-count .span-count').text( parseInt( response.data.total ) );
                        // Clear the comment input field
                        quill.root.innerHTML = '';
                    },
                    error: function(error) {
                        console.error('Error saving changes:', error);
                    }
                });
                
            });

            jQuery(document).on('click', '.userwall-wp-comment-count', function() {
                const isComment = jQuery(this).closest('.userwall-wp-comment').length ? true : false;
                const $commentDiv = isComment ? jQuery(this).closest('.userwall-wp-comment') : '';
                const thread = jQuery(this).closest('.userwall-wp-thread');
                // Get the current post ID from data attribute
                const postID = parseInt( thread.data('postid') );
                if ( isComment ) {
                    $commentDiv.find('.userwall-wp-reply-button').trigger('click');
                } else {
                    if ( thread.find('.userwall-wp-comment-section').is(':empty')) {
                        loadComments( postID, thread )
                     } else {
                         thread.find('.userwall-wp-comment-section').toggle();
                     }
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
                        renderPosts( response.data.posts );
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

            jQuery(document).on('click', '.userwall-wp-action[data-action="Edit"]', function() {
                const isComment = jQuery(this).closest('.userwall-wp-comment').length ? true : false;
                const $commentDiv = isComment ? jQuery(this).closest('.userwall-wp-comment') : '';
                const $postDiv = jQuery(this).closest('.userwall-wp-thread');
                const $content = isComment ? $commentDiv.find('.userwall-wp-comment-content') : $postDiv.find('.userwall-wp-thread-content');
                const $title = ! isComment ? $postDiv.find('.userwall-wp-thread-title-wrapper') : '';
                const $editForm = isComment ? $commentDiv.find('.comment-thread-edit-form') : $postDiv.find('.edit-form');
        
                $content.hide();
                $title.hide();
                $editForm.show();
                
                if ( isComment ) {
                    let editorContainer = `#quill-editor-edit-${$postDiv.data('postid')}-${$commentDiv.data('commentid')}`;
                    if ( ! jQuery( editorContainer ).find('.ql-editor').length ) {
                        // Initialize Quill editor for editing
                        let quillEditor = new Quill(editorContainer, {
                            theme: editor_theme,
                            modules: editorModules,
                        });

                        // Populate Quill editor with existing content
                        const existingContent = $content.html();
                        quillEditor.root.innerHTML = existingContent.replace(/^<p>\s*<\/p>/, '');
                    }
                } else {
                    let editorContainer = `#quill-editor-edit-${$postDiv.data('postid')}`;
                    if ( ! jQuery( editorContainer ).find('.ql-editor').length ) {
                        // Initialize Quill editor for editing
                        let quillEditor = new Quill(editorContainer, {
                            theme: editor_theme,
                            modules: editorModules,
                        });

                        // Populate Quill editor with existing content
                        const existingContent = $content.html();
                        quillEditor.root.innerHTML = existingContent.replace(/^<p>\s*<\/p>/, '');
                    }
                    
                }
            });
        
            // Function to cancel editing
            jQuery(document).on('click', '.cancel-button', function() {
                const isComment = jQuery(this).closest('.userwall-wp-comment').length ? true : false;
                const $commentDiv = isComment ? jQuery(this).closest('.userwall-wp-comment') : '';
                const $postDiv = jQuery(this).closest('.userwall-wp-thread');
                const $content = isComment ? $commentDiv.find('.userwall-wp-comment-content') : $postDiv.find('.userwall-wp-thread-content');
                const $title = ! isComment ? $postDiv.find('.userwall-wp-thread-title-wrapper') : '';
                const $editForm = isComment ? $commentDiv.find('.comment-thread-edit-form') : $postDiv.find('.edit-form');
        
                $editForm.hide();
                $content.show();
                $title.show();
            });
            
            // Function to save changes via AJAX
            jQuery(document).on('click','.save-button', function() {
                const isComment = jQuery(this).closest('.userwall-wp-comment').length ? true : false;
                const $commentDiv = isComment ? jQuery(this).closest('.userwall-wp-comment') : '';
                const $postDiv = jQuery(this).closest('.userwall-wp-thread');
                var content = '';
                const $content = isComment ? $commentDiv.find('.userwall-wp-comment-content') : $postDiv.find('.userwall-wp-thread-content');
                const $title = ! isComment ? $postDiv.find('.userwall-wp-thread-title-wrapper') : '';
                const $editForm = isComment ? $commentDiv.find('.comment-thread-edit-form') : $postDiv.find('.edit-form');
                var commentID = isComment ? $commentDiv.data('commentid') : 0;
                var postID = $postDiv.data('postid');

                if ( isComment ) {
                    const quill = new Quill(`#quill-editor-edit-${$postDiv.data('postid')}-${$commentDiv.data('commentid')}`);
                    content = quill.root.innerHTML; // Get Quill editor content
                } else {
                    const quill = new Quill(`#quill-editor-edit-${postID}`);
                    content = quill.root.innerHTML; // Get Quill editor content
                    $title.val( $postDiv.find( '.userwall-wp-thread-title' ).text() );
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
                            var template = wp.template('userwall-wp-thread-comment-template');
                            var newPosts = transformComments(response.data.comments);

                            var html = template(newPosts);
                            $commentDiv.replaceWith(html);
                        } else {
                            // Handle success, e.g., update the post content display
                            const $content = $postDiv;
                            var template = wp.template('userwall-wp-feed-template');
                            var newPosts = transformPosts(response.data.posts);

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
                                    modules: editorModules,
                                });

                                quill.on('text-change', function() {
                                    var editorContent = quill.getText();
                            
                                    // If there is text in the editor (not just whitespace)
                                    if (editorContent.trim().length > 0) {
                                        // Remove 'ql-blank' class
                                        jQuery('#' . quillContainerId).removeClass('ql-blank');
                                    } else {
                                        // Add 'ql-blank' class if the editor is empty
                                        jQuery('#' . quillContainerId).addClass('ql-blank');
                                    }
                                });
                            }
                            
                        }
                    },
                    error: function(error) {
                        console.error('Error saving changes:', error);
                    }
                });
            });

            jQuery(document).on('click', '.userwall-wp-reply-button', function () {
                // Find the closest comment container
                var commentContainer = jQuery(this).closest('.userwall-wp-comment');
                
                var commentId = commentContainer.data('commentid');

                var postContainer = jQuery(this).closest('.userwall-wp-thread');
                var postId =  postContainer.data('postid');

                // Get the dynamic ID for the Quill editor
                var quillEditorId = commentContainer.find('[data-comment-reply]').attr('data-comment-reply');
        
                // Toggle the reply form visibility
                commentContainer.find('.userwall-wp-reply-form').toggle();
                
                let quillContainer = '#' + quillEditorId;
                if ( jQuery( quillContainer).html() == '' ) {
                    // Initialize Quill editor inside the reply form using the dynamic ID
                    var quill = new Quill( quillContainer, {
                        theme: editor_theme, // You can customize the Quill editor's theme
                        modules: editorModules,
                    });

                    quill.on('text-change', function() {
                        var editorContent = quill.getText();
                
                        // If there is text in the editor (not just whitespace)
                        if (editorContent.trim().length > 0) {
                            // Remove 'ql-blank' class
                            jQuery(quillContainer).removeClass('ql-blank');
                        } else {
                            // Add 'ql-blank' class if the editor is empty
                            jQuery(quillContainer).addClass('ql-blank');
                        }
                    });
                }
                
                // Handle submit button click
                commentContainer.find('.userwall-wp-reply-submit').on('click', function () {
                    var replyContent = quill.root.innerHTML;
               
                    // Perform an AJAX request to submit the comment
                    $.ajax({
                        type: 'POST',
                        url: userwallWPObject.ajax_url,
                        data: {
                            action: 'userwall_wp_comment_reply', // AJAX action hook
                            post_id: postId,
                            commentId: commentId,
                            commentContent: replyContent,
                            nonce: userwallWPObject.nonce, // Nonce value (localized in your template)
                        },
                        success: function (response) {
                            // Handle the AJAX response here (update the comment section, etc.)
                            if (response.success) {
                                // Update your comment section with response.data
                                var template = wp.template('userwall-wp-thread-comment-template');
                                var newComments = transformComments(response.data.comments);
                                var html = template(newComments);
                                var commentHtml = jQuery( '<div class="tempWrap">' + html + '</div>' );
                                commentHtml.find('.userwall-wp-reply').remove();
                                commentHtml.find('.userwall-wp-comment-reply-section').remove();
                                commentContainer.find('.userwall-wp-comment-reply-section').prepend(commentHtml.html());
                                commentContainer.find('.userwall-wp-reply-form').toggle();
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

            const content = jQuery( '.post-quill-editor').find('.ql-editor').html();

            var tab = jQuery('.userwall-tab.active').data('tab');
            var form = jQuery('#userwall-wp-post-form')[0];

            const formData = new FormData(form);
            if ( tab == 'image' ) {
                formData.append("content", jQuery('#userwall-wp-post-form').find('[name="image_content"]').val() );
            } else {
                formData.append('content', content );
            }

            var title = jQuery('.userwall-wp-post-title-input');
            if ( title.length && title.val() ) {
                formData.append('post_title', title.val() );
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
                    renderPosts( response.data.posts );
                    // Reset post form.
                    jQuery( '.post-quill-editor').find('.ql-editor').html('');
                    if ( title.length && title.val() ) {
                        title.val('');
                    }
                    wp.hooks.doAction('userwall_wp_after_post_submitted', response, formData );
                },
                error: function(error) {
                    // Handle errors here (e.g., display an error message)
                },
            });
        }
    
        handleEllipsisClick() {
            var obj = jQuery( this );

            // Find .userwall-wp-thread-actions within the thread
            const actionsArea = obj.siblings('.userwall-wp-thread-actions');

            // Toggle the visibility of the actions area
            actionsArea.toggle();
        }
    
        closeActions() {
            // Hide all .userwall-wp-thread-actions elements
            jQuery('.userwall-wp-thread-actions').hide();
        }

        handleActionClick(event) {
            var $thread = jQuery(event.target).closest('.userwall-wp-thread');
            var $commentDiv = jQuery( event.target ).closest( '.userwall-wp-comment' );
            const actionType = jQuery(event.target).data('action');
            if ( actionType == 'Edit' ) {
                return;
            }
            var data = {
                action: 'userwall_wp_posts_action',
                action_type: actionType, // The action type you want to perform
                post_id: $thread.data('postid'), // The post ID associated with the action
                comment_id: $commentDiv.data('commentid'), // The comment ID associated with the action
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
              modules: editorModules,
            });

            quill.on('text-change', function() {
                var editorContent = quill.getText();
        
                // If there is text in the editor (not just whitespace)
                if (editorContent.trim().length > 0) {
                    // Remove 'ql-blank' class
                    jQuery('[data-comment-reply]').removeClass('ql-blank');
                } else {
                    // Add 'ql-blank' class if the editor is empty
                    jQuery('[data-comment-reply]').addClass('ql-blank');
                }
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
    
            var quill_config = {
                theme: editor_theme,
                modules: editorModules
            }
            const quillPostEditor = new Quill('.post-quill-editor', quill_config);

            var maxChars = userwallWPObject.char_limit; // Maximum characters allowed

            quillPostEditor.on('text-change', function(delta, oldDelta, source) {
                if ( maxChars !== 0 ) {
                    var text = quillPostEditor.getText().trim(); // Get text content of editor
                    var charCount = text.length; // Get the length of text

                    // Update character count
                    var charCountDiv = jQuery('#userwall-wp-charcount');
                    charCountDiv.text('Characters: ' + charCount + '/' + maxChars);

                    // Add 'max-reached' class to character count if limit is reached
                    if (charCount > maxChars) {
                        charCountDiv.html('Characters: ' + charCount + '/' + maxChars + ' <span class="max-reached">(Max limit reached)</span>');
                        
                        // Optional: Prevent further typing
                        quillPostEditor.deleteText(maxChars, charCount - maxChars);
                    } else {
                        charCountDiv.removeClass('max-reached');
                    }
                }
            });

            quillPostEditor.on('text-change', function() {
                var editorContent = quillPostEditor.getText();
        
                // If there is text in the editor (not just whitespace)
                if (editorContent.trim().length > 0) {
                    // Remove 'ql-blank' class
                    jQuery('.post-quill-editor').removeClass('ql-blank');
                } else {
                    // Add 'ql-blank' class if the editor is empty
                    jQuery('.post-quill-editor').addClass('ql-blank');
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
            if ( isSinglePost ) {
                return;
            }
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
                        user_wall: userwallWPObject.user_wall
                    },
                    success: (response) => {
                        var message = response.data.message;
                        
                        if ( response.data.message > 0 ) {
                            // Update your template with the new data
                            if ( ! $div.find('.userwall-wp-new-userwall').length ) {
                                $div.prepend('<div class="userwall-wp-new-userwall"><span class="userwall-wp-new-userwall-content">New ' + message + ' posts</span></div>');
                            } else {
                                $div.find('.userwall-wp-new-userwall').html( '<span class="userwall-wp-new-userwall-content">New ' + message + ' posts</span>' );
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
                    //richPreviewHTML = '<iframe src="' + url + '" frameborder="0" allowfullscreen></iframe>';
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
            if ( ! richPreviewHTML ) {
                return '';
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
