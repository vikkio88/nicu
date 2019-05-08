# nicu
developing micro rest api in seconds, shared hosting friendly
This is a simple startup project for a **nicu** restful api.

# Create a project
```
composer create-project vikkio88/nicu NAME_OF_PROJECT
```

## Run locally
to run this app locally using the php server just type
```
php start.php
```
This will use the ```router.php``` to simulate a simple URLRewrite

To change the default port edit the `config/app.json`
```
{
  "app": {
    "version": 0.1,
    "port": 8088 <--
  }
}
```

Or pass it as a parameter to the `start.php` script
```
php start.php 8090
```
## Run via docker
you can edit the file `docker-compose.yml` in order to change the name of the image.
```
docker-compose up -d
```
The app will be reachable at the port you specified in the `docker-compose.yml` file.

# How To
## Add a new route
Just go to `config/routes.php` and add a new route following the syntax:
```php
[
    'route' => '/NAME_OF_THE_ROUTE',
    'method' => 'get',
    'action' => AppSampleAction::class
]
```
There are three parameter to specify per each route:
1. **Route** The syntax for the routes is the one specified in [here](https://www.slimframework.com/docs/v3/objects/router.html).
2. **Method** This is the HTTP verb and can be any HTTP request type (options,post,get).
3. **Action** this is the Action that will be invoked when the route is matched, as long as it extends `ApiAction` and implements `action()` method returning an `array` it will work fine.

## Add new / read Config from action
Config lib is provided by [hassankhan/config](https://github.com/hassankhan/config), [here](https://github.com/hassankhan/config/tree/master/tests/mocks/pass) you can find some example of config files that are supported.
### Set
To add a config, add a file or a simple array key to the one specified:
```php
'someStuff' => 'Stuff'
```
to get it
```php
// $config being an instance of Config
$config->get('someStuff', 'fallback value');
```
### Get
if **Config** will be injected in the constructor, you will have access to all the configs in the 'config/ folder'
```php
class MyAction extends ApiAction {

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    protected function action(): array
    {
        return [
            'test' => $this->config->get('stuff')
        ];
    }
}
```

## Setup the Di Container
This small framework integrated [PhpDi](http://php-di.org) instead of Pimple, as autowiring makes everything a bit easier to read.

To link the interface to the implementation, you need to specify a provider into `config/providers`.

The provider body, will work a bit like the Laravel Providers, you will need to specify, using **php-di** syntax the bind between interface and implementation.

The **php-di** injection will autowire whatever is configured in those providers, and will inject the implementation on whatever you are creating injecting it into the Action constructor.
### Example
`config/providers.php`
```
return [
    'providers' => [
        SampleProvider::class
    ]
];
```

`SampleProvider.php`
```php
class SampleProvider extends Provider
{

    public function boot()
    {
        $this->bind(Interface::class, function(ContainerInterface $c){
            return new Implementation($c->get(AnotherInterface::class));
        });
    }
}
```

## Middlewares
Slim (so PSR) Middlewares are supported by **nicu**.
To find out more read [this](https://www.slimframework.com/docs/v3/concepts/middleware.html).

To add a new one you need to specify it into the file `config/app.php` under the config key `app.middlewares`:
```php
'middlewares' => [
    Cors::class => [
        "origin" => ["*"],
        "methods" => ["GET", "POST", "PUT", "PATCH", "DELETE"],
    ],
    function(RequestInterface $request, ResponseInterface $response,closure $next){
        // do something
        $next($request,$response);
        return $response;
    }
]
```
As ↑ shows, the config can be provided in two ways, either as a Middleware class, specifying a config as value in the array (config being an `array` too), or as a simple `\Callable`.

## DotEnv
You can specify a config as environment variable in a .env file.
```
SOME_STUFF="I like trains"
```
those will be loaded on the `index.php` on the bootstrap phase.

And you can inject it into a config like this.
```php
'stuff' => getenv('SOME_STUFF')
```

For more info about this check out [vlucas/phpdotenv](https://github.com/vlucas/phpdotenv) docs.

## Build

This framework allows you to create a build version of your rest api, and it will cleanup the vendor folder in order to have less file to upload.

To run the build just type
```
composer run build
```

This will create a cleaned up/production ready version of the app in the subfolder `./dist/` ready to be uploaded to your Apache or Nginx server.

