export default class Color {
  constructor (min) {
    this.min = min || 0
    this._init(this.min)
  }

  _init (min) {
    this.r = this.colorValue(min)
    this.g = this.colorValue(min)
    this.b = this.colorValue(min)
    this.style = this.createColorStyle(this.r, this.g, this.b)
  }

  colorValue (min) {
    return Math.floor(Math.random() * 255 + min)
  }

  createColorStyle (r, g, b) {
    return `rgba(${r}, ${g}, ${b}, .8)`
  }
}
