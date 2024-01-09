jQuery(document).ready(function($) {
    var myModal = $('#myModal').threadModal({
        content: '<div class="author">New Author</div><div class="caption">New Caption</div>',
        showCloseBtn: true,
        openTransition: 'fade',
        closeTransition: 'fade',
        openSpeed: 500,
        closeSpeed: 500,
        onOpen: function() {
            console.log('Modal opened.');
        },
        onClose: function() {
            console.log('Modal closed.');
        }
    })[0];

    // Example usage of public methods:
    $('#openModalBtn').click(function() {
        myModal.openModal();
    });

    $('#closeModalBtn').click(function() {
        myModal.closeModal();
    });
});