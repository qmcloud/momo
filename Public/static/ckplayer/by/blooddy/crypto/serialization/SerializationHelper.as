////////////////////////////////////////////////////////////////////////////////
//
//  © 2010 BlooDHounD
//
////////////////////////////////////////////////////////////////////////////////

package by.blooddy.crypto.serialization {

	import flash.utils.Dictionary;
	import flash.utils.describeType;
	import flash.utils.getQualifiedClassName;
	
	import avmplus.DescribeType;

	[ExcludeClass]
	/**
	 * Object property describer.
	 * 
	 * @author					BlooDHounD
	 * @version					1.0
	 * @playerversion			Flash 10.1
	 * @langversion				3.0
	 * @created					08.10.2010 2:05:14
	 */
	public final class SerializationHelper {

		//--------------------------------------------------------------------------
		//
		//  Class variables
		//
		//--------------------------------------------------------------------------

		/**
		 * @private
		 */
		private static const _HASH:Dictionary = new Dictionary( true );

		//--------------------------------------------------------------------------
		//
		//  Class methods
		//
		//--------------------------------------------------------------------------

		/**
		 * @param	o	<code>Object</code> to describe.
		 * 
		 * @return		Vector of <code>o</code> properties names.
		 */
		public static function getPropertyNames(o:Object):Vector.<String> {

			if ( typeof o != 'object' || !o ) Error.throwError( TypeError, 2007, 'o' );

			var c:Object = o is Class ? o as Class : o.constructor;

			var result:Vector.<String> = _HASH[ c ];

			if ( !result ) {

				result = new Vector.<String>();

				try {

					o = DescribeType.get( c, DescribeType.INCLUDE_ACCESSORS | DescribeType.INCLUDE_VARIABLES | DescribeType.INCLUDE_TRAITS | DescribeType.USE_ITRAITS );
					if ( o.traits ) {
						var a:Object;
						for each ( a in o.traits.accessors ) {
							if ( !a.uri && a.access.charAt( 0 ) == 'r' ) {
								result.push( a.name );
							}
						}
						for each ( a in o.traits.variables ) {
							if ( !a.uri ) {
								result.push( a.name );
							}
						}
					}

				} catch ( e:Error ) {
				
					var n:String;
					var list:XMLList;
					for each ( var x:XML in describeType( c ).factory.* ) {
						n = x.name();
						if (
							(
								(
									n ==  'accessor' &&
									x.@access.charAt( 0 ) == 'r'
								) ||
								n == 'variable' ||
								n == 'constant'
							) &&
							x.@uri.length() <= 0
						) {
							list = x.metadata;
							if ( list.length() <= 0 || list.( @name == 'Transient' ).length() <= 0 ) {
								result.push( x.@name.toString() );
							}
						}
					}
					
				}

				_HASH[ c ] = result;

			}

			return result.slice();

		}

		//--------------------------------------------------------------------------
		//
		//  Constructor
		//
		//--------------------------------------------------------------------------

		/**
		 * @private
		 */
		public function SerializationHelper() {
			super();
			Error.throwError( ArgumentError, 2012, getQualifiedClassName( this ) );
		}

	}

}