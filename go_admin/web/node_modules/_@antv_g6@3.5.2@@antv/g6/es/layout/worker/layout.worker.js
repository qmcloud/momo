/**
 * @fileoverview web worker for layout
 * @author changzhe.zb@antfin.com
 */
import Layout from '../index';
import { LAYOUT_MESSAGE } from './layoutConst';
var ctx = self;

function isLayoutMessage(event) {
  var type = event.data.type;
  return type === LAYOUT_MESSAGE.RUN;
}

function handleLayoutMessage(event) {
  var type = event.data.type;

  switch (type) {
    case LAYOUT_MESSAGE.RUN:
      {
        var _a = event.data,
            nodes = _a.nodes,
            edges = _a.edges,
            _b = _a.layoutCfg,
            layoutCfg = _b === void 0 ? {} : _b;
        var layoutType = layoutCfg.type;
        var LayoutClass = Layout[layoutType];

        if (!LayoutClass) {
          ctx.postMessage({
            type: LAYOUT_MESSAGE.ERROR,
            message: "layout " + layoutType + " not found"
          });
          break;
        }

        var layoutMethod = new LayoutClass(layoutCfg);
        layoutMethod.init({
          nodes: nodes,
          edges: edges
        });
        layoutMethod.execute();
        ctx.postMessage({
          type: LAYOUT_MESSAGE.END,
          nodes: nodes
        });
        layoutMethod.destroy();
        break;
      }

    default:
      break;
  }
} // listen to message posted to web worker


ctx.onmessage = function (event) {
  if (isLayoutMessage(event)) {
    handleLayoutMessage(event);
  }
}; // https://stackoverflow.com/questions/50210416/webpack-worker-loader-fails-to-compile-typescript-worker


export default null;