// Sample data for multiple threads and comments
var threadsData = [
    {
        thread_content: "This is the first thread.",
        thread_author: "User1",
        comments: [
            {
                comment_content: "Comment on the first thread.",
                comment_author: "User2"
            },
            {
                comment_content: "Another comment on the first thread.",
                comment_author: "User3"
            }
        ]
    },
    {
        thread_content: "This is the second thread.",
        thread_author: "User4",
        comments: [
            {
                comment_content: "Comment on the second thread.",
                comment_author: "User5"
            }
        ]
    }
];

// Function to render threads using wp.template
function renderThreads(threads, templateId, parentElement) {
    var template = wp.template( templateId );
    parentElement.append( template( { "threads" : threads } )  );
   /* jQuery( threads ).each(function(index, thread) {
        console.log( thread );
        //var template = wp.template( 'tmpl-reddit-style-thread-template' );
        // el.html( template( { name: "World" } ) );
        var template = wp.template( templateId );
        //console.log( template( thread ) );
        parentElement.append( 'test' );
        //var threadHTML = wp.template(templateId);
        //console.log( threadHTML );
        //parentElement.append( threadHTML(  thread ) );
    });*/
}

// Find the thread container in your main HTML and render threads
var threadContainer = jQuery("#reddit-container");

renderThreads(threadsData, "reddit-style-thread-template", threadContainer);
