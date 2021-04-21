/**
 * @private
 * Check that a row index is not out of bounds
 * @param {Matrix} matrix
 * @param {number} index
 * @param {boolean} [outer]
 */
export function checkRowIndex(matrix, index, outer) {
  let max = outer ? matrix.rows : matrix.rows - 1;
  if (index < 0 || index > max) {
    throw new RangeError('Row index out of range');
  }
}

/**
 * @private
 * Check that a column index is not out of bounds
 * @param {Matrix} matrix
 * @param {number} index
 * @param {boolean} [outer]
 */
export function checkColumnIndex(matrix, index, outer) {
  let max = outer ? matrix.columns : matrix.columns - 1;
  if (index < 0 || index > max) {
    throw new RangeError('Column index out of range');
  }
}

/**
 * @private
 * Check that the provided vector is an array with the right length
 * @param {Matrix} matrix
 * @param {Array|Matrix} vector
 * @return {Array}
 * @throws {RangeError}
 */
export function checkRowVector(matrix, vector) {
  if (vector.to1DArray) {
    vector = vector.to1DArray();
  }
  if (vector.length !== matrix.columns) {
    throw new RangeError(
      'vector size must be the same as the number of columns',
    );
  }
  return vector;
}

/**
 * @private
 * Check that the provided vector is an array with the right length
 * @param {Matrix} matrix
 * @param {Array|Matrix} vector
 * @return {Array}
 * @throws {RangeError}
 */
export function checkColumnVector(matrix, vector) {
  if (vector.to1DArray) {
    vector = vector.to1DArray();
  }
  if (vector.length !== matrix.rows) {
    throw new RangeError('vector size must be the same as the number of rows');
  }
  return vector;
}

export function checkIndices(matrix, rowIndices, columnIndices) {
  return {
    row: checkRowIndices(matrix, rowIndices),
    column: checkColumnIndices(matrix, columnIndices),
  };
}

export function checkRowIndices(matrix, rowIndices) {
  if (typeof rowIndices !== 'object') {
    throw new TypeError('unexpected type for row indices');
  }

  let rowOut = rowIndices.some((r) => {
    return r < 0 || r >= matrix.rows;
  });

  if (rowOut) {
    throw new RangeError('row indices are out of range');
  }

  if (!Array.isArray(rowIndices)) rowIndices = Array.from(rowIndices);

  return rowIndices;
}

export function checkColumnIndices(matrix, columnIndices) {
  if (typeof columnIndices !== 'object') {
    throw new TypeError('unexpected type for column indices');
  }

  let columnOut = columnIndices.some((c) => {
    return c < 0 || c >= matrix.columns;
  });

  if (columnOut) {
    throw new RangeError('column indices are out of range');
  }
  if (!Array.isArray(columnIndices)) columnIndices = Array.from(columnIndices);

  return columnIndices;
}

export function checkRange(matrix, startRow, endRow, startColumn, endColumn) {
  if (arguments.length !== 5) {
    throw new RangeError('expected 4 arguments');
  }
  checkNumber('startRow', startRow);
  checkNumber('endRow', endRow);
  checkNumber('startColumn', startColumn);
  checkNumber('endColumn', endColumn);
  if (
    startRow > endRow ||
    startColumn > endColumn ||
    startRow < 0 ||
    startRow >= matrix.rows ||
    endRow < 0 ||
    endRow >= matrix.rows ||
    startColumn < 0 ||
    startColumn >= matrix.columns ||
    endColumn < 0 ||
    endColumn >= matrix.columns
  ) {
    throw new RangeError('Submatrix indices are out of range');
  }
}

export function newArray(length, value = 0) {
  let array = [];
  for (let i = 0; i < length; i++) {
    array.push(value);
  }
  return array;
}

function checkNumber(name, value) {
  if (typeof value !== 'number') {
    throw new TypeError(`${name} must be a number`);
  }
}

export function checkNonEmpty(matrix) {
  if (matrix.isEmpty()) {
    throw new Error('Empty matrix has no elements to index');
  }
}
