<template>
  <main>
    <v-card>
      <v-card-title>
        <h1>新規登録</h1>
      </v-card-title>
      <div class="pa-4">
        <v-form class="ma-4">
          <div>
            <p class="font-weight-bold red--text">
              {{ errorMessage }}
            </p>
          </div>

          <v-text-field
            v-model="form.username"
            label="ユーザー名"
            :counter="255"
            :error-messages="usernameErrors"
            @input="$v.form.username.$touch()"
            @blur="$v.form.username.$touch()"
          />
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
          <v-text-field
            v-model="form.passwordConfirmation"
            label="パスワード（確認用）"
            :append-icon="showPasswordConfirmation ? 'mdi-eye' : 'mdi-eye-off'"
            :counter="255"
            :error-messages="passwordConfirmationErrors"
            :type="showPasswordConfirmation ? 'text' : 'password'"
            @blur="$v.form.passwordConfirmation.$touch()"
            @click:append="showPasswordConfirmation = !showPasswordConfirmation"
            @input="$v.form.passwordConfirmation.$touch()"
            @keyup="keyUpHalfSizeRestriction"
          />

          <div class="d-flex justify-center">
            <v-btn
              class="font-weight-bold"
              :disabled="$v.$invalid || disabledButton"
              :loading="registerWithEmailLoading"
              @click="registerUser('email')"
            >
              新規登録
            </v-btn>
          </div>
        </v-form>

        <v-divider/>

        <div class="d-flex justify-space-around my-4">
          <v-btn
            :disabled="disabledButton"
            :loading="registerWithTwitterLoading"
            @click="registerUser('twitter')"
          >
            <v-icon class="mr-2" color="#1DA1F2">
              mdi-twitter
            </v-icon>
            <span class="font-weight-bold">
              Twitterで新規登録
            </span>
          </v-btn>
          <v-btn
            :disabled="disabledButton"
            :loading="registerWithGoogleLoading"
            @click="registerUser('google')"
          >
            <v-icon class="mr-2" color="#4285F4">
              mdi-google
            </v-icon>
            <span class="font-weight-bold">
              Googleで新規登録
            </span>
          </v-btn>
        </div>
      </div>
    </v-card>
  </main>
</template>

<script>
import { validationMixin } from 'vuelidate';
import { required, minLength, maxLength, email, sameAs } from 'vuelidate/lib/validators';
import { createUserWithEmailAndPassword, getAuth, GoogleAuthProvider, signInWithPopup, TwitterAuthProvider } from 'firebase/auth';

export default {
  name: 'RegiserPage',
  mixins: [
    validationMixin,
  ],
  data() {
    return {
      form: {
        username: '',
        email: '',
        password: '',
        passwordConfirmation: '',
      },
      showPassword: false,
      showPasswordConfirmation: false,
      registerWithEmailLoading: false,
      registerWithTwitterLoading: false,
      registerWithGoogleLoading: false,
      disabledButton: false,
      errorMessage: '',
    }
  },
  validations: {
    form: {
      username: {
        required,
        maxLength: maxLength(255),
      },
      email: {
        required,
        email,
      },
      password: {
        required,
        minLength: minLength(8),
      },
      passwordConfirmation: {
        required,
        sameAs: sameAs('password'),
      },
    },
  },
  computed: {
    usernameErrors() {
      const errors = [];
      if (!this.$v.form.username.$dirty) return errors;
      !this.$v.form.username.required && errors.push('ユーザー名は必須です。')
      !this.$v.form.username.maxLength && errors.push('ユーザー名は255文字以内にしてください。')
      return errors
    },
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
      !this.$v.form.password.minLength && errors.push('パスワードは8文字以上にしてください')
      return errors
    },
    passwordConfirmationErrors() {
      const errors = []
      if (!this.$v.form.passwordConfirmation.$dirty) return errors
      !this.$v.form.passwordConfirmation.required && errors.push('パスワード(確認用)は必須です。')
      !this.$v.form.passwordConfirmation.sameAs && errors.push('パスワードと一致しません。')
      return errors
    },
  },
  methods: {
    // 入力制限（半角英数字）
    keyUpHalfSizeRestriction() {
      this.form.password = this.form.password.replace(/[^0-9a-zA-Z]/g,'');
    },
    // ユーザーの新規登録
    async registerUser($provider) {
      // 変数宣言
      let auth, userCredentials, provider, user;

      // ボタンの制限
      this.disabledButton = true;

      try {
        // Firebase Authenticationでアカウント作成
        auth = getAuth();
        switch ($provider) {
          case 'email':
            this.registerWithEmailLoading = true;
            userCredentials = await createUserWithEmailAndPassword(auth, this.form.email, this.form.password);
            break;
          case 'twitter':
            this.registerWithTwitterLoading = true;
            provider = new TwitterAuthProvider();
            userCredentials = await signInWithPopup(auth, provider);
            break;
          case 'google':
            this.registerWithGoogleLoading = true;
            provider = new GoogleAuthProvider();
            userCredentials = await signInWithPopup(auth, provider);
            break;
          default:
            throw new Error('エラー');
        }
        user = {
          username: userCredentials.user.displayName ?? this.form.username,
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
        this.registerWithEmailLoading = false;
        this.registerWithTwitterLoading = false;
        this.registerWithGoogleLoading = false;
        this.disabledButton = false;
      }
    },
  },
};
</script>
