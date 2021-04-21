import base from './tooltip-base';
export default Object.assign({
  getDefaultCfg: function getDefaultCfg() {
    return {
      item: 'node',
      offset: 12,
      formatText: function formatText(model) {
        return model.label;
      }
    };
  },
  getEvents: function getEvents() {
    return {
      'node:mouseenter': 'onMouseEnter',
      'node:mouseleave': 'onMouseLeave',
      'node:mousemove': 'onMouseMove'
    };
  }
}, base);