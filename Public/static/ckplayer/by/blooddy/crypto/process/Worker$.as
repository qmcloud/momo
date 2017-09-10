////////////////////////////////////////////////////////////////////////////////
//
//  © 2016 BlooDHounD
//
////////////////////////////////////////////////////////////////////////////////

package by.blooddy.crypto.process {

	import flash.events.Event;
	import flash.system.MessageChannel;
	import flash.system.MessageChannelState;
	import flash.system.Worker;
	import flash.system.WorkerDomain;
	import flash.utils.ByteArray;
	import flash.utils.clearTimeout;
	import flash.utils.setTimeout;
	
	[ExcludeClass]
	/**
	 * @internal
	 * 
	 * @author					BlooDHounD
	 * @version					1.0
	 * @playerversion			Flash 11.4
	 * @langversion				3.0
	 * @created					Mar 30, 2016 7:51:57 AM
	 */
	internal final class Worker$ {
		
		//--------------------------------------------------------------------------
		//
		//  Constructor
		//
		//--------------------------------------------------------------------------
		
		/**
		 * @internal
		 * @copy	flash.system.Worker.isSupported
		 */
		public static const isSupported:Boolean = Worker.isSupported;
		
		//--------------------------------------------------------------------------
		//
		//  Constructor
		//
		//--------------------------------------------------------------------------
		
		/**
		 * @internal
		 * Constructor
		 * 
		 * @param	bytes	Worker <code>ByteArray</code>
		 */
		public function Worker$(bytes:ByteArray) {
			if ( bytes && bytes.length ) {

				super();
				
				this.bytes = bytes;
				
			} else {
				Error.throwError( TypeError, 2007, 'bytes' );
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
		private var bytes:ByteArray;
		
		/**
		 * @private
		 */
		private var idle:uint;
		
		/**
		 * @private
		 */
		private var worker:Worker;
		
		/**
		 * @private
		 */
		private var input:MessageChannel;
		
		/**
		 * @private
		 */
		private var output:MessageChannel;
		
		/**
		 * @private
		 */
		private const queue:Vector.<Function> = new Vector.<Function>();
		
		//--------------------------------------------------------------------------
		//
		//  Methods
		//
		//--------------------------------------------------------------------------
		
		/**
		 * @param	data		data
		 * @param	callback	callback
		 */
		internal function send(data:Object, callback:Function=null):void {

			clearTimeout( this.idle );
			
			this.queue.push( callback );

			if ( !this.output ) {
				this.start();
			}
			
			this.output.send( data );

		}
		
		//--------------------------------------------------------------------------
		//
		//  Private methods
		//
		//--------------------------------------------------------------------------
		
		/**
		 * @private
		 */
		private function start():void {
			
			this.worker = WorkerDomain.current.createWorker( this.bytes );
			
			this.input = this.worker.createMessageChannel( Worker.current );
			this.output = Worker.current.createMessageChannel( this.worker );
			
			this.worker.setSharedProperty( 'output', this.input );
			this.worker.setSharedProperty( 'input', this.output );
			
			this.worker.start();
			
			this.input.addEventListener( Event.CHANNEL_MESSAGE, this.handler_input_message );

		}
		
		/**
		 * @private
		 */
		private function stop():void {
			
			this.input.removeEventListener( Event.CHANNEL_MESSAGE, this.handler_input_message );
			
			this.input.close();
			this.output.close();
			
			this.worker.setSharedProperty( 'output', null );
			this.worker.setSharedProperty( 'input', null );
			
			this.worker.terminate();
			
			this.input = null;
			this.output = null;
			this.worker = null;
			
		}
		
		//--------------------------------------------------------------------------
		//
		//  Event handlers
		//
		//--------------------------------------------------------------------------
		
		/**
		 * @private
		 */
		private function handler_input_message(event:Event):void {

			var result:Object = this.input.receive( true );

			this.queue.shift()( result );

			if ( this.queue.length <= 0 ) {
				this.idle = setTimeout( this.stop, 5 * 1e3 );
			}

		}
		
	}
	
}