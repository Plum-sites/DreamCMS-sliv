//CONFIG
var port = 3000;

//Initialization modules
var app = require('express')();
var http = require('http').Server(app);
var io = require('socket.io')(http, {
    path: '/nodejs'
});
var jwt = require('socketio-jwt');
var dotenv = require('dotenv').config({path: '.env'});
var request = require('request');
var { DateTime } = require('luxon');
var Datastore = require('nedb');

//Chat history
var db_history = new Datastore({filename : 'chat_history', autoload: true});

var online_users = [];
var banned_users = [];

//FORUM CHAT
var fchat_last = [];
var fchat_rps = [];

var lastForumPostTime = 0;

http.listen(port, function(){
    console.log('listening on *:' + port);
});

var mysql      = require('mysql');
var pool = mysql.createPool({
    connectionLimit : 10,
    host     : process.env.DB_HOST,
    user     : process.env.DB_USERNAME,
    password : process.env.DB_PASSWORD,
    database : process.env.DB_DATABASE
});

function getDateTime() {
    return DateTime.local().setZone('Europe/Moscow');
}

function updateBans() {
    pool.query('SELECT bannable_id, expired_at, comment FROM bans WHERE expired_at > now() AND deleted_at IS NULL', [], function (error, results, fields) {
        if (error) throw error;

        banned_users = results;
    });
}

setInterval(function (){
    updateBans();
}, 5000);

setInterval(function (){
    io.emit('forum.online', online_users);
}, 15000);

function checkBan(id){
    return banned_users.find(ban => ban.bannable_id === id);
}

io.on('connection', function (socket) {
    socket.emit('forum.online', online_users);

    socket.on('forum.chat.load', function(){
        if (fchat_rps[socket.conn.id]){
            if ((Date.now() - fchat_rps[socket.conn.id]) < 2000){
                return;
            }
        }
        fchat_rps[socket.conn.id] = Date.now();

        socket.emit('forum.chat.load', fchat_last);
    });
}).on('connection', jwt.authorize({
    secret: process.env.JWT_SECRET,
    timeout: 15000
})).on('authenticated', function(socket) {
    var user = socket.decoded_token;
    user.socket = socket.conn.id;
    user.time = getDateTime().toLocaleString(DateTime.TIME_24_WITH_SECONDS);

    if (user.role.toLowerCase().indexOf('игрок') > 0){
        user.role = '';
    }

    socket.on('forum.chat.moder', function(){
        socket.emit('forum.chat.moder', {
            moder: user.moder ? true : false
        });
    });

    socket.on('forum.online', function(){
        online_users = online_users.filter(obj => obj.uuid !== user.uuid);

        user.time = getDateTime().toLocaleString(DateTime.TIME_24_WITH_SECONDS);
        online_users.unshift({login: user.login, uuid: user.uuid, moder: user.moder, role: user.role});
    });

    socket.on('forum.posts.new', function(){
        if (fchat_rps[user.id]){
            if ((Date.now() - fchat_rps[user.id]) < 2000 && !user.moder){
                return;
            }
        }
        fchat_rps[user.id] = Date.now();

        socket.emit('forum.posts.new');
    });

    socket.on('forum.chat.delete', function(text){
        if (user.moder){
            fchat_last = fchat_last.filter(function (msg) {
                return msg.text !== text;
            });

            io.emit('forum.chat.delete', text);
        }
    });

    socket.on('forum.chat.msg', function(text){
        var unix = Math.round(+new Date()/1000);
        if (user.reg_time > (unix - 43200)){
            socket.emit('message', {type: 'error', title: 'Ошибка', msg: 'Мы можете пользоваться чатом только спустя 12 часов после регистрации!'});
            return;
        }

        if (fchat_rps[user.id]){
            if ((Date.now() - fchat_rps[user.id]) < 2000 && !user.moder){
                socket.emit('message', {type: 'warn', title: 'Упс!', msg: 'Не так быстро! Попробуйте через 3 секунды!'});
                return;
            }
        }
        fchat_rps[user.id] = Date.now();

        if (text.length > 500 && !user.moder){
            socket.emit('message', {type: 'error', title: 'Ошибка', msg: 'Не более 500 символов в одном сообщении!'});
            return;
        }

        text = text.replace(/<\/?[^>]+>/gi, '');

        if (checkBan(user.id)){
            socket.emit('message', {type: 'error', title: 'Ошибка', msg: 'Вам запрещено писать в чат!'});
            return;
        }

        request.post(
            process.env.APP_URL + '/api/text/filter',
            { form: { t: text } },
            function (error, response, body) {
                text = text.trim();
                if (!error && response.statusCode === 200) {
                    var message = {
                        user: {login: user.login, uuid: user.uuid, moder: user.moder, role: user.role},
                        text: JSON.parse(body).filtered,
                        time: getDateTime().toLocaleString(DateTime.TIME_24_WITH_SECONDS)
                    };

                    if (message.text.length <= 0 || text.length <= 0){
                        socket.emit('message', {type: 'error', title: 'Ошибка', msg: 'Нельзя отправлять пустые сообщения!'});
                        return;
                    }

                    fchat_last.push(message);

                    if (fchat_last.length >= 20){
                        fchat_last.splice(0, 1);
                    }

                    db_history.insert({login: message.user.login, text: message.text});

                    console.log('[' + user.login + ']: ' + text);

                    io.emit('forum.chat.msg', message);
                }else {
                    console.error(error + ' ' + response.statusCode);
                }
            }
        );
    });

    socket.on('disconnect', function() {
        online_users = online_users.filter(obj => obj.uuid !== user.uuid);
    });
});
