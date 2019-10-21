# SEO meta field nova
This custom nova field, can add SEO related fields to any Model through a morph relationship within one single trait.

## How to install
To install the package run the install below:
```
composer require gwd/seo-meta-nova-field
```

And then run the migrations:
```
php artisan migrate
```

## How to use the field
Find the model you want to have the SEO fields on, example could be `App\Models\Page`, then add the `SeoMetaTrait` trait:
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

Where the `@include('seo-meta::seo', ['page' => $page])`, should have the model instance with the relation to the `SeoMetaTrait` trait.

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

## Setup default values for a model
If the SEO values should have the same structure every time, then you are able to set the up with the following methods in the trait:
```
// Return the SEO title for the model
public function getSeoTitleDefault(): string

// Return the SEO description for the model
public function getSeoDescriptionDefault(): string

// Return the SEO keywords for the model
public function getSeoKeywordsDefault(): string

// Return the SEO image for the model
public function getSeoImageDefault(): string

// Return the SEO follow type for the model
public function getSeoFollowDefault(): string
```

## How does it look in Laravel Nova
If the field is shown **in the index view** of the Resource, then you should see a column with a dot:
![alt text](/assets/images/seo-field-index.jpg)

**In detail view** you will see a text saying `You need some SEO data` if no SEO is setup yet. But if you have any then, you will get the toggle button, which will show you an example how it will look like on Google and on Facebook:
![alt text](/assets/images/seo-field-detail-hidden.jpg)
![alt text](/assets/images/seo-field-detail-show.jpg)


**In form view** you should see all the SEO input fields:
![alt text](/assets/images/seo-field-form.jpg)
