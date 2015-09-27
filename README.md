# Quick CSRF
[![Latest Stable Version](https://poser.pugx.org/zoxta/csrf/v/stable)](https://packagist.org/packages/zoxta/csrf) [![Total Downloads](https://poser.pugx.org/zoxta/csrf/downloads)](https://packagist.org/packages/zoxta/csrf) [![Latest Unstable Version](https://poser.pugx.org/zoxta/csrf/v/unstable)](https://packagist.org/packages/zoxta/csrf) [![License](https://poser.pugx.org/zoxta/csrf/license)](https://packagist.org/packages/zoxta/csrf)

Quick CSRF offers stateless CSRF protection for forms that requires almost zero-configuration.
It uses the JSON Web Token standard so it does not depend on session/cookies.

Quick CSRF depends on the beautiful [lcobucci/jwt](https://github.com/lcobucci/jwt) JWT implementation.

# Installation
````
composer require zoxta/csrf
````

# Usage

Just instantiate the class and you will be ready to go. You will also find an sample usage in the `example` directory.

````php
<?php

use Zoxta\Csrf;

# instantiate the class
$CsrfToken = new JwtCsrfToken();

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

````
You can also just use the `isValid()` method immediately without any other requirements.

````php
if ($CsrfToken->isValid()) {

    # process the form request
    echo '<h1>Valid token, process form.</h1>';
    exit;

}
````

To echo the CSRFT token in your forms, you have two simple ways. You can either echo the token itself:

````html

<input type="hidden" name="_token" value="<?= $CsrfToken ?>">

````

Or you can echo the whole input field for simplicity using `$CsrfToken->field()` as the following:
````html
<form action="" method="post">

    <!-- form fields -->

    <?= $CsrfToken->field() ?>

    <input type="submit" value="Submit Form">
</form>
````

# TODO
- Ability to edit default JWT options.
- Ability to support sending tokens via GET requests or request header.
