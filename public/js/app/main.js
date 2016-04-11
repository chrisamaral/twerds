jQuery(function () {
  var $ = window.jQuery
  var dependencies = [
    '/js/app/el.js',
    '/js/app/http.js',
    '/js/app/cache.js',
    '/js/app/functions/load-friends.js',
    '/js/app/functions/mount-friends.js'
  ]

  function strapEvents () {
    $('.button-collapse').sideNav()
  }

  function setFriends (data) {
    Twerds.state.friends = data
    Twerds.renderFriendList(data.users)
  }

  function init () {
    strapEvents()

    inject(dependencies)
      .then(function () {
        Twerds.loadFriends()
      })
  }

  var Twerds = window.Twerds = {
    state: {},
    setFriends: setFriends,
    init: init
  }


  Twerds.init()
})

