//DECLARATION + INCLUDES
var mysql      = require('mysql2');
var connection = mysql.createConnection({
    host: 'localhost',
    user: 'toptask',
    password: '123soleil',
    database: 'toptasks'
});
var bcrypt = require('bcrypt');
const jwt = require('jsonwebtoken');

//CONNECT BDD
connection.connect(function(err){
if(!err) {
    console.log("Database is connected ...");
} else {
    console.log("Error connecting database ...");
}
});

//FONCTION LOGIN
exports.login = async function(req,res){
    var email= req.body.email;
    var password = req.body.password;
    connection.query('SELECT * FROM user WHERE email = ?',[email], async function (error, results, fields) {
      password_bdd = results[0].password.replace('$2y$', '$2a$');
      AREYOUTHEONE = await bcrypt.compare(password, password_bdd);
    if (error) {
      res.send({
        "code":400,
        "failed": error
      })
    }else{
      if(results.length >0){
        if(AREYOUTHEONE){
          const tkn = jwt.sign({
            username: results[0].username
          }, 'OHNOITISNOTVERYSECRET', { expiresIn: 60 * 60 });

          res.send({
            "code":200,
            "success":"login sucessfull",
            "token":tkn
              });
        }
        else{
          res.send({
            "code":204,
            "success":"Email and password does not match"
              });
        }
      }
      else{
        res.send({
          "code":204,
          "success":"Email does not exits"
            });
      }
    }
    });
  }