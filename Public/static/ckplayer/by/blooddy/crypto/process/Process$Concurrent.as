////////////////////////////////////////////////////////////////////////////////
//
//  © 2016 BlooDHounD
//
////////////////////////////////////////////////////////////////////////////////

package by.blooddy.crypto.process {

	import flash.utils.ByteArray;
	import flash.utils.Dictionary;
	import flash.utils.getQualifiedClassName;

	[ExcludeClass]
	/**
	 * @internal
	 * Worker implementation.
	 * 
	 * @author					BlooDHounD
	 * @version					1.0
	 * @playerversion			Flash 11.4
	 * @langversion				3.0
	 * @created					25.03.2016 15:52:46
	 */
	internal final class Process$Concurrent implements Process$ {

		//--------------------------------------------------------------------------
		//
		//  Class variables
		//
		//--------------------------------------------------------------------------
		
		/**
		 * @internal
		 */
		internal static const instance:Process$Concurrent = new Process$Concurrent();
		
		/**
		 * @private
		 */
		private static const _HASH:Dictionary = new Dictionary( true );
		
		//--------------------------------------------------------------------------
		//
		//  Constructor
		//
		//--------------------------------------------------------------------------
		
		/**
		 * @private
		 */
		public function Process$Concurrent() {
			if ( !instance && Worker$.isSupported ) {
				super();
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
		 * @inheritDoc
		 */
		public function process(WorkerClass:Class, defenitionName:String, methodName:String, arguments:Array, success:Function, fault:Function):void {
			
			var worker:Worker$ = _HASH[ WorkerClass ];
			if ( !worker ) {
				
				_HASH[ WorkerClass ] = worker = new Worker$( new WorkerClass() as ByteArray );
				
			}
			
			worker.send(
				{ d: defenitionName, m: methodName, a: arguments },
				function(result:Object):void {
					if ( result.fault ) fault( result.fault );
					else if ( result.success ) success( result.success );
				}
			);
			
		}
		
	}

}