<?php
session_start();
include'global.php';

if(isset($_SESSION['userid'])){
    header("Location: dashboard.php");
    die();
}

?>
<!DOCTYPE html>
<html lang="en">
    <head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">
    <link rel="stylesheet" href="css/home.css">
    <link rel="icon" type="image/png" href="images/icon.png">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <title>Kidoordinate</title>
    </head>
    
    <body>

        <div>
            <a id="top"></a>
            <nav class="navbar navbar-toggleable-md navbar-light bg-faded">
                <a class="navbar-brand" href="#">
                    <img src="images/logo.png" style="width:25%">
                </a>      
                <div class="navbar-nav" id="navLinks">
                    <a class="nav-item nav-link navbar-text navlink" href="./login.php">Login</a>
                    <a class="nav-item nav-link navbar-text navlink" href="./register.php">Register</a>
                </div>
            </nav>
        </div>
        
        <div class="jumbotron jumbtotron-fluid text-center" id="about">
            <div class="container">
                <a id="about"></a>
                <h1 class="display">Find your child's perfect playdate.</h1>
                <p class="lead">Kidoordinate is a free service that helps parents and guardians living in apartment complexes arrange playdates for their children — because all children should feel like they have a neighborhood.</p>
            </div>
        </div>

    <div class="container">
            <a id="portfolio"></a>
            <div class="card-columns">
                 <div class="card">
                    <div class="card-body" align="center">
                        <h4 class="card-title"><img src="images/icon1.png" width="75px" height="75px"><br><br><small class="text-muted"><i>Imbue your child's life with a sense of community.</i></small>
</h4>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body" align="center">
                        <h4 class="card-title"><img src="images/icon2.png" width="75px" height="75px"><br><br><small class="text-muted"><i>Help your child have fun with new friends.</i></small>
</h4>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-body" align="center">
                        <h4 class="card-title"><img src="images/icon3.png" width="75px" height="75px"><br><br><small class="text-muted"><i>Make connections in a safe and secure way.</i></small>
</h4>
                    </div>
                </div>
               
                
                <div class="card">
                    <div class="card-body" align="center">
                        <h4 class="card-title"><img src="images/icon4.png" width="75px" height="75px"><br><br><small class="text-muted"><i>... All instantly</i></small>
</h4>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body" align="center">
                        <h4 class="card-title"><img src="images/icon5.png" width="75px" height="75px"><br><br><small class="text-muted"><i>Find a support network of parents just like you.</i></small>
</h4>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body" align="center">
                        <h4 class="card-title"><img src="images/icon6.png" width="75px" height="75px"><br><br><small class="text-muted"><i>... And all at no cost.</i></small>
</h4>
                    </div>
                </div>
                </div>

                <div class="jumbotron jumbtotron-fluid text-center" id="testimonials">
            <div class="container">
                <a id="testimonials"></a>
                <h1 class="display">Testimonials</h1>
            </div>
        </div>

        <div class="container">
            <a id="portfolio"></a>
            <div class="card-columns">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Jeff F.<small class="text-muted"><i>  (Durham, NC)</i></small></h4>
                        <p class="card-text"><i>When I moved across the state as a single dad, I was afraid of the transition for my 4yo son. Kidoordinate helped me find new friends for him, even in our 200+ unit apartment complex.</i><br/></p>
                    </div>

                    </div>
                                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Nancy J.<small class="text-muted"><i>  (Raleigh, NC)</i></small></h4>
                        <p class="card-text"><i>Really enhances the sense of community.</i><br/></p>
                    </div>

                    </div>
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Janice R.<small class="text-muted"><i>  (Cary, NC)</i></small></h4>
                        <p class="card-text"><i>I was hesitant at first, but Kidoordinate quickly went from feeling like a website to a support network. Both my kid and I love the friends we've made!</i><br/></p>
                    </div>

                    </div>
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Bruce M.<small class="text-muted"><i>  (Greensboro, NC)</i></small></h4>
                        <p class="card-text"><i>Fast and easy. Perfect for arranging fun for my kid in my busy life.</i><br/></p>
                    </div>

                    </div>

                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Joyce K.<small class="text-muted"><i>  (Durham, NC)</i></small></h4>
                        <p class="card-text"><i>Highly recommend!!</i><br/></p>
                    </div>

                    </div>

                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Susan R.<small class="text-muted"><i>  (Cary, NC)</i></small></h4>
                        <p class="card-text"><i>My son is having so much fun hanging out with his new friends. Who knew so many great people lived just one or two floors away? Thanks for making my apartment feel like a neighborhood, Kidoordinate!</i><br/></p>
                    </div>

                    </div>
                </div>
        </div></div>

 <div class="container text-center">
        <small class="text-muted"><a href="#top">Back to Top</a></small>
        </div>
    </body>
</html>


