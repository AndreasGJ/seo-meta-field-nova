Nova.booting((Vue, router, store) => {
    Vue.component("seo-media", require("./partials/SeoMedia.vue").default);

    Vue.component('index-seo-meta', require('./components/IndexField.vue').default)
    Vue.component('detail-seo-meta', require('./components/DetailField.vue').default)
    Vue.component('form-seo-meta', require('./components/FormField.vue').default)
})
