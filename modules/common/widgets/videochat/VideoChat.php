<?php
/**
 * VideoCHat class file
 */

namespace app\modules\common\widgets\videochat;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

/**
 * Class VideoCHat
 * @package app\modules\common\widgets\videochat
 */
class VideoChat implements MessageComponentInterface
{
    const HOST = 'localhost';
    const PORT = 8090;
    protected $clients;
    private $rooms;

    /**
     * Run this from console as root
     * @param string $uri
     * @param array $params need to set ssl certificates paths for live https server
     [
        'local_cert' => '/etc/nginx/ssl/pragmi.crt',
        'local_pk' => '/etc/nginx/ssl/pragmi.key',
        'verify_peer' => false
     ]
     * @return bool
     */
    public static function init($uri = '0.0.0.0:8090', $params = [])
    {
        $connection = @fsockopen(parse_url($uri)['host'], parse_url($uri)['port']);
        if (is_resource($connection)) {
            return fclose($connection);
        }
        //putenv("RATCHET_DISABLE_XDEBUG_WARN=true");
        $app = new \Ratchet\Http\HttpServer(new \Ratchet\WebSocket\WsServer(new VideoChat()));
        $loop = \React\EventLoop\Factory::create();
        $secure_websockets = new \React\Socket\Server($uri, $loop);
        $secure_websockets = new \React\Socket\SecureServer($secure_websockets, $loop, $params);
        $secure_websockets_server = new \Ratchet\Server\IoServer($app, $secure_websockets, $loop);
        $secure_websockets_server->run();
    }

    /**
     * VideoChat constructor.
     */
    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
        $this->rooms = [];
    }
    
    /**
     *
     * @param ConnectionInterface $conn
     */
    public function onOpen(ConnectionInterface $conn) {
        // Store the new connection
        $this->clients->attach($conn);
    }

    /**
     *
     * @param ConnectionInterface $from
     * @param $msg
     */
    public function onMessage(ConnectionInterface $from, $msg) {
        $data = json_decode($msg);
        $action = $data->action;
        $room = isset($data->room) ? $data->room : "";

        if(($action == 'subscribe') && $room){
            //subscribe user to room only if he hasn't subscribed

            //if room exist and user is yet to subscribe, then subscibe him to room
            //OR
            //if room does not exist, create it by adding user to it
            if((array_key_exists($room, $this->rooms) && !in_array($from, $this->rooms[$room])) || !array_key_exists($room, $this->rooms)){
                $this->rooms[$room][] = $from;//subscribe user to room

                $this->notifyUsersOfConnection($room, $from, $data->sender);
            } else{
                //tell user he has subscribed on another device/browser
                $msg_to_send = json_encode(['action'=>'subRejected']);

                $from->send($msg_to_send);
            }
        }

        //for other actions
        else if($room && isset($this->rooms[$room])){
            //send to everybody subscribed to the room received except the sender
            foreach($this->rooms[$room] as $client){
                if ($client !== $from) {
                    $client->send($msg);
                }
            }
        }
    }

    /**
     *
     * @param ConnectionInterface $conn
     */
    public function onClose(ConnectionInterface $conn) {
        // The connection is closed, remove connection
        $this->clients->detach($conn);

        if(count($this->rooms)){//if there is at least one room created
            foreach($this->rooms as $room=>$arr_of_subscribers){//loop through the rooms
                foreach ($arr_of_subscribers as $key=>$ratchet_conn){//loop through the users connected to each room
                    if($ratchet_conn == $conn){//if the disconnecting user subscribed to this room
                        unset($this->rooms[$room][$key]);//remove him from the room

                        //notify other subscribers that he has disconnected
                        $this->notifyUsersOfDisconnection($room, $conn);
                    }
                }
            }
        }
    }

    /**
     *
     * @param ConnectionInterface $conn
     * @param \Exception $e
     */
    public function onError(ConnectionInterface $conn, \Exception $e) {
        //echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }

    /**
     *
     * @param $room
     * @param $from
     * @param string $sender
     */
    private function notifyUsersOfConnection($room, $from, $sender){

        //echo "User subscribed to room ".$room ."\n";

        $msg_to_broadcast = json_encode(['action'=>'newSub', 'room'=>$room, 'socketId'=>$sender]);

        //notify user that someone has joined room
        foreach($this->rooms[$room] as $client){
            if ($client !== $from) {
                $client->send($msg_to_broadcast);
            }
        }
    }

    /**
     * @param $room
     * @param $from
     */
    private function notifyUsersOfDisconnection($room, $from){
        $msg_to_broadcast = json_encode(['action'=>'imOffline', 'room'=>$room]);

        //notify user that remote has left the room
        foreach($this->rooms[$room] as $client){
            if ($client !== $from) {
                $client->send($msg_to_broadcast);
            }
        }
    }
}