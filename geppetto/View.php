<?php

/*
 * Class View
 * ---------------------------------------------------------------------------------------------------------------------
 */
class View{

    static function style(): string{
        return('
        <style>
        body{
            box-sizing: border-box;
              font-family: Verdana;
            
            }
            
            .container {
            display: flex;
            justify-content: center;
              align-items: center;
              height: 100vh;
            }
            
            form {
            box-sizing: border-box;
              display: inline-block;
              padding: 3em;
              border: 1px solid #ccc;
              border-radius: 5px;
            }
            
            p + p {
            margin-top: 1em;
            }
            
            label {
            /* Uniform size & alignment */
            display: inline-block;
            }
            
            input,
            textarea {
            width: 300px;
              box-sizing: border-box;
              border: 1px solid #222;
              border-radius: 5px;
              padding:10px;
            }
            
            input:focus,
            textarea:focus {
            /* Set the outline width and style */
            -outline-style: solid;
            }   
            
            .danger{
                padding: 20px;
                color: red;
                border: 1px solid red;
            }            
            .success{
                padding: 20px;
                color: green;
                border: 1px solid green;
            }
  
        </style>
        ');
    }


    static function login($login='', $message = array()){

        $htmlMessage = '';
        if(isset($message['type']) && isset($message['text'])){
            $htmlMessage = '<div class="'.$message['type'].'">'.$message['text'].'</div>';
        }

        return(
            '<html>'.
            self::style().'
        <body>
          <div class="container">
            <div class="item">'.
            $htmlMessage.'
            <form action="/geppetto.php" method="post">
              <p>
                <label for="login">Login:</label>
                </p>
                <p>
                <input type="text" id="login" name="login" value="'.$login.'"/>
              </p>
              <p>
                <label for="password">Password:</label>
              </p>
              <p>
                <input type="password" id="password" name="password" />
              </p>
              <p>
                <input class="button" type="submit" name="submit" id="submit" value="Login" />
              </p>
            </form>
            </div>
          </div>
        </body>
        </html>');
    }


    static function newUser($login='', $message= array()){

        $htmlMessage = '';
        if(isset($message['type']) && isset($message['text'])){
            $htmlMessage = '<div class="'.$message['type'].'">'.$message['text'].'</div>';
        }

        return(
            '<html>'.
            self::style().'
        <body>
          <div class="container">
            <div class="item">

            <form action="/geppetto.php" method="post">
            <h2>New user</h2>
            <p>There is no user registered, please create a user</p>
            '.$htmlMessage.'
              <p>
                <label for="login">Login:</label>
                </p>
                <p>
                <input type="text" id="login" name="login" value="'.$login.'"/>
              
              <p>
                <label for="password">Password:</label>
                                </p>
                <p>
                <input type="password" id="password" name="password" />
              </p>
              
              <p>
                <label for="password">Retype Password:</label>
                                </p>
                <p>
                <input type="password" id="password2" name="password2" />
              </p>
              <p>&nbsp;</p>
              <p>
             
              <input class="button" type="submit" id="submit" name="create" value="Create" />
            </p>
            </form>
            </div>
          </div>
        </body>
        </html>');
    }
}