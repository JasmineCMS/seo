# seo

SEO package for Jasmine

## Installation

`composer require jasminecms/seo`  
then run  
`php artisan migrate`

In your Page or breadable add the `Seoable` trait.

```php
use Seoable;
```

In your fieldsManifest add

```php
    public static function fieldsManifest(): FieldsManifest
    {
        return new FieldsManifest([
            'col-md-4'   => \JasmineSeo::fieldsBox() + [],
         ]);
    }
``` 

In before you return your page call
`JasmineSeo::build($seoableModel);`

In your blade in the head section call
`{!! JasmineSeo::generate() !!}`
