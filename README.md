# Fake Response

As a developer I was pretty tired of not being able to fake a response for an API I was using during functional testing. I wanted to create a simplistic but versatile app to solve this problem.

This app makes use of the [Slim Framework](https://www.slimframework.com/) to put things together. Please check the framework documentation where II have hinted in the documentation below.

This app also comes with a basic Docker compose file to enable easy further development, testing and deployment. I'm open to suggestions and corrections, feel free to create issues or a PR in Github.

## Todo

* Add the ability to expect content in a POST/PUT
* Tidy up the main loop
* Continue to improve documentation

## Configuring

To configure your fake api you need to supply a valid YAML file, there is a sample one in the `config` directory. 

### Global Request configuration

You can define a global set of headers that your fake API is expecting. This could be something like an API token or login credientials

```yaml
request:
  headers:
    Expected-Header: "HeaderValue"
```

### Configuring Routes

You should be able to configure any route that is understandable by the [Slim Routing](https://www.slimframework.com/docs/v4/objects/routing.html) system. You may also define any number of HTTP responses and any JSON content that goes along with it. As a client you can force this response by adding the `X-Requested-Response` header to your request with the relevant response you want.

#### A sample config

```yaml
routes:
  - path: /path/to/endpoint
    methods:
      get/patch/put/post/delete:
        200:
          body:
            key: value
            second-key: value
        400:
          body:
            error: 400
            message: Client error
```
