////////////////////////////////////////////////////////////////////////////////
//
//  © 2016 BlooDHounD
//
////////////////////////////////////////////////////////////////////////////////

package by.blooddy.crypto.process {

	import flash.events.EventDispatcher;
	import flash.utils.getQualifiedClassName;
	
	import by.blooddy.crypto.events.ProcessEvent;
	
	/**
	 * Dispatched when success.
	 * 
	 * <table>
	 *     <tr><th>Property</th><th>Value</th></tr>
	 *     <tr><td>bubbles</td><td><code>false</code></td></tr>
	 *     <tr><td>cancelable</td><td><code>false</code>; there is no default behavior to cancel.</td></tr>
	 *     <tr><td>currentTarget</td><td>The object that is actively processing the Event object with an event listener.</td></tr>
	 *     <tr><td>target</td><td>The object that reporting process result.</td></tr>
	 *     <tr><td>data</td><td>Result.</td></tr>
	 * </table>
	 * 
	 * @eventType    by.blooddy.crypto.events.ProcessEvent.COMPLETE
	 */
	[Event( type="by.blooddy.crypto.events.ProcessEvent", name="complete" )]

	/**
	 * Dispatched when fault.
	 * 
	 * <table>
	 *     <tr><th>Property</th><th>Value</th></tr>
	 *     <tr><td>bubbles</td><td><code>false</code></td></tr>
	 *     <tr><td>cancelable</td><td><code>false</code>; there is no default behavior to cancel.</td></tr>
	 *     <tr><td>currentTarget</td><td>The object that is actively processing the Event object with an event listener.</td></tr>
	 *     <tr><td>target</td><td>The object that reporting process error.</td></tr>
	 *     <tr><td>data</td><td>Error.</td></tr>
	 * </table>
	 * 
	 * @eventType    by.blooddy.crypto.events.ProcessEvent.ERROR
	 */
	[Event( type="by.blooddy.crypto.events.ProcessEvent", name="error" )]
	
	/**
	 * The class for asynchronous algorithms working.
	 * 
	 * @author					BlooDHounD
	 * @version					1.0
	 * @playerversion			Flash 10.1
	 * @langversion				3.0
	 * @created					24.03.2016 23:45:13
	 */
	public class Process extends EventDispatcher {

		CRYPTO::worker {
		
		//--------------------------------------------------------------------------
		//
		//  Class variables
		//
		//--------------------------------------------------------------------------
		
		/**
		 * @private
		 */
		private static const process:Process$ = ( function():Process$ {
			
			var c:Object;
			try {
				c = Process$Concurrent;
			} catch ( e:Error ) {
				c = Process$Consistent;
			}
			return c.internal::instance as Process$;
			
		}() );
		
		//--------------------------------------------------------------------------
		//
		//  Constructor
		//
		//--------------------------------------------------------------------------
		
		/**
		 * @internal
		 * Constructor
		 * @param	 WorkerClass	worker class
		 */
		public function Process(WorkerClass:Class) {
			if ( ( this as Object ).constructor == Process ) {
				Error.throwError( ArgumentError, 2012, getQualifiedClassName( this ) );
			} else {
				super();
				if ( !WorkerClass ) {
					Error.throwError( TypeError, 2007, 'WorkerClass' );
				}
				this.WorkerClass = WorkerClass;
			}
		}

		//--------------------------------------------------------------------------
		//
		//  Variables
		//
		//--------------------------------------------------------------------------
		
		/**
		 * @private
		 */
		private var WorkerClass:Class;
		
		//--------------------------------------------------------------------------
		//
		//  Protected methods
		//
		//--------------------------------------------------------------------------
		
		/**
		 * @internal
		 * @param	methodName	method name
		 * @param	arguments	arguments
		 */
		protected function call(methodName:String, ...arguments):void {

			process.process(
				WorkerClass,
				getQualifiedClassName( this ), methodName, arguments,
				this.complete, this.error
			);

		}

		//--------------------------------------------------------------------------
		//
		//  Private methods
		//
		//--------------------------------------------------------------------------
		
		/**
		 * @private
		 */
		private function complete(result:*):void {
			super.dispatchEvent( new ProcessEvent( ProcessEvent.COMPLETE, false, false, result ) );
		}
		
		/**
		 * @private
		 */
		private function error(error:*):void {
			if ( super.hasEventListener( ProcessEvent.ERROR ) ) {
				super.dispatchEvent( new ProcessEvent( ProcessEvent.ERROR, false, false, error ) );
			} else {
				throw error;
			}
		}

		}
		
	}

}