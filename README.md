# Dummy Bundle

## Install

### Enable bundle

Put this in `app/AppKernel.php` :

```
            new Vidarl\DummyBundle\VidarlDummyBundle(),
```

### Routes :

Put this route config in app/config/routing.yml

```
_VidarlDummyRoutes:
    resource: "@VidarlDummyBundle/Controller"
    prefix: /vdummy
    type: annotation

```

You then may access for instance http://localhost/admin/vdummy/ and http://localhost/vdummy/

## Paywall example

This bundle contains an example on how a paywall implementation with http cache can be done. The idea here is that info 
regarding if user has access behind paywall is not stored in eZ Platform permission system, but instead in a 3rd party system.

PaidSubscriber is a FOS Context provider which enrich the user-context-hash with info regarding if user has payed or not
PaidSubscriber::hasPaid is ment to be reimplemented and be responsible for checking if user has access or not
PaywallController is a customized view controller which exposes via template variable if user has access or not. It also ensures
http cache doesn't incorrectly cache responses if user_context_hash cache is obsolete (see documentation in PaidSubscriber for details)
Twig\HasPaidExtension is a twig template function which exposes if user has payed or not. This function should no longer be needed though, as the PaywallController
already exposes this information.

### Installation of paywall example

Resources\config\views.yml contains a template match. If you are using ezplatform-ee-demo or ezplatform-demo, you already have an
`article` match in `app/config/views.yml` which conflicts. You must manually remove that match in `app/config/views.yml`

You'll need to patch the .vcl for varnish/fastly.

Varnish:

```
cd vendor/vidarl && patch -p0 < dummy-bundle/patches/varnish/varnish5.vcl.patch
```

Fastly:

```
cd vendor/vidarl && patch -p0 < dummy-bundle/patches/patches/fastly/ez_main.vcl.patch
```
