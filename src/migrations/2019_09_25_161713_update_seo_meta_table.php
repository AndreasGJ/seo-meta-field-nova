<?php

use Gwd\SeoMeta\Models\SeoMetaItem;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateSeoMetaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('seo_meta', function (Blueprint $table) {
          $table->text('title')->change();
          $table->text('description')->change();
          $table->text('keywords')->change();
        });
        $locale = config('seo.fallback_locale');
        SeoMetaItem::query()
            ->update([
                'title' => DB::raw("concat('{\"$locale\": \"', title,'\"}')"),
                'description' => DB::raw("concat('{\"$locale\": \"', description,'\"}')"),
                'keywords' => DB::raw("concat('{\"$locale\": \"', keywords,'\"}')"),
            ]);
        Schema::table('seo_meta', function (Blueprint $table) {
            $table->json('title')->change();
            $table->json('description')->change();
            $table->json('keywords')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('seo_meta', function (Blueprint $table) {
            $table->string('title')->change();
            $table->string('description')->change();
            $table->string('keywords')->change();
        });
        $locale = config('seo.fallback_locale');
        SeoMetaItem::query()
            ->update([
                'title' => DB::raw("JSON_UNQUOTE(JSON_EXTRACT(title,\"$.$locale\"))"),
                'description' => DB::raw("JSON_UNQUOTE(JSON_EXTRACT(description,\"$.$locale\"))"),
                'keywords' => DB::raw("JSON_UNQUOTE(JSON_EXTRACT(keywords,\"$.$locale\"))"),
            ]);
    }
}
