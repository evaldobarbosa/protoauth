# ProtoAuth

Protoauth is a simple package that enables your app to serve a login endpoint that returns a JWT token to your user without user records. It is a package to begin an application, it is not a full feature to your app.

## Scenario
If you are begining a laravel project, already decided about provide a token jwt to your user, but still not decided which provider (aws cognito, keycloak ou any other) will do it, proto auth enables your application temporarily to do.

## How to use

You will need to add a repository to your composer.json. Copy code block above and paste after licence attribute.

```
repositories: [{
	"type": "git",
	"url": "https://github.com/evaldobarbosa/protoauth"
}],
```

After this copy and paste, you will run this composer require command.

```
composer require evaldobarbosa/protoauth --dev
```

Now you will create an api route to use ProtoAuth Login controller.

```
// routes/api.php

...

Route::post('login', \ProtoAuth\Controllers\Login::class);

...
```

## When I will remove ProtoAuth

As said before, at the moment that you decide about which authentication provider your application will use, this is the moment that comment or remove the route to ProtoAuth Login controller.

## If I want to use ProtoAuth without composer require...

Yes, you can.

Run a git clone at root folder of your application and add the ProtoAuth namespace inside the autoload.psr-4 section pointing to ProtoAuth/src folder and be happy.