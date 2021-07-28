<template>
    <span class="inline-block rounded-full w-2 h-2" :class="seoStatus"></span>
</template>

<script>
export default {
    props: ["resourceName", "field"],
    computed: {
        seoStatus() {
            const field = this.field;
            const value = this.field.value;
            if (!field.default_values && value) {
                if (
                    ["index, follow", "index, nofollow"].indexOf(
                        value.follow_type
                    ) >= 0
                ) {
                    let hasTitleForAnyLocale = false;
                    for (const locale in value.title) {
                        if (value.title[locale] && value.title[locale].trim() !== ''){
                            hasTitleForAnyLocale = true;
                        }
                    }
                    if (hasTitleForAnyLocale) {
                        return "bg-success";
                    }
                    return "bg-warning";
                }
            }
            return "bg-danger";
        }
    }
};
</script>
