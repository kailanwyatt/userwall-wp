(function($) {
    $.fn.UserWallWPMention = function(options) {
        // Default settings
        var settings = $.extend({
            // Default settings like URL to fetch users, etc.
            fetchUsersUrl: 'path-to-your-endpoint',
            debounceTime: 500,
            editor: null
        }, options);

        var editorBox = $(this);
        var quill = settings.editor;

        // Function to fetch user suggestions
        var fetchUserSuggestions = function(searchTerm) {
            $.ajax({
                url: threadsWPObject.ajax_url, // Use the localized variable
                type: 'GET',
                data: {
                    action: 'fetch_usernames', // Use the localized variable
                    term: searchTerm
                },
                success: function(response) {
                    if(response.success) {
                        showSuggestions(response.data); // Assuming the data is an array of usernames
                    }
                },
                error: function(error) {
                    console.error('Error fetching users:', error);
                }
            });
        };

        
        // Function to show user suggestions
        var showSuggestions = function(suggestions) {
            var suggestionBox = $('#suggestion-box');
            if (suggestionBox.length === 0) {
                suggestionBox = $('<div id="suggestion-box"></div>')
            }

            suggestionBox.empty(); // Clear previous suggestions

            if (suggestions.length) {
                suggestions.forEach(function(user) {
                    $('<div class="suggestion-item"></div>')
                        .text(user) // Assuming 'user' is just a username string. Adjust if your data structure is different
                        .appendTo(suggestionBox)
                        .on('click', function() {
                            var username = $(this).text();
                            // Focus on the editor before getting the selection
                            quill.focus();
                            setTimeout(function() { // Use setTimeout to allow the focus event to process
                                var cursorPosition = quill.getSelection(true).index;
                                if (cursorPosition === null || cursorPosition === undefined) {
                                    // Fallback if getSelection doesn't give a valid index
                                    cursorPosition = quill.getLength();
                                }
                                quill.insertText(cursorPosition, username + ' ');
                                quill.setSelection(cursorPosition + username.length + 1);
                                suggestionBox.hide(); // Hide after selection
                            }, 10);
                        });
                });

                var editorBounds = quill.getBounds(quill.getLength());
                suggestionBox.css({
                    position: 'absolute',
                    left: editorBounds.left + 'px',
                    top: (editorBounds.top + editorBounds.height) + 'px',
                    display: 'block'
                });
                editorBox.append( suggestionBox );
            } else {
                suggestionBox.hide(); // Hide if there are no suggestions
            }
        };


        // Event listener for typing in the Quill editor
        quill.on('text-change', function(delta, oldDelta, source) {
            if (source == 'user') {
                var text = quill.getText();
                var mentionCharIndex = text.lastIndexOf("@");
                
                if (mentionCharIndex > -1) {
                    var searchTerm = text.substring(mentionCharIndex);
                    if (searchTerm.length > 1) { // More than just '@'
                        fetchUserSuggestions(searchTerm);
                    }
                }
            }
        });

        // Event listener for selecting a suggestion
        // Assuming you have a way to select suggestions, like clicking on them or pressing enter
        $(document).on('click', '.suggestion-class', function() {
            var username = $(this).text();
            var cursorPosition = settings.editor.getSelection().index;
            settings.editor.insertText(cursorPosition, username + ' ');
            settings.editor.setSelection(cursorPosition + username.length + 1);
        });

        return this;
    };
    window.UserWallWPMention = $.fn.UserWallWPMention;
}(jQuery));

