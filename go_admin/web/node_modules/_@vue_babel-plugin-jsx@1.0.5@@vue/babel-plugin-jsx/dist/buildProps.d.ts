import * as t from '@babel/types';
import { NodePath } from '@babel/traverse';
import { State } from '.';
export declare type Slots = t.Identifier | t.ObjectExpression | null;
declare const buildProps: (path: NodePath<t.JSXElement>, state: State) => {
    tag: t.CallExpression | t.Identifier | t.StringLiteral | t.MemberExpression;
    props: t.ArrayExpression | t.ArrowFunctionExpression | t.AssignmentExpression | t.AwaitExpression | t.BigIntLiteral | t.BinaryExpression | t.LogicalExpression | t.BindExpression | t.FunctionExpression | t.BooleanLiteral | t.CallExpression | t.ClassExpression | t.ConditionalExpression | t.DecimalLiteral | t.DoExpression | t.Identifier | t.StringLiteral | t.NumericLiteral | t.NullLiteral | t.RegExpLiteral | t.MemberExpression | t.NewExpression | t.ObjectExpression | t.SequenceExpression | t.ParenthesizedExpression | t.ThisExpression | t.UnaryExpression | t.UpdateExpression | t.MetaProperty | t.Super | t.TaggedTemplateExpression | t.TemplateLiteral | t.YieldExpression | t.Import | t.OptionalMemberExpression | t.OptionalCallExpression | t.TypeCastExpression | t.JSXElement | t.JSXFragment | t.PipelinePrimaryTopicReference | t.RecordExpression | t.TupleExpression | t.TSAsExpression | t.TSTypeAssertion | t.TSNonNullExpression;
    isComponent: boolean;
    slots: null;
    directives: t.ArrayExpression[];
    patchFlag: number;
    dynamicPropNames: Set<string>;
};
export default buildProps;
