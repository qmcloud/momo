import base from './tooltip-base';
export default Object.assign({
  getDefaultCfg: function getDefaultCfg() {
    return {
      item: 'edge',
      offset: 12,
      formatText: function formatText(model) {
        return "source: " + model.source + " target: " + model.target;
      }
    };
  },
  getEvents: function getEvents() {
    return {
      'edge:mouseenter': 'onMouseEnter',
      'edge:mouseleave': 'onMouseLeave',
      'edge:mousemove': 'onMouseMove'
    };
  }
}, base);