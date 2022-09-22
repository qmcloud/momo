/**
 * dom获取
 */
 const btnConnect = $('#connect'); // 连接dom
 const btnLogout = $('#logout'); // 挂断dom
 const domLocalVideo = $('#localVideo'); // 本地视频dom
 
 const domRoom = $('#room'); // 获取房间号输入框dom

 /**
  * 连接
  */
 btnConnect.click(() => {
   const roomid = domRoom.val(); // 获取用户输入的房间号
   if (!roomid) {
     alert('房间号不能为空');
     return;
   };
   //启动摄像头
   if (localStream == null) {
     openCamera().then(stream => {
       localStream = stream; // 保存本地视频到全局变量
       pushStreamToVideo(domLocalVideo[0], stream);
       connect(roomid); // 成功打开摄像头后，开始创建或者加入输入的房间号
     }).catch(e => alert(`getUserMedia() error: ${e.name}`));
   }
 });
 
 /**
  * 挂断
  */
 btnLogout.click(() => {
   closeCamera(domLocalVideo[0]);
   logout(roomId); // 退出房间
   
   //移除远程视频
   $('#remoteDiv').empty();
 })