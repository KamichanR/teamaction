<template>
  <v-list-item>
    <v-row>
      <v-col cols="9">
        <v-text-field
          v-model="name"
          label="資格名"
          required
          :counter="255"
          :error-messages="nameErrors"
          @blur="$v.name.$touch()"
          @input="updateName($event)"
        />
      </v-col>

      <v-col cols="3">
        <v-text-field
          v-model="acquisition"
          label="取得日"
          type="date"
          required
          :error-messages="acquisitionErrors"
          @blur="$v.acquisition.$touch()"
          @input="updateAcquisition($event)"
        />
      </v-col>
    </v-row>

    <v-list-item-action>
      <v-btn
        icon
        @click="$emit('deleteCertification')"
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
  name: 'CertificationEdit',
  mixins: [
    validationMixin
  ],
  props: {
    certification: {
      type: Object,
      required: true,
    }
  },
  validations: {
    name: {
      required,
      maxLength: maxLength(255),
    },
    acquisition: {
      required,
    }
  },
  data() {
    return {
      name: this.certification.name,
      acquisition: this.certification.acquisition,
    }
  },
  computed: {
    nameErrors() {
      const errors = [];
      if (!this.$v.name.$dirty) return errors;
      !this.$v.name.required && errors.push('資格名は必須です。');
      !this.$v.name.maxLength && errors.push('資格名は255文字以内にしてください。');
      return errors;
    },
    acquisitionErrors() {
      const errors = [];
      if (!this.$v.acquisition.$dirty) return errors;
      !this.$v.acquisition.required && errors.push('取得日は必須です。');
      return errors;
    }
  },
  methods: {
    updateName($event) {
      this.$v.name.$touch();
      this.$emit('updateCertificationInvalid', this.$v.$invalid);
      this.$emit('updateCertificationName', $event);
    },
    updateAcquisition($event) {
      this.$v.acquisition.$touch();
      this.$emit('updateCertificationInvalid', this.$v.$invalid);
      this.$emit('updateCertificationAcquisition', $event);
    },
  },
}
</script>
