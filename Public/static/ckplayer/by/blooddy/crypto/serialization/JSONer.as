////////////////////////////////////////////////////////////////////////////////
//
//  © 2010 BlooDHounD
//
////////////////////////////////////////////////////////////////////////////////

package by.blooddy.crypto.serialization {

	import flash.system.ApplicationDomain;
	
	import by.blooddy.crypto.process.Process;

	/**
	 * The JSONer class lets applications import and export data
	 * using JavaScript Object Notation (JSON) format.
	 * 
	 * @see						http://www.json.org
	 * 
	 * @author					BlooDHounD
	 * @version					3.0
	 * @playerversion			Flash 10.1
	 * @langversion				3.0
	 * @created					01.10.2010 15:53:38
	 */
	public final class JSONer extends Process {

		//--------------------------------------------------------------------------
		//
		//  Class methods
		//
		//--------------------------------------------------------------------------

		[Deprecated( replacement="stringify" )]
		/**
		 * Returns a <code>String</code>,
		 * in JSON format, that represents an ActionScript value. 
		 * 
		 * @param	value	The ActionScript value to be converted
		 * 					into a JSON string
		 * 
		 * @return			JSON string
		 * 
		 * @throws	StackOverflowError	When the <code>stringify</code> method encounters a cyclic structure.
		 */
		public static function encode(value:*):String {
			return JSON$Encoder.encode( value );
		}
		
		/**
		 * Returns a <code>String</code>,
		 * in JSON format, that represents an ActionScript value.
		 *  
		 * Used native <code>JSON.stringify</code>, if possible.
		 * 
		 * @param	value	The ActionScript value to be converted
		 * 					into a JSON string
		 * 
		 * @return			JSON string
		 * 
		 * @throws	StackOverflowError	When the <code>stringify</code> method encounters a cyclic structure.
		 */
		public static const stringify:Function = ( ApplicationDomain.currentDomain.hasDefinition( 'JSON' )
			? ApplicationDomain.currentDomain.getDefinition( 'JSON' ).stringify
			: encode
		);
		
		[Deprecated( replacement="parse" )]
		/**
		 * Accepts a JSON-formatted String and returns
		 * an ActionScript Object that represents that value.
		 * 
		 * @param	value	The JSON string to be parsed
		 * 
		 * @return			ActionScript Object
		 * 
		 * @throws	SyntaxError		When wrong JSON string value passed.
		 */
		public static function decode(value:String):* {
			return JSON$Decoder.decode( value );
		}
		
		/**
		 * Accepts a JSON-formatted String and returns
		 * an ActionScript Object that represents that value.
		 * 
		 * Used native <code>JSON.parse</code>, if possible.
		 * 
		 * @param	value	The JSON string to be parsed
		 * 
		 * @return			ActionScript Object
		 * 
		 * @throws	SyntaxError		When wrong JSON string value passed.
		 */
		public static const parse:Function = ( ApplicationDomain.currentDomain.hasDefinition( 'JSON' )
			? ApplicationDomain.currentDomain.getDefinition( 'JSON' ).parse
			: decode
		);
		
		CRYPTO::worker {
		
		//--------------------------------------------------------------------------
		//
		//  Class variables
		//
		//--------------------------------------------------------------------------
		
		[Embed( source="JSONer.swf", mimeType="application/octet-stream" )]
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
		 * Creates a JSONer object.
		 */
		public function JSONer() {
			super( WorkerClass );
		}
		
		//--------------------------------------------------------------------------
		//
		//  Methods
		//
		//--------------------------------------------------------------------------
		
		/**
		 * Asynchronously returns a <code>String</code>,
		 * in JSON format, that represents an ActionScript value.
		 * 
		 * Dispatched JSON string in <code>ProcessEvent</code>.
		 * 
		 * @param	value	The ActionScript value to be converted
		 * 					into a JSON string
		 */
		public function stringify(value:*):void {
			super.call( 'stringify', value );
		}
		
		/**
		 * Asynchronously accepts a JSON-formatted String and returns
		 * an ActionScript Object that represents that value.
		 * 
		 * Dispatched ActionScript Object in <code>ProcessEvent</code>.
		 * 
		 * @param	value	The JSON string to be parsed
		 */
		public function parse(value:String):void {
			super.call( 'parse', value );
		}
		
		}
		
	}

}

import flash.errors.StackOverflowError;
import flash.system.ApplicationDomain;
import flash.utils.ByteArray;
import flash.utils.Dictionary;
import flash.utils.Endian;
import flash.xml.XMLDocument;
import flash.xml.XMLNode;

import avm2.intrinsics.memory.li16;
import avm2.intrinsics.memory.li32;
import avm2.intrinsics.memory.li8;

import by.blooddy.crypto.serialization.SerializationHelper;

/**
 * @private
 */
internal final class JSON$Encoder {

	//--------------------------------------------------------------------------
	//
	//  Static
	//
	//--------------------------------------------------------------------------
	
	// ReferenceError v10.1 fix
	if ( !( 'toJSON' in ByteArray.prototype ) ) {
		ByteArray.prototype.toJSON = null;
	}
	
	//--------------------------------------------------------------------------
	//
	//  Encode
	//
	//--------------------------------------------------------------------------
	
	internal static function encode(value:*):String {

		var bytes:ByteArray = new ByteArray();
		bytes.endian = Endian.BIG_ENDIAN;

		try {
		
			// writeValue( new Dictionary(), bytes, value );
			_TYPE_WPRITERS[ typeof value ]( new Dictionary(), bytes, value );
			
		} finally {
	
			_STR.clear();
			
		}
		
		bytes.position = 0;
		return bytes.readUTFBytes( bytes.length );

	}
	
	//--------------------------------------------------------------------------
	//  encode variables
	//--------------------------------------------------------------------------
	
	private static const _DOMAIN:ApplicationDomain = ApplicationDomain.currentDomain;
	
	private static const _STR:ByteArray = new ByteArray();
	
	private static const _ESCAPE_EXP:RegExp = /[\f\n\r\t\v\u0008"\\]/;
	
	private static const _ESCAPE:Vector.<int> = ( function():Vector.<int> {
		
		var escape:Vector.<int> = new Vector.<int>( 0x100, true );
		
		for ( var i:int = 0; i<0x100; ++i ) {
			escape[ i ] = i;
		}
		
		escape[ 0x08 ] = 0x5C62; // \b
		escape[ 0x09 ] = 0x5C74; // \t
		escape[ 0x0A ] = 0x5C6E; // \n
		escape[ 0x0B ] = 0x5C76; // \v
		escape[ 0x0C ] = 0x5C66; // \f
		escape[ 0x0D ] = 0x5C72; // \r
		escape[ 0x22 ] = 0x5C22; // \"
//		escape[ 0x2F ] = 0x5C2F; // \/
		escape[ 0x5C ] = 0x5C5C; // \\
		
		return escape;
		
	}() );
	
	private static const _VALID_KEY:Object = {
		'number':		true,
		'string':		true,
		'object':		false,
		'boolean':		false,
		'undefined':	false,
		'xml':			false,
		'function':		false
	};
	
	//--------------------------------------------------------------------------
	//  encode main methods
	//--------------------------------------------------------------------------
	
	private static function writeValue(hash:Dictionary, bytes:ByteArray, value:*):void {
		_TYPE_WPRITERS[ typeof value ]( hash, bytes, value );
	}
	
	private static function writeNull(hash:Dictionary, bytes:ByteArray, value:*):void {
		bytes.writeInt( 0x6E756C6C ); // null
	}
	
	//--------------------------------------------------------------------------
	//  encode type writers
	//--------------------------------------------------------------------------
	
	private static const _TYPE_WPRITERS:Object = {
		'number':		writeTypeNumber,
		'string':		writeTypeString,
		'object':		writeTypeObject,
		'boolean':		writeTypeBoolean,
		'undefined':	writeNull,
		'xml':			writeTypeXML,
		'function':		writeNull
	};
	
	private static function writeTypeNumber(hash:Dictionary, bytes:ByteArray, value:Number):void {
		if ( ( value * 0 ) == 0 /* isFinite( value ) */ ) {
			bytes.writeUTFBytes( value.toString() );
		} else {
			// writeNull( hash, bytes, null );
			bytes.writeInt( 0x6E756C6C );
		}
	}
	
	private static function writeTypeString(hash:Dictionary, bytes:ByteArray, value:String):void {
		if ( value ) {
			
			bytes.writeByte( 0x22 ); // "
			
			if ( _ESCAPE_EXP.test( value ) ) {
				
				var escape:Vector.<int> = _ESCAPE;
				
				var str:ByteArray = _STR;
				str.writeUTFBytes( value );
	
				var i:int = 0;
				var j:int = 0;
				var l:int = 0;
				var len:int = str.length;
				
				var c:int = 0;
				
				if ( len >= ApplicationDomain.MIN_DOMAIN_MEMORY_LENGTH ) {
					
					var tmp:ByteArray = _DOMAIN.domainMemory;
					
					_DOMAIN.domainMemory = str;
					
					do {
						
						c = escape[ li8( i ) ];
						if ( c > 0x100 ) {
							l = i - j;
							if ( l > 0 ) {
								bytes.writeBytes( str, j, l );
							}
							j = i + 1;
							bytes.writeShort( c );
						}
						
					} while ( ++i < len );

					_DOMAIN.domainMemory = tmp;
					
				} else {

					do {
					
						c = escape[ str[ i ] ];
						if ( c > 0x100 ) {
							l = i - j;
							if ( l > 0 ) {
								bytes.writeBytes( str, j, l );
							}
							j = i + 1;
							bytes.writeShort( c );
						}

					} while ( ++i < len );
				
				}
				
				l = i - j;
				if ( l > 0 ) {
					bytes.writeBytes( str, j, l );
				}
				
				str.length = 0;
				
			} else {
				
				bytes.writeUTFBytes( value );

			}
			
			bytes.writeByte( 0x22 ); // "
			
		} else {
			
			bytes.writeShort( 0x2222 ); // ""
		
		}
	}
	
	private static function writeTypeObject(hash:Dictionary, bytes:ByteArray, value:Object):void {
		if ( value ) {
			
			if ( value in hash ) {
				Error.throwError( StackOverflowError, 1129 );
			}
			
			hash[ value ] = true;

			var writers:Object = _TYPE_WPRITERS;
			
			var v:*;

			if (
				'toJSON' in value &&
				typeof value[ 'toJSON' ] == 'function' &&
				( v = value.toJSON( null ) ) != value
			) {
				
				// writeValue( hash, bytes, v );
				writers[ typeof v ]( hash, bytes, v );
				
			} else {
			
				var write:Function = _CLASS_WRITERS[ value.constructor ];
				if ( write ) {
					
					write( hash, bytes, value );
	
				} else {
				
					bytes.writeByte( 0x7B ); // {
					
					var k:String;
					var f:Boolean = false;
					
					if ( value.constructor != Object ) {
						
						for each ( k in SerializationHelper.getPropertyNames( value ) ) {
	
							try {
								v = value[ k ];
							} catch ( _:* ) {
								continue;
							}

							if ( typeof v != 'function' ) {
	
								if ( f ) bytes.writeByte( 0x2C ); // ,
								else f = true;
		
								writeTypeString( hash, bytes, k );
								bytes.writeByte( 0x3A ); // :
								// writeValue( hash, bytes, v );
								writers[ typeof v ]( hash, bytes, v );
								
							}
	
						}
						
					}
					
					if ( value is Dictionary ) {
						var validKey:Object = _VALID_KEY;
						for ( k in value ) {
							if ( validKey[ typeof k ] ) {						
								
								v = value[ k ];
								if ( typeof v != 'function' ) {
									
									if ( f ) bytes.writeByte( 0x2C ); // ,
									else f = true;
									
									writeTypeString( hash, bytes, k );
									bytes.writeByte( 0x3A ); // :
									// writeValue( hash, bytes, v );
									writers[ typeof v ]( hash, bytes, v );
	
								}
								
							}
						}
					} else {
						for ( k in value ) {
	
							v = value[ k ];
							if ( typeof v != 'function' ) {
								
								if ( f ) bytes.writeByte( 0x2C ); // ,
								else f = true;
								
								writeTypeString( hash, bytes, k );
								bytes.writeByte( 0x3A ); // :
								// writeValue( hash, bytes, v );
								writers[ typeof v ]( hash, bytes, v );
	
							}
						}
					}
					
					bytes.writeByte( 0x7D ); // }
						
				}
				
			}
			
			delete hash[ value ];
			
		} else {
			
			// writeNull( hash, bytes, null );
			bytes.writeInt( 0x6E756C6C );
			
		}
	}
	
	private static function writeTypeBoolean(hash:Dictionary, bytes:ByteArray, value:Boolean):void {
		if ( value ) {
			bytes.writeInt( 0x74727565 ); // true
		} else {
			bytes.writeInt( 0x66616C73 ); // fals
			bytes.writeByte( 0x65 ); // e
		}
	}
	
	private static function writeTypeXML(hash:Dictionary, bytes:ByteArray, value:XML):void {
		if ( 'toJSON' in value ) {
			// writeValue( hash, bytes, value.toJSON( null ) );
			var v:* = value.toJSON( null );
			_TYPE_WPRITERS[ typeof v ]( hash, bytes, v );		
		} else {
			// writeTypeString( hash, bytes, value.toXMLString() );
			bytes.writeInt( 0x22584D4C );
			bytes.writeByte( 0x22 );
		}
	}
	
	//--------------------------------------------------------------------------
	//  encode class writers
	//--------------------------------------------------------------------------
	
	private static const _CLASS_WRITERS:Dictionary = ( function():Dictionary {
		
		var writers:Dictionary = new Dictionary();

		writers[ Object ] =				writeClassObject;
		writers[ Array ] =				writeClassArray;
		writers[ Vector.<*> ] =			writeClassVector;
		writers[ Vector.<int> ] =		writeClassVectorInt;
		writers[ Vector.<uint> ] =		writeClassVectorUint;
		writers[ Vector.<Number> ] =	writeClassVectorNumber;
		writers[ XMLDocument ] =		writeClassXMLDocument;
		writers[ XMLNode ] =			writeClassXMLNode;
		//writers[ Date ] =				writeClassDate;
		writers[ Dictionary ] =			writeClassDictionary;
		//writers[ RegExp ] = 			writeClassRegExp;
		writers[ ByteArray ] =			writeClassByteArray;
		
		return writers;
		
	}() );
	
	private static function writeClassObject(hash:Dictionary, bytes:ByteArray, value:Object):void {
		
		bytes.writeByte( 0x7B ); // {
		
		var writers:Object = _TYPE_WPRITERS;
		
		var k:String;
		var v:*;
		var f:Boolean;
		
		for ( k in value ) {
			
			v = value[ k ];
			if ( typeof v != 'function' ) {

				if ( f ) bytes.writeByte( 0x2C );	// ,
				else f = true;
				
				writeTypeString( hash, bytes, k );
				bytes.writeByte( 0x3A ); // :
				// writeValue( hash, bytes, v );
				writers[ typeof v ]( hash, bytes, v );

			}
			
		}
		
		bytes.writeByte( 0x7D ); // }

	}
	
	private static function writeClassArray(hash:Dictionary, bytes:ByteArray, value:Array):void {

		bytes.writeByte( 0x5B ); // [
		
		var writers:Object = _TYPE_WPRITERS;
		
		var l:int = value.length;
		if ( l > 0 ) {
			var v:* = value[ 0 ];
			// writeValue( hash, bytes, value[ 0 ] );
			writers[ typeof v ]( hash, bytes, v );
			var i:int = 0;
			while ( ++i < l ) {
				bytes.writeByte( 0x2C ); // ,
				v = value[ i ];
				// writeValue( hash, bytes, value[ i ] );
				writers[ typeof v ]( hash, bytes, v );
			}
		}
		
		bytes.writeByte( 0x5D ); // ]
	
	}

	private static function writeClassVector(hash:Dictionary, bytes:ByteArray, value:Vector.<*>):void {

		bytes.writeByte( 0x5B ); // [
		
		var l:int = value.length;
		if ( l > 0 ) {
			var i:int = 0;
			if ( value is Vector.<String> ) {
				
				if ( value[ 0 ] == null ) {
					// writeNull( hash, bytes, null );
					bytes.writeInt( 0x6E756C6C );
				} else {
					writeTypeString( hash, bytes, value[ 0 ] );
				}
				while ( ++i < l ) {
					bytes.writeByte( 0x2C ); // ,
					if ( value[ i ] == null ) {
						// writeNull( hash, bytes, null );
						bytes.writeInt( 0x6E756C6C );
					} else {
						writeTypeString( hash, bytes, value[ i ] );
					}
				}

			} else if ( value is Vector.<Boolean> ) {
				
				writeTypeBoolean( hash, bytes, value[ 0 ] );
				while ( ++i < l ) {
					bytes.writeByte( 0x2C ); // ,
					writeTypeBoolean( hash, bytes, value[ i ] );
				}

			} else {

				var writers:Object = _TYPE_WPRITERS;
				
				var v:* = value[ 0 ];
				// writeValue( hash, bytes, value[ 0 ] );
				writers[ typeof v ]( hash, bytes, v );
				while ( ++i < l ) {
					bytes.writeByte( 0x2C ); // ,
					v = value[ i ];
					// writeValue( hash, bytes, value[ i ] );
					writers[ typeof v ]( hash, bytes, v );
				}

			}
		}
		
		bytes.writeByte( 0x5D ); // ]
		
	}

	private static function writeClassVectorInt(hash:Dictionary, bytes:ByteArray, value:Vector.<int>):void {

		bytes.writeByte( 0x5B ); // [
		
		var l:int = value.length;
		if ( l > 0 ) {
			// writeTypeNumber( hash, bytes, value[ 0 ] );
			bytes.writeUTFBytes( value[ 0 ].toString() );
			var i:int = 0;
			while ( ++i < l ) {
				bytes.writeByte( 0x2C ); // ,
				// writeTypeNumber( hash, bytes, value[ i ] );
				bytes.writeUTFBytes( value[ i ].toString() );
			}
		}
		
		bytes.writeByte( 0x5D ); // ]

	}
	
	private static function writeClassVectorUint(hash:Dictionary, bytes:ByteArray, value:Vector.<uint>):void {
		
		bytes.writeByte( 0x5B ); // [
		
		var l:int = value.length;
		if ( l > 0 ) {
			// writeTypeNumber( hash, bytes, value[ 0 ] );
			bytes.writeUTFBytes( value[ 0 ].toString() );
			var i:int = 0;
			while ( ++i < l ) {
				bytes.writeByte( 0x2C ); // ,
				// writeTypeNumber( hash, bytes, value[ i ] );
				bytes.writeUTFBytes( value[ i ].toString() );
			}
		}
		
		bytes.writeByte( 0x5D ); // ]
		
	}

	private static function writeClassVectorNumber(hash:Dictionary, bytes:ByteArray, value:Vector.<Number>):void {

		bytes.writeByte( 0x5B ); // [
		
		var l:int = value.length;
		if ( l > 0 ) {
			// writeTypeNumber( hash, bytes, value[ 0 ] );
			var v:Number = value[ 0 ];
			if ( ( v * 0 ) == 0 /* isFinite( value ) */ ) {
				bytes.writeUTFBytes( v.toString() );
			} else {
				// writeNull( hash, bytes, null );
				bytes.writeInt( 0x6E756C6C );
			}
			var i:int = 0;
			while ( ++i < l ) {
				bytes.writeByte( 0x2C ); // ,
				// writeTypeNumber( hash, bytes, value[ i ] );
				v = value[ i ];
				if ( ( v * 0 ) == 0 /* isFinite( value ) */ ) {
					bytes.writeUTFBytes( v.toString() );
				} else {
					// writeNull( hash, bytes, null );
					bytes.writeInt( 0x6E756C6C );
				}
			}
		}
		
		bytes.writeByte( 0x5D ); // ]

	}
	
	private static function writeClassXMLDocument(hash:Dictionary, bytes:ByteArray, value:XMLDocument):void {
		writeTypeXML( hash, bytes, new XML( value.childNodes.length > 0 ? value : new XML() ) );
	}
	
	private static function writeClassXMLNode(hash:Dictionary, bytes:ByteArray, value:XMLNode):void {
		writeTypeXML( hash, bytes, new XML( value ) );
	}

	private static function writeClassDate(hash:Dictionary, bytes:ByteArray, value:Date):void {
		writeTypeString( hash, bytes, value.toString() );
	}

	private static function writeClassDictionary(hash:Dictionary, bytes:ByteArray, value:Dictionary):void {
		
		bytes.writeByte( 0x7B ); // {
		
		var validKey:Object = _VALID_KEY;
		var writers:Object = _TYPE_WPRITERS;
		
		var k:*;
		var v:*;
		var f:Boolean;
		
		for ( k in value ) {
			if ( validKey[ typeof k ] ) {

				v = value[ k ];
				if ( typeof v != 'function' ) {
					
					if ( f ) bytes.writeByte( 0x2C ); // ,
					else f = true;
					
					writeTypeString( hash, bytes, k );
					bytes.writeByte( 0x3A ); // :
					// writeValue( hash, bytes, v );
					writers[ typeof v ]( hash, bytes, v );

				}

			}
		}
		
		bytes.writeByte( 0x7D ); // }

	}

	private static function writeClassRegExp(hash:Dictionary, bytes:ByteArray, value:RegExp):void {
		writeTypeString( hash, bytes, String( value ) );
	}

	private static function writeClassByteArray(hash:Dictionary, bytes:ByteArray, value:ByteArray):void {
		// writeNull( hash, bytes, value );
		bytes.writeInt( 0x6E756C6C );
	}
}

/**
 * @private
 */
internal final class JSON$Decoder {

	//--------------------------------------------------------------------------
	//
	//  Decode
	//
	//--------------------------------------------------------------------------
	
	internal static function decode(value:String):* {
		var result:*;
		if ( value ) {
			
			var tmp:ByteArray = _DOMAIN.domainMemory;
			
			var mem:ByteArray = new ByteArray();
			mem.writeUTFBytes( value );
			mem.writeByte( 0 ); // EOF
			
			if ( mem.length < ApplicationDomain.MIN_DOMAIN_MEMORY_LENGTH ) mem.length = ApplicationDomain.MIN_DOMAIN_MEMORY_LENGTH;
			_DOMAIN.domainMemory = mem;
			
			_POS = 0;
			
			try {
			
				var c:int = li8( _POS );
				if ( c != 0 ) {

					// result = readValue( mem, c );
					result = _VALUE_READERS[ c & 0xFF ]( mem, c )

					do { c = li8( _POS++ ) } while ( _SKIP[ c & 0xFF ] );

					if ( c != 0 ) {
						readError( mem, c );
					}
					
				}
				
			} finally {
			
				_STR.clear();
				
				_DOMAIN.domainMemory = tmp;
				
			}
			
		}
		return result;
	}
	
	//--------------------------------------------------------------------------
	//  decode variables
	//--------------------------------------------------------------------------

	private static const _DOMAIN:ApplicationDomain = ApplicationDomain.currentDomain;
	
	private static const _STR:ByteArray = new ByteArray();

	private static const _SKIP:Vector.<Boolean> = ( function():Vector.<Boolean> {
		
		var skip:Vector.<Boolean> = new Vector.<Boolean>( 0x100, true );
		
		skip[ 0x08 ] = true;	/* BACKSPACE */
		skip[ 0x09 ] = true;	/* TAB */
		skip[ 0x0A ] = true;	/* NEWLINE */
		skip[ 0x0B ] = true;	/* VERTICAL_TAB */
		skip[ 0x0C ] = true;	/* FORM_FEED */
		skip[ 0x0D ] = true;	/* CARRIAGE_RETURN */
		skip[ 0x20 ] = true;	/* SPACE */
		
		return skip;
		
	}() );
	
	private static const _NEWLINE:Vector.<Boolean> = ( function():Vector.<Boolean> {
		
		var newline:Vector.<Boolean> = new Vector.<Boolean>( 0x100, true );
		
		newline[ 0x00 ] = true;	/* EOS */
		newline[ 0x0A ] = true;	/* NEWLINE */
		newline[ 0x0D ] = true;	/* CARRIAGE_RETURN */
		
		return newline;
		
	}() );

	private static const _STRING:Vector.<Boolean> = ( function():Vector.<Boolean> {
		
		var string:Vector.<Boolean> = _NEWLINE.slice(); string.fixed = true;
		
		string[ 0x5C ] = true;	/* BACK_SLASH */
		
		return string;

	}() );
	
	private static const _ESCAPE:Vector.<int> = ( function():Vector.<int> {
	
		var escape:Vector.<int> = new Vector.<int>( 0x100, true );
		
		for ( var i:int = 0; i<0x100; ++i ) {
			escape[ i ] = i;
		}
		
		escape[ 0x62 ] = 0x08;
		escape[ 0x74 ] = 0x09;
		escape[ 0x6E ] = 0x0A;
		escape[ 0x76 ] = 0x0B;
		escape[ 0x66 ] = 0x0C;
		escape[ 0x72 ] = 0x0D;
		escape[ 0x22 ] = 0x22;
		escape[ 0x27 ] = 0x27;
		escape[ 0x5C ] = 0x5C;
		
		return escape;
	
	}() );
	
	private static const _DEC:Vector.<Boolean> = ( function():Vector.<Boolean> {
		
		var dec:Vector.<Boolean> = new Vector.<Boolean>( 0x100, true );
		
		for ( var i:int = 0x30; i<=0x39; ++i ) {	// 0..9
			dec[ i ] = true;
		}
		
		return dec;
		
	}() );
	
	private static const _HEX:Vector.<Boolean> = ( function():Vector.<Boolean> {
		
		var hex:Vector.<Boolean> = new Vector.<Boolean>( 0x100, true );

		var i:int = 0;

		for ( i=0x30; i<=0x39; ++i ) {	// 0..9
			hex[ i ] = true;
		}
		for ( i=0x41; i<=0x46; ++i ) {	// A..F
			hex[ i ] = true;
		}
		for ( i=0x61; i<=0x66; ++i ) {	// a..f
			hex[ i ] = true;
		}
		
		return hex;
		
	}() );

	private static const _IDENTIFIER:Vector.<Boolean> = ( function():Vector.<Boolean> {
		
		var identifier:Vector.<Boolean> = new Vector.<Boolean>( 0x100, true );
		
		var i:int = 0;
		
		identifier[ 0x24 ] = true;		// $
		for ( i=0x30; i<=0x39; ++i ) {	// 0..9
			identifier[ i ] = true;
		}
		for ( i=0x41; i<=0x5A; ++i ) {	// A..Z
			identifier[ i ] = true;
		}
		identifier[ 0x5F ] = true;		// _
		for ( i=0x61; i<=0x7A; ++i ) {	// a..z
			identifier[ i ] = true;
		}
		for ( i=0x80; i<0x100; ++i ) {	//
			identifier[ i ] = true;
		}

		return identifier;
		
	}() );
	
	private static const _NOT_VALID_IDENTIFIER:Object = {
		'null': true,
		'true': true,
		'false': true
	};

	private static const _VALUE_READERS:Vector.<Function> = ( function():Vector.<Function> {
		var readers:Vector.<Function> = new Vector.<Function>( 0x100, true );
		
		for ( var i:int = 0; i<0x100; ++i ) {
			readers[ i ] = readError;
		}
		
		readers[ 0x08 ] = readEmptyValue;	/* BACKSPACE */
		readers[ 0x09 ] = readEmptyValue;	/* TAB */
		readers[ 0x0A ] = readEmptyValue;	/* NEWLINE */
		readers[ 0x0B ] = readEmptyValue;	/* VERTICAL_TAB */
		readers[ 0x0C ] = readEmptyValue;	/* FORM_FEED */
		readers[ 0x0D ] = readEmptyValue;	/* CARRIAGE_RETURN */
		readers[ 0x20 ] = readEmptyValue;	/* SPACE */
		
		readers[ 0x22 ] = readString;		/* DOUBLE_QUOTE */
		readers[ 0x27 ] = readString;		/* SINGLE_QUOTE */

		readers[ 0x2D ] = readDash;			/* DASH */
		readers[ 0x2E ] = readDot;			/* DOT */
		readers[ 0x30 ] = readNumberZero;	/* ZERO */
		readers[ 0x31 ] = readNumber;		/* ONE */
		readers[ 0x32 ] = readNumber;		/* TWO */
		readers[ 0x33 ] = readNumber;		/* THREE */	
		readers[ 0x34 ] = readNumber;		/* FOUR */
		readers[ 0x35 ] = readNumber;		/* FIVE */
		readers[ 0x36 ] = readNumber;		/* SIX */
		readers[ 0x37 ] = readNumber;		/* SEVEN */
		readers[ 0x38 ] = readNumber;		/* EIGHT */
		readers[ 0x39 ] = readNumber;		/* NINE */
		
		readers[ 0x5B ] = readArray;		/* LEFT_BRACKET */
		
		readers[ 0x7B ] = readObject;		/* LEFT_BRACE */
		
		readers[ 0x6E ] = readNull;			/* n */
		readers[ 0x74 ] = readTrue;			/* t */
		readers[ 0x66 ] = readFalse;		/* f */
		readers[ 0x4E ] = readNaN;			/* N */
		readers[ 0x75 ] = readUndefined;	/* u */
		
		return readers;
	}() );
	
	private static const _IDENTIFIER_READERS:Vector.<Function> = ( function():Vector.<Function> {

		var readers:Vector.<Function> = _VALUE_READERS.slice(); readers.fixed = true;
		
		readers[ 0x08 ] = readEmptyIdentifier;	/* BACKSPACE */
		readers[ 0x09 ] = readEmptyIdentifier;	/* TAB */
		readers[ 0x0A ] = readEmptyIdentifier;	/* NEWLINE */
		readers[ 0x0B ] = readEmptyIdentifier;	/* VERTICAL_TAB */
		readers[ 0x0C ] = readEmptyIdentifier;	/* FORM_FEED */
		readers[ 0x0D ] = readEmptyIdentifier;	/* CARRIAGE_RETURN */
		readers[ 0x20 ] = readEmptyIdentifier;	/* SPACE */

		readers[ 0x5B ] = readError;			/* LEFT_BRACKET */
		readers[ 0x7B ] = readError;			/* LEFT_BRACE */

		var i:int;
		
		readers[ 0x24 ] = readIdentifier;		// $
		for ( i=0x41; i<=0x5A; ++i ) {			// A..Z
			readers[ i ] = readIdentifier;
		}
		readers[ 0x5F ] = readIdentifier;		// _
		for ( i=0x61; i<=0x7A; ++i ) {			// a..z
			readers[ i ] = readIdentifier;
		}
		for ( i=0x80; i<0x100; ++i ) {			// 
			readers[ i ] = readIdentifier;
		}
		
		return readers;

	}() );

	private static var _POS:int;

	//--------------------------------------------------------------------------
	//  decode main methods
	//--------------------------------------------------------------------------

	private static function readError(mem:ByteArray, c:int):* {
		Error.throwError( SyntaxError, 1132 );
	}
	
	private static function readEmptyValue(mem:ByteArray, c:int):* {
		do { c = li8( ++_POS ) & 0xFF } while ( _SKIP[ c ] );
		return _VALUE_READERS[ c ]( mem, c );
	}
	
	private static function readString(mem:ByteArray, to:int):String {
		
		var result:String;
		
		var pos:int = _POS + 1;

		var c:int = li8( pos );

		if ( c == to ) {

			return '';

		} else {
			
			var p:int = pos;
			var l:int = 0;
			
			var string:Vector.<Boolean> = _STRING;
			
			do {
				if ( string[ c & 0xFF ] ) {

					var newline:Vector.<Boolean> = _NEWLINE;

					if ( newline[ c & 0xFF ] ) {
						readError( mem, c & 0xFF );
					} else {
						
						var escape:Vector.<int> = _ESCAPE;
						var hex:Vector.<Boolean> = _HEX;

						var str:ByteArray = _STR;
						str.length = 0;
						
						do {
							
							if ( c == 0x5C /* BACK_SLASH */ ) { // escape
								
								l = pos - p;
								if ( l > 0 ) {
									str.writeBytes( mem, p, l );
								}
								
								c = li8( ++pos );
								
								if ( c == 0x75 /* u */ ) {
									
									if ( 
										hex[ li8( pos + 1 ) & 0xFF ] &&
										hex[ li8( pos + 2 ) & 0xFF ] && 
										hex[ li8( pos + 3 ) & 0xFF ] && 
										hex[ li8( pos + 4 ) & 0xFF ] 
									) {
										mem.position = pos + 1;
										c = parseInt( mem.readUTFBytes( 4 ), 16 );
										if ( c > 0xFF ) {
											str.writeShort( c );
										} else {
											str.writeByte( c );
										}
										pos += 4;
									} else {
										str.writeByte( c );
									}
									
								} else if ( c == 0x78 /* x */ ) {
									
									if ( 
										hex[ li8( pos + 1 ) & 0xFF ] &&
										hex[ li8( pos + 2 ) & 0xFF ] 
									) {
										mem.position = pos + 1;
										str.writeByte( parseInt( mem.readUTFBytes( 2 ), 16 ) );
										pos += 2;
									} else {
										str.writeByte( c );
									}
									
								} else {
									str.writeByte( escape[ c ] );
								}
								
								p = pos + 1;
								
							} else if ( newline[ c & 0xFF ] ) {
								readError( mem, c );
							}
							
							c = li8( ++pos );
							
						} while ( c != to );
						
						l = pos - p;
						if ( l > 0 ) {
							str.writeBytes( mem, p, l );
						}
						
						_POS = pos + 1;

						str.position = 0;
						return str.readUTFBytes( str.length );
						
					}

				}
				c = li8( ++pos );
			} while ( c != to );

			_POS = pos + 1;
			
			l = pos - p;
			if ( l > 1 ) {
				mem.position = p;
				return mem.readUTFBytes( pos - p );
			} else {
				return String.fromCharCode( li8( p ) & 0xFF );
			}
			
		}

	}
	
	private static function readNumberZero(mem:ByteArray, c:int):Number {

		var result:Number = 0;
		
		var pos:int = _POS;
		var p:int = pos;
		
		var num:Vector.<Boolean>;

		c = li8( ++pos );
		if ( c == 0x78 /* x */ || c == 0x58 /* X */ ) {

			num = _HEX;
			
			while ( num[ li8( ++pos ) & 0xFF ] ) {}
			
			p += 2;
			c = pos - p;
			if ( c > 0 ) {
				mem.position = p;
				result = parseInt( mem.readUTFBytes( c ), 16 );
			} else {
				readError( mem, c );
			}
			
		} else {
			
			num = _DEC;

			if ( num[ c & 0xFF ] ) {
				do { c = li8( ++pos ) } while ( num[ c & 0xFF ] );
			}
			if ( c == 0x2E /* DOT */ ) {
				if ( num[ c = li8( ++pos ) & 0xFF ] ) {
					do { c = li8( pos++ ) } while ( num[ c & 0xFF ] );
				} else {
					readError( mem, c );
				}
			}
			if ( c == 0x65 /* e */ || c == 0x45 /* E */ ) {
				c = li8( ++pos );
				if ( c == 0x2D /* DASH */ || c == 0x2B /* PLUS */ ) {
					c = li8( ++pos );
				}
				if ( num[ c & 0xFF ] ) {
					while ( num[ li8( ++pos ) & 0xFF ] ) {}
				} else {
					readError( mem, c );
				}
			}
			
			if ( pos > p + 1 ) {
				mem.position = p;
				result = parseFloat( mem.readUTFBytes( pos - p ) );
			}
			
		}

		_POS = pos;
		
		return result;

	}
	
	private static function readNumber(mem:ByteArray, c:int):Number {

		var pos:int = _POS;
		var p:int = pos;
		
		var num:Vector.<Boolean> = _DEC;
		
		do { c = li8( ++pos ) } while ( num[ c & 0xFF ] );
		if ( c == 0x2E /* DOT */ ) {
			c = li8( ++pos );
			if ( num[ c & 0xFF ] ) {
				do { c = li8( pos++ ) } while ( num[ c & 0xFF ] );
			} else {
				readError( mem, c );
			}
		}
		if ( c == 0x65 /* e */ || c == 0x45 /* E */ ) {
			c = li8( ++pos );
			if ( c == 0x2D /* DASH */ || c == 0x2B /* PLUS */ ) {
				c = li8( ++pos );
			}
			if ( num[ c & 0xFF ] ) {
				while ( num[ li8( ++pos ) & 0xFF ] ) {};
			} else {
				readError( mem, c );
			}
		}
		
		_POS = pos;

		mem.position = p;
		return parseFloat( mem.readUTFBytes( pos - p ) );

	}
	
	private static function readDot(mem:ByteArray, c:int):Number {
		
		var pos:int = _POS;
		var p:int = pos;
		
		var num:Vector.<Boolean> = _DEC;
		
		if ( num[ ( c = li8( ++pos ) ) & 0xFF ] ) {
			do { c = li8( ++pos ) } while ( num[ c & 0xFF ] );
		} else {
			readError( mem, c );
		}
		if ( c == 0x65 /* e */ || c == 0x45 /* E */ ) {
			c = li8( ++pos );
			if ( c == 0x2D /* DASH */ || c == 0x2B /* PLUS */ ) {
				c = li8( ++pos );
			}
			if ( num[ c & 0xFF ] ) {
				while ( num[ li8( ++pos ) & 0xFF ] ) {}
			} else {
				readError( mem, c );
			}
		}
		
		_POS = pos;
		
		mem.position = p;
		return parseFloat( mem.readUTFBytes( pos - p ) );
		
	}
	
	private static function readDash(mem:ByteArray, c:int):Number {
		c = li8( ++_POS ) & 0xFF;
		return -_VALUE_READERS[ c ]( mem, c );
	}
	
	private static function readArray(mem:ByteArray, c:int):Array {

		var result:Array = [];
		
		c = li8( ++_POS );
		if ( c != 0x5D ) {

			var skip:Vector.<Boolean> = _SKIP;
			
			while ( skip[ c & 0xFF ] ) { c = li8( ++_POS ) };

			if ( c != 0x5D ) {
				
				var valueReaders:Vector.<Function> = _VALUE_READERS;
				
				do {
					
					if ( c == 0x2C /* COMMA */ ) {

						result.push( undefined );

					} else {
		
						result.push( valueReaders[ c & 0xFF ]( mem, c ) );

						c = li8( _POS );
						if ( c != 0x2C /* COMMA */ ) {
							while ( skip[ c & 0xFF ] ) c = li8( ++_POS );
							if ( c != 0x2C /* COMMA */ ) break;
						}
						
					}
					
					do { c = li8( ++_POS ) } while ( skip[ c & 0xFF ] );
					
				} while ( c != 0x5D );
				
			}

		}
		
		++_POS;
		
		return result;

	}
	
	private static function readObject(mem:ByteArray, c:int):Object {

		var result:Object = {};
		
		c = li8( ++_POS );
		if ( c != 0x7D ) {
		
			var skip:Vector.<Boolean> = _SKIP;

			while ( skip[ c & 0xFF ] ) { c = li8( ++_POS ) };
			
			if ( c != 0x7D ) {
			
				var valueReaders:Vector.<Function> = _VALUE_READERS;
				var identReaders:Vector.<Function> = _IDENTIFIER_READERS;
				
				var key:String;
				
				do {
			
					key = identReaders[ c ]( mem, c );
		
					c = li8( _POS );
					if ( c != 0x3A /* COLON */ ) {
						while ( skip[ c & 0xFF ] ) c = li8( ++_POS );
						if ( c != 0x3A /* COLON */ ) readError( mem, c );
					}
		
					c = li8( ++_POS );
					result[ key ] = valueReaders[ c & 0xFF ]( mem, c );
		
					c = li8( _POS );
					if ( c != 0x2C /* COMMA */ ) {
						while ( skip[ c & 0xFF ] ) c = li8( ++_POS );
						if ( c != 0x2C /* COMMA */ ) break;
					}
		
					c = li8( ++_POS );
					
				} while ( true );
				
				if ( c != 0x7D /* RIGHT_BRACE */ ) {
					readError( mem, c );
				}

			}
	
		}

		++_POS;

		return result;

	}

	private static function readNull(mem:ByteArray, c:int):* {
		if ( li32( _POS ) != 0x6C6C756E ) { // null
			readError( mem, c );
		}
		_POS += 4;
		return null;
	}
	
	private static function readTrue(mem:ByteArray, c:int):Boolean {
		if ( li32( _POS ) != 0x65757274 ) { // true
			readError( mem, c );
		}
		_POS += 4;
		return true;
	}
	
	private static function readFalse(mem:ByteArray, c:int):Boolean {
		if ( li32( _POS + 1 ) != 0x65736C61 ) { // alse
			readError( mem, c );
		}
		_POS += 5;
		return false;
	}
	
	private static function readNaN(mem:ByteArray, c:int):Number {
		if ( li16( _POS + 1 ) != 0x4E61 ) { // aN
			readError( mem, c );
		}
		_POS += 3;
		return Number.NaN;
	}
	
	private static function readUndefined(mem:ByteArray, c:int):* {
		if (
			li32( _POS + 1 ) != 0x6665646E || // ndef
			li32( _POS + 5 ) != 0x64656E69 // ined
		) {
			readError( mem, c );
		}
		_POS += 9;
		return undefined;
	}

	private static function readEmptyIdentifier(mem:ByteArray, c:int):* {
		do { c = li8( ++_POS ) } while ( _SKIP[ c & 0xFF ] );
		return _IDENTIFIER_READERS[ c ]( mem, c );
	}
	
	private static function readIdentifier(mem:ByteArray, c:int):String {
		
		var pos:int = _POS;
		var p:int = pos;
		
		var identifier:Vector.<Boolean> = _IDENTIFIER;
		
		do {
			c = li8( ++pos );
		} while ( identifier[ c & 0xFF ] );

		_POS = pos;

		mem.position = p;
		var result:String = mem.readUTFBytes( pos - p );
		if ( result in _NOT_VALID_IDENTIFIER ) {
			readError( mem, li8( p ) );
		}
		return result;

	}
	
}