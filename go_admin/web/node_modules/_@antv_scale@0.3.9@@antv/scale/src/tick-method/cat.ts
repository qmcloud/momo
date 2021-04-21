import { filter, isNil, isNumber } from '@antv/util';
import { ScaleConfig } from '../types';
import extended from '../util/extended';

/**
 * 计算分类 ticks
 * @param cfg 度量的配置项
 * @returns 计算后的 ticks
 */
export default function calculateCatTicks(cfg: ScaleConfig): any[] {
  const { values, tickInterval, tickCount } = cfg;

  const ticks = values;
  if (isNumber(tickInterval)) {
    return filter(ticks, (__: any, i: number) => i % tickInterval === 0);
  }
  let { min, max } = cfg;
  if (isNil(min)) {
    min = 0;
  }
  if (isNil(max)) {
    max = values.length - 1;
  }
  if (isNumber(tickCount) && tickCount < max - min) {
    // 简单过滤，部分情况下小数的倍数也可以是整数
    // tslint:disable-next-line: no-shadowed-variable
    const { ticks } = extended(min, max, tickCount, false, [1, 2, 5, 3, 4, 7, 6, 8, 9]);
    const valid = filter(ticks, (tick) => tick >= min && tick <= max);
    return valid.map((index) => values[index]);
  }
  return values.slice(min, max + 1);
}
