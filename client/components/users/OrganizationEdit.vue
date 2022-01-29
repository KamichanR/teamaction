<template>
  <v-list-item>
    <v-text-field
      v-model="name"
      label="組織名"
      :counter="255"
      :error-messages="organizationNameErrors"
      @blur="$v.name.$touch()"
      @input="updateOrganization($event)"
    />

    <v-list-item-action>
      <v-btn
        icon
        @click="$emit('deleteOrganization')"
      >
        <v-icon>
          mdi-delete
        </v-icon>
      </v-btn>
    </v-list-item-action>
  </v-list-item>
</template>

<script>
import { validationMixin } from 'vuelidate';
import { required, maxLength } from 'vuelidate/lib/validators';

export default {
  name: 'OrganizationEdit',
  mixins: [
    validationMixin,
  ],
  props: {
    organization: {
      type: Object,
      required: true,
    },
  },
  validations: {
    name: {
      required,
      maxLength: maxLength(255),
    },
  },
  data() {
    return {
      name: this.organization.name,
    }
  },
  computed: {
    organizationNameErrors() {
      const errors = [];
      if (!this.$v.name.$dirty) return errors;
      !this.$v.name.required && errors.push('組織名は必須です。');
      !this.$v.name.maxLength && errors.push('組織名は255文字以内にしてください。');
      return errors;
    },
  },
  methods: {
    updateOrganization($event) {
      this.$v.name.$touch();
      this.$emit('updateOrganizationInvalid', this.$v.$invalid);
      this.$emit('updateOrganizationName', $event);
    },
  },
}
</script>
