(function (Twerds) {
  var listElem = document.getElementById('friend-list')

  /**
   *
   * @param {Event} e
   */
  function onClickUser (e) {
    e.preventDefault()
    Materialize.toast('Please, stop it.', 3000)
  }

  function li (friend) {
    return {
      tag: 'li',
      'data-id': friend.screen_name,
      className: 'collection-item avatar',
      children: [
        {
          tag: 'a',
          href: 'https://twitter.com/' + friend.screen_name,
          onClick: onClickUser,
          children: {
            tag: 'img',
            className: 'circle',
            src: friend.avatar
          }
        },
        {
          tag: 'div',
          className: 'title',
          children: [
            {
              tag: 'strong',
              children: friend.screen_name
            },
            ' - ' + friend.name
          ]
        }
      ]
    }
  }

  /**
   *
   * @param {Array} friends
   */
  function mountFriendList (friends) {
    el(friends.map(li), listElem)
  }

  Twerds.mountFriendList = mountFriendList
}(window.Twerds, window.jQuery))