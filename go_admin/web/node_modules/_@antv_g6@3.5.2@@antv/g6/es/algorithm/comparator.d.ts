export default class Comparator {
    private compare;
    /**
     * @param {function(a: *, b: *)} [compareFunction] - It may be custom compare function that, let's
     * say may compare custom objects together.
     */
    constructor(compareFunction: any);
    /**
     * Default comparison function. It just assumes that "a" and "b" are strings or numbers.
     * @param {(string|number)} a
     * @param {(string|number)} b
     * @returns {number}
     */
    static defaultCompareFunction(a: any, b: any): 1 | -1 | 0;
    /**
     * Checks if two variables are equal.
     * @param {*} a
     * @param {*} b
     * @return {boolean}
     */
    equal(a: any, b: any): boolean;
    /**
     * Checks if variable "a" is less than "b".
     * @param {*} a
     * @param {*} b
     * @return {boolean}
     */
    lessThan(a: any, b: any): boolean;
    /**
     * Checks if variable "a" is greater than "b".
     * @param {*} a
     * @param {*} b
     * @return {boolean}
     */
    greaterThan(a: any, b: any): boolean;
    /**
     * Checks if variable "a" is less than or equal to "b".
     * @param {*} a
     * @param {*} b
     * @return {boolean}
     */
    lessThanOrEqual(a: any, b: any): boolean;
    /**
     * Checks if variable "a" is greater than or equal to "b".
     * @param {*} a
     * @param {*} b
     * @return {boolean}
     */
    greaterThanOrEqual(a: any, b: any): boolean;
    /**
     * Reverses the comparison order.
     */
    reverse(): void;
}
