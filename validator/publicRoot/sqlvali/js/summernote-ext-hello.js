(function (factory) {
  /* global define */
  if (typeof define === 'function' && define.amd) {
    // AMD. Register as an anonymous module.
    define(['publicRoot/sqlvali/js/jquery'], factory);
  } else {
    // Browser globals: jQuery
    factory(window.jQuery);
  }
}(function ($) {
  // template
  var tmpl = $.summernote.renderer.getTemplate();

  /**
   * @class plugin.hello 
   * 
   * Hello Plugin  
   */
  $.summernote.addPlugin({
    /** @property {String} name name of plugin */
    name: 'sqlvali',
    /** 
     * @property {Object} buttons 
     * @property {Function} buttons.hello   function to make button
     * @property {Function} buttons.helloDropdown   function to make button
     * @property {Function} buttons.helloImage   function to make button
     */
    buttons: { // buttons
      hint: function () {

        var $b = tmpl.button('Insert SQL-Hint', {
          event : 'hint',
          title: 'Insert SQL-Hint'
        });
        $($b).html('abc');
        return $b;
      }
    },

    /**
     * @property {Object} events 
     * @property {Function} events.hello  run function when button that has a 'hello' event name  fires click
     * @property {Function} events.helloDropdown run function when button that has a 'helloDropdown' event name  fires click
     * @property {Function} events.helloImage run function when button that has a 'helloImage' event name  fires click
     */
    events: { // events
      hint: function (event, editor, layoutInfo) {
        var $editable = layoutInfo.editable();
        var $table = '';
        var $hints = '<p>{hints}<br>\n';
        do {
            $table = prompt("Insert name of table to display. Leave empty, if you already entered all.");
            if($table != null && $table != '') {
                $hints += '&nbsp;&nbsp;{hint title:' + $table + '}select * from ' + $table + '{/hint}<br>\n';
            } else {
                break;
            }
        } while(true);
        $hints += '{/hints}</p>';
        $node = $($hints);
        // Call insertText with 'hello'
        editor.insertNode($editable, $node[0]);
      },
      helloDropdown: function (event, editor, layoutInfo, value) {
        // Get current editable node
        var $editable = layoutInfo.editable();

        // Call insertText with 'hello'
        editor.insertText($editable, 'hello ' + value + '!!!!');
      },
      helloImage : function (event, editor, layoutInfo) {
        var $editable = layoutInfo.editable();

        var img = $('<img src="http://upload.wikimedia.org/wikipedia/commons/b/b0/NewTux.svg" />');
        editor.insertNode($editable, img[0]);
      }
    }
  });
}));
