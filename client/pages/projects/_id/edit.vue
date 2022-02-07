<template>
  <main>
    <v-card class="my-8 pa-8">
      <v-list>
        <v-subheader class="black--text">
          <h1>プロジェクト編集</h1>
        </v-subheader>

        <v-subheader class="black--text">
          <h2>プロジェクトの基本情報</h2>
        </v-subheader>

        <v-list-item>
          <v-text-field
            v-model="project.projectTitle"
            label="プロジェクト名"
            required
            :counter="255"
            :error-messages="projectTitleErrors"
            @input="$v.project.projectTitle.$touch()"
            @blur="$v.project.projectTitle.$touch()"
          />
        </v-list-item>

        <v-list-item>
          <v-file-input
            v-model="projectImage"
            accept="image/jpeg, image/png"
            label="プロジェクト画像"
          />
        </v-list-item>
      </v-list>

      <v-list-item>
        <div class="d-flex justify-center pa-0 my-4" style="width: 100%">
          <v-img
            :aspect-ratio="16/9"
            :src="projectImageUri"
            contain
            cover
            height="200"
          />
        </div>
      </v-list-item>

      <v-list-item>
        <v-select
          v-model="project.area"
          :error-messages="areaErrors"
          :items="data.areas"
          dense
          item-text="name"
          label="活動場所"
          outlined
          required
          return-object
          @blur="$v.project.area.$touch()"
          @change="$v.project.area.$touch()"
        />
      </v-list-item>

      <v-list-item>
        <v-select
          v-model="project.projectCategory"
          :error-messages="projectCategoryErrors"
          :items="data.projectCategories"
          dense
          item-text="name"
          label="カテゴリ"
          outlined
          required
          return-object
          @blur="$v.project.projectCategory.$touch()"
          @change="$v.project.projectCategory.$touch()"
        />
      </v-list-item>

      <v-list-item>
        <v-select
          v-model="project.skills"
          chips
          item-text="name"
          label="必要なスキル"
          multiple
          outlined
          return-object
          :items="data.skills"
        />
      </v-list-item>

      <v-list-item>
        <v-textarea
          v-model="project.purpose"
          label="プロジェクトの目的"
          outlined
          required
          :counter="500"
          :error-messages="purposeErrors"
          @input="$v.project.purpose.$touch()"
          @blur="$v.project.purpose.$touch()"
        />
      </v-list-item>

      <v-list-item>
        <v-textarea
          v-model="project.description"
          label="プロジェクトの説明"
          outlined
          required
          :counter="500"
          :error-messages="descriptionErrors"
          @input="$v.project.description.$touch()"
          @blur="$v.project.description.$touch()"
        />
      </v-list-item>

      <v-list-item>
        <v-textarea
          v-model="project.idealCandidate"
          label="求める人材"
          outlined
          :counter="500"
          :error-messages="idealCandidateErrors"
          @input="$v.project.idealCandidate.$touch()"
          @blur="$v.project.idealCandidate.$touch()"
        />
      </v-list-item>

      <v-list-item>
        <v-textarea
          v-model="project.merit"
          label="参加するメリット"
          outlined
          required
          :counter="500"
          :error-messages="meritErrors"
          @input="$v.project.merit.$touch()"
          @blur="$v.project.merit.$touch()"
        />
      </v-list-item>

      <v-divider/>

      <v-subheader class="black--text">
        <h2>募集する役割・役職</h2>
      </v-subheader>

      <ProjectRole
        v-for="(role, index) in project.projectRoles"
        :key="`role${index}`"
        :role="role"
        @deleteRole="deleteProjectRole(index)"
        @updateRoleInvalid="updateProjectRoleInvalid($event, index)"
        @updateRoleTitle="role.role_title = $event"
        @updateClose="role.close = $event"
        @updateNumberOfApplicants="role.number_of_applicants = $event"
        @updateDescription="role.description = $event"
      />

      <v-list-item>
        <v-btn icon @click="addProjectRole">
          <v-icon>
            mdi-plus
          </v-icon>
        </v-btn>
      </v-list-item>

      <v-divider/>

      <div class="d-flex justify-center mt-4">
        <v-btn
          class="font-weight-bold"
          color="primary"
          :disabled="$v.$invalid || childComponentsInvalids"
          @click="createProject"
        >
          更新
        </v-btn>
      </div>
    </v-card>

    <v-card>
      {{ project }}
    </v-card>

    <v-card>
      {{ projectRolesInvalids }}
    </v-card>

    <v-card>
      {{ childComponentsInvalids }}
    </v-card>
  </main>
</template>

<script>
import { validationMixin } from 'vuelidate';
import { required, maxLength } from 'vuelidate/lib/validators';
import { getDownloadURL, getStorage, ref, uploadBytes } from 'firebase/storage';
import ProjectRole from '@/components/projects/edit/ProjectRole';

export default {
  name: 'ProjectEditPage',
  components: {
    ProjectRole,
  },
  mixins: [
    validationMixin,
  ],
  middleware: [
    'loggedIn',
    'isAuthenticated',
  ],
  validations: {
    project: {
      projectTitle: {
        required,
        maxLength: maxLength(255),
      },
      area: {
        required,
      },
      projectCategory: {
        required,
      },
      purpose: {
        required,
        maxLength: maxLength(500),
      },
      description: {
        required,
        maxLength: maxLength(500),
      },
      idealCandidate: {
        maxLength: maxLength(500),
      },
      merit: {
        maxLength: maxLength(500),
      },
    },
  },
  async asyncData({ params, $axios, error }) {
    // プロジェクト情報取得
    let project = await $axios.$get(`api/data/project/${params.id}`);
    if (!project) error({ statusCode: 404 });
    project = project.project;

    const data = await $axios.$get('api/data/project');

    // 役割のバリデーション
    const projectRolesInvalids = new Array(project.projectRoles.length).fill(false);

    // すべての子コンポーネントのバリデーション結果
    const childComponentsInvalids = false;

    // 画像の取得
    const projectImage = await fetch(project.projectImageUri)
      .then(res => res.blob())
      .then(blob => new File([blob], project.projectImageUri));

    return {
      data,
      project,
      projectImage,
      projectRolesInvalids,
      childComponentsInvalids,
    };
  },
  computed: {
    projectTitleErrors() {
      const errors = [];
      if (!this.$v.project.projectTitle.$dirty) return errors;
      !this.$v.project.projectTitle.required && errors.push('プロジェクト名は必須です。');
      !this.$v.project.projectTitle.maxLength && errors.push('プロジェクト名は255文字以内にしてください。');
      return errors;
    },
    areaErrors() {
      const errors = [];
      if (!this.$v.project.area.$dirty) return errors;
      !this.$v.project.area.required && errors.push('活動場所は必須です。');
      return errors;
    },
    projectCategoryErrors() {
      const errors = [];
      if (!this.$v.project.projectCategory.$dirty) return errors;
      !this.$v.project.projectCategory.required && errors.push('カテゴリは必須です。');
      return errors;
    },
    purposeErrors() {
      const errors = [];
      if (!this.$v.project.purpose.$dirty) return errors;
      !this.$v.project.purpose.required && errors.push('プロジェクトの目的は必須です。');
      !this.$v.project.purpose.maxLength && errors.push('プロジェクトの目的は500文字以内にしてください。');
      return errors;
    },
    descriptionErrors() {
      const errors = [];
      if (!this.$v.project.description.$dirty) return errors;
      !this.$v.project.description.required && errors.push('プロジェクトの説明は必須です。');
      !this.$v.project.description.maxLength && errors.push('プロジェクトの説明は500文字以内にしてください。');
      return errors;
    },
    idealCandidateErrors() {
      const errors = [];
      if (!this.$v.project.idealCandidate.$dirty) return errors;
      !this.$v.project.idealCandidate.maxLength && errors.push('求める人材は500文字以内にしてください。');
      return errors;
    },
    meritErrors() {
      const errors = [];
      if (!this.$v.project.merit.$dirty) return errors;
      !this.$v.project.merit.maxLength && errors.push('参加するメリットは500文字以内にしてください。');
      return errors;
    },
    // 入力された画像のURLを取得
    projectImageUri() {
      // 入力されていない場合はデフォルト画像にする
      if (!this.projectImage) return 'https://firebasestorage.googleapis.com/v0/b/connect-335200.appspot.com/o/default_project_image.png?alt=media&token=82e7673e-5906-4d12-b6dd-d272569941ad';
      const uri = URL.createObjectURL(this.projectImage);
      return uri;
    },
  },
  methods: {
    updateProjectRoleInvalid($event, index) {
      this.projectRolesInvalids[index] = $event;
      this.childComponentsInvalids = this.projectRolesInvalids.includes(true);
    },
    deleteProjectRole(index) {
      if (this.project.projectRoles.length === 1) return;
      this.project.projectRoles.splice(index, 1);
      this.projectRolesInvalids.splice(index, 1);
      this.childComponentsInvalids = this.projectRolesInvalids.includes(true);
    },
    addProjectRole() {
      const projectRole = {
        role_title: '',
        number_of_applicants: '',
        close: '',
        description: '',
      };

      this.project.projectRoles.push(projectRole);
      this.projectRolesInvalids.push(true);
      this.childComponentsInvalids = true;
    },
    async createProject() {
      // 画像アップロードとURI取得
      if (this.projectImage) {
        const date = this.getDate();
        const projectImageName = date + this.projectImage.name;
        const storage = getStorage();
        const storageRef = ref(storage, projectImageName);
        await uploadBytes(storageRef, this.projectImage);
        this.project.projectImageUri = await getDownloadURL(storageRef);
      } else {
        this.project.projectImageUri = 'https://firebasestorage.googleapis.com/v0/b/connect-335200.appspot.com/o/default_project_image.png?alt=media&token=82e7673e-5906-4d12-b6dd-d272569941ad';
      }

      // プロジェクトの作成とIDの取得
      const projectId = await this.$axios.$post('/api/create/project', this.project);

      this.$router.push(`/projects/${projectId}`);
    },
    getDate() {
      const date = new Date();
      const y = date.getFullYear();
      const m = ("00" + (date.getMonth()+1)).slice(-2);
      const d = ("00" + date.getDate()).slice(-2);

      return y + m + d;
    },
  },
}
</script>
