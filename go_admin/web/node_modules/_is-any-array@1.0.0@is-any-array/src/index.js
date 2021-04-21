const toString = Object.prototype.toString;

export default function isAnyArray(object) {
  return toString.call(object).endsWith('Array]');
}
