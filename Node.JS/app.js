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
router.get('/list',index.list);
router.get('/tasks',index.tasks);
router.get('/users',index.users);

//Post Routes
router.post('/login',login.login);
router.post('/updateDoneTask',index.updateDoneTask);
router.post('/createTask',index.createTask);
router.post('/deleteTask',index.deleteTask);
router.post('/updateNameTask',index.updateNameTask);
router.post('/createProject',index.createProject);

app.use('/', router);
app.listen(3000);