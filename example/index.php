<?php

require_once '../vendor/autoload.php';

# set our JWT secret (optional)
# define('JWT_SECRET', 'YourSecretKey');

# init our class
$CsrfToken = new Zoxta\Csrf\JwtCsrfToken();

# if a form is submitted (using POST)
if (! empty($_POST['_token'])) {

    # check if CSRF is invalid
    if ($CsrfToken->isInvalid()) {
        # return an error if CSRF token is invalid/expired
        echo '<h1>Invalid token, stop.</h1>';
    } else {
        echo '<h1>Valid token, process form.</h1>';
    }

    exit;
}

# OR

if ($CsrfToken->isValid()) {
    # process the form request
    echo '<h1>Valid token, process form.</h1>';
    exit;
}

?>

<form action="" method="post">

    <input type="hidden" name="_token" value="<?= $CsrfToken ?>">

    <!-- OR  -->

    <?= $CsrfToken->field() ?>

    <input type="submit" value="Submit Form">
</form>