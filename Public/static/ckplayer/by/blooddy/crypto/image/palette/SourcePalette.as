////////////////////////////////////////////////////////////////////////////////
//
//  © 2016 BlooDHounD
//
////////////////////////////////////////////////////////////////////////////////

package by.blooddy.crypto.image.palette {

	import flash.display.BitmapData;
	
	/**
	 * This class provides a palette that can be used in <code>PNGEncoder</code>.
	 * 
	 * @author					BlooDHounD
	 * @version					1.0
	 * @playerversion			Flash 10.1
	 * @langversion				3.0
	 * @created					Mar 26, 2016 10:16:10 AM
	 */
	public class SourcePalette implements IPalette {
		
		//--------------------------------------------------------------------------
		//
		//  Constructor
		//
		//--------------------------------------------------------------------------
		
		/**
		 * Creates a SourcePalette object.
		 * 
		 * @param	image		The source image to use when creating the palette.
		 * 
		 * @throws	TypeError	The <code>image</code> parameter must 
		 * 						not be <code>null</code>.
		 * 
		 * @throws	RangeError	The number of colors is out of bounds.
		 */
		public function SourcePalette(image:BitmapData) {

			super();

			if ( image == null ) Error.throwError( TypeError, 2007, 'image' );

			var width:int = image.width;
			var height:int = image.height;
			
			var x:int = 0;
			var y:int = 0;
			
			var c:uint = 0;

			var i:int = 0;
			
			do {
				x = 0;
				do {
					
					c = image.getPixel32( x, y );
					if ( !( c in this._hash ) ) {
						
						if ( i > 256 ) Error.throwError( RangeError, 2006 );
						
						if ( c < 0xFF000000 ) this._transparent = true;
						
						this._list[ i ] = c;
						this._hash[ c ] = i;
						
						++i;
						
					}
					
				} while ( ++x < width );
			} while ( ++y < height );
		
			if ( i < 2 ) Error.throwError( RangeError, 2006 );
			
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
		private const _hash:Object = new Object();
		
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
			return this._hash[ color ];
		}
		
	}
	
}