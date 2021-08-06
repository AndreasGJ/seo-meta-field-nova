# SEO meta field nova

This custom nova field, can add SEO related fields to any Model through a morph relationship within one single trait.

## if you are upgrading from v1 please read the [upgrade guide](UPGRADING.md)

## How to install

To install the package run the install below:

```
composer require gwd/seo-meta-nova-field
```

And then publish the configs and migrations :

```
php artisan vendor:publish --provider="Gwd\SeoMeta\FieldServiceProvider"
```

And then run the migrations:

```
php artisan migrate
```

## How to use the field

Find the model you want to have the SEO fields on, example could be `App\Models\Page`, then add the `SeoMetaTrait`
trait:

```
...
use Gwd\SeoMeta\Traits\SeoMetaTrait;

class Page extends Model
{
    use SeoMetaTrait;
    ...
}
```

Then use the field in the nova resource `App\Nova\Page`:

```
...
use Gwd\SeoMeta\SeoMeta;

class Page extends Resource
{
  ...
  public function fields(Request $request)
  {
    return [
      ...,
      SeoMeta::make('SEO', 'seo_meta')
        ->disk('s3-public') //disk to store seo image, default is public
    ];
  }
}
```

Then go to the top of your layout blade as default it's `resources/views/welcome.blade.php`:

```
...
<head>
    @include('seo-meta::seo')
    ...
</head>
```

Where the `@include('seo-meta::seo', ['page' => $page])`, should have the model instance with the relation to
the `SeoMetaTrait` trait.

If you dont have any selected model/resource on the current page, then get the given SEO data for the page like this:

```
use Gwd\SeoMeta\Helper\Seo;
...
Route::get('/tester', function(){
    return view('page', [
        'seo' => Seo::renderAttributes('SEO title', 'SEO description', 'SEO keywords', 'SEO image', 'index, follow'), // Builds the seo array
    ]);
});
```

Here is how the `Seo::renderAttributes` static method looks like:

## Localization

- **for localization just update the config file with the available locales and the fallback locale**

## Setup default values for a model

If the SEO values should have the same structure every time, then you are able to set the up with the following methods
in the trait:

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

## Setup Sitemap functionality

If you want the sitemap functionality then activate the sitemap by changing the `seo.sitemap_status` config to `true`.
Then add the models which has the `SeoSitemapTrait` trait to the `seo.sitemap_models` array, like this:

```
    ...
    'sitemap_status' => env('SITEMAP_STATUS', true),

    ...
    'sitemap_models' => [
        App\Models\Page::class
    ],
```

### Add Sitemap trait to models

When you want the eloquent model to be shown in the sitemap then you need to add the `SeoSitemapTrait` trait to it:

```
...
use Gwd\SeoMeta\Traits\SeoSitemapTrait;

class Page extends Model
{
    use SeoMetaTrait, SeoSitemapTrait;
    ...

    /**
     * Get the Page url by item
     *
     * @return string
     */
    public function getSitemapItemUrl()
    {
        return url($this->slug);
    }

    /**
     * Query all the Page items which should be
     * part of the sitemap (crawlable for google).
     *
     * @return Builder
     */
    public static function getSitemapItems()
    {
        return static::all();
    }
}
```

Know you should be able to go to the `seo.sitemap_path` which is `/sitemap` as default. Then you should get an xml in
the correct sitemap structure for [Google Search Console](https://search.google.com/search-console/about).

## How does it look in Laravel Nova

If the field is shown **in the index view** of the Resource, then you should see a column with a dot:
![alt text](/assets/images/seo-field-index.jpg)

**In detail view** you will see a text saying `You need some SEO data` if no SEO is setup yet. But if you have any then,
you will get the toggle button, which will show you an example how it will look like on Google and on Facebook:
![alt text](/assets/images/seo-field-detail-hidden.jpg)
![alt text](/assets/images/seo-field-detail-show.jpg)

**In form view** you should see all the SEO input fields:
![alt text](/assets/images/seo-field-form.jpg)
