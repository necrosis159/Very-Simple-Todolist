//Declaration
var mysql = require('mysql2');
var connection = mysql.createConnection({
    host: 'localhost',
    user: 'toptask',
    password: '123soleil',
    database: 'toptasks'
});
const jwt = require('jsonwebtoken');

//CONNECT BDD
connection.connect(function (err) {
    if (!err) {
        console.log("Database is connected ...");
    } else {
        console.log("Error connecting database ...");
    }
});

exports.project = function (req, res) {
    try {
        var decoded = jwt.verify(req.body.token, 'OHNOITISNOTVERYSECRET');
    } catch (err) {
        res.send({
            "code": 400,
            "failed": "Incorrect or expired Token"
        })
    }

    query = connection.query('SELECT * FROM project', async function (error, results, fields) {
        let answer = '[';
        results.forEach(
            element => {
                answer = answer + '{"id":"' + element.id + '", "name":"' + element.name + '", "logo":"' + element.logo + '"},';
            }
        );
        answer = answer.substring(0, answer.length - 1);
        answer = answer + ']';
        var json = '{"result":true, "count":42}';
        obj = JSON.parse(answer);
        if (error) {
            res.send({
                "code": 400,
                "failed": error
            })
        } else {
            res.send({
                "code": 200,
                "result": obj
            })
        }
    });
}

exports.list = function (req, res) {
    try {
        var decoded = jwt.verify(req.body.token, 'OHNOITISNOTVERYSECRET');
    } catch (err) {
        res.send({
            "code": 400,
            "failed": "Incorrect or expired Token"
        })
    }

    query = connection.query('SELECT * FROM todo_list', async function (error, results, fields) {
        let answer = '[';
        results.forEach(
            element => {
                answer = answer + '{"id":"' + element.id + '", "id_project":"' + element.id_project + '", "is_archived":"' + element.is_archived + '"},';
            }
        );
        answer = answer.substring(0, answer.length - 1);
        answer = answer + ']';
        var json = '{"result":true, "count":42}';
        obj = JSON.parse(answer);
        if (error) {
            res.send({
                "code": 400,
                "failed": error
            })
        } else {
            res.send({
                "code": 200,
                "result": obj
            })
        }
    });
}
exports.tasks = function (req, res) {
    try {
        var decoded = jwt.verify(req.body.token, 'OHNOITISNOTVERYSECRET');
    } catch (err) {
        res.send({
            "code": 400,
            "failed": "Incorrect or expired Token"
        })
    }

    query = connection.query('SELECT * FROM todo_task_list', async function (error, results, fields) {
        let answer = '[';
        results.forEach(
            element => {
                answer = answer + '{"id":"' + element.id + '", "name":"' + element.name + '", "is_done":"' + element.is_done + '", "id_list":"' + element.id_list + '", "id_project":"' + element.id_project + '"},';
            }
        );
        answer = answer.substring(0, answer.length - 1);
        answer = answer + ']';
        var json = '{"result":true, "count":42}';
        obj = JSON.parse(answer);
        if (error) {
            res.send({
                "code": 400,
                "failed": error
            })
        } else {
            res.send({
                "code": 200,
                "result": obj
            })
        }
    });
}

exports.updateDoneTask = function (req, res) {
    try {
        var decoded = jwt.verify(req.body.token, 'OHNOITISNOTVERYSECRET');
    } catch (err) {
        res.send({
            "code": 400,
            "failed": "Incorrect or expired Token"
        })
    }

    if(typeof req.body.idTask === 'undefined' || req.body.idTask === null){
        res.send({
            "code": 400,
            "error": 'Give me an ID!'
        })
    }
    query = connection.query("UPDATE `todo_task_list` SET `is_done` = '1' WHERE `todo_task_list`.`id` = "+req.body.idTask+";");
    res.send({
        "code": 200,
        "idTask": req.body.idTask,
        "answer": "task updated"
    })
}

exports.createTask = function (req, res) {
    try {
        var decoded = jwt.verify(req.body.token, 'OHNOITISNOTVERYSECRET');
    } catch (err) {
        res.send({
            "code": 400,
            "failed": "Incorrect or expired Token"
        })
    }

    if(typeof req.body.name === 'undefined' || req.body.name === null){
        res.send({
            "code": 400,
            "error": 'Give me a name!'
        })
    }
    if(typeof req.body.idList === 'undefined' || req.body.idList === null){
        res.send({
            "code": 400,
            "error": 'Give me a list ID!'
        })
    }
    if(typeof req.body.idProject === 'undefined' || req.body.idProject === null){
        res.send({
            "code": 400,
            "error": 'Give me a project ID!'
        })
    }

    query = connection.query("INSERT INTO `todo_task_list` (`id`, `name`, `is_done`, `id_list`, `id_project`) VALUES (NULL, '"+req.body.name+"', '0', '"+req.body.idList+"', '"+req.body.idProject+"');");
    res.send({
        "code": 200,
        "name": req.body.name,
        "idList": req.body.idList,
        "idProject": req.body.idProject,
        "answer": "task created"
    })
}

exports.deleteTask = function (req, res) {
    try {
        var decoded = jwt.verify(req.body.token, 'OHNOITISNOTVERYSECRET');
    } catch (err) {
        res.send({
            "code": 400,
            "failed": "Incorrect or expired Token"
        })
    }

    if(typeof req.body.idTask === 'undefined' || req.body.idTask === null){
        res.send({
            "code": 400,
            "error": 'Give me a task ID!'
        })
    }
    query = connection.query("DELETE FROM `todo_task_list` WHERE `todo_task_list`.`id` ="+req.body.idTask);
    res.send({
        "code": 200,
        "idTask": req.body.idTask,
        "answer": "task deleted"
    })
}

exports.updateNameTask = function (req, res) {
    try {
        var decoded = jwt.verify(req.body.token, 'OHNOITISNOTVERYSECRET');
    } catch (err) {
        res.send({
            "code": 400,
            "failed": "Incorrect or expired Token"
        })
    }

    if(typeof req.body.idTask === 'undefined' || req.body.idTask === null){
        res.send({
            "code": 400,
            "error": 'Give me a task ID!'
        })
    }

    if(typeof req.body.name === 'undefined' || req.body.name === null){
        res.send({
            "code": 400,
            "error": 'Give me a name!'
        })
    }

    query = connection.query("UPDATE `todo_task_list` SET `name` = '"+req.body.name+"' WHERE `todo_task_list`.`id` = "+req.body.idTask+";");
    res.send({
        "code": 200,
        "idTask": req.body.idTask,
        "answer": "task updated"
    })
}



exports.users = function (req, res) {
    try {
        var decoded = jwt.verify(req.body.token, 'OHNOITISNOTVERYSECRET');
    } catch (err) {
        res.send({
            "code": 400,
            "failed": "Incorrect or expired Token"
        })
    }

    query = connection.query('SELECT * FROM user', async function (error, results, fields) {
        let answer = '[';
        results.forEach(
            element => {
                answer = answer + '{"id":"' + element.id + '", "email":"' + element.email + '", "username":"' + element.username + '", "firstname":"' + element.firstname + '", "lastname":"' + element.lastname + '", "is_active":"' + element.is_active + '", "business_name":"' + element.business_name + '", "rules":"' + element.rules + '", "job":"' + element.job + '"},';
            }
        );
        answer = answer.substring(0, answer.length - 1);
        answer = answer + ']';
        var json = '{"result":true, "count":42}';
        obj = JSON.parse(answer);
        if (error) {
            res.send({
                "code": 400,
                "failed": error
            })
        } else {
            res.send({
                "code": 200,
                "result": obj
            })
        }
    });
}

exports.createProject = function (req, res) {
    try {
        var decoded = jwt.verify(req.body.token, 'OHNOITISNOTVERYSECRET');
    } catch (err) {
        res.send({
            "code": 400,
            "failed": "Incorrect or expired Token"
        })
    }

    if(typeof req.body.name === 'undefined' || req.body.name === null){
        res.send({
            "code": 400,
            "error": 'Give me a name!'
        })
    }
    query = connection.query("INSERT INTO `project` (`id`, `name`, `logo`) VALUES (NULL, '"+req.body.name+"', NULL);", async function (error, results, fields) {
        res.send({
            "code": 200,
            "error": 'Project created!'
        })
    });
    
}