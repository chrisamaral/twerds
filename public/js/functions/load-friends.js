(function (Twerds) {
  function refreshUI (response) {
    var friends = Twerds.state.friends = response.data

    Twerds.mountFriendList(friends.users)
  }

  function loadFriends () {
    return fetch.GET('/friends').then(refreshUI)
  }

  Twerds.loadFriends = loadFriends
}(window.Twerds))