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

## How does it look in Laravel Nova
If the field is shown **in the index view** of the Resource, then you should see a column with a dot:
![alt text](/assets/images/seo-field-index.jpg)

**In detail view** you will see a text saying `You need some SEO data` if no SEO is setup yet. But if you have any then, you will get the toggle button, which will show you an example how it will look like on Google and on Facebook:
![alt text](/assets/images/seo-field-detail-hidden.jpg)
![alt text](/assets/images/seo-field-detail-show.jpg)


**In form view** you should see all the SEO input fields:
![alt text](/assets/images/seo-field-form.jpg)
