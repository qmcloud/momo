$(function(){
  var canvas = document.getElementById( 'canvas' ),
  context = canvas.getContext( '2d' );
  var canvasWidth = window.innerWidth; 
  var canvasHeight = window.innerHeight;
  canvas.width = canvasWidth;
  canvas.height = canvasHeight;
  var x = canvasWidth*.5;
  var y = canvasHeight*0.12;
  var particle;
  var time;
  var particles = [];
  canvas.addEventListener("touchend",function(event){
        clearInterval(time);
        particle = new  Particle(x, y);
        particle.xVel = Math.random();
        if(particles.length<12){
          particles.push(particle); //加入数组
        }
        time = setInterval(loop, 30); //20刷新一次
  })
  // canvas.onclick = function(e){
  //       clearInterval(time);
  //       particle = new  Particle(x, y);
  //       particle.xVel = Math.random();
  //       if(particles.length<12){
  //         particles.push(particle); //加入数组
  //       }
  //       time = setInterval(loop, 30); //20刷新一次
  //   }
  //     
    var particles = [];
     function loop(){ 
      // 清除canvas中的内容 
        context.clearRect( 0, 0, canvasWidth, canvasHeight);
        context.fillStyle = "rgba(0,0,0,0)";
        context.fillRect(0,0, canvasWidth, canvasHeight);

           // 绘制数组中的每一个粒子
            for (i=0; i<particles.length; i++) {
                if (particles[i].yPos >= canvasHeight){  //保留半径大于0的粒子
                    particles.shift();
                    if( particles.length == 0){
                        //console.log("stop"+particles.length);
                        clearInterval(time);
                        context.clearRect( 0, 0, canvasWidth, canvasHeight);
                    }
                   // console.log("shift");
                }
                var particle = particles[i];
                if(particle){
                    particle.draw(context); 
                    particle.update(); 
                }
            }
        
     }
     
 //粒子类
  function Particle (xPos, yPos) { 
    this.xPos = xPos;//中心X坐标
    this.yPos = yPos; //中心Y坐标
    if(Math.round(Math.random()) == 0){
            this.yVel =Math.random()*4;
        }else{
            this.yVel =Math.random()*4;
        }
    this.rad = 3;//半径初始化
    this.xVel = 0;
    this.gravity = 0.2;//重力影响
    this.radChange = 0.2; //半径变化
    this.Xmax = 100; //半径变化
    this.opcity = 1;
    this.opcityChange = 0.1;

    this.counter = rgbaStr();// 颜色
    this.draw = function(c) {
                //c.fillStyle = this.counter+","+this.opcity+")";
                c.fillStyle ="rgba(255,97,1,"+this.opcity+")";
                c.strokeStyle="rgba(255,255,255,"+this.opcity+")";
                c.lineWidth =0.05;
                var r = 0,a = 15,
                start = 0,end = 0;
                c.beginPath();
                for (var q = 0; q < 1000; q++) {
                    start += Math.PI * 2 / 500;
                    end = start + Math.PI * 2 / 500;
                    r = a * Math.sqrt(this.rad/(15 - 16 * Math.sin(start) * Math.sqrt(Math.cos(start) * Math.cos(start))));
                    c.arc(this.xPos, this.yPos, r, start, end, false);
                }
                c.fill();
                c.stroke();

        }
    this.update = function(){ //更新自己的方法
            this.yVel += this.gravity;
        if(this.yPos - y >= 200){
            this.opcity -= this.opcityChange;
        }
        if(Math.round(Math.random()) == 0 ){
             this.xVel +=this.gravity;
        }else{
             this.xVel -= this.gravity;
        }
        this.yPos += this.yVel;
        this.xPos += this.xVel;
        if ( this.rad < 10 )
        this.rad += this.radChange;
        if(this.rad < 0 )
            this.rad = 0;
    }

  }

function rand(min,max){
    return min+parseInt((max-min)*Math.random());
}

//随机rgba颜色值
function rgbaStr(){
    return "rgba("+rand(100,255)+","+rand(1,200)+","+rand(10,200);
}
})


