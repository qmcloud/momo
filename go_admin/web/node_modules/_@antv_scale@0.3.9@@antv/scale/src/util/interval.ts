
import { fixedBase } from '@antv/util';

function snapMultiple(v, base, snapType) {
  let div;
  if (snapType === 'ceil') {
    div = Math.ceil(v / base);
  } else if (snapType === 'floor') {
    div = Math.floor(v / base);
  } else {
    div = Math.round(v / base);
  }
  return div * base;
}

export default function intervalTicks(min, max, interval) {
  // 变成 interval 的倍数
  let minTick = snapMultiple(min, interval, 'floor'); 
  let maxTick = snapMultiple(max, interval, 'ceil'); 
  // 统一小数位数
  minTick = fixedBase(minTick, interval);
  maxTick = fixedBase(maxTick, interval);
  const ticks = [];
  for (let i = minTick; i <= maxTick; i = i + interval) {
    const tickValue = fixedBase(i, interval); // 防止浮点数加法出现问题
    ticks.push(tickValue);
  }
  return {
    min: minTick,
    max: maxTick,
    ticks
  };
}