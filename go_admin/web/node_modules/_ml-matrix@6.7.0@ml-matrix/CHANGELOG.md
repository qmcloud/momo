# Changelog

## [6.7.0](https://www.github.com/mljs/matrix/compare/v6.6.0...v6.7.0) (2021-03-12)


### Features

* add Kronecker sum ([#119](https://www.github.com/mljs/matrix/issues/119)) ([a600b2c](https://www.github.com/mljs/matrix/commit/a600b2cb00f86576620a187f158d52680f752d89))

## [6.6.0](https://www.github.com/mljs/matrix/compare/v6.5.3...v6.6.0) (2021-01-04)


### Features

* add support for empty matrices ([#116](https://www.github.com/mljs/matrix/issues/116)) ([211de6e](https://www.github.com/mljs/matrix/commit/211de6e0880720033862f94a9629e48ae1787109))

### [6.5.3](https://www.github.com/mljs/matrix/compare/v6.5.2...v6.5.3) (2020-10-11)


### Bug Fixes

* update dependencies and move documentation to gh-pages ([78e0724](https://www.github.com/mljs/matrix/commit/78e07240ae9f114c4876a7838e37d9cc95336620))

### [6.5.2](https://github.com/mljs/matrix/compare/v6.5.1...v6.5.2) (2020-10-09)


### Bug Fixes

* benchmark of transposeViewMul ([#106](https://github.com/mljs/matrix/issues/106)) ([6407086](https://github.com/mljs/matrix/commit/64070866dfcde5fe48fa70de443ddd804f67d998))


### Reverts

* Revert "chore: migrate release to GitHub actions (#107)" ([21ba23a](https://github.com/mljs/matrix/commit/21ba23ac133fba057769d843d09614fad09edfc9)), closes [#107](https://github.com/mljs/matrix/issues/107)

## [6.5.1](https://github.com/mljs/matrix/compare/v6.5.0...v6.5.1) (2020-07-28)


### Bug Fixes

* **types:** add missing removeColumn and removeRow types ([8010f31](https://github.com/mljs/matrix/commit/8010f3182684589558e8497d9b9230dc4725d848))
* **types:** add types for addColum and addRow ([#105](https://github.com/mljs/matrix/issues/105)) ([b372b80](https://github.com/mljs/matrix/commit/b372b8083b24c8ff4ce55b3a5c0d2d67e16e0e8e))



# [6.5.0](https://github.com/mljs/matrix/compare/v6.4.1...v6.5.0) (2020-05-03)


### Bug Fixes

* do not change input matrices in correlation and covariance functions ([#103](https://github.com/mljs/matrix/issues/103)) ([32e3537](https://github.com/mljs/matrix/commit/32e3537aae0ed4d8cf20c6230a4d411f944b1bcb))


### Features

* add options to toString method ([67b007c](https://github.com/mljs/matrix/commit/67b007cd0e3fb80131e3cdb434868fe3c503ef89))
* add toString method ([dcd5ab2](https://github.com/mljs/matrix/commit/dcd5ab28a8190e3602335bca40d7d34b7afbb15e))



## [6.4.1](https://github.com/mljs/matrix/compare/v6.4.0...v6.4.1) (2019-09-30)


### Bug Fixes

* correctly ready elements in QR#orthogonalMatrix ([2f527a3](https://github.com/mljs/matrix/commit/2f527a3))



# [6.4.0](https://github.com/mljs/matrix/compare/v6.3.0...v6.4.0) (2019-08-16)


### Features

* add CholeskyDecomposition.isPositiveDefinite method ([#94](https://github.com/mljs/matrix/issues/94)) ([6bb33a9](https://github.com/mljs/matrix/commit/6bb33a9))



# [6.3.0](https://github.com/mljs/matrix/compare/v6.2.0...v6.3.0) (2019-08-16)


### Features

* add UMD build ([#92](https://github.com/mljs/matrix/issues/92)) ([3b82b07](https://github.com/mljs/matrix/commit/3b82b07))



# [6.2.0](https://github.com/mljs/matrix/compare/v6.1.2...v6.2.0) (2019-07-20)


### Features

* add NIPALS loop for factorization ([#91](https://github.com/mljs/matrix/issues/91)) ([043c8b6](https://github.com/mljs/matrix/commit/043c8b6))



## [6.1.2](https://github.com/mljs/matrix/compare/v6.1.1...v6.1.2) (2019-06-29)


### Bug Fixes

* use more Float64Array in decompositions ([0bd8f1b](https://github.com/mljs/matrix/commit/0bd8f1b))
* **Matrix:** use Float64Array to improve performance ([9dfe983](https://github.com/mljs/matrix/commit/9dfe983))
* **SVD:** use Float64Array to avoid deopt ([85acd13](https://github.com/mljs/matrix/commit/85acd13))



## [6.1.1](https://github.com/mljs/matrix/compare/v6.1.0...v6.1.1) (2019-06-28)



# [6.1.0](https://github.com/mljs/matrix/compare/v6.0.0...v6.1.0) (2019-06-22)


### Features

* add echelonForm method ([eac0588](https://github.com/mljs/matrix/commit/eac0588))
* add reducedEchelonForm method ([f32a8aa](https://github.com/mljs/matrix/commit/f32a8aa))
* add statistical operations ([43fc4ef](https://github.com/mljs/matrix/commit/43fc4ef))



# [6.0.0](https://github.com/mljs/matrix/compare/v6.0.0-6...v6.0.0) (2019-04-25)



# [6.0.0-6](https://github.com/mljs/matrix/compare/v6.0.0-5...v6.0.0-6) (2019-04-25)


### Bug Fixes

* add linearDependencies to TS definitions ([22c4f60](https://github.com/mljs/matrix/commit/22c4f60))


### Code Refactoring

* rework a lot of things ([1b3cb03](https://github.com/mljs/matrix/commit/1b3cb03))


### Features

* add a custom Node.js inspect function ([cb51169](https://github.com/mljs/matrix/commit/cb51169))
* rename reverse methods to split ([def2977](https://github.com/mljs/matrix/commit/def2977))


### BREAKING CHANGES

* The signature of a few methods changed to take an options object:
- Matrix.rand / Matrix.random
- Matrix.randInt
- Matrix.prototype.repeat
- Matrix.prototype.scaleRows
- Matrix.prototype.scaleColumns



# [6.0.0-5](https://github.com/mljs/matrix/compare/v6.0.0-4...v6.0.0-5) (2019-04-18)


### Code Refactoring

* remove configurable super class and circular dependencies ([dd35ec8](https://github.com/mljs/matrix/commit/dd35ec8))


### BREAKING CHANGES

* * It is no longer possible to make a Matrix class that extends a custom constructor
* `matrix.det()` was moved to a standalone function: `determinant(matrix)`
* `matrix.pseudoInverse()` was moved to a standalone function: `pseudoInverse(matrix)`
* `matrix.linearDependencies()` was moved to a standalone function: `linearDependencies(matrix)`
* Matrix views must be created using their constructors instead of Matrix methods.
  For example, `matrix.transposeView()` becomes `new MatrixTransposeView(matrix)`



# [6.0.0-4](https://github.com/mljs/matrix/compare/v6.0.0-3...v6.0.0-4) (2019-04-18)


### Features

* implement reverseRows and reverseColumns methods ([77e5ed7](https://github.com/mljs/matrix/commit/77e5ed7))



# [6.0.0-3](https://github.com/mljs/matrix/compare/v6.0.0-2...v6.0.0-3) (2019-04-18)



# [6.0.0-2](https://github.com/mljs/matrix/compare/v6.0.0-1...v6.0.0-2) (2019-04-18)


### Features

* make JSON.stringify always return a 2D array from any matrix ([021115b](https://github.com/mljs/matrix/commit/021115b))



# [6.0.0-1](https://github.com/mljs/matrix/compare/v6.0.0-0...v6.0.0-1) (2019-04-18)


### Code Refactoring

* make sum by row or column return an array ([dbe7c99](https://github.com/mljs/matrix/commit/dbe7c99))


### Features

* add entropy method ([63b95d1](https://github.com/mljs/matrix/commit/63b95d1))
* add mean by dimension and product methods ([6b57aae](https://github.com/mljs/matrix/commit/6b57aae))
* add variance and standardDeviation methods ([f42f1b6](https://github.com/mljs/matrix/commit/f42f1b6))


### BREAKING CHANGES

* `matrix.sum('row')` and `matrix.sum('column')` now return an array instead of a Matrix.



# [6.0.0-0](https://github.com/mljs/matrix/compare/v5.3.0...v6.0.0-0) (2019-04-18)


### chore

* remove support for Node 6 ([42e4fde](https://github.com/mljs/matrix/commit/42e4fde))


### Code Refactoring

* stop extending Array ([1837678](https://github.com/mljs/matrix/commit/1837678))


### BREAKING CHANGES

* Node.js 6 is no longer supported.
* * Matrix no longer extends the Array class. It means that it is not
  possible to access and set values using array indices (e.g. matrix[i][j]).
  The only supported way is to use matrix.get() and matrix.set().
* New matrices are now always filled with zeros instead of `undefined`.
* The static Matrix.empty() function was removed.



# [5.3.0](https://github.com/mljs/matrix/compare/v5.2.1...v5.3.0) (2019-03-23)


### Bug Fixes

* add isEchelonForm and isReducedEchelonForm to typings ([690edd1](https://github.com/mljs/matrix/commit/690edd1))
* correct matrix.d.ts file. ([#86](https://github.com/mljs/matrix/issues/86)) ([ebb273c](https://github.com/mljs/matrix/commit/ebb273c))


### Features

* add isEchelonForm and isReducedEchelonForm ([#84](https://github.com/mljs/matrix/issues/84)) ([dee2a94](https://github.com/mljs/matrix/commit/dee2a94))



## [5.2.1](https://github.com/mljs/matrix/compare/v5.2.0...v5.2.1) (2019-01-07)


### Bug Fixes

* correct matrix.d.ts to follow TypeScript 3 ([#81](https://github.com/mljs/matrix/issues/81)) ([99329fd](https://github.com/mljs/matrix/commit/99329fd))



# [5.2.0](https://github.com/mljs/matrix/compare/v5.1.1...v5.2.0) (2018-09-25)


### Bug Fixes

* complete type definitions ([ca63059](https://github.com/mljs/matrix/commit/ca63059))


### Features

* create index.d.ts ([#74](https://github.com/mljs/matrix/issues/74)) ([905c987](https://github.com/mljs/matrix/commit/905c987))



## [5.1.1](https://github.com/mljs/matrix/compare/v5.1.0...v5.1.1) (2018-05-11)


### Bug Fixes

* prevent infinite loop ([f684d90](https://github.com/mljs/matrix/commit/f684d90))



# [5.1.0](https://github.com/mljs/matrix/compare/v5.0.1...v5.1.0) (2018-05-04)


### Features

* add linearDependencies method ([88ee3df](https://github.com/mljs/matrix/commit/88ee3df))


### Performance Improvements

* add transposeViewMul benchmark ([0d24ea9](https://github.com/mljs/matrix/commit/0d24ea9))



## [5.0.1](https://github.com/mljs/matrix/compare/v5.0.0...v5.0.1) (2017-07-28)


### Bug Fixes

* Add test case ([4b72211](https://github.com/mljs/matrix/commit/4b72211))
* bug with SVD ([f615aa3](https://github.com/mljs/matrix/commit/f615aa3))
* rollup didn't understood .. ([3af231d](https://github.com/mljs/matrix/commit/3af231d))



# [5.0.0](https://github.com/mljs/matrix/compare/v4.0.0...v5.0.0) (2017-07-21)


### Code Refactoring

* change decompositions to classes ([00c18e8](https://github.com/mljs/matrix/commit/00c18e8))


### BREAKING CHANGES

* Now decompositions have to be created with "new".



# [4.0.0](https://github.com/mljs/matrix/compare/v3.0.0...v4.0.0) (2017-07-19)


### Code Refactoring

* remove dependency on ml-array-utils ([1e7119d](https://github.com/mljs/matrix/commit/1e7119d))


### Features

* **wrap:** create a 2D or 1D WrapperMatrix ([#52](https://github.com/mljs/matrix/issues/52)) ([7900d67](https://github.com/mljs/matrix/commit/7900d67))
* add norm method ([#57](https://github.com/mljs/matrix/issues/57)) ([221391a](https://github.com/mljs/matrix/commit/221391a))
* allows to select only rows or columns as view ([#51](https://github.com/mljs/matrix/issues/51)) ([46eb916](https://github.com/mljs/matrix/commit/46eb916))


### BREAKING CHANGES

* The new ml-array-rescale dependency removes support for Node 4



# [3.0.0](https://github.com/mljs/matrix/compare/v2.3.0...v3.0.0) (2017-04-25)



# [2.3.0](https://github.com/mljs/matrix/compare/v2.2.0...v2.3.0) (2017-02-28)


### Features

* add pseudoinverse function based on SVD ([3279a15](https://github.com/mljs/matrix/commit/3279a15))



# [2.2.0](https://github.com/mljs/matrix/compare/v2.1.0...v2.2.0) (2016-12-14)


### Bug Fixes

* Matrix and Lu circular dependency ([ab706b9](https://github.com/mljs/matrix/commit/ab706b9))
* styling issues picked up by Travis CI ([f211a1f](https://github.com/mljs/matrix/commit/f211a1f))


### Features

* **det:** add 2x2 and 3x3 determinants ([04ae195](https://github.com/mljs/matrix/commit/04ae195))
* **det:** add determinant based on LU decomposition ([90532ef](https://github.com/mljs/matrix/commit/90532ef))
* **det:** add determinant synonym ([5395b56](https://github.com/mljs/matrix/commit/5395b56))
* **sum:** sum by 'row' or 'column' ([bf5d070](https://github.com/mljs/matrix/commit/bf5d070))



# [2.1.0](https://github.com/mljs/matrix/compare/v2.0.0...v2.1.0) (2016-10-07)


### Bug Fixes

* use Symbol.species as Matrix constructor in selection ([fee325e](https://github.com/mljs/matrix/commit/fee325e))
* use Symbol.species in evaluated static methods ([39800f9](https://github.com/mljs/matrix/commit/39800f9))


### Features

* add fast multiplication algorithm (strassen) ([fdc1c07](https://github.com/mljs/matrix/commit/fdc1c07))
* add maxValue option to Matrix.randInt ([e5a8541](https://github.com/mljs/matrix/commit/e5a8541))
* add value parameter to Matrix.eye ([f52e4fd](https://github.com/mljs/matrix/commit/f52e4fd)), closes [#43](https://github.com/mljs/matrix/issues/43)
* implement optimized algorithm for 2x2 and 3x3 multiplication ([4055ef9](https://github.com/mljs/matrix/commit/4055ef9))



# [2.0.0](https://github.com/mljs/matrix/compare/v1.4.0...v2.0.0) (2016-08-04)


### Features

* add column view ([5ff6680](https://github.com/mljs/matrix/commit/5ff6680))
* add flipColumn and flipRow views ([55ee4a6](https://github.com/mljs/matrix/commit/55ee4a6))
* add method subMatrixView ([aa1df18](https://github.com/mljs/matrix/commit/aa1df18))
* add row view ([a9e99f2](https://github.com/mljs/matrix/commit/a9e99f2))
* add selection method and selection view ([59aa861](https://github.com/mljs/matrix/commit/59aa861))
* make use of Symbol.species to allow creating new matrices in any class ([eaee5de](https://github.com/mljs/matrix/commit/eaee5de))



# [1.4.0](https://github.com/mljs/matrix/compare/v1.3.0...v1.4.0) (2016-08-03)


### Features

* add concept of abstract matrix ([cbefc9b](https://github.com/mljs/matrix/commit/cbefc9b))
* add method setSubMatrix ([89b4242](https://github.com/mljs/matrix/commit/89b4242))
* add method with one argument template ([b66ee9f](https://github.com/mljs/matrix/commit/b66ee9f))
* add repeat method ([8b9eecb](https://github.com/mljs/matrix/commit/8b9eecb))
* add transposeView ([fb0a0c9](https://github.com/mljs/matrix/commit/fb0a0c9))


### BREAKING CHANGES

* This is a non trivial change and could potentially break existing code.
There is no known backward incompatibility though.



# [1.3.0](https://github.com/mljs/matrix/compare/v1.2.1...v1.3.0) (2016-07-25)


### Features

* add methods scaleRows and scaleColumns ([8516f83](https://github.com/mljs/matrix/commit/8516f83))



## [1.2.1](https://github.com/mljs/matrix/compare/v1.2.0...v1.2.1) (2016-07-07)


### Bug Fixes

* do not use rest parameters ([2c4502e](https://github.com/mljs/matrix/commit/2c4502e))



# [1.2.0](https://github.com/mljs/matrix/compare/v1.1.5...v1.2.0) (2016-07-07)


### Features

* add support for Math.pow ([2524b73](https://github.com/mljs/matrix/commit/2524b73)), closes [#21](https://github.com/mljs/matrix/issues/21)



## [1.1.5](https://github.com/mljs/matrix/compare/v1.1.4...v1.1.5) (2016-05-31)



## [1.1.4](https://github.com/mljs/matrix/compare/v1.1.3...v1.1.4) (2016-05-27)



## [1.1.3](https://github.com/mljs/matrix/compare/v1.1.2...v1.1.3) (2016-05-27)



## [1.1.2](https://github.com/mljs/matrix/compare/v1.1.1...v1.1.2) (2016-05-18)



## [1.1.1](https://github.com/mljs/matrix/compare/v1.1.0...v1.1.1) (2016-05-18)



# [1.1.0](https://github.com/mljs/matrix/compare/v1.0.4...v1.1.0) (2016-05-13)



## [1.0.4](https://github.com/mljs/matrix/compare/v1.0.3...v1.0.4) (2015-11-21)



## [1.0.3](https://github.com/mljs/matrix/compare/v1.0.2...v1.0.3) (2015-11-19)


### Bug Fixes

* random not correctly filling rectangular matrices ([a79c3eb](https://github.com/mljs/matrix/commit/a79c3eb))



## [1.0.2](https://github.com/mljs/matrix/compare/v1.0.1...v1.0.2) (2015-10-05)



## [1.0.1](https://github.com/mljs/matrix/compare/v1.0.0...v1.0.1) (2015-09-11)



# [1.0.0](https://github.com/mljs/matrix/compare/v1.0.0-0...v1.0.0) (2015-09-10)



# [1.0.0-0](https://github.com/mljs/matrix/compare/v0.1.0...v1.0.0-0) (2015-09-09)


### Bug Fixes

* **matrix:** abs method should return the instance ([cd96b4b](https://github.com/mljs/matrix/commit/cd96b4b))


### Features

* add fullname synonyms for some methods ([4845a43](https://github.com/mljs/matrix/commit/4845a43))
* add static min and max methods ([41707af](https://github.com/mljs/matrix/commit/41707af))
* support all arithmetic operators and Math functions including static versions ([521e4fe](https://github.com/mljs/matrix/commit/521e4fe)), closes [#7](https://github.com/mljs/matrix/issues/7)



# [0.1.0](https://github.com/mljs/matrix/compare/v0.0.4...v0.1.0) (2015-06-11)



## [0.0.4](https://github.com/mljs/matrix/compare/v0.0.1...v0.0.4) (2015-06-11)



## 0.0.1 (2014-10-24)
