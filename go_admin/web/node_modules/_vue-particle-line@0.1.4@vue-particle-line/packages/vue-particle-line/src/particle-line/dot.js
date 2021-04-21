import Color from './color'

export default class Dot {
  constructor (ctx, canvasWidth, canvasHeight, x, y) {
    this.ctx = ctx
    this.x = x || Math.random() * canvasWidth
    this.y = y || Math.random() * canvasHeight
    this._init()
  }

  _init () {
    this.vx = -0.5 + Math.random()
    this.vy = -0.5 + Math.random()
    this.radius = Math.random() * 3
    this.color = new Color()
  }

  draw () {
    this.ctx.beginPath()
    this.ctx.fillStyle = this.color.style
    this.ctx.arc(this.x, this.y, this.radius, 0, Math.PI * 2, false)
    this.ctx.fill()
  }
}
