(function (Twerds) {
  function refreshUI (response) {
    Twerds.mountFriendList(response.data.friends)
  }

  function loadFriends () {
    return fetch.GET('/friends').then(refreshUI)
  }

  Twerds.loadFriends = loadFriends
}(window.Twerds))