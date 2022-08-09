<template>
    <DefaultField :field="field" :errors="errors">
        <template #field>
            <div class="form-group mb-3">
                <label class="mb-1 block">{{ __('Title') }}:</label>
                <input
                    :id="field.name + '-title'"
                    type="text"
                    class="w-full form-control form-input form-input-bordered"
                    :class="errorClasses"
                    :placeholder="field.name"
                    v-model="value.title"
                    @change="setHasChanged"
                    @input="setHasChanged"
                />
                <p
                    class="help-block"
                    v-if="field.title_format && field.title_format !== ':text'"
                >{{ field.title_format.replace(':text', value.title || '') }}</p>
            </div>
            <div class="form-group mb-3">
                <label class="mb-1 block">{{ __('Description') }}:</label>
                <textarea
                    class="w-full form-control form-input form-input-bordered py-3 h-auto"
                    :id="field.name + '-description'"
                    placeholder="Enter SEO description"
                    v-model="value.description"
                    @input="setHasChanged"
                />
            </div>
            <div class="form-group mb-3">
                <label class="mb-1 block">{{ __('Keywords') }}:</label>
                <textarea
                    class="w-full form-control form-input form-input-bordered py-3 h-auto"
                    :id="field.name + '-keywords'"
                    placeholder="Enter SEO keywords"
                    v-model="value.keywords"
                    @input="setHasChanged"
                />
            </div>
            <div class="form-group mb-3" ref="canonicalLinksWrapper">
                <label class="mb-1 block">{{ __('Canonical link(s)') }}:</label>
                <div
                    v-for="(canonicalLink, index) in canonicalLinks"
                    :key="index"
                    class="flex"
                >
                    <input
                        type="text"
                        class="w-full form-control form-input form-input-bordered mb-2 flex-grow"
                        v-model="canonicalLink.value"
                        @change="setHasChanged"
                    />
                    <DefaultButton @click.prevent="removeCanonicalLink(index)">-</DefaultButton>
                </div>

                <DefaultButton @click.prevent="addCanonicalLink()">
                    {{ __('+ Add canonical link') }}
                </DefaultButton>
            </div>
            <div class="form-group mb-3">
                <label class="mb-1 block">{{ __('Follow') }}:</label>
                <SelectControl
                    :id="field.name + '-follow'"
                    :selected="value.follow_type"
                    class="w-full"
                    :options="followOptions"
                    @change="value.follow_type = $event;setHasChanged();"
                >
                    <option value selected>
                        {{
                            __('Choose an option')
                        }}
                    </option>
                </SelectControl>
            </div>
            <div class="form-group mb-3">
                <label class="mb-1 block">{{ __('Image') }}:</label>
                <seo-media
                    :value="field.image_url"
                    :file="imageFile"
                    @imageSelect="uploadImage"
                ></seo-media>
            </div>
        </template>
    </DefaultField>
</template>

<script>
import {FormField, HandlesValidationErrors} from "laravel-nova";

export default {
    mixins: [FormField, HandlesValidationErrors],

    props: ["resourceName", "resourceId", "field"],

    data() {
        const field = this.field;

        return {
            hasChanged: false,
            imageFile: null,
            canonicalLinks: field.canonical_links ? field.canonical_links.map(link => {
                return {
                    value: link
                }
            }) : [],
            value: this.field.value || {},
            followOptions:
                field && field.follow_type_options
                    ? Object.keys(field.follow_type_options).map(value => ({
                        value,
                        label: field.follow_type_options[value]
                    }))
                    : []
        };
    },
    methods: {
        addCanonicalLink() {
            this.canonicalLinks.push({
                value: 'https://'
            });

            this.$nextTick(() => {
                const inputs = this.$refs.canonicalLinksWrapper.querySelectorAll('input');
                if (inputs && inputs.length) {
                    inputs[inputs.length - 1].focus();
                }
            });

            this.setHasChanged();
        },

        removeCanonicalLink(index) {
            this.canonicalLinks.splice(index, 1);
            this.setHasChanged();
        },

        /*
         * Set the initial, internal value for the field.
         */
        setInitialValue() {
            this.value = this.field.value || {};
        },

        /**
         * Fill the given FormData object with the field's internal value.
         */
        fill(formData) {

            if (!this.value) {
                this.value = {};
            }

            if(!this.value.params) {
                this.value.params = {};
            }

            this.value.params.canonical_links = this.canonicalLinks.map(link => link.value);

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
        },
        uploadImage(file) {
            this.imageFile = file;
            this.setHasChanged();
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
