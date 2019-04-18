<?php


namespace INocturneSwoole\Connection\Tests;

use INocturneSwoole\Connection\MySQLException;
use INocturneSwoole\Connection\MySQLPool;
use Swoole\Coroutine;

require '../vendor/autoload.php';

class MySQL
{
    public function run()
    {
        $server = new \Swoole\Http\Server("0.0.0.0", 9502,SWOOLE_BASE);
        $server->set([
            'worker_num' => 1,
            'daemonize' => false,
            'log_file'   => 'swoole.log',
        ]);
        $server->on('Request', function ($request, $response) {
            //echo 'request1:cid:' . Coroutine::getuid() . PHP_EOL;
            \INocturneSwoole\Connection\MySQLPool::init([
                'test' => [
                    'serverInfo'    =>
                        [

                        ],
                    'maxSpareConns' => 5,
                    'maxConns'      => 10,
                    'maxSpareExp'   => 3600
                ],
            ]);
            $swoole_mysql = \INocturneSwoole\Connection\MySQLPool::fetch('test');
            $ret          = $swoole_mysql->query('select * from user_record_out');
            defer(function ()use($swoole_mysql){
                \INocturneSwoole\Connection\MySQLPool::recycle($swoole_mysql);
            });
//            $swoole_mysql2 = \INocturneSwoole\Connection\MySQLPool::recycle($swoole_mysql);
//            echo 'request2:cid:' . Coroutine::getuid() . PHP_EOL;
//            $ret           = $swoole_mysql2->query('select sleep(1)');
            //\INocturneSwoole\Connection\MySQLPool::recycle($swoole_mysql);
            $response->end('1');
        });
        $server->start();
    }
}

var_dump((new MySQL())->run());


