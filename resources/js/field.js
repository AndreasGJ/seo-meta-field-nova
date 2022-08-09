import SeoMedia from "./partials/SeoMedia";
import IndexField from "./components/IndexField";
import DetailField from "./components/DetailField";
import FormField from "./components/FormField";

Nova.booting((Vue) => {
    Vue.component("seo-media", SeoMedia);

    Vue.component('index-seo-meta', IndexField)
    Vue.component('detail-seo-meta', DetailField)
    Vue.component('form-seo-meta', FormField)
})
