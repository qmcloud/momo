var utils = require('./utils')

function Chunk (uploader, file, offset) {
  utils.defineNonEnumerable(this, 'uploader', uploader)
  utils.defineNonEnumerable(this, 'file', file)
  utils.defineNonEnumerable(this, 'bytes', null)
  this.offset = offset
  this.tested = false
  this.retries = 0
  this.pendingRetry = false
  this.preprocessState = 0
  this.readState = 0
  this.loaded = 0
  this.total = 0
  this.chunkSize = this.uploader.opts.chunkSize
  this.startByte = this.offset * this.chunkSize
  this.endByte = this.computeEndByte()
  this.xhr = null
}

var STATUS = Chunk.STATUS = {
  PENDING: 'pending',
  UPLOADING: 'uploading',
  READING: 'reading',
  SUCCESS: 'success',
  ERROR: 'error',
  COMPLETE: 'complete',
  PROGRESS: 'progress',
  RETRY: 'retry'
}

utils.extend(Chunk.prototype, {

  _event: function (evt, args) {
    args = utils.toArray(arguments)
    args.unshift(this)
    this.file._chunkEvent.apply(this.file, args)
  },

  computeEndByte: function () {
    var endByte = Math.min(this.file.size, (this.offset + 1) * this.chunkSize)
    if (this.file.size - endByte < this.chunkSize && !this.uploader.opts.forceChunkSize) {
      // The last chunk will be bigger than the chunk size,
      // but less than 2 * this.chunkSize
      endByte = this.file.size
    }
    return endByte
  },

  getParams: function () {
    return {
      chunkNumber: this.offset + 1,
      chunkSize: this.uploader.opts.chunkSize,
      currentChunkSize: this.endByte - this.startByte,
      totalSize: this.file.size,
      identifier: this.file.uniqueIdentifier,
      filename: this.file.name,
      relativePath: this.file.relativePath,
      totalChunks: this.file.chunks.length
    }
  },

  getTarget: function (target, params) {
    if (!params.length) {
      return target
    }
    if (target.indexOf('?') < 0) {
      target += '?'
    } else {
      target += '&'
    }
    return target + params.join('&')
  },

  test: function () {
    this.xhr = new XMLHttpRequest()
    this.xhr.addEventListener('load', testHandler, false)
    this.xhr.addEventListener('error', testHandler, false)
    var testMethod = utils.evalOpts(this.uploader.opts.testMethod, this.file, this)
    var data = this.prepareXhrRequest(testMethod, true)
    this.xhr.send(data)

    var $ = this
    function testHandler (event) {
      var status = $.status(true)
      if (status === STATUS.ERROR) {
        $._event(status, $.message())
        $.uploader.uploadNextChunk()
      } else if (status === STATUS.SUCCESS) {
        $._event(status, $.message())
        $.tested = true
      } else if (!$.file.paused) {
        // Error might be caused by file pause method
        // Chunks does not exist on the server side
        $.tested = true
        $.send()
      }
    }
  },

  preprocessFinished: function () {
    // Compute the endByte after the preprocess function to allow an
    // implementer of preprocess to set the fileObj size
    this.endByte = this.computeEndByte()
    this.preprocessState = 2
    this.send()
  },

  readFinished: function (bytes) {
    this.readState = 2
    this.bytes = bytes
    this.send()
  },

  send: function () {
    var preprocess = this.uploader.opts.preprocess
    var read = this.uploader.opts.readFileFn
    if (utils.isFunction(preprocess)) {
      switch (this.preprocessState) {
        case 0:
          this.preprocessState = 1
          preprocess(this)
          return
        case 1:
          return
      }
    }
    switch (this.readState) {
      case 0:
        this.readState = 1
        read(this.file, this.file.fileType, this.startByte, this.endByte, this)
        return
      case 1:
        return
    }
    if (this.uploader.opts.testChunks && !this.tested) {
      this.test()
      return
    }

    this.loaded = 0
    this.total = 0
    this.pendingRetry = false

    // Set up request and listen for event
    this.xhr = new XMLHttpRequest()
    this.xhr.upload.addEventListener('progress', progressHandler, false)
    this.xhr.addEventListener('load', doneHandler, false)
    this.xhr.addEventListener('error', doneHandler, false)

    var uploadMethod = utils.evalOpts(this.uploader.opts.uploadMethod, this.file, this)
    var data = this.prepareXhrRequest(uploadMethod, false, this.uploader.opts.method, this.bytes)
    this.xhr.send(data)

    var $ = this
    function progressHandler (event) {
      if (event.lengthComputable) {
        $.loaded = event.loaded
        $.total = event.total
      }
      $._event(STATUS.PROGRESS, event)
    }

    function doneHandler (event) {
      var msg = $.message()
      $.processingResponse = true
      $.uploader.opts.processResponse(msg, function (err, res) {
        $.processingResponse = false
        if (!$.xhr) {
          return
        }
        $.processedState = {
          err: err,
          res: res
        }
        var status = $.status()
        if (status === STATUS.SUCCESS || status === STATUS.ERROR) {
          // delete this.data
          $._event(status, res)
          status === STATUS.ERROR && $.uploader.uploadNextChunk()
        } else {
          $._event(STATUS.RETRY, res)
          $.pendingRetry = true
          $.abort()
          $.retries++
          var retryInterval = $.uploader.opts.chunkRetryInterval
          if (retryInterval !== null) {
            setTimeout(function () {
              $.send()
            }, retryInterval)
          } else {
            $.send()
          }
        }
      }, $.file, $)
    }
  },

  abort: function () {
    var xhr = this.xhr
    this.xhr = null
    this.processingResponse = false
    this.processedState = null
    if (xhr) {
      xhr.abort()
    }
  },

  status: function (isTest) {
    if (this.readState === 1) {
      return STATUS.READING
    } else if (this.pendingRetry || this.preprocessState === 1) {
      // if pending retry then that's effectively the same as actively uploading,
      // there might just be a slight delay before the retry starts
      return STATUS.UPLOADING
    } else if (!this.xhr) {
      return STATUS.PENDING
    } else if (this.xhr.readyState < 4 || this.processingResponse) {
      // Status is really 'OPENED', 'HEADERS_RECEIVED'
      // or 'LOADING' - meaning that stuff is happening
      return STATUS.UPLOADING
    } else {
      var _status
      if (this.uploader.opts.successStatuses.indexOf(this.xhr.status) > -1) {
        // HTTP 200, perfect
        // HTTP 202 Accepted - The request has been accepted for processing, but the processing has not been completed.
        _status = STATUS.SUCCESS
      } else if (this.uploader.opts.permanentErrors.indexOf(this.xhr.status) > -1 ||
          !isTest && this.retries >= this.uploader.opts.maxChunkRetries) {
        // HTTP 415/500/501, permanent error
        _status = STATUS.ERROR
      } else {
        // this should never happen, but we'll reset and queue a retry
        // a likely case for this would be 503 service unavailable
        this.abort()
        _status = STATUS.PENDING
      }
      var processedState = this.processedState
      if (processedState && processedState.err) {
        _status = STATUS.ERROR
      }
      return _status
    }
  },

  message: function () {
    return this.xhr ? this.xhr.responseText : ''
  },

  progress: function () {
    if (this.pendingRetry) {
      return 0
    }
    var s = this.status()
    if (s === STATUS.SUCCESS || s === STATUS.ERROR) {
      return 1
    } else if (s === STATUS.PENDING) {
      return 0
    } else {
      return this.total > 0 ? this.loaded / this.total : 0
    }
  },

  sizeUploaded: function () {
    var size = this.endByte - this.startByte
    // can't return only chunk.loaded value, because it is bigger than chunk size
    if (this.status() !== STATUS.SUCCESS) {
      size = this.progress() * size
    }
    return size
  },

  prepareXhrRequest: function (method, isTest, paramsMethod, blob) {
    // Add data from the query options
    var query = utils.evalOpts(this.uploader.opts.query, this.file, this, isTest)
    query = utils.extend(this.getParams(), query)

    // processParams
    query = this.uploader.opts.processParams(query, this.file, this, isTest)

    var target = utils.evalOpts(this.uploader.opts.target, this.file, this, isTest)
    var data = null
    if (method === 'GET' || paramsMethod === 'octet') {
      // Add data from the query options
      var params = []
      utils.each(query, function (v, k) {
        params.push([encodeURIComponent(k), encodeURIComponent(v)].join('='))
      })
      target = this.getTarget(target, params)
      data = blob || null
    } else {
      // Add data from the query options
      data = new FormData()
      utils.each(query, function (v, k) {
        data.append(k, v)
      })
      if (typeof blob !== 'undefined') {
        data.append(this.uploader.opts.fileParameterName, blob, this.file.name)
      }
    }

    this.xhr.open(method, target, true)
    this.xhr.withCredentials = this.uploader.opts.withCredentials

    // Add data from header options
    utils.each(utils.evalOpts(this.uploader.opts.headers, this.file, this, isTest), function (v, k) {
      this.xhr.setRequestHeader(k, v)
    }, this)

    return data
  }

})

module.exports = Chunk
