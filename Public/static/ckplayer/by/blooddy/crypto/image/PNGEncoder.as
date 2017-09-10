////////////////////////////////////////////////////////////////////////////////
//
//  © 2010 BlooDHounD
//
////////////////////////////////////////////////////////////////////////////////

package by.blooddy.crypto.image {

	import flash.display.BitmapData;
	import flash.display.PNGEncoderOptions;
	import flash.system.ApplicationDomain;
	import flash.utils.ByteArray;
	import flash.utils.getQualifiedClassName;
	
	import by.blooddy.crypto.CRC32;
	import by.blooddy.crypto.process.Process;

	/**
	 * Encodes image data using PNG compression algorithm. This class may use 
	 * different compression techniques provided in <code>PNG8Encoder</code> and
	 * <code>PNG24Encoder</code>.
	 *
	 * @see https://www.w3.org/Graphics/PNG/	W3C
	 * @see	by.blooddy.crypto.image.PNG8Encoder
	 * @see	by.blooddy.crypto.image.PNG24Encoder
	 * @see	by.blooddy.crypto.image.palette.IPalette
	 * @see	by.blooddy.crypto.image.PNGFilter
	 * @see	flash.display.BitmapData#encode
	 * 
	 * @author					BlooDHounD
	 * @version					2.0
	 * @playerversion			Flash 10.1
	 * @langversion				3.0
	 * @created					05.07.2010 17:44:26
	 */
	public class PNGEncoder extends Process {

		//--------------------------------------------------------------------------
		//
		//  Class variables
		//
		//--------------------------------------------------------------------------
		
		/**
		 * @private
		 */
		protected static const _NATIVE:Boolean = ApplicationDomain.currentDomain.hasDefinition( 'flash.display.PNGEncoderOptions' );

		/**
		 * @private
		 */
		protected static const _TMP:ByteArray = new ByteArray();
		
		//--------------------------------------------------------------------------
		//
		//  Class methods
		//
		//--------------------------------------------------------------------------

		/**
		 * Creates a PNG-encoded byte sequence from the specified <code>BitmapData</code>.
		 * 
		 * Used <code>flash.display.BitmapData.encode</code>, if possible.
		 * 
		 * @param	image			The <code>BitmapData</code> of the image you wish to encode.
		 * 
		 * @param	filter			The encoding algorithm you wish to apply while encoding.
		 * 							Use the constants provided in 
		 * 							<code>by.blooddy.crypto.image.PNGFilter</code> class.
		 * 
		 * @return					The sequence of bytes containing the encoded image.
		 * 
		 * @see						by.blooddy.crypto.image.PNGFilter
		 * @see						flash.display.BitmapData#encode()
		 */
		public static function encode(image:BitmapData, filter:uint=0):ByteArray {
			
			if ( image == null ) Error.throwError( TypeError, 2007, 'image' );
			if ( filter < 0 || filter > 4 ) Error.throwError( ArgumentError, 2008, 'filter' );
			
			if ( _NATIVE ) {
				return image.encode( image.rect, new PNGEncoderOptions( filter == PNGFilter.NONE ) );
			} else {
				var size:uint = image.width * image.height;
				if ( size >= 32 && size <= 64 ) return PNG8Encoder.encode( image, filter );
				else return PNG24Encoder.encode( image, filter );
			}

		}

		/**
		 * 	
		 * 
		 * @param	bytes			The <code>ByteArray</code> to be encoded.
		 * @param	width			Image width.
		 * @param	height			Image height.
		 * 
		 * @param	filter			The encoding algorithm you wish to apply while encoding.
		 * 							Use the constants provided in 
		 * 							<code>by.blooddy.crypto.image.PNGFilter</code> class.
		 * 
		 * @return					The sequence of bytes containing the encoded image.
		 * 
		 * @see						by.blooddy.crypto.image.PNGFilter
		 */
		public static function encodeBytes(bytes:ByteArray, width:uint, height:uint, filter:uint=0):ByteArray {
			
			if ( bytes == null ) Error.throwError( TypeError, 2007, 'bytes' );
			if ( filter < 0 || filter > 4 ) Error.throwError( ArgumentError, 2008, 'filter' );
			
			var image:BitmapData = new BitmapData( width, height, false );
			
			var pos:uint = bytes.position;
			bytes.position = 0;
			image.setPixels( image.rect, bytes );
			bytes.position = pos;
			
			return encode( image, filter );
			
		}
		
		//--------------------------------------------------------------------------
		//
		//  Helper methods
		//
		//--------------------------------------------------------------------------
		
		/**
		 * @internal
		 */
		protected static function writeSignature(mem:ByteArray):void {
			mem.writeUnsignedInt( 0x89504e47 );
			mem.writeUnsignedInt( 0x0D0A1A0A );
		}
		
		/**
		 * @internal
		 */
		protected static function writeChunk(mem:ByteArray, chunk:ByteArray):void {
			mem.writeUnsignedInt( chunk.length - 4 );
			mem.writeBytes( chunk, 0 );
			mem.writeUnsignedInt( CRC32.hashBytes( chunk ) );
		}
		
		/**
		 * @internal
		 */
		protected static function writeIHDR(mem:ByteArray, width:uint, height:uint, bits:uint, colors:uint):void {

			var chunk:ByteArray = _TMP;

			chunk.writeUnsignedInt( 0x49484452 );
			chunk.writeUnsignedInt( width );	// width
			chunk.writeUnsignedInt( height );	// height
			chunk.writeByte( bits );			// Bit depth
			chunk.writeByte( colors );			// Colour type
			chunk.writeByte( 0x00 );			// Compression method
			chunk.writeByte( 0x00 );			// Filter method
			chunk.writeByte( 0x00 );			// Interlace method

			writeChunk( mem, chunk );

			chunk.length = 0;

		}
		
		/**
		 * @internal
		 */
		protected static function writeIDAT(mem:ByteArray, data:ByteArray):void {
			
			var chunk:ByteArray = _TMP;

			data.compress();

			chunk.writeUnsignedInt( 0x49444154 );
			chunk.writeBytes( data );

			writeChunk( mem, chunk );
			
			chunk.length = 0;
			
		}
		
		/**
		 * @internal
		 */
		protected static function writeTEXT(mem:ByteArray, keyword:String, text:String):void {
			
			var chunk:ByteArray = _TMP;
			
			chunk.writeUnsignedInt( 0x74455874 );
			chunk.writeMultiByte( keyword, 'latin-1' );
			chunk.writeByte( 0 );
			chunk.writeMultiByte( text, 'latin-1' );

			writeChunk( mem, chunk );

			chunk.length = 0;

		}
		
		/**
		 * @internal
		 */
		protected static function writeIEND(mem:ByteArray):void {
			
			var chunk:ByteArray = _TMP;
			
			chunk.writeUnsignedInt( 0x49454E44 );

			writeChunk( mem, chunk );

			chunk.length = 0;
			
		}
		
		CRYPTO::worker {
		
		//--------------------------------------------------------------------------
		//
		//  Class variables
		//
		//--------------------------------------------------------------------------
		
		[Embed( source="PNGEncoder.swf", mimeType="application/octet-stream" )]
		/**
		 * @private
		 */
		private static const WorkerClass:Class;
		
		//--------------------------------------------------------------------------
		//
		//  Constructor
		//
		//--------------------------------------------------------------------------

		/**
		 * Creates a PNGEncoder object.
		 */
		public function PNGEncoder() {
			
			var c:Class = ( this as Object ).constructor;
			
			if ( c == PNGEncoder || c == PNG24Encoder || c == PNG8Encoder ) {
				super( WorkerClass );
			} else {
				Error.throwError( ArgumentError, 2012, getQualifiedClassName( this ) );
			}

		}

		//--------------------------------------------------------------------------
		//
		//  Methods
		//
		//--------------------------------------------------------------------------

		/**
		 * Asynchronously creates a PNG-encoded byte sequence from the specified <code>BitmapData</code>.
		 * 
		 * Dispatched sequence of bytes in <code>ProcessEvent</code>.
		 * 
		 * @param	image			The <code>BitmapData</code> of the image you wish to encode.
		 * 
		 * @param	filter			The encoding algorithm you wish to apply while encoding.
		 * 							Use the constants provided in 
		 * 							<code>by.blooddy.crypto.image.PNGFilter</code> class.
		 */
		public function encode(image:BitmapData, filter:uint=0):void {

			if ( image == null ) Error.throwError( TypeError, 2007, 'image' );
			if ( filter < 0 || filter > 4 ) Error.throwError( ArgumentError, 2008, 'filter' );
			
			super.call( 'encodeBytes', image.getPixels( image.rect ), image.width, image.height, filter );
			
		}
		
		/**
		 * Asynchronously creates a PNG-encoded byte sequence from the specified <code>ByteArray</code>.
		 * 
		 * Dispatched sequence of bytes in <code>ProcessEvent</code>.
		 * 
		 * @param	bytes			The <code>ByteArray</code> to be encoded.
		 * @param	width			Image width.
		 * @param	height			Image height.
		 * 
		 * @param	filter			The encoding algorithm you wish to apply while encoding.
		 * 							Use the constants provided in 
		 * 							<code>by.blooddy.crypto.image.PNGFilter</code> class.
		 */
		public function encodeBytes(bytes:ByteArray, width:uint, height:uint, filter:uint=0):void {
			
			if ( bytes == null ) Error.throwError( TypeError, 2007, 'bytes' );
			if ( filter < 0 || filter > 4 ) Error.throwError( ArgumentError, 2008, 'filter' );
			
			super.call( 'encodeBytes', bytes, width, height, filter );
			
		}
		
		}
		
	}

}