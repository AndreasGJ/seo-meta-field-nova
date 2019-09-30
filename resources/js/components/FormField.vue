<template>
  <default-field :field="field" :errors="errors">
    <template slot="field">
      <div class="form-group mb-3">
        <label class="mb-1 block">Title:</label>
        <input
          :id="field.name + '-title'"
          type="text"
          class="w-full form-control form-input form-input-bordered"
          :class="errorClasses"
          :placeholder="field.name"
          v-model="value.title"
        />
        <p
          class="help-block"
          v-if="field.title_format && field.title_format !== ':text'"
        >{{ field.title_format.replace(':text', value.title || '') }}</p>
      </div>
      <div class="form-group mb-3">
        <label class="mb-1 block">Description:</label>
        <textarea
          class="w-full form-control form-input form-input-bordered py-3 h-auto"
          :id="field.name + '-description'"
          placeholder="Enter SEO description"
          v-model="value.description"
        />
      </div>
      <div class="form-group mb-3">
        <label class="mb-1 block">Keywords:</label>
        <textarea
          class="w-full form-control form-input form-input-bordered py-3 h-auto"
          :id="field.name + '-keywords'"
          placeholder="Enter SEO keywords"
          v-model="value.keywords"
        />
      </div>
      <div class="form-group mb-3">
        <label class="mb-1 block">Follow:</label>
        <select-control
          :id="field.name + '-follow'"
          v-model="value.follow_type"
          class="w-full form-control form-select"
          :options="followOptions"
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
        <seo-media :value="field.image_url" :file="imageFile" @change="imageFile = $event"></seo-media>
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
    return {
      imageFile: null,
      followOptions: [
        { value: "index, follow", label: "Index and follow" },
        { value: "noindex, follow", label: "No index and follow" },
        {
          value: "index, nofollow",
          label: "Index and no follow"
        },
        {
          value: "noindex, nofollow",
          label: "No index and no follow"
        }
      ]
    };
  },
  methods: {
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
      formData.append(
        this.field.attribute,
        this.value ? JSON.stringify(this.value) : ""
      );
      if (this.imageFile) {
        formData.append(this.field.attribute + "_image", this.imageFile);
      }
    },

    /**
     * Update the field's internal value.
     */
    handleChange(value) {
      this.value = value;
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
