Installation
============

Open a command console, enter your project directory and execute:

```console
$ composer require --no-scripts userbase/client-bundle
```

Create the bundle configuration:

```yaml
# config/packages/userbase-client-bundle.yaml
user_base_client:
  http_client:
    url: '%env(USERBASE_DSN)%'
```

```shell
# .env.local
USERBASE_DSN=https://key:secret@userbase.example.com/api/v1
```

Complete the installation:

```console
$ composer auto-scripts post-install-cmd
```

## Caching

It is possible to cache the user data obtained from Userbase in the course of
Authentication.  This can help to reduce the load Userbase must bear and make
your application speedier.

First you will configure a [PSR-6][] cache pool and adapter and then you will
configure the Bundle to make use of the cache.

### Configure a Cache

You should familiarise yourself with the [Symfony Cache][] and [Cache
Component][] documentation.

By way of example, here is the config needed to have FrameworkBundle create a
cache using the [FilesystemAdapter][].

Configure the adapter in your Service Container config:

```
# config/services.yaml

parameters:
  ...
  app.userbase_cache.path: 'path/to/a/directory'
  app.userbase_cache.default_lifetime_secs: 600

services:
  ...
  userbase_cache.adapter:
    class: Symfony\Component\Cache\Adapter\FilesystemAdapter
    arguments:
      - 'userbase'
      - '%app.userbase_cache.default_lifetime_secs%'
      - '%app.userbase_cache.path%'
```

This will make available a cache adapter as a service with the id
`userbase_cache.adapter`.  A cache pool can now be configured:

```
# config/packages/prod/cache.yaml

framework:
  cache:
    pools:
      userbase_cache:
        adapter: userbase_cache.adapter
```

This will make available a cache pool with the id `userbase_cache`.  The Bundle
can now be configured:

```
# config/packages/prod/userbase.yaml

user_base_client:
  ...
  cache:
    id: userbase_cache
    lifetime: '%app.userbase_cache.default_lifetime_secs%' # or, e.g. 3600
```

Finally, clear the application cache to activate the configuration:

```
bin/console cache:clear
```

That's it.

Please note that the Filesystem cache adapter is not ideal because it is slow
and has no built-in means of evicting items from the cache (eviction can be
achieved using the `bin/console cache:pool:prune` console command).  A better
choice of adapter is [Memcached][] or [Redis][].

[PSR-6]: <https://www.php-fig.org/psr/psr-6/> "PSR-6: Caching Interface"
[Symfony Cache]: <https://symfony.com/doc/current/cache.html>
[Cache Component]: <https://symfony.com/doc/current/components/cache.html> "The Cache Component"
[FilesystemAdapter]: <https://symfony.com/doc/current/components/cache/adapters/filesystem_adapter.html> "Filesystem Cache Adapter"
[Memcached]: <https://symfony.com/doc/current/components/cache/adapters/memcached_adapter.html> "Memcached Cache Adapter"
[Redis]: <https://symfony.com/doc/current/components/cache/adapters/redis_adapter.html> "Redis Cache Adapter"
