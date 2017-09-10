////////////////////////////////////////////////////////////////////////////////
//
//  (C) 2010 BlooDHounD
//
////////////////////////////////////////////////////////////////////////////////

package by.blooddy.crypto.image.palette {

	/**
	 * This class provides a palette that can be used in <code>PNGEncoder</code>.
	 * 
	 * @see						http://en.wikipedia.org/wiki/List_of_palettes#RGB_arrangements
	 * 
	 * @author					BlooDHounD
	 * @version					1.0
	 * @playerversion			Flash 10.1
	 * @langversion				3.0
	 * @created					25.09.2010 0:22:29
	 */
	public class LevelsPalette implements IPalette {

		//--------------------------------------------------------------------------
		//
		//  Constructor
		//
		//--------------------------------------------------------------------------

		/**
		 * Creates a LevelsPalette object.
		 * 
		 * @param	rLevel		Red level.
		 * @param	gLevel		Green level.
		 * @param	bLevel		Blue level.
		 * @param	transparent	Is transparent.
		 * 
		 * @throws	TypeError	The <code>image</code> parameter must 
		 * 						not be <code>null</code>.
		 * 
		 * @throws	RangeError	The number of colors is out of bounds.
		 */
		public function LevelsPalette(rLevel:uint=8, gLevel:uint=8, bLevel:uint=4, transparent:Boolean=false) {

			super();

			var maxColors:uint = rLevel * gLevel * bLevel + int( transparent );
			if ( maxColors < 2 || maxColors > 256 ) Error.throwError( RangeError, 2006 );

			this._rRatio = gLevel * bLevel;
			this._gRatio = bLevel;

			var rc:Number = 0xFF / ( rLevel - 1 );
			var gc:Number = 0xFF / ( gLevel - 1 );
			var bc:Number = 0xFF / ( bLevel - 1 );

			this._rc = 1 / rc;
			this._gc = 1 / gc;
			this._bc = 1 / bc;

			var ri:int;
			var gi:int;
			var bi:int;
			var i:int = -1;
			var c:int;

			this._list.length = maxColors;
			this._list.fixed = true;

			for ( ri=0; ri<rLevel; ++ri ) {
				for ( gi=0; gi<gLevel; ++gi ) {
					for ( bi=0; bi<bLevel; ++bi ) {

						c =	( Math.round( ri * rc ) << 16 ) |
							( Math.round( gi * gc ) <<  8 ) |
							  Math.round( bi * bc )         ;

						this._hash[ c ] = ++i;

						this._list[ i ] = c | 0xFF000000;

					}
				}
			}

			if ( transparent ) {
				this._list[ ++i ] = 0x00000000;
				this._transparent = true;
			}

			this._list.fixed = true;
			
		}

		//--------------------------------------------------------------------------
		//
		//  Variables
		//
		//--------------------------------------------------------------------------

		/**
		 * @private
		 */
		private const _list:Vector.<uint> = new Vector.<uint>();

		/**
		 * @private
		 */
		private const _hash:Array = new Array();

		/**
		 * @private
		 */
		private var _rRatio:int;

		/**
		 * @private
		 */
		private var _gRatio:int;

		/**
		 * @private
		 */
		private var _rc:Number;

		/**
		 * @private
		 */
		private var _gc:Number;

		/**
		 * @private
		 */
		private var _bc:Number;

		//--------------------------------------------------------------------------
		//
		//  Properties
		//
		//--------------------------------------------------------------------------
		
		/**
		 * @private
		 */
		private var _transparent:Boolean;
		
		/**
		 * @inheritDoc
		 */
		public function get transparent():Boolean {
			return this._transparent;
		}
		
		//--------------------------------------------------------------------------
		//
		//  Methods
		//
		//--------------------------------------------------------------------------

		/**
		 * @inheritDoc
		 */
		public function getList():Vector.<uint> {
			return this._list.slice();
		}

		/**
		 * @inheritDoc
		 */
		public function getIndexByColor(color:uint):uint {
			if ( this._transparent && color < 0x33000000 ) {

				return this._list.length - 1;

			} else {

				color &= 0xFFFFFF;

				if ( color in this._hash ) {

					return this._hash[ color ];

				} else {

					var i:int =	int(   ( color >> 16 )          * this._rc ) * this._rRatio +
								int( ( ( color >>  8 ) & 0xFF ) * this._gc ) * this._gRatio +
								int(   ( color         & 0xFF ) * this._bc );

					this._hash[ color ] = i;

					return i;

				}

			}
		}

	}

}