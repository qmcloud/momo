'use strict';

// join 主动加入房间
// leave 主动离开房间
// new-peer 有人加入房间，通知已经在房间的人
// peer-leave 有人离开房间，通知已经在房间的人
// offer 发送offer给对端peer
// answer发送offer给对端peer
// candidate 发送candidate给对端peer

const SIGNAL_TYPE_JOIN = "join";
const SIGNAL_TYPE_RESP_JOIN = "resp-join";  // 告知加入者对方是谁
const SIGNAL_TYPE_LEAVE = "leave";
const SIGNAL_TYPE_NEW_PEER = "new-peer";
const SIGNAL_TYPE_PEER_LEAVE = "peer-leave";
const SIGNAL_TYPE_OFFER = "offer";
const SIGNAL_TYPE_ANSWER = "answer";
const SIGNAL_TYPE_CANDIDATE = "candidate";
const ERR_MSG = "err-msg";

var localUserId = Math.random().toString(36).substr(2); // 本地uid
var remoteUserId = -1;      // 对端
var roomId = 0;

var localVideo = document.querySelector('#localVideo');
var remoteVideo = document.querySelector('#remoteVideo');
var localStream = null;
var remoteStream = null;
var pc = null;

var zeroRTCEngine;

var startVideo = document.querySelector('#startVideo');
var stop = document.querySelector('#stop');
var reply = document.querySelector('#reply');
var download = document.querySelector('#download');

var heartCheck = {
    timeout: 10000,
    timeoutObj: null,
    serverTimeoutObj: null,
    num: 3,
    start: function () {
        var self = this;
        var _num = this.num
        this.timeoutObj && clearTimeout(this.timeoutObj);
        this.serverTimeoutObj && clearTimeout(this.serverTimeoutObj);
        this.timeoutObj = setTimeout(function () {
            //这里发送一个心跳，后端收到后，返回一个心跳消息，
            //onmessage拿到返回的心跳就说明连接正常
            if (zeroRTCEngine.signaling.readyState === 1) {
                var data = {
                    type: "heatbeat",
                    content: "ping",
                }
                var message = JSON.stringify(data);
                zeroRTCEngine.sendMessage(message);
            }

            self.serverTimeoutObj = setTimeout(function () {
                _num--
                if (_num <= 0) {
                    console.log("the ping num is more then 3, close socket!")
                    zeroRTCEngine.close();
                }
            }, self.timeout);

        }, this.timeout)
    }
}

function handleIceCandidate(event) {
    console.info("handleIceCandidate");
    if (event.candidate) {
        var candidateJson = {
            'label': event.candidate.sdpMLineIndex,
            'id': event.candidate.sdpMid,
            'candidate': event.candidate.candidate
        };
        var jsonMsg = {
            'cmd': SIGNAL_TYPE_CANDIDATE,
            'roomId': roomId,
            'uid': localUserId,
            'remoteUid':remoteUserId,
            'msg': JSON.stringify(candidateJson) 
        };
        var message = JSON.stringify(jsonMsg);
        zeroRTCEngine.sendMessage(message);
        console.info("handleIceCandidate message: " + message);
        console.info("send candidate message");
    } else {
        console.warn("End of candidates");
    }
}

function handleRemoteStreamAdd(event) {
    console.info("handleRemoteStreamAdd");
    remoteStream = event.streams[0];
    remoteVideo.srcObject = remoteStream;
}

function handleConnectionStateChange() {
    if(pc != null) {
        console.info("ConnectionState -> " + pc.connectionState);
    }
}

function handleIceConnectionStateChange() {
    if(pc != null) {
        console.info("IceConnectionState -> " + pc.iceConnectionState);
    }
}


function createPeerConnection() {
    var defaultConfiguration = {  
        bundlePolicy: "max-bundle",
        rtcpMuxPolicy: "require",
        iceTransportPolicy:"all",//relay 或者 
        // 修改ice数组测试效果，需要进行封装
        iceServers: [
            {
                "urls": [
                    "turn:192.168.0.143:3478?transport=udp",
                    "turn:192.168.0.143:3478?transport=tcp"       // 可以插入多个进行备选
                ],
                "username": "lqf",
                "credential": "123456"
            },
            {
                "urls": [
                    "stun:192.168.0.143:3478"
                ]
            }
        ]
    };

    pc = new RTCPeerConnection(); // 音视频通话的核心类 //defaultConfiguration 局域网不用传
    pc.onicecandidate = handleIceCandidate;
    pc.ontrack = handleRemoteStreamAdd;
    pc.onconnectionstatechange = handleConnectionStateChange;
    pc.oniceconnectionstatechange = handleIceConnectionStateChange

    //localStream.getTracks().forEach((track) => pc.addTrack(track, localStream)); // 把本地流设置给RTCPeerConnection
    localStream.getTracks().forEach((track) => {
        pc.addTrack(track, localStream)
    })
}

function createOfferAndSendMessage(session) {
    pc.setLocalDescription(session)
        .then(function () {
            var jsonMsg = {
                'cmd': 'offer',
                'roomId': roomId,
                'uid': localUserId,
                'remoteUid': remoteUserId,
                'msg': JSON.stringify(session)
            };
            var message = JSON.stringify(jsonMsg);
            console.info("send offer message: " + message);
            zeroRTCEngine.sendMessage(message);
            console.info("send offer message");
        })
        .catch(function (error) {
            console.error("offer setLocalDescription failed: " + error);
        });

}

function handleCreateOfferError(error) {
    console.error("handleCreateOfferError: " + error);
}

function createAnswerAndSendMessage(session) {
    pc.setLocalDescription(session)
        .then(function () {
            var jsonMsg = {
                'cmd': 'answer',
                'roomId': roomId,
                'uid': localUserId,
                'remoteUid': remoteUserId,
                'msg': JSON.stringify(session)
            };
            var message = JSON.stringify(jsonMsg);
            zeroRTCEngine.sendMessage(message);
            // console.info("send answer message: " + message);
            console.info("send answer message");
        })
        .catch(function (error) {
            console.error("answer setLocalDescription failed: " + error);
        });

}

function handleCreateAnswerError(error) {
    console.error("handleCreateAnswerError: " + error);
}



var ZeroRTCEngine = function (wsUrl) {
    this.init(wsUrl);
    zeroRTCEngine = this;
    return this;
}

ZeroRTCEngine.prototype.init = function (wsUrl) {
    // 设置websocket  url
    this.wsUrl = wsUrl;
    /** websocket对象 */
    this.signaling = null;
}

ZeroRTCEngine.prototype.createWebsocket = function () {
    zeroRTCEngine = this;
    zeroRTCEngine.signaling = new WebSocket(this.wsUrl);

    zeroRTCEngine.signaling.onopen = function () {
        heartCheck.start()
        zeroRTCEngine.onOpen();
    }

    zeroRTCEngine.signaling.onmessage = function (ev) {
        heartCheck.start()
        zeroRTCEngine.onMessage(ev);
    }

    zeroRTCEngine.signaling.onerror = function (ev) {
        zeroRTCEngine.onError(ev);
    }

    zeroRTCEngine.signaling.onclose = function (ev) {
        zeroRTCEngine.onClose(ev);
    }
}

ZeroRTCEngine.prototype.onOpen = function () {
    console.log("websocket open");
}
ZeroRTCEngine.prototype.onMessage = function (event) {
    console.log("onMessage: " + event.data);
    var jsonMsg = null;
    try {
         jsonMsg = JSON.parse(event.data);
    } catch(e) {
        console.warn("onMessage parse Json failed:" + e);
        return;
    }

    switch (jsonMsg.cmd) {
        case SIGNAL_TYPE_NEW_PEER:
            handleRemoteNewPeer(jsonMsg);
            break;
        case SIGNAL_TYPE_RESP_JOIN:
            handleResponseJoin(jsonMsg);
            break;
        case SIGNAL_TYPE_PEER_LEAVE:
            handleRemotePeerLeave(jsonMsg);
            break;
        case SIGNAL_TYPE_OFFER:
            handleRemoteOffer(jsonMsg);
            break;
        case SIGNAL_TYPE_ANSWER:
            handleRemoteAnswer(jsonMsg);
            break;
        case SIGNAL_TYPE_CANDIDATE:
            handleRemoteCandidate(jsonMsg);
            break;
        case ERR_MSG:
            alert(jsonMsg.msg)
            break
    }
}

ZeroRTCEngine.prototype.onError = function (event) {
    if(event.data==undefined){
        alert("websocket err :" + event.data)
    }
    console.log("onError: " + event.data);
}

ZeroRTCEngine.prototype.onClose = function (event) {
    console.log("onClose -> code: " + event.code + ", reason:" + EventTarget.reason);
}

ZeroRTCEngine.prototype.sendMessage = function (message) {
    console.log(message)
    this.signaling.send(message);
}

function handleResponseJoin(message) {
    console.info("handleResponseJoin, remoteUid: " + message.remoteUid);
    remoteUserId = message.remoteUid;
    // doOffer();
}

function handleRemotePeerLeave(message) {
    console.info("handleRemotePeerLeave, remoteUid: " + message.remoteUid);
    remoteVideo.srcObject = null;
    if(pc != null) {
        pc.close();
        pc = null;
    }
}

function handleRemoteNewPeer(message) {
    console.info("handleRemoteNewPeer, remoteUid: " + message.remoteUid);
    remoteUserId = message.remoteUid;
    doOffer();
}

function handleRemoteOffer(message) {
    console.info("handleRemoteOffer");
    if(pc == null) {
        createPeerConnection();
    }
    var desc = JSON.parse(message.msg);
    pc.setRemoteDescription(desc);
    doAnswer();
}

function handleRemoteAnswer(message) {
    console.info("handleRemoteAnswer");
    var desc = JSON.parse(message.msg);
    pc.setRemoteDescription(desc);
}

function handleRemoteCandidate(message) {
    console.info("handleRemoteCandidate");
    var jsonMsg = JSON.parse(message.msg);
    var candidateMsg = {
        'sdpMLineIndex': jsonMsg.label,
        'sdpMid': jsonMsg.id,
        'candidate': jsonMsg.candidate
    };
    var candidate = new RTCIceCandidate(candidateMsg);
    pc.addIceCandidate(candidate).catch(e => {
        console.error("addIceCandidate failed:" + e.name);
    });
}

function doOffer() {
    // 创建RTCPeerConnection
    console.log("开始RTCPeerConnection")
    if (pc == null) {
        createPeerConnection();
    }
    pc.createOffer().then(createOfferAndSendMessage).catch(handleCreateOfferError);
    console.log("完成RTCPeerConnection")

}

function doAnswer() {
    pc.createAnswer().then(createAnswerAndSendMessage).catch(handleCreateAnswerError);
}


function doJoin(roomId) {
    var jsonMsg = {
        'cmd': 'join',
        'roomId': roomId,
        'uid': localUserId,
    };
    var message = JSON.stringify(jsonMsg);
    zeroRTCEngine.sendMessage(message);
    console.info("doJoin message: " + message);
}

function doLeave() {
    var jsonMsg = {
        'cmd': 'leave',
        'roomId': roomId,
        'uid': localUserId,
    };
    var message = JSON.stringify(jsonMsg);
    zeroRTCEngine.sendMessage(message);
    console.info("doLeave message: " + message);
    hangup();
}

function hangup() {
    localVideo.srcObject = null; // 0.关闭自己的本地显示
    remoteVideo.srcObject = null; // 1.不显示对方
    closeLocalStream(); // 2. 关闭本地流
    if(pc != null) {
        pc.close(); // 3.关闭RTCPeerConnection
        pc = null;
    }
}

function closeLocalStream() {
    if(localStream != null) {
        localStream.getTracks().forEach((track) => {
                track.stop();
        });
    }
}

function openLocalStream(stream) {
    console.log('Open local stream');
    doJoin(roomId);
    localVideo.srcObject = stream;      // 显示画面
    localStream = stream;   // 保存本地流的句柄
    console.log("localStream:"+localStream)
}


function initLocalStream() {
    navigator.mediaDevices.getUserMedia({
        audio: true,
        video: true
    })
        .then(openLocalStream)
        .catch(function (e) {
            alert("getUserMedia() error: " + e.name);
        });
}
// engine = new Engine("wss://129.211.184.235:8089/ws");
// zeroRTCEngine = new ZeroRTCEngine("ws://192.168.0.143:10000");
zeroRTCEngine = new ZeroRTCEngine(HOST+"?uid="+localUserId);
zeroRTCEngine.createWebsocket();
document.getElementById('userid').value = localUserId
document.getElementById('joinBtn').onclick = function () {
    roomId = document.getElementById('zero-roomId').value;

    if (roomId == "" || roomId == "请输入房间ID") {
        alert("请输入房间ID");
        return;
    }

    if(document.getElementById('userid').value==""){
        alert("请输入用户ID");
        return;
    }
    console.log("加入按钮被点击, roomId: " + roomId);
    // 初始化本地码流
    initLocalStream();
}

document.getElementById('leaveBtn').onclick = function () {
    console.log("离开按钮被点击");
    roomId = document.getElementById('zero-roomId').value;

    if (roomId == "" || roomId == "请输入房间ID") {
        alert("请输入房间ID");
        return;
    }
    doLeave();
}

//录制
const player = document.querySelector('#player');
const recordPlayer = document.querySelector('#recordPlayer');
let blobs = [], mediaRecorder;

async function record(recordType) {
    const getMediaMethod = recordType === 'screen' ? 'getDisplayMedia' : 'getUserMedia';
    const stream = await navigator.mediaDevices[getMediaMethod]({
        video: {
            width: 500,
            height: 300,
            frameRate: 20
        }
    });
    player.srcObject = stream;

    mediaRecorder = new MediaRecorder(stream, {
        mimeType: 'video/webm'
    });
    mediaRecorder.ondataavailable = (e) => {
        blobs.push(e.data);
    };
    mediaRecorder.start(100);
}

const downloadBtn = document.querySelector('#download');
const startScreenBtn = document.querySelector('#startScreen');
const stopBtn = document.querySelector('#stop');
const replyBtn = document.querySelector('#reply');

startScreenBtn.addEventListener('click', () => {
    record('screen');
});


stopBtn.addEventListener('click', () => {
    mediaRecorder && mediaRecorder.stop();
});

replyBtn.addEventListener('click', () => {
    const blob = new Blob(blobs, {type : 'video/webm'});
    recordPlayer.src = URL.createObjectURL(blob);
    recordPlayer.style.display="block"
    recordPlayer.play();
});

download.addEventListener('click', () => {
    var blob = new Blob(blobs, {type: 'video/webm'});
    var url = URL.createObjectURL(blob);

    var a = document.createElement('a');
    a.href = url;
    a.style.display = 'none';
    a.download = 'record.webm';
    a.click();
});


