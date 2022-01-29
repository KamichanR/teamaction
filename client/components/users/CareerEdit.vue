<template>
  <v-list-item>
    <v-row>
      <v-col cols="12">
        <v-text-field
          v-model="company"
          label="会社名"
          :counter="255"
          :error-messages="companyErrors"
          @blur="$v.company.$touch()"
          @input="updateCompany($event)"
        />
      </v-col>

      <v-col cols="6">
        <v-text-field
          v-model="position"
          label="役職"
          :counter="255"
          :error-messages="positionErrors"
          @blur="$v.position.$touch()"
          @input="updatePosition($event)"
        />
      </v-col>

      <v-col cols="3">
        <v-text-field
          v-model="begin"
          label="開始年月日"
          required
          type="date"
          :error-messages="beginErrors"
          @blur="$v.begin.$touch()"
          @input="updateBegin($event)"
        />
      </v-col>

      <v-col cols="3">
        <v-text-field
          v-model="end"
          label="終了年月日"
          type="date"
          @input="updateEnd($event)"
        />
      </v-col>

      <v-col cols="12">
        <v-textarea
          v-model="description"
          label="業務内容"
          outlined
          :counter="500"
          :error-messages="descriptionErrors"
          @blur="$v.description.$touch()"
          @input="updateDescription($event)"
        />
      </v-col>
    </v-row>

    <v-list-item-action>
      <v-btn
        icon
        @click="$emit('deleteCareer')"
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
  name: 'CareerEdit',
  mixins: [
    validationMixin,
  ],
  props: {
    career: {
      type: Object,
      required: true,
    },
  },
  validations: {
    company: {
      required,
      maxLength: maxLength(255),
    },
    position: {
      maxLength: maxLength(255),
    },
    begin: {
      required,
    },
    description: {
      maxLength: maxLength(500),
    },
  },
  data() {
    return {
      begin: this.career.begin,
      end: this.career.end,
      company: this.career.company,
      position: this.career.position,
      description: this.career.description,
    }
  },
  computed: {
    companyErrors() {
      const errors = [];
      if (!this.$v.company.$dirty) return errors;
      !this.$v.company.required && errors.push('会社名は必須です。');
      !this.$v.company.maxLength && errors.push('会社名は255文字以内にしてください。');
      return errors;
    },
    positionErrors() {
      const errors = [];
      if (!this.$v.position.$dirty) return errors;
      !this.$v.position.maxLength && errors.push('役職名は255文字以内にしてください。');
      return errors;
    },
    beginErrors() {
      const errors = [];
      if (!this.$v.begin.$dirty) return errors;
      !this.$v.begin.required && errors.push('開始年月日は必須です。');
      return errors;
    },
    descriptionErrors() {
      const errors = [];
      if (!this.$v.description.$dirty) return errors;
      !this.$v.description.maxLength && errors.push('役職名は500文字以内にしてください。');
      return errors;
    },
  },
  methods: {
    updateCompany($event) {
      this.$v.company.$touch();
      this.$emit('updateCareerInvalid', this.$v.$invalid);
      this.$emit('updateCareerCompany', $event);
    },
    updatePosition($event) {
      this.$v.position.$touch();
      this.$emit('updateCareerInvalid', this.$v.$invalid);
      this.$emit('updateCareerPosition', $event);
    },
    updateBegin($event) {
      this.$v.begin.$touch();
      this.$emit('updateCareerInvalid', this.$v.$invalid);
      this.$emit('updateCareerBegin', $event);
    },
    updateEnd($event) {
      this.$emit('updateCareerEnd', $event);
    },
    updateDescription($event) {
      this.$v.description.$touch();
      this.$emit('updateCareerInvalid', this.$v.$invalid);
      this.$emit('updateCareerDescription', $event);
    },
  },
}
</script>
