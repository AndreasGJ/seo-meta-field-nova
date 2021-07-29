# upgrading

This custom nova field, can add SEO related fields to any Model through a morph relationship within one single trait.

## From v1 to v2

publish the configs and migrations ( **this will override old config file so take backup from your old config values to
add them again** ):

```
php artisan vendor:publish --provider="Gwd\SeoMeta\FieldServiceProvider" --force
```

---
**IMPORTANT NOTE**

This package now utilize localization . so , for backward compatibility we added a migration to update the old string
columns to the new json columns with the default locale in the config file . so u **MUST** read the migration carefully
and modify it if needed before running the following command

---
And then run the migrations:

```
php artisan migrate
```

## Setup default values for a model

If the SEO values should have the same structure every time, then you are able to set the up with the following methods
in the trait:

- ```this section has been completely changed and now can be used with the following code```

```
    /**
     * REGISTERING THE DEFAULT VALUES IF EXISTS
     */
    public function registerDefaultValues(): void
    {
        
        // add default SEO title for the model 
        $this->addTitleDefault(string $value = null, string $locale = null): void
        
        // add default SEO description for the model 
        $this->addDescriptionDefault(string $value = null, string $locale = null): void
        
        // add default SEO keywords for the model 
        $this->addKeywordsDefault(string $value = null, string $locale = null): void
        
        // add default SEO image for the model 
        $this->addImageDefault(string $value = null): void
        
         // add default SEO follow for the model 
        $this->addFollowDefault(string $value): void
    }
```

## Config file

- the follwoing keys has been added or changed ( **read the config file comments carefully** )

```php
[
    'default_seo_title' => config('app.name'),
    
    'default_seo_description' => null,
    
    'default_seo_keywords' => null,
    
    'available_locales' => ['en'],
    
    'fallback_locale' => 'en'
]
```
