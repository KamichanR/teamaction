<template>
  <v-row justify="center" align="center">
    <v-col cols="12" sm="8" md="6">
      <v-card>
        <v-card-title class="headline">
          TEAMACTION
        </v-card-title>
        <v-card-text>
          <p>
            このサイトはKamichan_Rのポートフォリオの一部として作成したものになります。
          </p>
          <p>
            プロジェクトの発案者が簡単にその情報を掲載して、一緒にプロジェクトを実行する仲間を募集できるサービスを想定しています。
          </p>
          <p>
            フロントエンドはNuxt.js、バックエンドはLaravelを使用しています。
          </p>
        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn icon href="https://github.com/KamichanR" target="_blank">
            <v-icon x-large>
              mdi-github
            </v-icon>
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-col>
  </v-row>
</template>

<script>
export default {
  name: 'IndexPage',
}
</script>
