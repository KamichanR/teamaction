<template>
  <main>
    <v-card class="my-8 pa-8">
      <v-row>
        <v-col cols="12">
          <v-list>
            <v-subheader class="black--text text-subtitle-1">
              基本情報
            </v-subheader>

            <v-list-item>
              <v-text-field
                v-model="user.username"
                label="ユーザー名"
                required
                :counter="32"
                :error-messages="usernameErrors"
                @input="$v.user.username.$touch()"
                @blur="$v.user.username.$touch()"
              />
            </v-list-item>

            <v-list-item>
              <v-file-input
                v-model="usericonImage"
                accept="image/jpeg, image/png"
                label="アイコン画像"
              />
            </v-list-item>

            <v-list-item>
              <div class="d-flex justify-center pa-0 my-4" style="width: 100%">
                <v-avatar size="200">
                  <v-img
                    :src="usericonImageUri"
                    cover
                  />
                </v-avatar>
              </div>
            </v-list-item>

            <v-list-item>
              <v-select
                v-model="user.area"
                dense
                item-text="name"
                label="活動場所"
                outlined
                required
                return-object
                :items="data.areas"
              />
            </v-list-item>

            <v-list-item>
              <v-select
                v-model="user.workstyles"
                chips
                item-text="name"
                label="ワークスタイル"
                multiple
                outlined
                return-object
                :items="data.workstyles"
              />
            </v-list-item>

            <v-list-item>
              <v-select
                v-model="user.skills"
                chips
                item-text="name"
                label="スキル"
                multiple
                outlined
                return-object
                :items="data.skills"
              />
            </v-list-item>

            <v-divider/>

            <v-subheader class="black--text text-subtitle-1">
              外部アカウント
            </v-subheader>

            <v-list-item>
              <v-list-item-icon>
                <v-icon color="#1DA1F2" large>
                  mdi-twitter
                </v-icon>
              </v-list-item-icon>
              <v-text-field
                v-model="user.twitterUri"
                label="Twitterリンク"
                :counter="255"
                :error-messages="twitterUriErrors"
                @input="$v.user.twitterUri.$touch()"
                @blur="$v.user.twitterUri.$touch()"
              ></v-text-field>
            </v-list-item>

            <v-list-item>
              <v-list-item-icon>
                <v-icon color="#4267B2" large>
                  mdi-facebook
                </v-icon>
              </v-list-item-icon>
              <v-text-field
                v-model="user.facebookUri"
                label="Facebookリンク"
                :counter="255"
                :error-messages="facebookUriErrors"
                @input="$v.user.facebookUri.$touch()"
                @blur="$v.user.facebookUri.$touch()"
              ></v-text-field>
            </v-list-item>

            <v-list-item>
              <v-list-item-icon>
                <v-icon large>
                  mdi-web
                </v-icon>
              </v-list-item-icon>
              <v-text-field
                v-model="user.websiteUri"
                label="Webサイトリンク"
                :counter="255"
                :error-messages="websiteUriErrors"
                @input="$v.user.websiteUri.$touch()"
                @blur="$v.user.websiteUri.$touch()"
              ></v-text-field>
            </v-list-item>

            <v-divider/>

            <v-subheader class="black--text text-subtitle-1">
              所属団体
            </v-subheader>

            <OrganizationEdit
              v-for="(organization, index) in user.organizations"
              :key="`organization-${index}`"
              :organization="organization"
              @deleteOrganization="deleteOrganization(index)"
              @updateOrganizationInvalid="updateOrganizationInvalid($event, index)"
              @updateOrganizationName="organization.name = $event"
            />

            <v-list-item>
              <v-btn icon @click="addOrganization">
                <v-icon>
                  mdi-plus
                </v-icon>
              </v-btn>
            </v-list-item>

            <v-divider/>

            <v-subheader class="black--text text-subtitle-1">
              自己紹介・今後やりたいこと
            </v-subheader>

            <v-list-item>
              <v-textarea
                v-model="user.introduction"
                label="自己紹介"
                outlined
                :counter="500"
                :error-messages="introductionErrors"
              />
            </v-list-item>

            <v-list-item>
              <v-textarea
                v-model="user.wannaDo"
                label="今後やりたいこと"
                outlined
                :counter="500"
                :error-messages="wannaDoErrors"
              />
            </v-list-item>

            <v-divider/>

            <v-subheader class="black--text text-subtitle-1">
              経歴
            </v-subheader>

            <CareerEdit
              v-for="(career, index) in user.careers"
              :key="`career${index}`"
              :career="career"
              @deleteCareer="deleteCareer(index)"
              @updateCareerInvalid="updateCareerInvalid($event, index)"
              @updateCareerBegin="career.begin = $event"
              @updateCareerCompany="career.company = $event"
              @updateCareerDescription="career.description = $event"
              @updateCareerEnd="career.end = $event"
              @updateCareerPosition="career.position = $event"
            />

            <v-list-item>
              <v-btn icon @click="addCareer">
                <v-icon>
                  mdi-plus
                </v-icon>
              </v-btn>
            </v-list-item>

            <v-divider/>

            <v-subheader class="black--text text-subtitle-1">
              資格
            </v-subheader>

            <CertificationEdit
              v-for="(certification, index) in user.certifications"
              :key="`certification-${index}`"
              :certification="certification"
              @deleteCertification="deleteCertification(index)"
              @updateCertificationInvalid="updateCertificationInvalid($event, index)"
              @updateCertificationName="certification.name = $event"
              @updateCertificationAcquisition="certification.acquisition = $event"
            />

            <v-list-item>
              <v-btn icon @click="addCertification">
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
                @click="updateUser"
              >
                更新
              </v-btn>
            </div>
          </v-list>
        </v-col>
      </v-row>
    </v-card>
  </main>
</template>

<script>
import { validationMixin } from 'vuelidate';
import { required, maxLength } from 'vuelidate/lib/validators';
import { getDownloadURL, getStorage, ref, uploadBytes } from 'firebase/storage';
import OrganizationEdit from '@/components/users/OrganizationEdit';
import CareerEdit from '@/components/users/CareerEdit';
import CertificationEdit from '@/components/users/CertificationEdit';

export default {
  name: 'UserEditPage',
  components: {
    OrganizationEdit,
    CareerEdit,
    CertificationEdit,
  },
  mixins: [
    validationMixin,
  ],
  validations: {
    user: {
      username: {
        required,
        maxLength: maxLength(32),
      },
      twitterUri: {
        maxLength: maxLength(255),
      },
      facebookUri: {
        maxLength: maxLength(255),
      },
      websiteUri: {
        maxLength: maxLength(255),
      },
      introduction: {
        maxLength: maxLength(500),
      },
      wannaDo: {
        maxLength: maxLength(500),
      },
    },
  },
  middleware: [
    'loggedIn',
    'isAuthenticated',
  ],
  async asyncData({ params, $axios, error }) {
    // ユーザー情報取得
    let user = await $axios.$get(`api/data/user/${params.id}`);
    const data = await $axios.$get('api/data/user/edit');

    if (!user) error({ statusCode: 404 });
    user = user.user;

    // Firebase Storageから画像を取得
    const usericonImage = await fetch(user.usericonImageUri)
      .then(res => res.blob())
      .then(blob => new File([blob], user.usericonImageUri));

    // 所属団体・経歴・資格のバリデーション
    const organizationsInvalids = new Array(user.organizations.length).fill(false);
    const careersInvalids = new Array(user.careers.length).fill(false);
    const certificationsInvalids = new Array(user.certifications.length).fill(false);

    // すべての子コンポーネントのバリデーション結果
    const childComponentsInvalids = false;

    return {
      user,
      usericonImage,
      data,
      organizationsInvalids,
      careersInvalids,
      certificationsInvalids,
      childComponentsInvalids,
    }
  },
  computed: {
    usernameErrors() {
      const errors = [];
      if (!this.$v.user.username.$dirty) return errors;
      !this.$v.user.username.required && errors.push('ユーザー名は必須です。');
      !this.$v.user.username.maxLength && errors.push('ユーザー名は32文字以内にしてください。');
      return errors;
    },
    twitterUriErrors() {
      const errors = [];
      if (!this.$v.user.twitterUri.$dirty) return errors;
      !this.$v.user.twitterUri.maxLength && errors.push('リンクは255文字以内にしてください');
      return errors;
    },
    facebookUriErrors() {
      const errors = [];
      if (!this.$v.user.facebookUri.$dirty) return errors;
      !this.$v.user.facebookUri.maxLength && errors.push('リンクは255文字以内にしてください');
      return errors;
    },
    websiteUriErrors() {
      const errors = [];
      if (!this.$v.user.websiteUri.$dirty) return errors;
      !this.$v.user.websiteUri.maxLength && errors.push('リンクは255文字以内にしてください');
      return errors;
    },
    introductionErrors() {
      const errors = [];
      if (!this.$v.user.introduction.$dirty) return errors;
      !this.$v.user.introduction.maxLength && errors.push('自己紹介は500文字以内にしてください');
      return errors;
    },
    wannaDoErrors() {
      const errors = [];
      if (!this.$v.user.wannaDo.$dirty) return errors;
      !this.$v.user.wannaDo.maxLength && errors.push('今後やりたいことは500文字以内にしてください');
      return errors;
    },
    // 入力された画像のURLを取得
    usericonImageUri() {
      // 入力されていない場合はデフォルトアイコンにする
      if (!this.usericonImage) return 'https://firebasestorage.googleapis.com/v0/b/connect-335200.appspot.com/o/default_usericon_image.jpg?alt=media&token=64fae014-0bfa-4cc7-9404-7906eace4642';
      const uri = URL.createObjectURL(this.usericonImage);
      return uri;
    }
  },
  methods: {
    updateOrganizationInvalid($event, index) {
      this.organizationsInvalids[index] = $event;
      this.childComponentsInvalids = this.organizationsInvalids.includes(true);
    },
    addOrganization() {
      const organization = {
        id: null,
        user_id: this.$auth.user.id,
        name: '',
      };

      this.user.organizations.push(organization);
      this.organizationsInvalids.push(true);
      this.childComponentsInvalids = this.organizationsInvalids.includes(true);
    },
    deleteOrganization(index) {
      this.user.organizations.splice(index, 1);
      this.organizationsInvalids.splice(index, 1);
      this.childComponentsInvalids = this.organizationsInvalids.includes(true);
    },
    updateCareerInvalid($event, index) {
      this.careersInvalids[index] = $event;
      this.childComponentsInvalids = this.careersInvalids.includes(true);
    },
    addCareer() {
      const career = {
        id: null,
        user_id: this.$auth.user.id,
        begin: '',
        end: '',
        company: '',
        position: '',
        description: '',
      };

      this.user.careers.push(career);
      this.careersInvalids.push(true);
      this.childComponentsInvalids = this.careersInvalids.includes(true);
    },
    deleteCareer(index) {
      this.user.careers.splice(index, 1);
      this.careersInvalids.splice(index, 1);
      this.childComponentsInvalids = this.careersInvalids.includes(true);
    },
    updateCertificationInvalid($event, index) {
      this.certificationsInvalids[index] = $event;
      this.childComponentsInvalids = this.certificationsInvalids.includes(true);
    },
    addCertification() {
      const certification = {
        id: null,
        user_id: this.$auth.user.id,
        name: '',
        acquisition: '',
      };

      this.user.certifications.push(certification);
      this.certificationsInvalids.push(true);
      this.childComponentsInvalids = this.certificationsInvalids.includes(true);
    },
    deleteCertification(index) {
      this.user.certifications.splice(index, 1);
      this.certificationsInvalids.splice(index, 1);
      this.childComponentsInvalids = this.certificationsInvalids.includes(true);
    },
    async updateUser() {
      // 画像アップロードとURI取得
      if (this.usericonImage) {
        const date = this.getDate();
        const usericonImageName = date + this.usericonImage.name;
        const storage = getStorage();
        const storageRef = ref(storage, usericonImageName);
        await uploadBytes(storageRef, this.usericonImage);
        this.user.usericonImageUri = await getDownloadURL(storageRef);
      } else {
        this.user.usericonImageUri = 'https://firebasestorage.googleapis.com/v0/b/connect-335200.appspot.com/o/default_usericon_image.jpg?alt=media&token=64fae014-0bfa-4cc7-9404-7906eace4642';
      }

      // ユーザー情報の更新
      await this.$axios.$patch('/api/update/user', this.user);

      this.$router.push(`/users/${this.user.userId}`);
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
