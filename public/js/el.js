(function () {
  function isUpperCase (letter) {
    return letter !== letter.toLowerCase()
  }

  function looksLikeAnEventHandler (name) {
    return name.length > 2 && name.substr(0, 2) === 'on' && isUpperCase(name[2])
  }

  /**
   *
   * @param {Element} elem
   * @param {String} name
   * @param val
   */
  function setAttribute (elem, name, val) {
    if (looksLikeAnEventHandler(name)) {
      elem.addEventListener(
        name.substr(2).toLowerCase(),
        val
      )
    } else if (name === 'className' || name === 'value' || typeof val !== 'string') {
      elem[name] = val
    } else {
      elem.setAttribute(name, val)
    }
  }

  /**
   *
   * @param config
   * @returns {Element}
   */
  function createComplexElement (config) {
    var tag = config.tag
    var children = config.children

    children = Array.isArray(children)
      ? children
      : [children]

    delete config.children
    delete config.tag

    var elem = document.createElement(tag)

    for (var key in config) {
      if (config.hasOwnProperty(key)) {
        setAttribute(elem, key, config[key])
      }
    }

    for (var i = 0; i < children.length; i++) {
      if (!children[i]) continue

      elem.appendChild(createElement(children[i]))
    }

    return elem
  }

  /**
   *
   * @param {Object|String} config
   * @returns {Element|Comment|Text}
   */
  function createElement (config) {
    if (!config) return document.createComment('void')

    if (typeof config === 'object') {
      return createComplexElement(config)
    }

    return document.createTextNode(config)
  }

  /**
   *
   * @param {Element} target
   */
  function empty (target) {
    while (target.firstChild) {
      target.removeChild(target.firstChild);
    }
  }

  function wrap (config, target) {
    if (!target) return createElement(config)

    config = Array.isArray(config)
      ? config
      : [config]


    empty(target)

    for (var i = 0; i < config.length; i++) {
      if (config[i]) target.appendChild(createElement(config[i]))
    }
  }

  window.el = wrap
}())
