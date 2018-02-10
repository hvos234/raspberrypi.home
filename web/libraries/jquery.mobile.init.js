$( document ).on( "mobileinit", function() {
  $.extend( $.mobile , {
    // Links that point to other domains or that have rel="external", data-ajax="false" or target attributes will not be loaded with Ajax. 
    // Instead, these links will cause a full page refresh with no animated transition.
    // see http://view.jquerymobile.com/master/demos/navigation-linking-pages/
    // In version 1.1, we added support for using data-ajax="false" on a parent container which allows you to exclude a large number of links from the Ajax navigation system. 
    // This avoids the need to add this attribute to every link in a container. 
    // To activate this functionality, $.mobile.ignoreContentEnabled must be set to true because this feature adds overhead we don't want to enable by default.
    ignoreContentEnabled : true,
    //pushStateEnabled: true
  });
});