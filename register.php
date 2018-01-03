<?php
session_start();
include 'global.php';
?>


<!DOCTYPE html>
<html lang="en">
    <head>

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">
        <link href="css/register.css" rel="stylesheet">
        <link rel="icon" type="image/png" href="images/icon.png">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <title>Kidoordinate - Register</title>
    </head>

    <body>

        <div>
            <a id="top"></a>
            <nav class="navbar navbar-toggleable-md navbar-light bg-faded">
                <a class="navbar-brand" href="./index.php">
                    <img src="images/logo.png" style="width:25%" >
                </a>
                <div class="navbar-nav" id="navLinks">
                    <a class="nav-item nav-link navbar-text navlink" href="./login.php">Login</a>
                    <a class="nav-item nav-link navbar-text navlink" href="./register.php">Register</a>
                </div>
            </nav>
        </div>
        <div class="jumbotron jumbtotron-fluid" id="regBack">
            <div class="container">
                <?php
                if(isset($_SESSION['userid'])){
                    exit('<h1>You\'re already registered!</h1>');
                }

                if($_POST){
                    //there is submitted form data

                    if(!isset($_POST['confirm'])){
                        exit('<h2>You must confirm that you are the legal guardian and that all the information is correct!<h2>');
                    }

                    //validate all fields filled
                    $required = array('username', 'firstname', 'lastname', 'password', 'password2', 'phone', 'aptcomplex', 'addressline1', 'city', 'state', 'zip', 'bio', 'kidfirstname', 'kidlastname', 'kidage', 'kidbio');
                    $error = false;
                    foreach($required as $field) {
                        if (empty($_POST[$field])) {
                            $error = true;
                        }
                    }

                    if ($error) {
                        exit('<h2>All fields are required.</h2>');
                    }

                    //filter fields
                    $username = strip_tags(trim($_POST['username']));
                    $firstname = strip_tags(trim($_POST['firstname']));
                    $lastname = strip_tags(trim($_POST['lastname']));
                    $bio = strip_tags(trim($_POST['bio']));
                    $kidfirstname = strip_tags(trim($_POST['kidfirstname']));
                    $kidlastname = strip_tags(trim($_POST['kidlastname']));
                    $kidbio = strip_tags(trim($_POST['kidbio']));

                    if(strlen($username) > 255){
                        exit('<h2>Username is too long!</h2>');
                    }

                    if(strlen($firstname) > 255){
                        exit('<h2>First name is too long!</h2>');
                    }

                    if(strlen($lastname) > 255){
                        exit('<h2>Last name is too long!</h2>');
                    }

                    if(strlen($bio) > 5000){
                        exit('<h2>Bio is too long!</h2>');
                    }

                    if(strlen($kidfirstname) > 255){
                        exit('<h2>Kid first name is too long!</h2>');
                    }

                    if(strlen($kidlastname) > 255){
                        exit('<h2>Kid last name is too long!</h2>');
                    }

                    if(strlen($kidbio) > 5000){
                        exit('<h2>Kid bio is too long!</h2>');
                    }

                    if(!is_numeric($_POST['kidage'])){
                        exit('<h2>Kid age is not a number!</h2>');
                    }

                    //validate passwords match
                    if(strcmp($_POST['password'], $_POST['password2']) != 0){
                        exit('<h2>The two passwords didn\'t match!</h2>');
                    }

                    //validate phone number
                    $phone = preg_replace("/[^0-9]/", '', $_POST['phone']);
                    if (strlen($phone) == 11) {
                        $phone = preg_replace("/^1/", '',$justNums);
                    }
                    if (strlen($phone) != 10) {
                        exit('<h2>Invalid phone number!</h2>');
                    }

                    //validate email
                    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
                    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        exit('<h2>Invalid email!</h2>');
                    }

                    //connect to db
                    $conn = new mysqli($host, $user, $pass, $db);
                    if($conn->connect_error){
                        die('<h2>Connection failed!</h2>');
                    }

                    //find the date and time
                    $datetime = date("Y-m-d H:i:s");

                    //set zero
                    $zero = 0;

                    //crpyt password
                    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

                    //ensure this is a unique user
                    $stmt = $conn->prepare("SELECT id FROM parents WHERE username = ? OR email = ? OR phone = ?");
                    $stmt->bind_param("ssi", $username, $_POST['email'], $phone);
                    $stmt->execute();
                    $stmt->store_result();
                    if($stmt->num_rows >= 1) {
                        exit('<h2>Someone with that username, email, or phone already exists!</h2>');
                    }
                    $stmt->close();

                    $filetype = pathinfo($_FILES["addressverification"]["name"],PATHINFO_EXTENSION);

                    //find latitude and longitude
                    $apiaddress = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($_POST['addressline1']) . ',' . urlencode($_POST['city']) . ',' . urlencode($_POST['state']) . '&key=' . $gmapskey;
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $apiaddress);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    $geoloc = json_decode(curl_exec($ch), true);

                    if(strcmp($geoloc['status'], 'ZERO_RESULTS')==0){
                        exit('<h2>Invalid address!</h2>');
                    }
                    $formattedaddress = explode(",", $geoloc['results'][0]['formatted_address']);
                    $latitude = $geoloc['results'][0]['geometry']['location']['lat'];
                    $longitude = $geoloc['results'][0]['geometry']['location']['lng'];
                    $addressline1 = trim($formattedaddress[0]);
                    $city = trim($formattedaddress[1]);
                    $stateandzip = explode(" ", trim($formattedaddress[2]));

                    //prepared statements to enter parent data
                    $stmt = $conn->prepare('INSERT INTO parents (username, password, firstname, lastname, email, phone, complex, addressline1, addressline2, city, state, zip, latitude, longitude, bio, lastonline, activated, picformat) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
                    $stmt->bind_param('sssssisssssiddssis', $username, $password, $firstname, $lastname, $_POST['email'], $phone, $_POST['aptcomplex'], $addressline1, $_POST['addressline2'], $city, $stateandzip[0], $stateandzip[1], $latitude, $longitude, $bio, $datetime, $zero, $filetype);
                    $stmt->execute();
                    $parentid = $conn->insert_id;
                    $stmt->close();

                    //prepare statements to enter kid data
                    $stmt = $conn->prepare('INSERT INTO kids (firstname, lastname, age, bio, parentid) VALUES (?, ?, ?, ?, ?)');
                    $stmt->bind_param('ssisi', $kidfirstname, $kidlastname, $_POST['kidage'], $kidbio, $parentid);
                    $stmt->execute();
                    $kidid = $conn->insert_id;
                    $stmt->close();

                    //associate parent with kid
                    $stmt = $conn->prepare('INSERT INTO parentkidrelation (parentid, kidid) VALUES (?, ?)');
                    $stmt->bind_param('ii', $parentid, $kidid);
                    $stmt->execute();
                    $stmt->close();

                    //now handle file upload
                    $directory = "uploads/";
                    $uploadstatus = 1;
                    $filename = $directory.$parentid.'.'.$filetype;

                    //file verification
                    $check = getimagesize($_FILES["addressverification"]["tmp_name"]);
                    if($check == false) {
                        echo "<h2>File is not an image.</h2>";
                        $uploadstatus = 0;
                    }

                    //file size verification
                    if ($_FILES["addressverification"]["size"] > 1048576) {
                        echo "<h2>Sorry, your file is too large. It must be < 1 MB.</h2>";
                        $uploadstatus = 0;
                    }

                    //extension verification
                    if($filetype != "jpg" && $filetype != "png" && $filetype != "jpeg" && $filetype != "JPG" && $filetype != "PNG" && $filetype != "JPEG") {
                        echo "<h2>Sorry, only JPG, JPEG, PNG files are allowed.</h2>";
                        $uploadstatus = 0;
                    }

                    if($uploadstatus == 1){
                        move_uploaded_file($_FILES["addressverification"]["tmp_name"], $filename);
                    }
                    else {
                        exit('<h2>Please try again.</h2>');
                    }

                    echo '<h2>You have been registered! Please wait to be activated.</h2>';
                }
                else {
                    //display registration form
                    echo '<form id="register" method="post" action="" class="form-signin" enctype="multipart/form-data">
    <h2 class="form-signin-heading">Join the family.</h2>
  <label for="username">Username</label>
    <input type="text" id="username" name="username" class="form-control" required>
      <p></p>
      <label for="password">Password</label>
    <input type="password" id="password" name="password" class="form-control" required>
      <p></p>
      <label for="password2">Confirm password</label>
    <input type="password" id="password2" name="password2" class="form-control" required>
      <p></p>
      <label for="firstname">First Name</label>
    <input type="text" id="firstname" name="firstname" class="form-control" required>
      <p></p>
      <label for="lastname">Last Name</label>
    <input type="text" id="lastname" name="lastname" class="form-control" required>
      <p></p>
      <label for="email">Email</label>
    <input type="email" id="email" name="email" class="form-control" required>
      <p></p>
      <label for="phone">Phone number</label>
    <input type="tel" id="phone" name="phone" class="form-control" required>
      <p></p>
          <label for="aptcomplex">Apartment Complex</label>
    <input type="text" id="aptcomplex" name="aptcomplex" class="form-control" onFocus="initAutocomplete()" required>
      <p></p>
          <label for="addressline1">Address Line 1</label>
    <input type="text" id="addressline1" name="addressline1" class="form-control" required>
      <p></p>
          <label for="addressline2">Address Line 2</label>
    <input type="text" id="addressline2" name="addressline2" class="form-control">
      <p></p>
       <label for="addressline2">City</label>
    <input type="text" id="city" name="city" class="form-control" required>
      <p></p>
      <label for="state">State</label>
      <select name="state" id="state" class="form-control" required>
	<option value="AL">Alabama</option>
	<option value="AK">Alaska</option>
	<option value="AZ">Arizona</option>
	<option value="AR">Arkansas</option>
	<option value="CA">California</option>
	<option value="CO">Colorado</option>
	<option value="CT">Connecticut</option>
	<option value="DE">Delaware</option>
	<option value="DC">District Of Columbia</option>
	<option value="FL">Florida</option>
	<option value="GA">Georgia</option>
	<option value="HI">Hawaii</option>
	<option value="ID">Idaho</option>
	<option value="IL">Illinois</option>
	<option value="IN">Indiana</option>
	<option value="IA">Iowa</option>
	<option value="KS">Kansas</option>
	<option value="KY">Kentucky</option>
	<option value="LA">Louisiana</option>
	<option value="ME">Maine</option>
	<option value="MD">Maryland</option>
	<option value="MA">Massachusetts</option>
	<option value="MI">Michigan</option>
	<option value="MN">Minnesota</option>
	<option value="MS">Mississippi</option>
	<option value="MO">Missouri</option>
	<option value="MT">Montana</option>
	<option value="NE">Nebraska</option>
	<option value="NV">Nevada</option>
	<option value="NH">New Hampshire</option>
	<option value="NJ">New Jersey</option>
	<option value="NM">New Mexico</option>
	<option value="NY">New York</option>
	<option value="NC">North Carolina</option>
	<option value="ND">North Dakota</option>
	<option value="OH">Ohio</option>
	<option value="OK">Oklahoma</option>
	<option value="OR">Oregon</option>
	<option value="PA">Pennsylvania</option>
	<option value="RI">Rhode Island</option>
	<option value="SC">South Carolina</option>
	<option value="SD">South Dakota</option>
	<option value="TN">Tennessee</option>
	<option value="TX">Texas</option>
	<option value="UT">Utah</option>
	<option value="VT">Vermont</option>
	<option value="VA">Virginia</option>
	<option value="WA">Washington</option>
	<option value="WV">West Virginia</option>
	<option value="WI">Wisconsin</option>
	<option value="WY">Wyoming</option>
</select><p></p>
       <label for="addressline2">Zip Code</label>
    <input type="num" id="zip" name="zip" class="form-control" required>
      <p></p>

          <label for="bio" >Bio</label>
          <textarea rows="4" cols="50" id="bio" name="bio" class="form-control" required>
</textarea>
          <p></p>
          <label for="addressverification">Upload address verification</label>
          <input type="file" id="addressverification" name="addressverification" class="form-control" required>
          <p></p><p></p><hr><br/>More children can be added later<p></p>
          <label for="kidfirstname">Kid First Name</label>
    <input type="text" id="kidfirstname" name="kidfirstname" class="form-control" required>
      <p></p>
      <label for="kidlastname">Kid Last Name</label>
    <input type="text" id="kidlastname" name="kidlastname" class="form-control" required>
      <p></p>
      <label for="kidage" >Kid age</label>
    <input type="num" id="kidage" name="kidage" class="form-control" required>
      <p></p>
          <label for="kidbio" >Kid Bio</labe>
          <textarea rows="4" cols="50" id="kidbio" name="kidbio" class="form-control" required>
</textarea><p></p>
<div class="checkbox">
          <label>
            <input type="checkbox" value="confirm"> I certify that I am a legal guardian, and that the above information is correct.
          </label>
        </div>
        <button class="btn btn-lg btn-primary btn-block" type="create">Register</button>
      </form>';
                }

                ?>
            </div></div>
        <script>
            var placeSearch, autocomplete;
            var componentForm = {
                street_number: 'short_name',
                route: 'long_name',
                locality: 'long_name',
                administrative_area_level_1: 'long_name',
                postal_code: 'short_name'
            };
            var formIDs = [
                "addressline1",
                "city",
                "state",
                "zip"
            ];
            function initAutocomplete() {
                // Create the autocomplete object, restricting the search to geographical
                // location types.
                autocomplete = new google.maps.places.Autocomplete(
                    /** @type {!HTMLInputElement} */(document.getElementById('aptcomplex')),
                    {types: ['establishment']});

                // When the user selects an address from the dropdown, populate the address
                // fields in the form.
                autocomplete.addListener('place_changed', fillInAddress);
            }

            function fillInAddress() {
                // Get the place details from the autocomplete object.
                var place = autocomplete.getPlace();
                document.getElementById("aptcomplex").value = place.name;
                document.getElementById("addressline1").value = place.address_components[0].long_name + " " + place.address_components[1].long_name;
                document.getElementById("city").value = place.address_components[2].long_name;
                document.getElementById("state").value = place.address_components[4].short_name;
                document.getElementById("zip").value = place.address_components[6].long_name;


            }

        </script>
        <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC6CD6noeRauhJTj6bK-PJvYXsKjzs52wk&libraries=places&callback=initAutocomplete"></script>
    </body></html>
