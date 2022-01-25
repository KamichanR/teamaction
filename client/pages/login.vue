<template>
  <main>
    <v-card>
      <v-card-title>
        <h1>ログイン</h1>
      </v-card-title>
      <div class="pa-4">
        <v-form class="ma-4">
          <div>
            <p class="font-weight-bold red--text">
              {{ errorMessage }}
            </p>
          </div>

          <v-text-field
            v-model="form.email"
            label="メールアドレス"
            :counter="255"
            :error-messages="emailErrors"
            @input="$v.form.email.$touch()"
            @blur="$v.form.email.$touch()"
          />
          <v-text-field
            v-model="form.password"
            label="パスワード"
            :counter="255"
            :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
            :error-messages="passwordErrors"
            :type="showPassword ? 'text' : 'password'"
            @blur="$v.form.password.$touch()"
            @click:append="showPassword = !showPassword"
            @input="$v.form.password.$touch()"
            @keyup="keyUpHalfSizeRestriction"
          />

          <div class="d-flex justify-center">
            <v-btn
              class="font-weight-bold"
              :disabled="$v.$invalid || disabledButton"
              :loading="loginWithEmailLoading"
              @click="loginUser('email')"
            >
              ログイン
            </v-btn>
          </div>
        </v-form>

        <v-divider/>

        <div class="d-flex justify-space-around my-4">
          <v-btn
            :disabled="disabledButton"
            :loading="loginWithTwitterLoading"
            @click="loginUser('twitter')"
          >
            <v-icon
              left
              color="#1DA1F2"
            >
              mdi-twitter
            </v-icon>
            <span class="font-weight-bold">Twitterでログイン</span>
          </v-btn>
          <v-btn
            :disabled="disabledButton"
            :loading="loginWithGoogleLoading"
            @click="loginUser('google')"
          >
            <v-icon
              left
              color="#4285F4"
            >
              mdi-google
            </v-icon>
            <span class="font-weight-bold">Googleでログイン</span>
          </v-btn>
        </div>
      </div>
    </v-card>
  </main>
</template>

<script>
import { validationMixin } from 'vuelidate';
import { required, minLength, email } from 'vuelidate/lib/validators';
import { getAuth, signInWithEmailAndPassword, signInWithPopup, TwitterAuthProvider } from '@firebase/auth';

export default {
  name: 'LoginPage',
  mixins: [
    validationMixin,
  ],
  data() {
    return {
      form: {
        email: '',
        password: '',
      },
      showPassword: false,
      loginWithEmailLoading: false,
      loginWithTwitterLoading: false,
      loginWithGoogleLoading: false,
      disabledButton: false,
      errorMessage: '',
    }
  },
  validations: {
    form: {
      email: {
        required,
        email,
      },
      password: {
        required,
        minLength: minLength(8),
      },
    },
  },
  computed: {
    emailErrors() {
      const errors = []
      if (!this.$v.form.email.$dirty) return errors
      !this.$v.form.email.email && errors.push('有効なメールアドレスを入力してください。')
      !this.$v.form.email.required && errors.push('メールアドレスは必須です。')
      return errors
    },
    passwordErrors() {
      const errors = []
      if (!this.$v.form.password.$dirty) return errors
      !this.$v.form.password.required && errors.push('パスワードは必須です。')
      !this.$v.form.password.minLength && errors.push('パスワードは8文字以上です。')
      return errors
    },
  },
  methods: {
    // 入力制限（半角英数字）
    keyUpHalfSizeRestriction() {
      this.form.password = this.form.password.replace(/[^0-9a-zA-Z]/g,'');
    },
    // ユーザーのログイン
    async loginUser($provider) {
      // 変数宣言
      let auth, userCredentials, provider, user;

      // ボタンの制限
      this.disabledButton = true;

      try {
        // Firebase AuthenticationでUIDを取得
        auth = getAuth();
        switch ($provider) {
          case 'email':
            this.loginWithEmailLoading = true;
            userCredentials = await signInWithEmailAndPassword(auth, this.form.email, this.form.password);
            break;
          case 'twitter':
            this.loginWithTwitterLoading = true;
            provider = new TwitterAuthProvider();
            userCredentials = await signInWithPopup(auth, provider);
            break;
          case 'google':
            this.loginWithGoogleLoading = true;
            provider = new TwitterAuthProvider();
            userCredentials = await signInWithPopup(auth, provider);
            break;
          default:
            throw new Error('エラー');
        }
        user = {
          uid: userCredentials.user.uid,
        };

        // Laravel Sanctum SPA認証
        await this.$axios.get('sanctum/csrf-cookie');

        // ログイン処理
        this.$auth.loginWith('laravelSanctum', {
          withCredentials: true,
          data: user,
        });
      } catch (error) {
        this.errorMessage = 'エラーが発生しました。';
        this.loginWithEmailLoading = false;
        this.loginWithTwitterLoading = false;
        this.loginWithGoogleLoading = false;
        this.disabledButton = false;
      }
    },
  },
}
</script>
