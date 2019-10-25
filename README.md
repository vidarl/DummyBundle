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

You may the access for instance http://localhost/admin/vdummy/ and You may the access for instance http://localhost/vdummy/