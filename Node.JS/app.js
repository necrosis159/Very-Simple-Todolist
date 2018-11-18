//Declaration
var express    = require("express");
var login = require('./routes/loginroutes');
var index = require('./routes/indexroutes');
var bodyParser = require('body-parser');
var app = express();
app.use(bodyParser.urlencoded({ extended: true }));
app.use(bodyParser.json());
app.use(function(req, res, next) {
    res.header("Access-Control-Allow-Origin", "*");
    res.header("Access-Control-Allow-Headers", "Origin, X-Requested-With, Content-Type, Accept");
    next();
});
var router = express.Router();

//Get Routes
router.get('/project',index.project);
router.get('/tasklist',index.tasklist);
router.get('/tasks',index.tasks);
router.get('/users',index.users);
router.get('/log', index.log);
router.get('/outsideAccess', index.outsideAccess);

//Post Routes
router.post('/login',login.login);
router.post('/createTask',index.createTask);
router.post('/createProject',index.createProject);
router.post('/createUser',index.createUser);
router.post('/createOutsideAccess',index.createOutsideAccess);

//Put Routes
router.put('/updateDoneTask',index.updateDoneTask);
router.put('/updateNameTask',index.updateNameTask);
router.put('/updateNameProject',index.updateNameProject);
router.put('/updateLogoProject',index.updateLogoProject);
router.put('/updateNameTaskList',index.updateNameTaskList);
router.put('/updateIsArchivedTaskList',index.updateIsArchivedTaskList);
router.put('/updateEmailUser',index.updateEmailUser);
router.put('/updatePasswordUser',index.updatePasswordUser);
router.put('/updateFistnameUser',index.updateFistnameUser);
router.put('/updateLastnameUser',index.updateLastnameUser);
router.put('/updateIsactiveUser',index.updateIsactiveUser);
router.put('/updateBusinessnameUser',index.updateBusinessnameUser);
router.put('/updateJobUser',index.updateJobUser);
router.put('/updateNameOutsideAccess',index.updateNameOutsideAccess);
router.put('/updateCanEditOutsideAccess',index.updateCanEditOutsideAccess);

//Delete Routes
router.delete('/deleteProject',index.deleteProject);
router.delete('/deleteTask',index.deleteTask);
router.delete('/deleteOutsideAccess',index.deleteOutsideAccess);
router.delete('/deleteTasklist',index.deleteTasklist);
router.delete('/deleteUser',index.deleteUser);

app.use('/', router);
app.listen(3000);