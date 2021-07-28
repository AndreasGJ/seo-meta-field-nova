<template>
    <default-field :field="field" :errors="errors">
        <template slot="field" v-if="ready">
          <div v-for="(locale,index) in availableLocales" :key="index">
            <div class="form-group mb-3">
                <label class="mb-1 block">Title [{{locale}}]:</label>
                <input
                    :id="field.name + locale + '-title'"
                    type="text"
                    class="w-full form-control form-input form-input-bordered"
                    :class="errorClasses"
                    :placeholder="field.name[locale]"
                    v-model="value.title[locale]"
                    @input="setHasChanged"
                />
                <p
                    class="help-block"
                    v-if="field.title_format && field.title_format !== ':text'"
                >{{ field.title_format.replace(':text', value.title[locale] || '') }}</p>
            </div>
            <div class="form-group mb-3">
                <label class="mb-1 block">Description [{{locale}}]:</label>
                <textarea
                    class="w-full form-control form-input form-input-bordered py-3 h-auto"
                    :id="field.name + locale + '-description'"
                    placeholder="Enter SEO description"
                    v-model="value.description[locale]"
                    @input="setHasChanged"
                />
            </div>
            <div class="form-group mb-3">
                <label class="mb-1 block">Keywords [{{locale}}]:</label>
                <textarea
                    class="w-full form-control form-input form-input-bordered py-3 h-auto"
                    :id="field.name + locale + '-keywords'"
                    placeholder="separate between keywords by ', ' ex : keyword1, keyword2"
                    v-model="value.keywords[locale]"
                    @input="setHasChanged"
                />
            </div>
          </div>
            <div class="form-group mb-3">
                <label class="mb-1 block">Follow:</label>
                <select-control
                    :id="field.name + '-follow'"
                    v-model="value.follow_type"
                    class="w-full form-control form-select"
                    :options="followOptions"
                    @change="setHasChanged"
                >
                    <option value selected>
                        {{
                        __('Choose an option')
                        }}
                    </option>
                </select-control>
            </div>
            <div class="form-group mb-3">
                <label class="mb-1 block">Image:</label>
                <seo-media
                    :value="field.image_url"
                    :file="imageFile"
                    @change="imageFile = $event;setHasChanged($event)"
                ></seo-media>
            </div>
        </template>
    </default-field>
</template>

<script>
import { FormField, HandlesValidationErrors } from "laravel-nova";

export default {
    mixins: [FormField, HandlesValidationErrors],

    props: ["resourceName", "resourceId", "field"],

    data() {
        const field = this.field;

        return {
            hasChanged: false,
            imageFile: null,
            followOptions:
                field && field.follow_type_options
                    ? Object.keys(field.follow_type_options).map(value => ({
                          value,
                          label: field.follow_type_options[value]
                      }))
                    : [],
          availableLocales:field.available_locales,
          ready:false
        };
    },
    methods: {
        /*
         * Set the initial, internal value for the field.
         */
        setInitialValue() {
            this.value = this.field.value || this.field.default_value;
            this.ready = true;
        },

        /**
         * Fill the given FormData object with the field's internal value.
         */
        fill(formData) {
            formData.append(
                this.field.attribute,
                this.value ? JSON.stringify(this.value) : ""
            );
            if (this.imageFile) {
                formData.append(
                    this.field.attribute + "_image",
                    this.imageFile
                );
            }
        },

        /**
         * Update the field's internal value.
         */
        handleChange(value) {
            this.value = value;
        },

        /**
         * Has changed input
         */
        setHasChanged() {
            this.hasChanged = true;
        }
    }
};
</script>

<style lang="scss" scoped>
.help-block {
    font-size: 16px;
    margin: 6px 0 0;
    font-style: italic;
}
</style>
