(function () {
  var loadedScripts = {}
  var onTheFly = {}

  /**
   * insert script tag if not present
   * @param {string} src script src
   * @returns {Promise} promise that resolves once the script has been loaded
   */
  function insertScript (src) {
    return new Promise(function (resolve, reject) {
      if (loadedScripts[src]) return resolve()

      var script = window.document.createElement('script')

      script.src = src

      script.onload = function () {
        loadedScripts[src] = true
        delete onTheFly[src]
        resolve()
      }

      script.onerror = function (err) {
        delete onTheFly[src]
        reject(err)
      }

      window.document.body.appendChild(script)
    })
  }

  /**
   * load src as a script tag avoiding duplication
   * @param {string} src script src
   * @returns {Promise} promise that resolves once the script has been loaded
   */
  function loadScript (src) {
    if (!onTheFly[src]) {
      onTheFly[src] = insertScript(src)
    }
    return onTheFly[src]
  }

  function loadMultipleScripts (scripts) {
    if (!Array.isArray(scripts)) {
      scripts = []
      for (var i = 0; i < arguments.length; i++) scripts.push(arguments[i])
    }

    return Promise.all(scripts.map(function (src) {
      return loadScript(src)
    }))
  }

  window.inject = loadMultipleScripts
}())