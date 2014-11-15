(function($)
{

    $(document).ready(function()
    {
          var oldLive = jQuery.fn.live;
          jQuery.fn.live = function( types, data, fn ) {
            // migrateWarn("jQuery.fn.live() is deprecated");
            if ( oldLive ) {
              return oldLive.apply( this, arguments );
            }
            jQuery( this.context ).on( types, this.selector, data, fn );
            return this;
          };

          //BSA
          setTimeout(function()
          {
              var close = '<a href="#" class="close_bsap">X</a>';
              $('.bsap .bsa_it').prepend(close);
              $('.close_bsap').live('click', function(e)
              {
                  e.preventDefault();
                  $(this).closest('.bsap').hide();
              });
          }, 1500);

    });

})(jQuery);