import { head, indexOf, last, map, size } from '@antv/util';

export const DEFAULT_Q = [1, 5, 2, 2.5, 4, 3];

export const ALL_Q = [1, 5, 2, 2.5, 4, 3, 1.5, 7, 6, 8, 9];

const eps = Number.EPSILON * 100;

// https://stackoverflow.com/questions/4467539/javascript-modulo-gives-a-negative-result-for-negative-numbers
function mod(n: number, m: number) {
  return ((n % m) + m) % m;
}

function simplicity(q: number, Q: number[], j: number, lmin: number, lmax: number, lstep: number) {
  const n = size(Q);
  const i = indexOf(Q, q);
  let v = 0;
  const m = mod(lmin, lstep);
  if ((m < eps || lstep - m < eps) && lmin <= 0 && lmax >= 0) {
    v = 1;
  }
  return 1 - i / (n - 1) - j + v;
}

function simplicityMax(q: number, Q: number[], j: number) {
  const n = size(Q);
  const i = indexOf(Q, q);
  const v = 1;
  return 1 - i / (n - 1) - j + v;
}

function density(k: number, m: number, dmin: number, dmax: number, lmin: number, lmax: number) {
  const r = (k - 1) / (lmax - lmin);
  const rt = (m - 1) / (Math.max(lmax, dmax) - Math.min(dmin, lmin));
  return 2 - Math.max(r / rt, rt / r);
}

function densityMax(k: number, m: number) {
  if (k >= m) {
    return 2 - (k - 1) / (m - 1);
  }
  return 1;
}

function coverage(dmin: number, dmax: number, lmin: number, lmax: number) {
  const range = dmax - dmin;
  return 1 - (0.5 * (Math.pow(dmax - lmax, 2) + Math.pow(dmin - lmin, 2))) / Math.pow(0.1 * range, 2);
}

function coverageMax(dmin: number, dmax: number, span: number) {
  const range = dmax - dmin;
  if (span > range) {
    const half = (span - range) / 2;
    return 1 - Math.pow(half, 2) / Math.pow(0.1 * range, 2);
  }
  return 1;
}

function legibility() {
  return 1;
}

/**
 * An Extension of Wilkinson's Algorithm for Position Tick Labels on Axes
 * https://www.yuque.com/preview/yuque/0/2019/pdf/185317/1546999150858-45c3b9c2-4e86-4223-bf1a-8a732e8195ed.pdf
 * @param dmin 最小值
 * @param dmax 最大值
 * @param m tick个数
 * @param onlyLoose 是否允许扩展min、max，不绝对强制，例如[3, 97]
 * @param Q nice numbers集合
 * @param w 四个优化组件的权重
 */
export default function extended(
  dmin: number,
  dmax: number,
  m: number = 5,
  onlyLoose: boolean = true,
  Q: number[] = DEFAULT_Q,
  w: [number, number, number, number] = [0.25, 0.2, 0.5, 0.05]
): { min: number; max: number; ticks: number[] } {
  // 异常数据情况下，直接返回，防止 oom
  if (typeof dmin !== 'number' || typeof dmax !== 'number' || !m) {
    return {
      min: 0,
      max: 0,
      ticks: [],
    };
  }
 
  // js 极大值极小值问题，差值小于 1e-15 会导致计算出错
  if (dmax - dmin < 1e-15 || m === 1) {
    return {
      min: dmin,
      max: dmax,
      ticks: [dmin],
    };
  }

  const best = {
    score: -2,
    lmin: 0,
    lmax: 0,
    lstep: 0,
  };
  let j = 1;
  while (j < Infinity) {
    for (const q of Q) {
      const sm = simplicityMax(q, Q, j);
      if (Number.isNaN(sm)) {
        throw new Error('NaN');
      }
      if (w[0] * sm + w[1] + w[2] + w[3] < best.score) {
        j = Infinity;
        break;
      }
      let k = 2;
      while (k < Infinity) {
        const dm = densityMax(k, m);
        if (w[0] * sm + w[1] + w[2] * dm + w[3] < best.score) {
          break;
        }

        const delta = (dmax - dmin) / (k + 1) / j / q;
        let z = Math.ceil(Math.log10(delta));

        while (z < Infinity) {
          const step = j * q * Math.pow(10, z);
          const cm = coverageMax(dmin, dmax, step * (k - 1));

          if (w[0] * sm + w[1] * cm + w[2] * dm + w[3] < best.score) {
            break;
          }

          const minStart = Math.floor(dmax / step) * j - (k - 1) * j;
          const maxStart = Math.ceil(dmin / step) * j;

          if (minStart > maxStart) {
            z = z + 1;
            continue;
          }
          for (let start = minStart; start <= maxStart; start = start + 1) {
            const lmin = start * (step / j);
            const lmax = lmin + step * (k - 1);
            const lstep = step;

            const s = simplicity(q, Q, j, lmin, lmax, lstep);
            const c = coverage(dmin, dmax, lmin, lmax);
            const g = density(k, m, dmin, dmax, lmin, lmax);
            const l = legibility();

            const score = w[0] * s + w[1] * c + w[2] * g + w[3] * l;
            if (score > best.score && (!onlyLoose || (lmin <= dmin && lmax >= dmax))) {
              best.lmin = lmin;
              best.lmax = lmax;
              best.lstep = lstep;
              best.score = score;
            }
          }
          z = z + 1;
        }
        k = k + 1;
      }
    }
    j = j + 1;
  }
  // 步长为浮点数时处理精度
  const toFixed = Number.isInteger(best.lstep) ? 0 : Math.ceil(Math.abs(Math.log10(best.lstep)));
  const range = [];
  for (let tick = best.lmin; tick <= best.lmax; tick += best.lstep) {
    range.push(tick);
  }
  const ticks = toFixed ? map(range, (x: number) => Number.parseFloat(x.toFixed(toFixed))) : range;

  return {
    min: Math.min(dmin, head(ticks)),
    max: Math.max(dmax, last(ticks)),
    ticks,
  };
}
