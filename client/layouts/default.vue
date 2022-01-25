<template>
  <v-app dark>
    <v-navigation-drawer
      v-model="drawer"
      fixed
      clipped
      app
    >
      <v-list>
        <v-list-item
          v-for="(item, i) in items"
          :key="i"
          :to="item.to"
          router
          exact
        >
          <v-list-item-action>
            <v-icon>{{ item.icon }}</v-icon>
          </v-list-item-action>
          <v-list-item-content>
            <v-list-item-title v-text="item.title" />
          </v-list-item-content>
        </v-list-item>
      </v-list>
    </v-navigation-drawer>
    <v-app-bar fixed clipped-left app>
      <v-app-bar-nav-icon @click.stop="drawer = !drawer" />
      <v-toolbar-title v-text="title" />
      <v-spacer/>
      <div v-if="$auth.loggedIn">
        <v-btn
          class="mx-2"
          @click="$auth.logout()"
        >
          ログアウト
        </v-btn>
        <v-btn
          class="mx-2"
          to="/projects/create"
        >
          新規作成
        </v-btn>
      </div>
      <div v-else>
        <v-btn
          class="mx-2"
          to="/register"
        >
          新規登録
        </v-btn>
        <v-btn
          class="mx-2"
          to="/login"
        >
          ログイン
        </v-btn>
      </div>
      <v-btn
        v-if="$auth.loggedIn"
        icon
        @click.stop="rightDrawer = !rightDrawer"
      >
        <v-icon>mdi-menu</v-icon>
      </v-btn>
    </v-app-bar>
    <v-main>
      <v-container>
        <Nuxt />
      </v-container>
    </v-main>
    <v-navigation-drawer
      v-if="$auth.loggedIn"
      v-model="rightDrawer"
      fixed
      right
      temporary
    >
      <v-list>
        <v-list-item>
          <v-btn block :disabled="disabledButton">
            プロフィール
          </v-btn>
        </v-list-item>
        <v-divider/>
        <v-list-item>
          <v-btn block :disabled="disabledButton" @click="linkWithProvider('twitter')">
            <v-icon class="mr-2" color="#1DA1F2">
              mdi-twitter
            </v-icon>
            <span>
              Twitterと連携
            </span>
          </v-btn>
        </v-list-item>
        <v-list-item>
          <v-btn :disabled="disabledButton" block>
            <v-icon class="mr-2" color="#4285F4">
              mdi-google
            </v-icon>
            <span>
              Googleと連携
            </span>
          </v-btn>
        </v-list-item>
      </v-list>
    </v-navigation-drawer>
    <v-footer absolute app>
      <span>&copy; {{ new Date().getFullYear() }}</span>
    </v-footer>
  </v-app>
</template>

<script>
import { getAuth, GoogleAuthProvider, linkWithPopup, TwitterAuthProvider } from '@firebase/auth';

export default {
  name: 'DefaultLayout',
  data() {
    return {
      disabledButton: false,
      linkWithTwitterLoading: false,
      linkWithGoogleLoading: false,
      drawer: true,
      items: [
        {
          icon: 'mdi-apps',
          title: 'Welcome',
          to: '/',
        },
        {
          icon: 'mdi-chart-bubble',
          title: 'Inspire',
          to: '/inspire',
        },
      ],
      right: true,
      rightDrawer: false,
      title: 'TEAMACTION',
    }
  },
  methods: {
    async linkWithProvider($provider) {
      // 変数宣言
      let auth, provider;

      // ボタンの制限
      this.disabledButton = true;

      try {
        // 外部アカウントの連携
        auth = getAuth();
        switch ($provider) {
          case 'twitter':
            this.linkWithTwitterLoading = true;
            provider = new TwitterAuthProvider();
            break;
          case 'google':
            this.linkWithGoogleLoading = true;
            provider = new GoogleAuthProvider();
            break;
          default:
            throw new Error('エラー');
        }
        await linkWithPopup(auth.currentUser, provider);
        this.$axios.get(`/api/auth/twitter`);
      } catch (error) {
        this.linkWithTwitterLoading = false;
        this.linkWithGoogleLoading = false;
        this.disabledButton = false;
      }
    }
  },
}
</script>
