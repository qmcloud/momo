////////////////////////////////////////////////////////////////////////////////
//
//  © 2016 BlooDHounD
//
////////////////////////////////////////////////////////////////////////////////

package by.blooddy.crypto.process {

	import flash.display.Sprite;
	import flash.events.Event;
	import flash.system.MessageChannel;
	import flash.system.MessageChannelState;
	import flash.system.Worker;
	import flash.utils.getDefinitionByName;
	
	[ExcludeClass]
	[SWF( width="1", height="1", frameRate="1", scriptTimeLimit="-1", scriptRecursionLimit="-1" )]
	/**
	 * @internal
	 * Internal worker class.
	 * 
	 * @author					BlooDHounD
	 * @version					1.0
	 * @playerversion			Flash 11.4
	 * @langversion				3.0
	 * @created					Mar 28, 2016 11:24:15 AM
	 */
	public final class Worker$Background extends Sprite {

		//--------------------------------------------------------------------------
		//
		//  Variables
		//
		//--------------------------------------------------------------------------
		
		/**
		 * @private
		 */
		private static const input:MessageChannel = Worker.current.getSharedProperty( 'input' )
		
		/**
		 * @private
		 */
		private static const output:MessageChannel = Worker.current.getSharedProperty( 'output' )

		//--------------------------------------------------------------------------
		//
		//  Private methods
		//
		//--------------------------------------------------------------------------
		
		/**
		 * @private
		 */
		private static function process():void {
			
			if ( output.state != MessageChannelState.OPEN ) return;

			var data:Object = input.receive( true );

			var defenition:Object;
			var result:Object;
			
			try {
				
				defenition = getDefinitionByName( data.d );
				result = { success: defenition[ data.m ].apply( defenition, data.a ) };
				
			} catch ( e:Error ) {
				
				result = { fault: e };
				
			}
			
			output.send( result );
			
		}
		
		//--------------------------------------------------------------------------
		//
		//  Initialization
		//
		//--------------------------------------------------------------------------
		
		/**
		 * @private
		 */
		input.addEventListener( Event.CHANNEL_MESSAGE, function(event:Event):void {

			while ( input.messageAvailable ) {
				process();
			}

		} );
		
		/**
		 * @private
		 */
		while ( input.messageAvailable ) {
			process();
		}
		
	}
	
}