Nova.booting((Vue, router, store) => {
    Vue.component("seo-media", require("./partials/SeoMedia"));

    Vue.component("index-seo-meta", require("./components/IndexField"));
    Vue.component("detail-seo-meta", require("./components/DetailField"));
    Vue.component("form-seo-meta", require("./components/FormField"));
});
