import * as mat3 from '@antv/gl-matrix/lib/gl-matrix/mat3';
mat3.translate = function (out, a, v) {
    var transMat = new Array(9);
    mat3.fromTranslation(transMat, v);
    return mat3.multiply(out, transMat, a);
};
mat3.rotate = function (out, a, rad) {
    var rotateMat = new Array(9);
    mat3.fromRotation(rotateMat, rad);
    return mat3.multiply(out, rotateMat, a);
};
mat3.scale = function (out, a, v) {
    var scaleMat = new Array(9);
    mat3.fromScaling(scaleMat, v);
    return mat3.multiply(out, scaleMat, a);
};
mat3.transform = function (m, actions) {
    var out = [].concat(m);
    for (var i = 0, len = actions.length; i < len; i++) {
        var action = actions[i];
        switch (action[0]) {
            case 't':
                mat3.translate(out, out, [action[1], action[2]]);
                break;
            case 's':
                mat3.scale(out, out, [action[1], action[2]]);
                break;
            case 'r':
                mat3.rotate(out, out, action[1]);
                break;
            default:
                break;
        }
    }
    return out;
};
export default mat3;
//# sourceMappingURL=mat3.js.map