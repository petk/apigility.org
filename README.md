# Apigility.org web site

(C) Copyright 2013-2017 by [Zend](https://www.zend.com/), a [Rogue Wave Company](http://www.roguewave.com/).

## Docker

For development, we use [docker-compose](https://docs.docker.com/compose/);
make sure you have both that and Docker installed on your machine.

Build the images:

```console
$ docker-compose build
```

And then launch them:

```console
$ docker-compose up -d
```

You can then browse to `http://localhost:8080`, and any changes you make in the
project will be reflected immediately.

## Creating a new release

Use the `Makefile`:

```console
$ make all AG_VERSION=<Apigility release version>
```

This will update the installer for the new release version.

To see the changes reflected in your development version, you may also run:

```console
$ composer build
```

which will update:

- `config/autoload/global.php`
- `data/releases.json`
- `composer.lock` (if the documentation was updated)

Spot-check those, commit, and push.

## CSS and JavaScript

CSS can be found in the `asset/sass/` directory (we use SASS for defining our CSS),
and JS can be found in the `asset/js/` directory.

After changing CSS or JS you need rebuild assets, as following:

```bash
$ cd asset
$ npm install
$ gulp
```

New files will be generated in `public/js/` and `public/css/`, and old files will
be removed. The file `asset/rev-manifest.json` will contain new revision names for
our assets. The file is used by the [`asset()` view helper](https://docs.zendframework.com/zend-view/helpers/asset/).

The above commands are run automatically when you execute `composer build`.

## Documentation

Documentation is written in a separate repository,
[apigility-documentation](https://github.com/zfcampus/apigility-documentation),
and the apigility.org website consumes that documentation. To update the
documentation to display on the website, run:

```console
$ make documentation
```

and commit the `composer.lock` when done.

### Rendering Documentation

Since the documentation is written in GitHub Flavored Markdown, we use "fenced
code blocks," which provide us with the ability to specify the programming
language used in the code block in order to provide syntax highlighting.

Unfortunately, no Markdown libraries we found for PHP would perform the syntax
highlighting beyond a CSS class referring to the language.

This website solves the problem by identifying each fenced code block and the
syntax specified for it, and passing it to [pygmentize](http://pygments.org/).
This means the `pygmentize` executable needs to be available on the system
running the website.

If it is, and it is available at `/usr/bin/pygmentize`, you do not need to do
anything. If it is on any other path, add the following to your
`config/autoload/local.php` file (create it if it hasn't been):

```php
'pygmentize' => 'path/to/pygmentize'
```

## Deployment

The easiest way to deploy is to use the following:

```console
$ make deploy AG_VERSION=1.0.4 VERSION=$(date -u +"%Y.%m.%d.%H.%M") PHP=$(which php)
```

### Notes

`AG_VERSION` is the current Apigility version to display on the download page
and homepage; it should be whatever the latest version is in
zfcampus/zf-apigility-skeleton.

`VERSION` is explicitly provided to prevent re-calculation during execution of
the scripts, which can lead to version mismatch after creating the package but
before deployment.

Some environment variables you may also need to add to the string:

- `ZS_CLIENT`, if `zs-client.phar` is not on your `$PATH`
- `APP_TARGET`, to point at a target you've created with `zs-client.phar` to
  point at the appropriate Zend Server instance. (`zs-client.phar` is the
  [Zend Server SDK](https://github.com/zend-patterns/ZendServerSDK).)
