<template>
  <v-list-item>
    <v-row>
      <v-col cols="8">
        <v-text-field
          v-model="roleTitle"
          label="役割"
          required
          :counter="255"
          :error-messages="roleTitleErrors"
          @blur="$v.roleTitle.$touch()"
          @input="updateRoleTitle($event)"
        />
      </v-col>

      <v-col cols="2">
        <v-text-field
          v-model="close"
          label="締切日"
          type="date"
          required
          :error-messages="closeErrors"
          @blur="$v.close.$touch()"
          @input="updateClose($event)"
        />
      </v-col>

      <v-col cols="2">
        <v-text-field
          v-model="numberOfApplicants"
          label="募集人数"
          type="number"
          min="1"
          required
          :error-messages="numberOfApplicantsErrors"
          @blur="$v.numberOfApplicants.$touch()"
          @input="updateNumberOfApplicants($event)"
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
        @click="$emit('deleteRole')"
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
import { required, minValue, maxLength } from 'vuelidate/lib/validators';

export default {
  name: 'ProjectRolePage',
  mixins: [
    validationMixin,
  ],
  props: {
    role: {
      type: Object,
      required: true,
    }
  },
  validations: {
    roleTitle: {
      required,
      maxLength: maxLength(255),
    },
    close: {
      required,
    },
    numberOfApplicants: {
      required,
      minValue: minValue(1),
    },
    description: {
      required,
      maxLength: maxLength(500),
    },
  },
  data() {
    return {
      roleTitle: this.role.roleTitle,
      numberOfApplicants: this.role.numberOfApplicants,
      close: this.role.close,
      description: this.role.description,
    }
  },
  computed: {
    roleTitleErrors() {
      const errors = [];
      if (!this.$v.roleTitle.$dirty) return errors;
      !this.$v.roleTitle.required && errors.push('役割は必須です。');
      !this.$v.roleTitle.maxLength && errors.push('役割は255文字以内にしてください。');
      return errors;
    },
    closeErrors() {
      const errors = [];
      if (!this.$v.close.$dirty) return errors;
      !this.$v.close.required && errors.push('募集締切は必須です。');
      return errors;
    },
    numberOfApplicantsErrors() {
      const errors = [];
      if (!this.$v.numberOfApplicants.$dirty) return errors;
      !this.$v.numberOfApplicants.required && errors.push('募集人数は必須です。');
      !this.$v.numberOfApplicants.minValue && errors.push('募集人数は1人以上にしてください。');
      return errors;
    },
    descriptionErrors() {
      const errors = [];
      if (!this.$v.description.$dirty) return errors;
      !this.$v.description.required && errors.push('業務内容は必須です。');
      !this.$v.description.maxLength && errors.push('業務内容は500文字以内にしてください。');
      return errors;
    },
  },
  methods: {
    updateRoleTitle($event) {
      this.$v.roleTitle.$touch();
      this.$emit('updateRoleInvalid', this.$v.$invalid);
      this.$emit('updateRoleTitle', $event);
    },
    updateClose($event) {
      this.$v.close.$touch();
      this.$emit('updateRoleInvalid', this.$v.$invalid);
      this.$emit('updateClose', $event);
    },
    updateNumberOfApplicants($event) {
      this.$v.numberOfApplicants.$touch();
      this.$emit('updateRoleInvalid', this.$v.$invalid);
      this.$emit('updateNumberOfApplicants', $event);
    },
    updateDescription($event) {
      this.$v.description.$touch();
      this.$emit('updateRoleInvalid', this.$v.$invalid);
      this.$emit('updateDescription', $event);
    },
  }
}
</script>
