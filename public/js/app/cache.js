(function (Twerds) {

  Twerds.cache = {}

  function persist () {
    try {
      window.sessionStorage.twerdsCache = JSON.stringify(Twerds.cache)
    } catch (e) {

    }
  }
  try {
    var o = JSON.parse(window.sessionStorage.twerdsCache)
    if (typeof o === 'object') Twerds.cache = o
  } catch (e) {

  }

  /**
   *
   * @param {String} name
   * @param {Function} fn
   * @returns {Promise}
   */
  Twerds.cached = function (name, fn) {
    function lookup () {
      var args = []
      for (var i = 0; i < arguments.length; i++) args.push(arguments[i])
      var signature = name + '(' + args.join(', ') + ')'

      if (Twerds.cache[signature]) {
        return Promise.resolve({
          data: Twerds.cache[signature]
        })
      }

      return fn.apply(null, args)
        .then(function (response) {
          Twerds.cache[signature] = response.data
          persist()
          return response
        })
    }

    return lookup
  }
}(window.Twerds))