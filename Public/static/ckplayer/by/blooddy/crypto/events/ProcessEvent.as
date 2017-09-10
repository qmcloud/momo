////////////////////////////////////////////////////////////////////////////////
//
//  © 2016 BlooDHounD
//
////////////////////////////////////////////////////////////////////////////////

package by.blooddy.crypto.events {

	import flash.events.ErrorEvent;
	import flash.events.Event;
	import flash.utils.getQualifiedClassName;
	
	/**
	 * An object dispatches an ProcessEvent object when asynchronous operation is completed.
	 * 
	 * @see	by.blooddy.crypto.process.Process
	 * 
	 * @author					BlooDHounD
	 * @version					1.0
	 * @playerversion			Flash 11.4
	 * @langversion				3.0
	 * @created					25.03.2016 1:17:56
	 */
	public class ProcessEvent extends Event {

		//--------------------------------------------------------------------------
		//
		//  Class variables
		//
		//--------------------------------------------------------------------------
		
		/**
		 * The <code>ProcessEvent.COMPLETE</code> constant defines the value of the type
		 * property of a <code>complete</code> event object.
		 * 
		 * <table>
		 *     <tr><th>Property</th><th>Value</th></tr>
		 *     <tr><td>bubbles</td><td><code>false</code></td></tr>
		 *     <tr><td>cancelable</td><td><code>false</code>; there is no default behavior to cancel.</td></tr>
		 *     <tr><td>currentTarget</td><td>The object that is actively processing the Event object with an event listener.</td></tr>
		 *     <tr><td>target</td><td>The object that reporting process result.</td></tr>
		 *     <tr><td>data</td><td>Result.</td></tr>
		 * </table>
		 */
		public static const COMPLETE:String =	Event.COMPLETE;

		/**
		 * The <code>ProcessEvent.ERROR</code> constant defines the value of the type
		 * property of a <code>error</code> event object.
		 * 
		 * <table>
		 *     <tr><th>Property</th><th>Value</th></tr>
		 *     <tr><td>bubbles</td><td><code>false</code></td></tr>
		 *     <tr><td>cancelable</td><td><code>false</code>; there is no default behavior to cancel.</td></tr>
		 *     <tr><td>currentTarget</td><td>The object that is actively processing the Event object with an event listener.</td></tr>
		 *     <tr><td>target</td><td>The object that reporting process error.</td></tr>
		 *     <tr><td>data</td><td>Error.</td></tr>
		 * </table>
		 */
		public static const ERROR:String =		ErrorEvent.ERROR;
	
		//--------------------------------------------------------------------------
		//
		//  Constructor
		//
		//--------------------------------------------------------------------------
		
		/**
		 * Creates ProcessEvent object.
		 * 
		 * @param	type		The type of the event. Event listeners can access
		 * 						this information through the inherited type property.
		 * @param	bubbles		Determines whether the Event object bubbles. Event
		 * 						listeners can access this information through the
		 * 						inherited bubbles property.
		 * @param	cancelable	Determines whether the Event object can be canceled.
		 * 						Event listeners can access this information through
		 * 						the inherited cancelable property.
		 * @param	data		Result data.
		 */
		public function ProcessEvent(type:String, bubbles:Boolean=false, cancelable:Boolean=false, data:*=null) {
			super( type, bubbles, cancelable );
			this.data = data;
		}

		//--------------------------------------------------------------------------
		//
		//  Variables
		//
		//--------------------------------------------------------------------------
		
		/**
		 * Contains the reference to the result data.
		 * 
		 * @default		null
		 */
		public var data:*;
		
		//--------------------------------------------------------------------------
		//
		//  Methods
		//
		//--------------------------------------------------------------------------
		
		/**
		 * @private
		 */
		public override function clone():Event {
			return new ProcessEvent( super.type, super.bubbles, super.cancelable, this.data );
		}
		
		/**
		 * @private
		 */
		public override function toString():String {
			return super.formatToString( getQualifiedClassName( this ).split( '.' ).pop(), 'type', 'bubbles' , 'cancelable', 'data' );
		}
		
	}

}