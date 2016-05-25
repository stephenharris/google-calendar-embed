/**
 * TinyMCE plugin to look for the pasting of a Google calendar iframe code and converting it to a shortcode.
 */
tinymce.PluginManager.add( 'googlecalendarembed', function( editor ) {

    var googlecalendarembed = {};
    googlecalendarembed.convertContent = function( event ) {
        var content = event.content;
        content = content.replace(/&amp;/g, '&').replace(/&lt;/g, '<').replace(/&gt;/g, '>').replace(/&nbsp;/g, ' ').trim();
        regex = /(<iframe) ([^\]><]*?src="https:\/\/calendar.google.com\/calendar\/embed\\?[^\]><]*?)(><\/iframe>)/gi;
        if ( regex.test(content) ) {
            content = content.replace(regex, '[googlecalendar $2]');
            event.content = content
        }
    }
    //Convert any pasted content andany content on switching from Text to Visual editor
    editor.on( 'beforesetcontent',googlecalendarembed.convertContent );
});