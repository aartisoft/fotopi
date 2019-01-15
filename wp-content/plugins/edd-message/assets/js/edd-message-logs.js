var EDD_Message_LogModal;

(function ($) {
    'use strict';

    var l10n = typeof EDD_Message_Data_Logs != 'undefined' ? EDD_Message_Data_Logs.l10n : {},
        api = EDD_Message_LogModal = {

        /**
         * Modal jQuery object.
         *
         * @since 1.1
         */
        $modal: null,

        /**
         * Modal loading jQuery object.
         *
         * @since 1.1
         */
        $modal_loading: null,

        /**
         * Modal content jQuery object.
         *
         * @since 1.1
         */
        $modal_content: null,

        /**
         * Initialize.
         *
         * @since 1.1
         */
        init: function () {

            api.get_elements();
            api.setup_handlers();
        },

        /**
         * Gets all elements.
         *
         * @since 1.1
         */
        get_elements: function () {

            api.$modal = $('#edd-message-log-message-modal');

            if (api.$modal.length) {

                api.$modal_loading = api.$modal.find('.edd-message-log-message-modal-loading');
                api.$modal_content = api.$modal.find('.edd-message-log-message-modal-content');
            }
        },

        /**
         * Sets up event handles.
         *
         * @since 1.1
         */
        setup_handlers: function () {

            $(document).on('click', '[data-log-view-message]', api.view_message_button);
            $(document).on('click', '[data-log-modal-close]', api.close_modal_button);
            $(document).on('click', '[data-log-delete]', api.log_delete_button);
        },

        /**
         * Fires when clicking the view message button
         *
         * @since 1.1
         *
         * @param e
         */
        view_message_button: function (e) {

            var log_ID = $(this).attr('data-log-id');

            if (!log_ID) {

                return;
            }

            api.view_message(log_ID);
        },

        /**
         * Opens and loads the message.
         *
         * @since 1.1
         */
        view_message: function (log_ID) {

            api.open_modal();

            $.post(
                ajaxurl,
                {
                    action: 'edd_message_log_get_message',
                    log_ID: log_ID
                },
                function (response) {

                    api.$modal_loading.hide();

                    if (typeof response.success == 'undefined' || !response.success) {

                        if (typeof response.data.error != 'undefined') {

                            api.$modal_content.html('<div class="notice notice-error inline"><p>'
                                + response.data.error
                                + '</p></div>');
                        }

                        return;
                    }

                    api.$modal_content.html(response.data.message);
                }
            )
        },

        /**
         * Fires on clicking the close modal button.
         *
         * @since 1.1
         *
         * @param e
         */
        close_modal_button: function (e) {

            e.preventDefault();

            api.close_modal();
        },

        /**
         * Opens the modal.
         *
         * @since 1.1
         */
        open_modal: function () {

            api.$modal.show();
        },

        /**
         * Closes the modal.
         *
         * @since 1.1
         */
        close_modal: function () {

            api.$modal_content.html('');
            api.$modal_loading.show();
            api.$modal.hide();
        },

        /**
         * Fires on clicking the delete log button.
         *
         * @since 1.1
         */
        log_delete_button: function (e) {

            var log_ID = $(this).attr('data-log-id'),
                $row = $(this).closest('tr');

            if (!log_ID || !$row.length) {

                return;
            }

            api.log_delete(log_ID, $row);
        },

        /**
         * Deletes a log.
         *
         * @since 1.1
         *
         * @param log_ID
         * @param $row
         */
        log_delete: function (log_ID, $row) {

            $.post(
                ajaxurl,
                {
                    action: 'edd_message_delete_message',
                    log_ID: log_ID
                },
                function (response) {

                    if (typeof response.success == 'undefined' || !response.success) {

                        if (typeof response.data != 'undefined' && typeof response.data.error != 'undefined') {

                            alert(response.data.error);

                        } else if (typeof l10n.could_not_delete != 'undefined') {

                            alert(l10n.could_not_delete);
                        }

                        return;
                    }

                    $row.css('background', '#f1a49b').fadeOut();
                }
            )
        }
    }

    $(api.init);

})(jQuery);