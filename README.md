# Site-Endpoints

Fork of https://github.com/b13/slimphp-bridge

Endpoints allows you to easily create/map an URI pathname to a custom controller of yours.
You decide if you need the ExtbaseBridge (which initialize Extbase as TSFE) as custom Middlewares as well.

Endpoints is a way to register easily, rapid, and without complex configurations URI pathnames to controllers.

- Configure the routes inside your site-configuration (`/config/sites/<identifier>/config.yaml`) and map it/them to a specific custom controller, method and flush caches
- Perform a request to the configured route(s)

**If you would like to register custom entrypoints for third-party-extensions using the PHP-/YAML-API, you may [head over to the README provided from the `site/site-endpointsexample` package](https://github.com/iammati/site-endpointsexample)!**

## Requirements
- PHP +8.0
- TYPO3 +11.3

Note that it has not been tested _yet_ on any lower TYPO3/PHP version.

## Features

- Dynamically map an URI pathname towards a TYPO3 controller and action which gets resolved automatically
- Rapidly API integration
- Third-party-extension can register custom routes so both options exists to register them; site-configuration and the `EndpointsProvider`

### Planned features

- Ship custom JavaScript file/function which makes it easier to perform XHRs (XMLHttpRequests)

## Configuration

1. Edit the `/config/sites/<identifier>/config.yaml` file and add a routes-configuration [as here](https://github.com/iammati/site-endpoints/blob/master/config.yaml.dist)
2. Edit the controllers/actions to your own setup (or leave it as it - for test purpose)
3. Flush all caches
4. Open the `/api/v1/article` or `/api/v1/test` to ensure the configured URI pathnames are working.

## Thanks 💛

To the [b13/slimphp-bridge package](https://github.com/b13/slimphp-bridge) which has been used as a base/inspiration to develop this extension!
