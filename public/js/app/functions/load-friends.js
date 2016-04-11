(function (Twerds) {
  var getFriendsApi = Twerds.cached('loadFriends', function () {
    return fetch.GET('/friends')
  })

  function loadFriends () {
    return getFriendsApi()
      .then(function (response) {
        Twerds.setFriends(response.data)
        return response
      })
  }

  Twerds.loadFriends = loadFriends
}(window.Twerds))