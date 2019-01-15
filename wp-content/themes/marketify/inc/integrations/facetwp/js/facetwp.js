(function() {
  jQuery(function($) {
    var el;
    el = '.facetwp-template.edd_downloads_list';
    return $(document).on('facetwp-loaded', function() {
      $(el).find($('.edd_download.content-grid-download')).attr('style', '');
      if (typeof WPPlaylistView === 'function') {
        $('.wp-playlist').each(function() {
          return new WPPlaylistView({
            el: this
          });
        });
      }
      return $('#edd_download_pagination').remove();
    });
  });

}).call(this);
