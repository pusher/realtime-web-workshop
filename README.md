# Realtime Web Workshop - By Pusher

## Before we start

1. Download this repo if you want to 'hack along'
2. Pusher libraries
   * [Client libraries](http://pusher.com/docs/client_libraries)
   * [Server libraries](http://pusher.com/docs/server_libraries)
3. The Pusher JavaScript library
  * The script tag
4. jQuery Mobile
   * [Website](http://jquerymobile.com/)
   * [Docs](http://jquerymobile.com/demos/1.1.0/)  
5. Debugging basics
   * See [Debugging Pusher](http://pusher.com/docs/debugging)
6. [Sign up for Pusher](http://pusher.com/signup)

## Ex 1. Connect

1. Include the Pusher script tag
2. Create a new Pusher instance
3. How do we know we're connected?
   * Pusher debug console
   * `Pusher.log`
   * `pusher.connection`

## Ex 2. Subscribe

1. Subscribe to a `messages` channel
2. Bind to the `new_message` event
3. Test that the functionality works
   * Use the Event Creator
   * Call the handler function directly
   * Fake the event using `channel.emit`

## Ex 3. Publish

1. Trigger/Publish a `new_message` event on the `messages` channel. The event data should have a `text` property.
2. Create an input field and button in the UI to send the data via the server to be validated to be published.