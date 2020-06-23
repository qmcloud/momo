function Queue (n) {
    n = parseInt(n || 1, 10);
    return (n && n > 0) ? new Queue.prototype.init(n) : null;
}
 
Queue.prototype = {
    init: function (n) {
        this.threads = [];
        this.taskList = [];
 
        while (n--) {
            this.threads.push(new this.Thread)
        }
    },
 
    /**
     * @callback {Fucntion} promise对象done时的回调函数，它的返回值必须是一个promise对象
     */
    push: function (callback) {
        if (typeof callback !== 'function') return;
 
        var index = this.indexOfIdle();
 
        if (index != -1) {
            this.threads[index].idle(callback)
            try { console.log('Thread-' + (index+1) + ' accept the task!') } catch (e) {}
        }
        else {
            this.taskList.push(callback);
 
            for (var i = 0, l = this.threads.length; i < l; i++) {
 
                (function(thread, self, id){
                    thread.idle(function(){
                        if (self.taskList.length > 0) {
                            try { console.log('Thread-' + (id+1) + ' accept the task!') } catch (e) {}
 
                            var promise = self.taskList.pop()();    // 正确的返回值应该是一个promise对象
                            return promise.promise ? promise : thread.promise;
                        } else {
                            return thread.promise
                        }
                    })
                })(this.threads[i], this, i);
 
            }
        }
    },
    indexOfIdle: function () {
        var threads = this.threads,
            thread = null,
            index = -1;
 
        for (var i = 0, l = threads.length; i < l; i++) {
            thread = threads[i];
 
            if (thread.promise.state() === 'resolved') {
                index = i;
                break;
            }
        }
 
        return index;
    },
    Thread: function () {
        this.promise = $.Deferred().resolve().promise();
 
        this.idle = function (callback) {
            this.promise = this.promise.then(callback)
        }
    }
};
 
Queue.prototype.init.prototype = Queue.prototype;