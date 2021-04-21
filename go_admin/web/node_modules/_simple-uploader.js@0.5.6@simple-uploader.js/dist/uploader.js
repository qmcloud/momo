/*!
 * Uploader - Uploader library implements html5 file upload and provides multiple simultaneous, stable, fault tolerant and resumable uploads
 * @version v0.5.6
 * @author dolymood <dolymood@gmail.com>
 * @link https://github.com/simple-uploader/Uploader
 * @license MIT
 */
!function(e){if("object"==typeof exports)module.exports=e();else if("function"==typeof define&&define.amd)define(e);else{var f;"undefined"!=typeof window?f=window:"undefined"!=typeof global?f=global:"undefined"!=typeof self&&(f=self),f.Uploader=e()}}(function(){var define,module,exports;return (function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);throw new Error("Cannot find module '"+o+"'")}var f=n[o]={exports:{}};t[o][0].call(f.exports,function(e){var n=t[o][1][e];return s(n?n:e)},f,f.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(_dereq_,module,exports){
var utils = _dereq_('./utils')

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

},{"./utils":5}],2:[function(_dereq_,module,exports){
var each = _dereq_('./utils').each

var event = {

  _eventData: null,

  on: function (name, func) {
    if (!this._eventData) this._eventData = {}
    if (!this._eventData[name]) this._eventData[name] = []
    var listened = false
    each(this._eventData[name], function (fuc) {
      if (fuc === func) {
        listened = true
        return false
      }
    })
    if (!listened) {
      this._eventData[name].push(func)
    }
  },

  off: function (name, func) {
    if (!this._eventData) this._eventData = {}
    if (!this._eventData[name] || !this._eventData[name].length) return
    if (func) {
      each(this._eventData[name], function (fuc, i) {
        if (fuc === func) {
          this._eventData[name].splice(i, 1)
          return false
        }
      }, this)
    } else {
      this._eventData[name] = []
    }
  },

  trigger: function (name) {
    if (!this._eventData) this._eventData = {}
    if (!this._eventData[name]) return true
    var args = this._eventData[name].slice.call(arguments, 1)
    var preventDefault = false
    each(this._eventData[name], function (fuc) {
      preventDefault = fuc.apply(this, args) === false || preventDefault
    }, this)
    return !preventDefault
  }
}

module.exports = event

},{"./utils":5}],3:[function(_dereq_,module,exports){
var utils = _dereq_('./utils')
var event = _dereq_('./event')
var File = _dereq_('./file')
var Chunk = _dereq_('./chunk')

var version = '0.5.6'

var isServer = typeof window === 'undefined'

// ie10+
var ie10plus = isServer ? false : window.navigator.msPointerEnabled
var support = (function () {
  if (isServer) {
    return false
  }
  var sliceName = 'slice'
  var _support = utils.isDefined(window.File) && utils.isDefined(window.Blob) &&
                utils.isDefined(window.FileList)
  var bproto = null
  if (_support) {
    bproto = window.Blob.prototype
    utils.each(['slice', 'webkitSlice', 'mozSlice'], function (n) {
      if (bproto[n]) {
        sliceName = n
        return false
      }
    })
    _support = !!bproto[sliceName]
  }
  if (_support) Uploader.sliceName = sliceName
  bproto = null
  return _support
})()

var supportDirectory = (function () {
  if (isServer) {
    return false
  }
  var input = window.document.createElement('input')
  input.type = 'file'
  var sd = 'webkitdirectory' in input || 'directory' in input
  input = null
  return sd
})()

function Uploader (opts) {
  this.support = support
  /* istanbul ignore if */
  if (!this.support) {
    return
  }
  this.supportDirectory = supportDirectory
  utils.defineNonEnumerable(this, 'filePaths', {})
  this.opts = utils.extend({}, Uploader.defaults, opts || {})

  this.preventEvent = utils.bind(this._preventEvent, this)

  File.call(this, this)
}

/**
 * Default read function using the webAPI
 *
 * @function webAPIFileRead(fileObj, fileType, startByte, endByte, chunk)
 *
 */
var webAPIFileRead = function (fileObj, fileType, startByte, endByte, chunk) {
  chunk.readFinished(fileObj.file[Uploader.sliceName](startByte, endByte, fileType))
}

Uploader.version = version

Uploader.defaults = {
  chunkSize: 1024 * 1024,
  forceChunkSize: false,
  simultaneousUploads: 3,
  singleFile: false,
  fileParameterName: 'file',
  progressCallbacksInterval: 500,
  speedSmoothingFactor: 0.1,
  query: {},
  headers: {},
  withCredentials: false,
  preprocess: null,
  method: 'multipart',
  testMethod: 'GET',
  uploadMethod: 'POST',
  prioritizeFirstAndLastChunk: false,
  allowDuplicateUploads: false,
  target: '/',
  testChunks: true,
  generateUniqueIdentifier: null,
  maxChunkRetries: 0,
  chunkRetryInterval: null,
  permanentErrors: [404, 415, 500, 501],
  successStatuses: [200, 201, 202],
  onDropStopPropagation: false,
  initFileFn: null,
  readFileFn: webAPIFileRead,
  checkChunkUploadedByResponse: null,
  initialPaused: false,
  processResponse: function (response, cb) {
    cb(null, response)
  },
  processParams: function (params) {
    return params
  }
}

Uploader.utils = utils
Uploader.event = event
Uploader.File = File
Uploader.Chunk = Chunk

// inherit file
Uploader.prototype = utils.extend({}, File.prototype)
// inherit event
utils.extend(Uploader.prototype, event)
utils.extend(Uploader.prototype, {

  constructor: Uploader,

  _trigger: function (name) {
    var args = utils.toArray(arguments)
    var preventDefault = !this.trigger.apply(this, arguments)
    if (name !== 'catchAll') {
      args.unshift('catchAll')
      preventDefault = !this.trigger.apply(this, args) || preventDefault
    }
    return !preventDefault
  },

  _triggerAsync: function () {
    var args = arguments
    utils.nextTick(function () {
      this._trigger.apply(this, args)
    }, this)
  },

  addFiles: function (files, evt) {
    var _files = []
    var oldFileListLen = this.fileList.length
    utils.each(files, function (file) {
      // Uploading empty file IE10/IE11 hangs indefinitely
      // Directories have size `0` and name `.`
      // Ignore already added files if opts.allowDuplicateUploads is set to false
      if ((!ie10plus || ie10plus && file.size > 0) && !(file.size % 4096 === 0 && (file.name === '.' || file.fileName === '.'))) {
        var uniqueIdentifier = this.generateUniqueIdentifier(file)
        if (this.opts.allowDuplicateUploads || !this.getFromUniqueIdentifier(uniqueIdentifier)) {
          var _file = new File(this, file, this)
          _file.uniqueIdentifier = uniqueIdentifier
          if (this._trigger('fileAdded', _file, evt)) {
            _files.push(_file)
          } else {
            File.prototype.removeFile.call(this, _file)
          }
        }
      }
    }, this)
    // get new fileList
    var newFileList = this.fileList.slice(oldFileListLen)
    if (this._trigger('filesAdded', _files, newFileList, evt)) {
      utils.each(_files, function (file) {
        if (this.opts.singleFile && this.files.length > 0) {
          this.removeFile(this.files[0])
        }
        this.files.push(file)
      }, this)
      this._trigger('filesSubmitted', _files, newFileList, evt)
    } else {
      utils.each(newFileList, function (file) {
        File.prototype.removeFile.call(this, file)
      }, this)
    }
  },

  addFile: function (file, evt) {
    this.addFiles([file], evt)
  },

  cancel: function () {
    for (var i = this.fileList.length - 1; i >= 0; i--) {
      this.fileList[i].cancel()
    }
  },

  removeFile: function (file) {
    File.prototype.removeFile.call(this, file)
    this._trigger('fileRemoved', file)
  },

  generateUniqueIdentifier: function (file) {
    var custom = this.opts.generateUniqueIdentifier
    if (utils.isFunction(custom)) {
      return custom(file)
    }
    /* istanbul ignore next */
    // Some confusion in different versions of Firefox
    var relativePath = file.relativePath || file.webkitRelativePath || file.fileName || file.name
    /* istanbul ignore next */
    return file.size + '-' + relativePath.replace(/[^0-9a-zA-Z_-]/img, '')
  },

  getFromUniqueIdentifier: function (uniqueIdentifier) {
    var ret = false
    utils.each(this.files, function (file) {
      if (file.uniqueIdentifier === uniqueIdentifier) {
        ret = file
        return false
      }
    })
    return ret
  },

  uploadNextChunk: function (preventEvents) {
    var found = false
    var pendingStatus = Chunk.STATUS.PENDING
    var checkChunkUploaded = this.uploader.opts.checkChunkUploadedByResponse
    if (this.opts.prioritizeFirstAndLastChunk) {
      utils.each(this.files, function (file) {
        if (file.paused) {
          return
        }
        if (checkChunkUploaded && !file._firstResponse && file.isUploading()) {
          // waiting for current file's first chunk response
          return
        }
        if (file.chunks.length && file.chunks[0].status() === pendingStatus) {
          file.chunks[0].send()
          found = true
          return false
        }
        if (file.chunks.length > 1 && file.chunks[file.chunks.length - 1].status() === pendingStatus) {
          file.chunks[file.chunks.length - 1].send()
          found = true
          return false
        }
      })
      if (found) {
        return found
      }
    }

    // Now, simply look for the next, best thing to upload
    utils.each(this.files, function (file) {
      if (!file.paused) {
        if (checkChunkUploaded && !file._firstResponse && file.isUploading()) {
          // waiting for current file's first chunk response
          return
        }
        utils.each(file.chunks, function (chunk) {
          if (chunk.status() === pendingStatus) {
            chunk.send()
            found = true
            return false
          }
        })
      }
      if (found) {
        return false
      }
    })
    if (found) {
      return true
    }

    // The are no more outstanding chunks to upload, check is everything is done
    var outstanding = false
    utils.each(this.files, function (file) {
      if (!file.isComplete()) {
        outstanding = true
        return false
      }
    })
    // should check files now
    // if now files in list
    // should not trigger complete event
    if (!outstanding && !preventEvents && this.files.length) {
      // All chunks have been uploaded, complete
      this._triggerAsync('complete')
    }
    return outstanding
  },

  upload: function (preventEvents) {
    // Make sure we don't start too many uploads at once
    var ret = this._shouldUploadNext()
    if (ret === false) {
      return
    }
    !preventEvents && this._trigger('uploadStart')
    var started = false
    for (var num = 1; num <= this.opts.simultaneousUploads - ret; num++) {
      started = this.uploadNextChunk(!preventEvents) || started
      if (!started && preventEvents) {
        // completed
        break
      }
    }
    if (!started && !preventEvents) {
      this._triggerAsync('complete')
    }
  },

  /**
   * should upload next chunk
   * @function
   * @returns {Boolean|Number}
   */
  _shouldUploadNext: function () {
    var num = 0
    var should = true
    var simultaneousUploads = this.opts.simultaneousUploads
    var uploadingStatus = Chunk.STATUS.UPLOADING
    utils.each(this.files, function (file) {
      utils.each(file.chunks, function (chunk) {
        if (chunk.status() === uploadingStatus) {
          num++
          if (num >= simultaneousUploads) {
            should = false
            return false
          }
        }
      })
      return should
    })
    // if should is true then return uploading chunks's length
    return should && num
  },

  /**
   * Assign a browse action to one or more DOM nodes.
   * @function
   * @param {Element|Array.<Element>} domNodes
   * @param {boolean} isDirectory Pass in true to allow directories to
   * @param {boolean} singleFile prevent multi file upload
   * @param {Object} attributes set custom attributes:
   *  http://www.w3.org/TR/html-markup/input.file.html#input.file-attributes
   *  eg: accept: 'image/*'
   * be selected (Chrome only).
   */
  assignBrowse: function (domNodes, isDirectory, singleFile, attributes) {
    if (typeof domNodes.length === 'undefined') {
      domNodes = [domNodes]
    }

    utils.each(domNodes, function (domNode) {
      var input
      if (domNode.tagName === 'INPUT' && domNode.type === 'file') {
        input = domNode
      } else {
        input = document.createElement('input')
        input.setAttribute('type', 'file')
        // display:none - not working in opera 12
        utils.extend(input.style, {
          visibility: 'hidden',
          position: 'absolute',
          width: '1px',
          height: '1px'
        })
        // for opera 12 browser, input must be assigned to a document
        domNode.appendChild(input)
        // https://developer.mozilla.org/en/using_files_from_web_applications)
        // event listener is executed two times
        // first one - original mouse click event
        // second - input.click(), input is inside domNode
        domNode.addEventListener('click', function (e) {
          if (domNode.tagName.toLowerCase() === 'label') {
            return
          }
          input.click()
        }, false)
      }
      if (!this.opts.singleFile && !singleFile) {
        input.setAttribute('multiple', 'multiple')
      }
      if (isDirectory) {
        input.setAttribute('webkitdirectory', 'webkitdirectory')
      }
      attributes && utils.each(attributes, function (value, key) {
        input.setAttribute(key, value)
      })
      // When new files are added, simply append them to the overall list
      var that = this
      input.addEventListener('change', function (e) {
        that._trigger(e.type, e)
        if (e.target.value) {
          that.addFiles(e.target.files, e)
          e.target.value = ''
        }
      }, false)
    }, this)
  },

  onDrop: function (evt) {
    this._trigger(evt.type, evt)
    if (this.opts.onDropStopPropagation) {
      evt.stopPropagation()
    }
    evt.preventDefault()
    this._parseDataTransfer(evt.dataTransfer, evt)
  },

  _parseDataTransfer: function (dataTransfer, evt) {
    if (dataTransfer.items && dataTransfer.items[0] &&
      dataTransfer.items[0].webkitGetAsEntry) {
      this.webkitReadDataTransfer(dataTransfer, evt)
    } else {
      this.addFiles(dataTransfer.files, evt)
    }
  },

  webkitReadDataTransfer: function (dataTransfer, evt) {
    var self = this
    var queue = dataTransfer.items.length
    var files = []
    utils.each(dataTransfer.items, function (item) {
      var entry = item.webkitGetAsEntry()
      if (!entry) {
        decrement()
        return
      }
      if (entry.isFile) {
        // due to a bug in Chrome's File System API impl - #149735
        fileReadSuccess(item.getAsFile(), entry.fullPath)
      } else {
        readDirectory(entry.createReader())
      }
    })
    function readDirectory (reader) {
      reader.readEntries(function (entries) {
        if (entries.length) {
          queue += entries.length
          utils.each(entries, function (entry) {
            if (entry.isFile) {
              var fullPath = entry.fullPath
              entry.file(function (file) {
                fileReadSuccess(file, fullPath)
              }, readError)
            } else if (entry.isDirectory) {
              readDirectory(entry.createReader())
            }
          })
          readDirectory(reader)
        } else {
          decrement()
        }
      }, readError)
    }
    function fileReadSuccess (file, fullPath) {
      // relative path should not start with "/"
      file.relativePath = fullPath.substring(1)
      files.push(file)
      decrement()
    }
    function readError (fileError) {
      throw fileError
    }
    function decrement () {
      if (--queue === 0) {
        self.addFiles(files, evt)
      }
    }
  },

  _assignHelper: function (domNodes, handles, remove) {
    if (typeof domNodes.length === 'undefined') {
      domNodes = [domNodes]
    }
    var evtMethod = remove ? 'removeEventListener' : 'addEventListener'
    utils.each(domNodes, function (domNode) {
      utils.each(handles, function (handler, name) {
        domNode[evtMethod](name, handler, false)
      }, this)
    }, this)
  },

  _preventEvent: function (e) {
    utils.preventEvent(e)
    this._trigger(e.type, e)
  },

  /**
   * Assign one or more DOM nodes as a drop target.
   * @function
   * @param {Element|Array.<Element>} domNodes
   */
  assignDrop: function (domNodes) {
    this._onDrop = utils.bind(this.onDrop, this)
    this._assignHelper(domNodes, {
      dragover: this.preventEvent,
      dragenter: this.preventEvent,
      dragleave: this.preventEvent,
      drop: this._onDrop
    })
  },

  /**
   * Un-assign drop event from DOM nodes
   * @function
   * @param domNodes
   */
  unAssignDrop: function (domNodes) {
    this._assignHelper(domNodes, {
      dragover: this.preventEvent,
      dragenter: this.preventEvent,
      dragleave: this.preventEvent,
      drop: this._onDrop
    }, true)
    this._onDrop = null
  }
})

module.exports = Uploader

},{"./chunk":1,"./event":2,"./file":4,"./utils":5}],4:[function(_dereq_,module,exports){
var utils = _dereq_('./utils')
var Chunk = _dereq_('./chunk')

function File (uploader, file, parent) {
  utils.defineNonEnumerable(this, 'uploader', uploader)
  this.isRoot = this.isFolder = uploader === this
  utils.defineNonEnumerable(this, 'parent', parent || null)
  utils.defineNonEnumerable(this, 'files', [])
  utils.defineNonEnumerable(this, 'fileList', [])
  utils.defineNonEnumerable(this, 'chunks', [])
  utils.defineNonEnumerable(this, '_errorFiles', [])
  utils.defineNonEnumerable(this, 'file', null)
  this.id = utils.uid()

  if (this.isRoot || !file) {
    this.file = null
  } else {
    if (utils.isString(file)) {
      // folder
      this.isFolder = true
      this.file = null
      this.path = file
      if (this.parent.path) {
        file = file.substr(this.parent.path.length)
      }
      this.name = file.charAt(file.length - 1) === '/' ? file.substr(0, file.length - 1) : file
    } else {
      this.file = file
      this.fileType = this.file.type
      this.name = file.fileName || file.name
      this.size = file.size
      this.relativePath = file.relativePath || file.webkitRelativePath || this.name
      this._parseFile()
    }
  }

  this.paused = uploader.opts.initialPaused
  this.error = false
  this.allError = false
  this.aborted = false
  this.completed = false
  this.averageSpeed = 0
  this.currentSpeed = 0
  this._lastProgressCallback = Date.now()
  this._prevUploadedSize = 0
  this._prevProgress = 0

  this.bootstrap()
}

utils.extend(File.prototype, {

  _parseFile: function () {
    var ppaths = parsePaths(this.relativePath)
    if (ppaths.length) {
      var filePaths = this.uploader.filePaths
      utils.each(ppaths, function (path, i) {
        var folderFile = filePaths[path]
        if (!folderFile) {
          folderFile = new File(this.uploader, path, this.parent)
          filePaths[path] = folderFile
          this._updateParentFileList(folderFile)
        }
        this.parent = folderFile
        folderFile.files.push(this)
        if (!ppaths[i + 1]) {
          folderFile.fileList.push(this)
        }
      }, this)
    } else {
      this._updateParentFileList()
    }
  },

  _updateParentFileList: function (file) {
    if (!file) {
      file = this
    }
    var p = this.parent
    if (p) {
      p.fileList.push(file)
    }
  },

  _eachAccess: function (eachFn, fileFn) {
    if (this.isFolder) {
      utils.each(this.files, function (f, i) {
        return eachFn.call(this, f, i)
      }, this)
      return
    }
    fileFn.call(this, this)
  },

  bootstrap: function () {
    if (this.isFolder) return
    var opts = this.uploader.opts
    if (utils.isFunction(opts.initFileFn)) {
      opts.initFileFn.call(this, this)
    }

    this.abort(true)
    this._resetError()
    // Rebuild stack of chunks from file
    this._prevProgress = 0
    var round = opts.forceChunkSize ? Math.ceil : Math.floor
    var chunks = Math.max(round(this.size / opts.chunkSize), 1)
    for (var offset = 0; offset < chunks; offset++) {
      this.chunks.push(new Chunk(this.uploader, this, offset))
    }
  },

  _measureSpeed: function () {
    var smoothingFactor = this.uploader.opts.speedSmoothingFactor
    var timeSpan = Date.now() - this._lastProgressCallback
    if (!timeSpan) {
      return
    }
    var uploaded = this.sizeUploaded()
    // Prevent negative upload speed after file upload resume
    this.currentSpeed = Math.max((uploaded - this._prevUploadedSize) / timeSpan * 1000, 0)
    this.averageSpeed = smoothingFactor * this.currentSpeed + (1 - smoothingFactor) * this.averageSpeed
    this._prevUploadedSize = uploaded
    if (this.parent && this.parent._checkProgress()) {
      this.parent._measureSpeed()
    }
  },

  _checkProgress: function (file) {
    return Date.now() - this._lastProgressCallback >= this.uploader.opts.progressCallbacksInterval
  },

  _chunkEvent: function (chunk, evt, message) {
    var uploader = this.uploader
    var STATUS = Chunk.STATUS
    var that = this
    var rootFile = this.getRoot()
    var triggerProgress = function () {
      that._measureSpeed()
      uploader._trigger('fileProgress', rootFile, that, chunk)
      that._lastProgressCallback = Date.now()
    }
    switch (evt) {
      case STATUS.PROGRESS:
        if (this._checkProgress()) {
          triggerProgress()
        }
        break
      case STATUS.ERROR:
        this._error()
        this.abort(true)
        uploader._trigger('fileError', rootFile, this, message, chunk)
        break
      case STATUS.SUCCESS:
        this._updateUploadedChunks(message, chunk)
        if (this.error) {
          return
        }
        clearTimeout(this._progeressId)
        this._progeressId = 0
        var timeDiff = Date.now() - this._lastProgressCallback
        if (timeDiff < uploader.opts.progressCallbacksInterval) {
          this._progeressId = setTimeout(triggerProgress, uploader.opts.progressCallbacksInterval - timeDiff)
        }
        if (this.isComplete()) {
          clearTimeout(this._progeressId)
          triggerProgress()
          this.currentSpeed = 0
          this.averageSpeed = 0
          uploader._trigger('fileSuccess', rootFile, this, message, chunk)
          if (rootFile.isComplete()) {
            uploader._trigger('fileComplete', rootFile, this)
          }
        } else if (!this._progeressId) {
          triggerProgress()
        }
        break
      case STATUS.RETRY:
        uploader._trigger('fileRetry', rootFile, this, chunk)
        break
    }
  },

  _updateUploadedChunks: function (message, chunk) {
    var checkChunkUploaded = this.uploader.opts.checkChunkUploadedByResponse
    if (checkChunkUploaded) {
      var xhr = chunk.xhr
      utils.each(this.chunks, function (_chunk) {
        if (!_chunk.tested) {
          var uploaded = checkChunkUploaded.call(this, _chunk, message)
          if (_chunk === chunk && !uploaded) {
            // fix the first chunk xhr status
            // treated as success but checkChunkUploaded is false
            // so the current chunk should be uploaded again
            _chunk.xhr = null
          }
          if (uploaded) {
            // first success and other chunks are uploaded
            // then set xhr, so the uploaded chunks
            // will be treated as success too
            _chunk.xhr = xhr
          }
          _chunk.tested = true
        }
      }, this)
      if (!this._firstResponse) {
        this._firstResponse = true
        this.uploader.upload(true)
      } else {
        this.uploader.uploadNextChunk()
      }
    } else {
      this.uploader.uploadNextChunk()
    }
  },

  _error: function () {
    this.error = this.allError = true
    var parent = this.parent
    while (parent && parent !== this.uploader) {
      parent._errorFiles.push(this)
      parent.error = true
      if (parent._errorFiles.length === parent.files.length) {
        parent.allError = true
      }
      parent = parent.parent
    }
  },

  _resetError: function () {
    this.error = this.allError = false
    var parent = this.parent
    var index = -1
    while (parent && parent !== this.uploader) {
      index = parent._errorFiles.indexOf(this)
      parent._errorFiles.splice(index, 1)
      parent.allError = false
      if (!parent._errorFiles.length) {
        parent.error = false
      }
      parent = parent.parent
    }
  },

  isComplete: function () {
    if (!this.completed) {
      var outstanding = false
      this._eachAccess(function (file) {
        if (!file.isComplete()) {
          outstanding = true
          return false
        }
      }, function () {
        if (this.error) {
          outstanding = true
        } else {
          var STATUS = Chunk.STATUS
          utils.each(this.chunks, function (chunk) {
            var status = chunk.status()
            if (status === STATUS.ERROR || status === STATUS.PENDING || status === STATUS.UPLOADING || status === STATUS.READING || chunk.preprocessState === 1 || chunk.readState === 1) {
              outstanding = true
              return false
            }
          })
        }
      })
      this.completed = !outstanding
    }
    return this.completed
  },

  isUploading: function () {
    var uploading = false
    this._eachAccess(function (file) {
      if (file.isUploading()) {
        uploading = true
        return false
      }
    }, function () {
      var uploadingStatus = Chunk.STATUS.UPLOADING
      utils.each(this.chunks, function (chunk) {
        if (chunk.status() === uploadingStatus) {
          uploading = true
          return false
        }
      })
    })
    return uploading
  },

  resume: function () {
    this._eachAccess(function (f) {
      f.resume()
    }, function () {
      this.paused = false
      this.aborted = false
      this.uploader.upload()
    })
    this.paused = false
    this.aborted = false
  },

  pause: function () {
    this._eachAccess(function (f) {
      f.pause()
    }, function () {
      this.paused = true
      this.abort()
    })
    this.paused = true
  },

  cancel: function () {
    this.uploader.removeFile(this)
  },

  retry: function (file) {
    var fileRetry = function (file) {
      if (file.error) {
        file.bootstrap()
      }
    }
    if (file) {
      file.bootstrap()
    } else {
      this._eachAccess(fileRetry, function () {
        this.bootstrap()
      })
    }
    this.uploader.upload()
  },

  abort: function (reset) {
    if (this.aborted) {
      return
    }
    this.currentSpeed = 0
    this.averageSpeed = 0
    this.aborted = !reset
    var chunks = this.chunks
    if (reset) {
      this.chunks = []
    }
    var uploadingStatus = Chunk.STATUS.UPLOADING
    utils.each(chunks, function (c) {
      if (c.status() === uploadingStatus) {
        c.abort()
        this.uploader.uploadNextChunk()
      }
    }, this)
  },

  progress: function () {
    var totalDone = 0
    var totalSize = 0
    var ret = 0
    this._eachAccess(function (file, index) {
      totalDone += file.progress() * file.size
      totalSize += file.size
      if (index === this.files.length - 1) {
        ret = totalSize > 0 ? totalDone / totalSize : this.isComplete() ? 1 : 0
      }
    }, function () {
      if (this.error) {
        ret = 1
        return
      }
      if (this.chunks.length === 1) {
        this._prevProgress = Math.max(this._prevProgress, this.chunks[0].progress())
        ret = this._prevProgress
        return
      }
      // Sum up progress across everything
      var bytesLoaded = 0
      utils.each(this.chunks, function (c) {
        // get chunk progress relative to entire file
        bytesLoaded += c.progress() * (c.endByte - c.startByte)
      })
      var percent = bytesLoaded / this.size
      // We don't want to lose percentages when an upload is paused
      this._prevProgress = Math.max(this._prevProgress, percent > 0.9999 ? 1 : percent)
      ret = this._prevProgress
    })
    return ret
  },

  getSize: function () {
    var size = 0
    this._eachAccess(function (file) {
      size += file.size
    }, function () {
      size += this.size
    })
    return size
  },

  getFormatSize: function () {
    var size = this.getSize()
    return utils.formatSize(size)
  },

  getRoot: function () {
    if (this.isRoot) {
      return this
    }
    var parent = this.parent
    while (parent) {
      if (parent.parent === this.uploader) {
        // find it
        return parent
      }
      parent = parent.parent
    }
    return this
  },

  sizeUploaded: function () {
    var size = 0
    this._eachAccess(function (file) {
      size += file.sizeUploaded()
    }, function () {
      utils.each(this.chunks, function (chunk) {
        size += chunk.sizeUploaded()
      })
    })
    return size
  },

  timeRemaining: function () {
    var ret = 0
    var sizeDelta = 0
    var averageSpeed = 0
    this._eachAccess(function (file, i) {
      if (!file.paused && !file.error) {
        sizeDelta += file.size - file.sizeUploaded()
        averageSpeed += file.averageSpeed
      }
      if (i === this.files.length - 1) {
        ret = calRet(sizeDelta, averageSpeed)
      }
    }, function () {
      if (this.paused || this.error) {
        ret = 0
        return
      }
      var delta = this.size - this.sizeUploaded()
      ret = calRet(delta, this.averageSpeed)
    })
    return ret
    function calRet (delta, averageSpeed) {
      if (delta && !averageSpeed) {
        return Number.POSITIVE_INFINITY
      }
      if (!delta && !averageSpeed) {
        return 0
      }
      return Math.floor(delta / averageSpeed)
    }
  },

  removeFile: function (file) {
    if (file.isFolder) {
      while (file.files.length) {
        var f = file.files[file.files.length - 1]
        this._removeFile(f)
      }
    }
    this._removeFile(file)
  },

  _delFilePath: function (file) {
    if (file.path && this.filePaths) {
      delete this.filePaths[file.path]
    }
    utils.each(file.fileList, function (file) {
      this._delFilePath(file)
    }, this)
  },

  _removeFile: function (file) {
    if (!file.isFolder) {
      utils.each(this.files, function (f, i) {
        if (f === file) {
          this.files.splice(i, 1)
          return false
        }
      }, this)
      file.abort()
      var parent = file.parent
      var newParent
      while (parent && parent !== this) {
        newParent = parent.parent
        parent._removeFile(file)
        parent = newParent
      }
    }
    file.parent === this && utils.each(this.fileList, function (f, i) {
      if (f === file) {
        this.fileList.splice(i, 1)
        return false
      }
    }, this)
    if (!this.isRoot && this.isFolder && !this.files.length) {
      this.parent._removeFile(this)
      this.uploader._delFilePath(this)
    }
    file.parent = null
  },

  getType: function () {
    if (this.isFolder) {
      return 'folder'
    }
    return this.file.type && this.file.type.split('/')[1]
  },

  getExtension: function () {
    if (this.isFolder) {
      return ''
    }
    return this.name.substr((~-this.name.lastIndexOf('.') >>> 0) + 2).toLowerCase()
  }

})

module.exports = File

function parsePaths (path) {
  var ret = []
  var paths = path.split('/')
  var len = paths.length
  var i = 1
  paths.splice(len - 1, 1)
  len--
  if (paths.length) {
    while (i <= len) {
      ret.push(paths.slice(0, i++).join('/') + '/')
    }
  }
  return ret
}

},{"./chunk":1,"./utils":5}],5:[function(_dereq_,module,exports){
var oproto = Object.prototype
var aproto = Array.prototype
var serialize = oproto.toString

var isFunction = function (fn) {
  return serialize.call(fn) === '[object Function]'
}

var isArray = Array.isArray || /* istanbul ignore next */ function (ary) {
  return serialize.call(ary) === '[object Array]'
}

var isPlainObject = function (obj) {
  return serialize.call(obj) === '[object Object]' && Object.getPrototypeOf(obj) === oproto
}

var i = 0
var utils = {
  uid: function () {
    return ++i
  },
  noop: function () {},
  bind: function (fn, context) {
    return function () {
      return fn.apply(context, arguments)
    }
  },
  preventEvent: function (evt) {
    evt.preventDefault()
  },
  stop: function (evt) {
    evt.preventDefault()
    evt.stopPropagation()
  },
  nextTick: function (fn, context) {
    setTimeout(utils.bind(fn, context), 0)
  },
  toArray: function (ary, start, end) {
    if (start === undefined) start = 0
    if (end === undefined) end = ary.length
    return aproto.slice.call(ary, start, end)
  },

  isPlainObject: isPlainObject,
  isFunction: isFunction,
  isArray: isArray,
  isObject: function (obj) {
    return Object(obj) === obj
  },
  isString: function (s) {
    return typeof s === 'string'
  },
  isUndefined: function (a) {
    return typeof a === 'undefined'
  },
  isDefined: function (a) {
    return typeof a !== 'undefined'
  },

  each: function (ary, func, context) {
    if (utils.isDefined(ary.length)) {
      for (var i = 0, len = ary.length; i < len; i++) {
        if (func.call(context, ary[i], i, ary) === false) {
          break
        }
      }
    } else {
      for (var k in ary) {
        if (func.call(context, ary[k], k, ary) === false) {
          break
        }
      }
    }
  },

  /**
   * If option is a function, evaluate it with given params
   * @param {*} data
   * @param {...} args arguments of a callback
   * @returns {*}
   */
  evalOpts: function (data, args) {
    if (utils.isFunction(data)) {
      // `arguments` is an object, not array, in FF, so:
      args = utils.toArray(arguments)
      data = data.apply(null, args.slice(1))
    }
    return data
  },

  extend: function () {
    var options
    var name
    var src
    var copy
    var copyIsArray
    var clone
    var target = arguments[0] || {}
    var i = 1
    var length = arguments.length
    var force = false

    // 如果第一个参数为布尔,判定是否深拷贝
    if (typeof target === 'boolean') {
      force = target
      target = arguments[1] || {}
      i++
    }

    // 确保接受方为一个复杂的数据类型
    if (typeof target !== 'object' && !isFunction(target)) {
      target = {}
    }

    // 如果只有一个参数，那么新成员添加于 extend 所在的对象上
    if (i === length) {
      target = this
      i--
    }

    for (; i < length; i++) {
      // 只处理非空参数
      if ((options = arguments[i]) != null) {
        for (name in options) {
          src = target[name]
          copy = options[name]

          // 防止环引用
          if (target === copy) {
            continue
          }
          if (force && copy && (isPlainObject(copy) || (copyIsArray = isArray(copy)))) {
            if (copyIsArray) {
              copyIsArray = false
              clone = src && isArray(src) ? src : []
            } else {
              clone = src && isPlainObject(src) ? src : {}
            }
            target[name] = utils.extend(force, clone, copy)
          } else if (copy !== undefined) {
            target[name] = copy
          }
        }
      }
    }
    return target
  },

  formatSize: function (size) {
    if (size < 1024) {
      return size.toFixed(0) + ' bytes'
    } else if (size < 1024 * 1024) {
      return (size / 1024.0).toFixed(0) + ' KB'
    } else if (size < 1024 * 1024 * 1024) {
      return (size / 1024.0 / 1024.0).toFixed(1) + ' MB'
    } else {
      return (size / 1024.0 / 1024.0 / 1024.0).toFixed(1) + ' GB'
    }
  },

  defineNonEnumerable: function (target, key, value) {
    Object.defineProperty(target, key, {
      enumerable: false,
      configurable: true,
      writable: true,
      value: value
    })
  }
}

module.exports = utils

},{}]},{},[3])
(3)
});