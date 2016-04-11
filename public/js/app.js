jQuery(function () {
  var $ = window.jQuery
  var dependencies = [
    '/js/el.js',
    '/js/http.js',
    '/js/functions/load-friends.js',
    '/js/functions/mount-friends.js'
  ]

  function strapEvents () {
    $('.button-collapse').sideNav()
  }

  var Twerds = window.Twerds = {
    state: {},
    init: function () {
      strapEvents()

      inject(dependencies)
        .then(function () {
          Twerds.loadFriends()
        })
    }
  }


  Twerds.init()
})

