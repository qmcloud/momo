// 兼容处理
const PeerConnection = window.RTCPeerConnection || window.mozRTCPeerConnection || window.webkitRTCPeerConnection;
const SessionDescription = window.RTCSessionDescription || window.mozRTCSessionDescription || window.webkitRTCSessionDescription;
const GET_USER_MEDIA = navigator.getUserMedia ? "getUserMedia" :
                     navigator.mozGetUserMedia ? "mozGetUserMedia" :
                     navigator.webkitGetUserMedia ? "webkitGetUserMedia" : "getUserMedia";
const v = document.createElement("video");
const SRC_OBJECT = 'srcObject' in v ? "srcObject" :
                 'mozSrcObject' in v ? "mozSrcObject" :
                 'webkitSrcObject' in v ? "webkitSrcObject" : "srcObject";


//本地stream
let localStream = null;
// socket连接
const socket = io(THSConfig.signalServer);
// 本地socket id
let socketId;
// 房间 id
let roomId;
// 对RTCPeerConnection连接进行缓存
let rtcPeerConnects = {};

/**
 * 启动摄像头
 */
const openCamera = () => {
  return navigator.mediaDevices[GET_USER_MEDIA]({
    audio: true,
    video: true
  });
}

/**
 * 关闭摄像头
 * @param {dom} video video节点
 */
const closeCamera = video => {
  video[SRC_OBJECT].getTracks()[0].stop(); // audio
  video[SRC_OBJECT].getTracks()[1].stop(); // video
}

/**
 * 视频流绑定到video节点展示
 * @param {dom} video video节点
 * @param {obj} stream 视频流
 */
const pushStreamToVideo = (video, stream) => {
  console.log('视频流绑定到video节点展示', video, stream)
  video[SRC_OBJECT] = stream;
}

/**
 * 连接（给signal server 发送创建或者加入房间的消息）
 * @param {string} roomid 房间号
 */
 const connect = roomid => {
  console.log('创建或者加入房间', roomid)
  socket.emit('createAndJoinRoom', {
    room: roomid
  });
}


/**
 * 监听signal server创建房间或者加入房间成功的消息，signal server会判断房间里是否有人
 */
socket.on('created', async data => {
  // data: [id,room,peers]
  console.log('created: ', data);
  // 保存signal server给我分配的socketId
  socketId = data.id;
  // 保存创建房间或者加入房间的room id
  roomId = data.room;
  // 如果data.peers = []，说明房间里没有人，是创建房间，以下步骤则不会执行
  // 如果data.peers != []，说明房间里有人，是加入房间，给返回的每一个peers，创建WebRtcPeerConnection并发送offer消息
  for (let i = 0; i < data.peers.length; i++) {
    let otherSocketId = data.peers[i].id;
    // 创建WebRtcPeerConnection // 注意：这个函数是下一个步骤写的。
    let pc = getWebRTCConnect(otherSocketId);
    // 创建offer
    const offer = await pc.createOffer(THSConfig.offerOptions);
    // 发送offer
    onCreateOfferSuccess(pc, otherSocketId, offer);
  }
})


/**
 * offer创建成功回调
 * @param {*} pc 
 * @param {*} otherSocketId 
 * @param {*} offer 
 */
function onCreateOfferSuccess(pc, otherSocketId, offer) {
  console.log('createOffer: success ' + ' id:' + otherSocketId + ' offer: ', offer);
  // 设置本地setLocalDescription 将自己的描述信息加入到PeerConnection中
  pc.setLocalDescription(offer);
  // 构建offer
  const message = {
    from: socketId,
    to: otherSocketId,
    room: roomId,
    sdp: offer.sdp
  };
  console.log('发送offer消息', message)
  // 发送offer消息
  socket.emit('offer', message);
}


/**
 * 监听signal server转发过来的offer消息，将对方的描述信息加入到PeerConnection中，然后构建answer
 */
 socket.on('offer', data => {
  // data:  [from,to,room,sdp]
  console.log('收到offer: ', data);
  // 获取RTCPeerConnection
  const pc = getWebRTCConnect(data.from);
  console.log('getWebRTCConnect: ', pc);
  // 构建RTCSessionDescription参数
  const rtcDescription = {
    type: 'offer',
    sdp: data.sdp
  };

  console.log('offer设置远端setRemoteDescription')
  // 设置远端setRemoteDescription
  pc.setRemoteDescription(new SessionDescription(rtcDescription));
  console.log('setRemoteDescription: ', rtcDescription);

  // createAnswer
  pc.createAnswer(THSConfig.offerOptions)
    .then(offer => onCreateAnswerSuccess(pc, data.from, offer))
    .catch(error => onCreateAnswerError(error));
})

/**
 * answer创建成功回调
 * @param {*} pc 
 * @param {*} otherSocketId 
 * @param {*} offer 
 */
function onCreateAnswerSuccess(pc, otherSocketId, offer) {
  console.log('createAnswer: success ' + ' id:' + otherSocketId + ' offer: ', offer);
  // 设置本地setLocalDescription，将对方的描述信息加入到PeerConnection中
  pc.setLocalDescription(offer);
  // 构建answer信息
  const message = {
    from: socketId,
    to: otherSocketId,
    room: roomId,
    sdp: offer.sdp
  };
  console.log('发送answer消息', message)
  // 发送answer消息
  socket.emit('answer', message);
}

/**
 * answer创建失败回调
 * @param {*} error 
 */
function onCreateAnswerError(error) {
  console.log('createAnswer: fail error ' + error);
}


/**
 * 监听signal server转发过来的answer消息，将对方的描述信息加入到PeerConnection中
 */
socket.on('answer', data => {
  // data:  [from,to,room,sdp]
  console.log('收到answer: ', data);
  // 获取RTCPeerConnection
  const pc = getWebRTCConnect(data.from);

  // 构建RTCSessionDescription参数
  const rtcDescription = {
    type: 'answer',
    sdp: data.sdp
  };

  console.log('answer设置远端setRemoteDescription')
  console.log('setRemoteDescription: ', rtcDescription);
  //设置远端setRemoteDescription
  pc.setRemoteDescription(new SessionDescription(rtcDescription));
})


/**
 * 获取RTCPeerConnection
 * @param {string} otherSocketId 对方socketId
 */
function getWebRTCConnect(otherSocketId) {
  if (!otherSocketId) return;
  // 查询全局中是否已经保存了连接
  let pc = rtcPeerConnects[otherSocketId];
  console.log('建立连接：', otherSocketId, pc)
  if (typeof (pc) === 'undefined') { // 如果没有保存，就创建RTCPeerConnection
    // 构建RTCPeerConnection
    pc = new PeerConnection(THSConfig.iceServers); // PeerConnection是4.3.2定义的兼容处理

    // 设置获取icecandidate信息回调 此处可暂时忽略，将在4.3.5讲解
    pc.onicecandidate = e => onIceCandidate(pc, otherSocketId, e);
    // 设置获取对端stream数据回调-track方式 此处可暂时忽略，将在4.3.5讲解
    pc.ontrack = e => {
      console.log('我接到数据流了！！', pc, otherSocketId, e)
      onTrack(pc, otherSocketId, e);
    }
    // 设置获取对端stream数据回调 此处可暂时忽略，将在4.3.5讲解
    pc.onremovestream = e => onRemoveStream(pc, otherSocketId, e);
    // peer设置本地流 此处可暂时忽略，将在4.3.5讲解
    if (localStream != null) {
      localStream.getTracks().forEach(track => {
        pc.addTrack(track, localStream);
      });
    }

    // 缓存peer连接
    rtcPeerConnects[otherSocketId] = pc;
  }
  return pc;
}

/**
 * 移除RTCPeerConnection连接缓存
 * @param {string} otherSocketId 对方socketId
 */
function removeRtcConnect(otherSocketId) {
  delete rtcPeerConnects[otherSocketId];
}


/**
 * RTCPeerConnection 事件回调，获取icecandidate信息回调
 * @param {*} pc 
 * @param {*} otherSocketId 
 * @param {*} event 
 */
 function onIceCandidate(pc, otherSocketId, event) {
  console.log('onIceCandidate to ' + otherSocketId + ' candidate: ', event);
  if (event.candidate !== null) {
    // 构建信息 [from,to,room,candidate[sdpMid,sdpMLineIndex,sdp]]
    const message = {
      from: socketId,
      to: otherSocketId,
      room: roomId,
      candidate: {
        sdpMid: event.candidate.sdpMid,
        sdpMLineIndex: event.candidate.sdpMLineIndex,
        sdp: event.candidate.candidate
      }
    };
    console.log('向信令服务器发送candidate', message)
    // 向信令服务器发送candidate
    socket.emit('candidate', message);
  }
}

/**
 * 监听signal server转发过来的candidate消息
 */
socket.on('candidate', data => {
  // data:  [from,to,room,candidate[sdpMid,sdpMLineIndex,sdp]]
  console.log('candidate: ', data);
  const iceData = data.candidate;
  
  // 获取RTCPeerConnection
  const pc = getWebRTCConnect(data.from);
  
  const rtcIceCandidate = new RTCIceCandidate({
    candidate: iceData.sdp,
    sdpMid: iceData.sdpMid,
    sdpMLineIndex: iceData.sdpMLineIndex
  });

  console.log('添加对端Candidate')
  // 添加对端Candidate
  pc.addIceCandidate(rtcIceCandidate);
})

/**
 * 获取对端stream数据回调-ontrack模式
 * @param {*} pc pushStreamToVideo
 * @param {*} otherSocketId 
 * @param {*} event 
 */
 function onTrack(pc, otherSocketId, event) {
  console.log('onTrack from: ' + otherSocketId);
  console.log(event)
  let otherVideoDom = $('#' + otherSocketId);
  if (otherVideoDom.length === 0) { // TODO 未知原因：会两次onTrack，就会导致建立两次dom
    const video = document.createElement('video');
    video.id = otherSocketId;
    video.autoplay = 'autoplay';
    video.muted = 'muted';
    video.style.width = 200;
    video.style.height = 200;
    video.style.marginRight = 5;
    $('#remoteDiv').append(video);
  }
  pushStreamToVideo($('#' + otherSocketId)[0], event.streams[0])
}


/**
 * onRemoveStream回调
 * @param {*} pc 
 * @param {*} otherSocketId 
 * @param {*} event 
 */
 function onRemoveStream(pc, otherSocketId, event) {
  console.log('onRemoveStream from: ' + otherSocketId);
  // peer关闭
  getWebRTCConnect(otherSocketId).close;
  // 删除peer对象
  removeRtcConnect(otherSocketId)
  // 移除video
  $('#' + otherSocketId).remove();
}


/**
 * 挂断（退出房间）
 * @param {string} roomid 房间号
 */
const logout = roomid => {
  // 构建数据
  const data = {
    from: socketId, // 全局变量，我方的socketId
    room: roomid, // 全局变量，当前房间号
  };
  // 向信令服务器发出退出信号，让其转发给房间里的其他用户
  socket.emit('exit', data);
  // 数据重置
  socketId = '';
  roomId = '';
  // 关闭每个peer连接
  for (let i in rtcPeerConnects) {
    let pc = rtcPeerConnects[i];
    pc.close();
    pc = null;
  }
  // 重置RTCPeerConnection连接
  rtcPeerConnects = {};
  // 移除本地视频
  localStream = null;
}


/**
 * 监听signal server转发过来的exit消息，和退出房间的客户端断开连接
 */
socket.on('exit', data => {
  // data: [from,room]
  console.log('exit: ', data);
  // 获取RTCPeerConnection
  const pc = rtcPeerConnects[data.from];
  if (typeof (pc) == 'undefined') {
    return;
  } else {
    // RTCPeerConnection关闭
    getWebRTCConnect(data.from).close;

    // 删除peer对象
    removeRtcConnect(data.from)
    console.log($('#' + data.from))
    // 移除video
    $('#' + data.from).remove();
  }
})
