//Declaration
var mysql = require('mysql2');
var connection = mysql.createConnection({
    host: 'localhost',
    user: 'toptask',
    password: '123soleil',
    database: 'toptasks'
});
const jwt = require('jsonwebtoken');
var bcrypt = require('bcrypt'); //Using the same password hashing as Symfony
const saltRounds = 13; //Same Salt as used on Symfony

//CONNECT BDD
connection.connect(function (err) {
    if (!err) {
        console.log("Database is connected ...");
    } else {
        console.log("Error connecting database ...");
    }
});



/*   LOGS   */
//GET ALL LOG
exports.log = function (req, res) {
    try {
        var decoded = jwt.verify(req.body.token, 'OHNOITISNOTVERYSECRET');
    } catch (err) {
        res.send({
            "code": 400,
            "failed": "Incorrect or expired Token"
        })
    }

    query = connection.query('SELECT * FROM log', async function (error, results, fields) {
        let answer = '[';
        results.forEach(
            element => {
                answer = answer + '{"id":"' + element.id + '", "id project":"' + element.idproject + '", "user":"' + element.user + '", "action:":"' + element.action + '"},';
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



/*   PROJECT   */

//GET ALL PROJECT
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

//CREATE PROJECT
exports.createProject = function (req, res) {
    try {
        var decoded = jwt.verify(req.body.token, 'OHNOITISNOTVERYSECRET');
    } catch (err) {
        res.send({
            "code": 400,
            "failed": "Incorrect or expired Token"
        })
    }

    if (typeof req.body.name === 'undefined' || req.body.name === null) {
        res.send({
            "code": 400,
            "error": 'Give me a name!'
        })
    }
    query = connection.query("INSERT INTO `project` (`id`, `name`, `logo`) VALUES (NULL, '" + req.body.name + "', NULL);", async function (error, results, fields) {
        res.send({
            "code": 200,
            "error": 'Project created!'
        })
    });

}

//DELETE PROJECT
exports.deleteProject = function (req, res) {
    try {
        var decoded = jwt.verify(req.body.token, 'OHNOITISNOTVERYSECRET');
    } catch (err) {
        res.send({
            "code": 400,
            "failed": "Incorrect or expired Token"
        })
    }

    if (typeof req.body.idProject === 'undefined' || req.body.idProject === null) {
        res.send({
            "code": 400,
            "error": 'Give me a project ID!'
        })
    }

    query = connection.query("DELETE FROM `project` WHERE `project`.`id` = " + req.body.idProject + ";");
    res.send({
        "code": 200,
        "id": req.body.idProject,
        "answer": "project deleted"
    })
}

//UPDATE THE NAME OF THE PROJECT
exports.updateNameProject = function (req, res) {
    try {
        var decoded = jwt.verify(req.body.token, 'OHNOITISNOTVERYSECRET');
    } catch (err) {
        res.send({
            "code": 400,
            "failed": "Incorrect or expired Token"
        })
    }

    if (typeof req.body.name === 'undefined' || req.body.name === null) {
        res.send({
            "code": 400,
            "error": 'Give me a name!'
        })
    }

    if (typeof req.body.idProject === 'undefined' || req.body.idProject === null) {
        res.send({
            "code": 400,
            "error": 'Give me a project id!'
        })
    }

    query = connection.query("UPDATE `project` SET `name` = '" + req.body.name + "' WHERE `project`.`id` = " + req.body.idProject + ";", async function (error, results, fields) {
        res.send({
            "code": 200,
            "error": 'Project updated!'
        })
    });

}

//UPDATE LOGO OF THE PROJECT
exports.updateLogoProject = function (req, res) {
    try {
        var decoded = jwt.verify(req.body.token, 'OHNOITISNOTVERYSECRET');
    } catch (err) {
        res.send({
            "code": 400,
            "failed": "Incorrect or expired Token"
        })
    }

    if (typeof req.body.logoFullUrl === 'undefined' || req.body.logoFullUrl === null) {
        res.send({
            "code": 400,
            "error": 'Give me a logo url!'
        })
    }

    if (typeof req.body.idProject === 'undefined' || req.body.idProject === null) {
        res.send({
            "code": 400,
            "error": 'Give me a project id!'
        })
    }

    query = connection.query("UPDATE `project` SET `logo` = '" + req.body.logoFullUrl + "' WHERE `project`.`id` = " + req.body.idProject + ";", async function (error, results, fields) {
        res.send({
            "code": 200,
            "error": 'Project updated!'
        })
    });

}



/*   TASKLIST   */

//GET ALL TASKLIST
exports.tasklist = function (req, res) {
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

//UPDATE A NAME OF A TASK LIST
exports.updateNameTaskList = function (req, res) {
    try {
        var decoded = jwt.verify(req.body.token, 'OHNOITISNOTVERYSECRET');
    } catch (err) {
        res.send({
            "code": 400,
            "failed": "Incorrect or expired Token"
        })
    }

    if (typeof req.body.idTaskList === 'undefined' || req.body.idTaskList === null) {
        res.send({
            "code": 400,
            "error": 'Give me a tasklist ID!'
        })
    }

    if (typeof req.body.name === 'undefined' || req.body.name === null) {
        res.send({
            "code": 400,
            "error": 'Give me a name!'
        })
    }

    query = connection.query("UPDATE `todo_list` SET `name` = '" + req.body.name + "' WHERE `todo_list`.`id` = " + req.body.idTaskList + ";");

    res.send({
        "code": 200,
        "idTask": req.body.idTaskList,
        "answer": "task list updated"
    })
}

//UPDATE IS ARCHIVED OF A TASK LIST
exports.updateIsArchivedTaskList = function (req, res) {
    try {
        var decoded = jwt.verify(req.body.token, 'OHNOITISNOTVERYSECRET');
    } catch (err) {
        res.send({
            "code": 400,
            "failed": "Incorrect or expired Token"
        })
    }

    if (typeof req.body.idTaskList === 'undefined' || req.body.idTaskList === null) {
        res.send({
            "code": 400,
            "error": 'Give me a tasklist ID!'
        })
    }

    if (typeof req.body.isarchived === 'undefined' || req.body.isarchived === null) {
        res.send({
            "code": 400,
            "error": 'is archived?'
        })
    }

    query = connection.query("UPDATE `todo_list` SET `is_archived` = '" + req.body.isarchived + "' WHERE `todo_list`.`id` = " + req.body.idTaskList + ";");

    res.send({
        "code": 200,
        "idTask": req.body.idTaskList,
        "answer": "task list updated"
    })
}

//DELETE A TASKLIST
exports.deleteTasklist = function (req, res) {
    try {
        var decoded = jwt.verify(req.body.token, 'OHNOITISNOTVERYSECRET');
    } catch (err) {
        res.send({
            "code": 400,
            "failed": "Incorrect or expired Token"
        })
    }

    if (typeof req.body.idTasklist === 'undefined' || req.body.idTasklist === null) {
        res.send({
            "code": 400,
            "error": 'Give me a tasklist ID!'
        })
    }
    query = connection.query("DELETE FROM `todo_list` WHERE `todo_list`.`id` =" + req.body.idTasklist);
    res.send({
        "code": 200,
        "answer": "tasklist deleted"
    })
}



/*   TASKS   */

//GET ALL TASKS
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

//UPDATE THE TASK IF IT IS DONE OR NOT
exports.updateDoneTask = function (req, res) {
    try {
        var decoded = jwt.verify(req.body.token, 'OHNOITISNOTVERYSECRET');
    } catch (err) {
        res.send({
            "code": 400,
            "failed": "Incorrect or expired Token"
        })
    }

    if (typeof req.body.idTask === 'undefined' || req.body.idTask === null) {
        res.send({
            "code": 400,
            "error": 'Give me an ID!'
        })
    }

    if (typeof req.body.state === 'undefined' || req.body.state === null) {
        res.send({
            "code": 400,
            "error": 'Tell me if the task is done or not!'
        })
    }
    query = connection.query("UPDATE `todo_task_list` SET `is_done` = '" + req.body.state + "' WHERE `todo_task_list`.`id` = " + req.body.idTask + ";");
    res.send({
        "code": 200,
        "idTask": req.body.idTask,
        "answer": "task updated"
    })
}

//CREATE A TASK
exports.createTask = function (req, res) {
    try {
        var decoded = jwt.verify(req.body.token, 'OHNOITISNOTVERYSECRET');
    } catch (err) {
        res.send({
            "code": 400,
            "failed": "Incorrect or expired Token"
        })
    }

    if (typeof req.body.name === 'undefined' || req.body.name === null) {
        res.send({
            "code": 400,
            "error": 'Give me a name!'
        })
    }
    if (typeof req.body.idList === 'undefined' || req.body.idList === null) {
        res.send({
            "code": 400,
            "error": 'Give me a list ID!'
        })
    }
    if (typeof req.body.idProject === 'undefined' || req.body.idProject === null) {
        res.send({
            "code": 400,
            "error": 'Give me a project ID!'
        })
    }

    query = connection.query("INSERT INTO `todo_task_list` (`id`, `name`, `is_done`, `id_list`, `id_project`) VALUES (NULL, '" + req.body.name + "', '0', '" + req.body.idList + "', '" + req.body.idProject + "');");
    res.send({
        "code": 200,
        "name": req.body.name,
        "idList": req.body.idList,
        "idProject": req.body.idProject,
        "answer": "task created"
    })
}

//DELETE A TASK
exports.deleteTask = function (req, res) {
    try {
        var decoded = jwt.verify(req.body.token, 'OHNOITISNOTVERYSECRET');
    } catch (err) {
        res.send({
            "code": 400,
            "failed": "Incorrect or expired Token"
        })
    }

    if (typeof req.body.idTask === 'undefined' || req.body.idTask === null) {
        res.send({
            "code": 400,
            "error": 'Give me a task ID!'
        })
    }
    query = connection.query("DELETE FROM `todo_task_list` WHERE `todo_task_list`.`id` =" + req.body.idTask);
    res.send({
        "code": 200,
        "idTask": req.body.idTask,
        "answer": "task deleted"
    })
}

//UPDATE A NAME OF A TASK
exports.updateNameTask = function (req, res) {
    try {
        var decoded = jwt.verify(req.body.token, 'OHNOITISNOTVERYSECRET');
    } catch (err) {
        res.send({
            "code": 400,
            "failed": "Incorrect or expired Token"
        })
    }

    if (typeof req.body.idTask === 'undefined' || req.body.idTask === null) {
        res.send({
            "code": 400,
            "error": 'Give me a task ID!'
        })
    }

    if (typeof req.body.name === 'undefined' || req.body.name === null) {
        res.send({
            "code": 400,
            "error": 'Give me a name!'
        })
    }

    query = connection.query("UPDATE `todo_task_list` SET `name` = '" + req.body.name + "' WHERE `todo_task_list`.`id` = " + req.body.idTask + ";");
    res.send({
        "code": 200,
        "idTask": req.body.idTask,
        "answer": "task updated"
    })
}



/*   USERS   */

//GET ALL USERS
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

//DELETE A USER
exports.deleteUser = function (req, res) {
    try {
        var decoded = jwt.verify(req.body.token, 'OHNOITISNOTVERYSECRET');
    } catch (err) {
        res.send({
            "code": 400,
            "failed": "Incorrect or expired Token"
        })
    }

    if (typeof req.body.idUser === 'undefined' || req.body.idUser === null) {
        res.send({
            "code": 400,
            "error": 'Give me a user ID!'
        })
    }
    query = connection.query("DELETE FROM `user` WHERE `user`.`id` =" + req.body.idUser);
    res.send({
        "code": 200,
        "answer": "user deleted"
    })
}

//CREATE A USER
exports.createUser = async function (req, res) {
    try {
        var decoded = jwt.verify(req.body.token, 'OHNOITISNOTVERYSECRET');
    } catch (err) {
        res.send({
            "code": 400,
            "failed": "Incorrect or expired Token"
        })
    }

    if (typeof req.body.email === 'undefined' || req.body.email === null) {
        res.send({
            "code": 400,
            "error": 'Give me an email!'
        })
    }

    if (typeof req.body.password === 'undefined' || req.body.password === null) {
        res.send({
            "code": 400,
            "error": 'Give me a password!'
        })
    }


    if (typeof req.body.username === 'undefined' || req.body.username === null) {
        res.send({
            "code": 400,
            "error": 'Give me an username!'
        })
    }

    if (typeof req.body.firstname === 'undefined' || req.body.firstname === null) {
        res.send({
            "code": 400,
            "error": 'Give me a firstname!'
        })
    }

    if (typeof req.body.lastname === 'undefined' || req.body.lastname === null) {
        res.send({
            "code": 400,
            "error": 'Give me a lastname!'
        })
    }

    if (typeof req.body.business_name === 'undefined' || req.body.business_name === null) {
        res.send({
            "code": 400,
            "error": 'Give me a business name!'
        })
    }

    if (typeof req.body.job === 'undefined' || req.body.job === null) {
        res.send({
            "code": 400,
            "error": 'Give me a job!'
        })
    }

    bcrypt.genSalt(saltRounds, function (err, salt) {
        bcrypt.hash(req.body.password, salt, function (err, hash) {
            password = hash.replace('$2b$', '$2y$');
            query = connection.query("INSERT INTO `user` (`id`, `email`, `username`, `password`, `firstname`, `lastname`, `is_active`, `business_name`, `rules`, `job`) VALUES (NULL, '" + req.body.email + "', '" + req.body.username + "', '" + password + "', '" + req.body.firstname + "', '" + req.body.lastname + "', '1', '" + req.body.business_name + "', 'ROLE_ADMIN', '" + req.body.job + "');");

            res.send({
                "code": 200,
                "answer": "User created"
            })
        });
    });


}

//UPDATE A EMAIL OF A USER
exports.updateEmailUser = function (req, res) {
    try {
        var decoded = jwt.verify(req.body.token, 'OHNOITISNOTVERYSECRET');
    } catch (err) {
        res.send({
            "code": 400,
            "failed": "Incorrect or expired Token"
        })
    }

    if (typeof req.body.idUser === 'undefined' || req.body.idUser === null) {
        res.send({
            "code": 400,
            "error": 'Give me a user ID!'
        })
    }

    if (typeof req.body.email === 'undefined' || req.body.email === null) {
        res.send({
            "code": 400,
            "error": 'Give me a email!'
        })
    }

    query = connection.query("UPDATE `user` SET `email` = '" + req.body.email + "' WHERE `user`.`id` = " + req.body.idUser + ";");
    res.send({
        "code": 200,
        "answer": "User updated"
    })
}

//UPDATE A PASSWORD OF A USER
exports.updatePasswordUser = function (req, res) {
    try {
        var decoded = jwt.verify(req.body.token, 'OHNOITISNOTVERYSECRET');
    } catch (err) {
        res.send({
            "code": 400,
            "failed": "Incorrect or expired Token"
        })
    }

    if (typeof req.body.idUser === 'undefined' || req.body.idUser === null) {
        res.send({
            "code": 400,
            "error": 'Give me a user ID!'
        })
    }

    if (typeof req.body.password === 'undefined' || req.body.password === null) {
        res.send({
            "code": 400,
            "error": 'Give me a password!'
        })
    }
    bcrypt.genSalt(saltRounds, function (err, salt) {
        bcrypt.hash(req.body.password, salt, function (err, hash) {
            password = hash.replace('$2b$', '$2y$');
            query = connection.query("UPDATE `user` SET `password` = '" + password + "' WHERE `user`.`id` = " + req.body.idUser + ";");

            res.send({
                "code": 200,
                "answer": "User updated"
            })
        });
    });
}

//UPDATE THE FIRSTNAME OF A USER
exports.updateFistnameUser = function (req, res) {
    try {
        var decoded = jwt.verify(req.body.token, 'OHNOITISNOTVERYSECRET');
    } catch (err) {
        res.send({
            "code": 400,
            "failed": "Incorrect or expired Token"
        })
    }

    if (typeof req.body.idUser === 'undefined' || req.body.idUser === null) {
        res.send({
            "code": 400,
            "error": 'Give me a user ID!'
        })
    }

    if (typeof req.body.firstname === 'undefined' || req.body.firstname === null) {
        res.send({
            "code": 400,
            "error": 'Give me a firstname!'
        })
    }

    query = connection.query("UPDATE `user` SET `firstname` = '" + req.body.firstname + "' WHERE `user`.`id` = " + req.body.idUser + ";");
    res.send({
        "code": 200,
        "answer": "User updated"
    })
}

//UPDATE THE LASTNAME OF A USER
exports.updateLastnameUser = function (req, res) {
    try {
        var decoded = jwt.verify(req.body.token, 'OHNOITISNOTVERYSECRET');
    } catch (err) {
        res.send({
            "code": 400,
            "failed": "Incorrect or expired Token"
        })
    }

    if (typeof req.body.idUser === 'undefined' || req.body.idUser === null) {
        res.send({
            "code": 400,
            "error": 'Give me a user ID!'
        })
    }

    if (typeof req.body.lastname === 'undefined' || req.body.lastname === null) {
        res.send({
            "code": 400,
            "error": 'Give me a lastname!'
        })
    }

    query = connection.query("UPDATE `user` SET `lastname` = '" + req.body.lastname + "' WHERE `user`.`id` = " + req.body.idUser + ";");
    res.send({
        "code": 200,
        "answer": "User updated"
    })
}

//UPDATE IS ACTIVE OF A USER
exports.updateIsactiveUser = function (req, res) {
    try {
        var decoded = jwt.verify(req.body.token, 'OHNOITISNOTVERYSECRET');
    } catch (err) {
        res.send({
            "code": 400,
            "failed": "Incorrect or expired Token"
        })
    }

    if (typeof req.body.idUser === 'undefined' || req.body.idUser === null) {
        res.send({
            "code": 400,
            "error": 'Give me a user ID!'
        })
    }

    if (typeof req.body.isactive === 'undefined' || req.body.isactive === null) {
        res.send({
            "code": 400,
            "error": 'is active?'
        })
    }

    try {
        query = connection.query("UPDATE `user` SET `is_active` = '" + req.body.isactive + "' WHERE `user`.`id` = " + req.body.idUser + ";");
        res.send({
            "code": 200,
            "answer": "User updated"
        })
    } catch (err) {
        res.send({
            "code": 400,
            "answer": err
        })
    }
}

//UPDATE THE BUSINESS NAME OF A USER
exports.updateBusinessnameUser = function (req, res) {
    try {
        var decoded = jwt.verify(req.body.token, 'OHNOITISNOTVERYSECRET');
    } catch (err) {
        res.send({
            "code": 400,
            "failed": "Incorrect or expired Token"
        })
    }

    if (typeof req.body.idUser === 'undefined' || req.body.idUser === null) {
        res.send({
            "code": 400,
            "error": 'Give me a user ID!'
        })
    }

    if (typeof req.body.businessname === 'undefined' || req.body.businessname === null) {
        res.send({
            "code": 400,
            "error": 'Give me a business name!'
        })
    }

    query = connection.query("UPDATE `user` SET `business_name` = '" + req.body.businessname + "' WHERE `user`.`id` = " + req.body.idUser + ";");
    res.send({
        "code": 200,
        "answer": "User updated"
    })
}

//UPDATE THE JOB OF A USER
exports.updateJobUser = function (req, res) {
    try {
        var decoded = jwt.verify(req.body.token, 'OHNOITISNOTVERYSECRET');
    } catch (err) {
        res.send({
            "code": 400,
            "failed": "Incorrect or expired Token"
        })
    }

    if (typeof req.body.idUser === 'undefined' || req.body.idUser === null) {
        res.send({
            "code": 400,
            "error": 'Give me a user ID!'
        })
    }

    if (typeof req.body.job === 'undefined' || req.body.job === null) {
        res.send({
            "code": 400,
            "error": 'Give me a job!'
        })
    }

    query = connection.query("UPDATE `user` SET `job` = '" + req.body.job + "' WHERE `user`.`id` = " + req.body.idUser + ";");
    res.send({
        "code": 200,
        "answer": "User updated"
    })
}



/*   OUTSIDE ACCESS   */

//GET ALL OUTSIDE ACCESS
exports.outsideAccess = function (req, res) {
    try {
        var decoded = jwt.verify(req.body.token, 'OHNOITISNOTVERYSECRET');
    } catch (err) {
        res.send({
            "code": 400,
            "failed": "Incorrect or expired Token"
        })
    }

    query = connection.query('SELECT * FROM outside_access', async function (error, results, fields) {
        let answer = '[';
        results.forEach(
            element => {
                answer = answer + '{"id":"' + element.id + '", "identifier":"' + element.identifier + '", "can_edit":"' + element.can_edit + '", "id_project":"' + element.id_project + '", "name":"' + element.name + '"},';
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

//CREATE OUTSIDE ACCESS
exports.createOutsideAccess = function (req, res) {
    try {
        var decoded = jwt.verify(req.body.token, 'OHNOITISNOTVERYSECRET');
    } catch (err) {
        res.send({
            "code": 400,
            "failed": "Incorrect or expired Token"
        })
    }

    if (typeof req.body.identifier === 'undefined' || req.body.identifier === null) {
        res.send({
            "code": 400,
            "error": 'Give me a identifier!'
        })
    }

    if (typeof req.body.canedit === 'undefined' || req.body.canedit === null) {
        res.send({
            "code": 400,
            "error": 'Can edit?'
        })
    }

    if (typeof req.body.idProject === 'undefined' || req.body.idProject === null) {
        res.send({
            "code": 400,
            "error": 'Give me a project ID!'
        })
    }

    if (typeof req.body.name === 'undefined' || req.body.name === null) {
        res.send({
            "code": 400,
            "error": 'Give me a name!'
        })
    }

    query = connection.query("INSERT INTO `outside_access` (`id`, `identifier`, `can_edit`, `id_project`, `name`) VALUES (NULL, '" + req.body.identifier + "', '" + req.body.canedit + "', '" + req.body.idProject + "', '" + req.body.name + "');");
    res.send({
        "code": 200,
        "name": req.body.name,
        "idList": req.body.idList,
        "idProject": req.body.idProject,
        "answer": "Outside Access created"
    })
}

//DELETE OUTSIDE ACCESS
exports.deleteOutsideAccess = function (req, res) {
    try {
        var decoded = jwt.verify(req.body.token, 'OHNOITISNOTVERYSECRET');
    } catch (err) {
        res.send({
            "code": 400,
            "failed": "Incorrect or expired Token"
        })
    }

    if (typeof req.body.idOutsideAccess === 'undefined' || req.body.idOutsideAccess === null) {
        res.send({
            "code": 400,
            "error": 'Give me a task ID!'
        })
    }
    query = connection.query("DELETE FROM `outside_access` WHERE `outside_access`.`id` =" + req.body.idOutsideAccess);
    res.send({
        "code": 200,
        "idTask": req.body.idTask,
        "answer": "Outside Access deleted"
    })
}

//UPDATE A NAME OF A OUTSIDE ACCESS
exports.updateNameOutsideAccess = function (req, res) {
    try {
        var decoded = jwt.verify(req.body.token, 'OHNOITISNOTVERYSECRET');
    } catch (err) {
        res.send({
            "code": 400,
            "failed": "Incorrect or expired Token"
        })
    }

    if (typeof req.body.idOutsideAccess === 'undefined' || req.body.idOutsideAccess === null) {
        res.send({
            "code": 400,
            "error": 'Give me a outsideAccess ID!'
        })
    }

    if (typeof req.body.name === 'undefined' || req.body.name === null) {
        res.send({
            "code": 400,
            "error": 'Give me a name!'
        })
    }

    query = connection.query("UPDATE `outside_access` SET `name` = '" + req.body.name + "' WHERE `outside_access`.`id` = " + req.body.idOutsideAccess + ";");
    res.send({
        "code": 200,
        "answer": "Outside Access updated"
    })
}

//UPDATE CAN EDIT OF A OUTSIDE ACCESS
exports.updateCanEditOutsideAccess = function (req, res) {
    try {
        var decoded = jwt.verify(req.body.token, 'OHNOITISNOTVERYSECRET');
    } catch (err) {
        res.send({
            "code": 400,
            "failed": "Incorrect or expired Token"
        })
    }

    if (typeof req.body.idOutsideAccess === 'undefined' || req.body.idOutsideAccess === null) {
        res.send({
            "code": 400,
            "error": 'Give me a outsideAccess ID!'
        })
    }

    if (typeof req.body.canEdit === 'undefined' || req.body.canEdit === null) {
        res.send({
            "code": 400,
            "error": 'Can edit?'
        })
    }

    query = connection.query("UPDATE `outside_access` SET `can_edit` = '" + req.body.canEdit + "' WHERE `outside_access`.`id` = " + req.body.idOutsideAccess + ";");
    res.send({
        "code": 200,
        "answer": "Outside Access updated"
    })
}