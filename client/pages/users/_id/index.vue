<template>
  <main>
    <v-card class="my-8 pa-8">
      <v-row>
        <v-col class="d-flex justify-center pa-0" cols="12">
          <v-avatar size="200">
            <v-img
              :src="user.usericonImageUri"
              cover
            />
          </v-avatar>
        </v-col>

        <v-col class="d-flex justify-center pa-0" cols="12">
          <v-card-title>
            <h1>{{ user.username }}</h1>
            <a
              v-if="$auth.loggedIn && $auth.user.id === user.userId"
              :href="`/users/${$auth.user.id}/edit`"
            >
              <v-icon class="ml-4" large>mdi-pencil</v-icon>
            </a>
          </v-card-title>
        </v-col>

        <v-col class="d-flex justify-center pa-0" cols="12">
          <p class="ma-0">
            <span
              v-for="(organization, index) in user.organizations"
              :key="index"
              class="organization"
            >{{ organization.name }}</span>
          </p>
        </v-col>

        <v-col class="d-flex justify-center justify-content" cols="12">
          <div class="mx-4">
            <v-icon large>
              mdi-map-marker
            </v-icon>
            <span v-if="user.area"  class="text-subtitle-1">
              {{ user.area.name }}
            </span>
            <span v-else class="text-subtitle-1">
              未登録
            </span>
          </div>

          <div v-if="user.twitterUri" class="mx-4">
            <a :href="user.twitterUri" target="_blank" rel="noopener noreferrer">
              <v-icon color="#1DA1F2" large>
                mdi-twitter
              </v-icon>
            </a>
          </div>

          <div v-if="user.facebookUri" class="mx-4">
            <a :href="user.facebookUri" target="_blank" rel="noopener noreferrer">
              <v-icon color="#4267B2" large>
                mdi-facebook
              </v-icon>
            </a>
          </div>

          <div v-if="user.websiteUri" class="mx-4">
            <a :href="user.websiteUri" target="_blank" rel="noopener noreferrer">
              <v-icon large>
                mdi-web
              </v-icon>
            </a>
          </div>
        </v-col>

        <v-divider/>

        <v-col cols="6">
          <v-card-title class="d-flex justify-center font-weight-bold">
            <h2>ワークスタイル</h2>
          </v-card-title>

          <v-row>
            <v-col
              v-for="(workstyle, index) in user.workstyles"
              :key="index"
              cols="6"
            >
              <v-sheet
                class="pa-2 light-green accent-4 rounded-pill"
                elevation="6"
              >
                <p class="text-center font-weight-medium ma-0">
                  {{ workstyle.name }}
                </p>
              </v-sheet>
            </v-col>
          </v-row>
        </v-col>

        <v-divider vertical/>

        <v-col cols="6">
          <v-card-title class="d-flex justify-center font-weight-bold">
            <h2>スキル</h2>
          </v-card-title>
          <v-row>
            <v-col
              v-for="(skill, index) in user.skills"
              :key="index"
              cols="6"
            >
              <v-sheet
                class="pa-2 light-green accent-4 rounded-pill"
                elevation="6"
              >
                <p class="text-center font-weight-medium ma-0">
                  {{ skill.name }}
                </p>
              </v-sheet>
            </v-col>
          </v-row>
        </v-col>

        <v-col cols="12">
          <v-list>
            <v-list-item>
              <div>
                <v-card-title class="d-flex justify-center font-weight-bold">
                  <h2>自己紹介</h2>
                </v-card-title>

                <p>
                  {{ user.introduction }}
                </p>
              </div>
            </v-list-item>

            <v-list-item>
              <div>
                <v-card-title class="d-flex justify-center font-weight-bold">
                  <h2>やりたいこと</h2>
                </v-card-title>

                <p>
                  {{ user.wannaDo }}
                </p>
              </div>
            </v-list-item>

            <v-divider/>
          </v-list>
        </v-col>

        <v-col cols="6">
          <v-list>
           <v-card-title class="d-flex justify-center font-weight-bold">
              <h2>経歴</h2>
            </v-card-title>

            <v-list-item
              v-for="(career, index) in user.careers"
              :key="`career-${index}`"
            >
              <v-list-item-content>
                <v-list-item-title>
                  <h3>{{ career.company }}</h3>
                </v-list-item-title>
                <v-list-item-subtitle class="text--primary">
                  {{ career.position }}
                </v-list-item-subtitle>
                <v-list-item-subtitle>
                  {{ career.begin }} ~ {{ career.end }}
                </v-list-item-subtitle>
              </v-list-item-content>
            </v-list-item>
          </v-list>
        </v-col>

        <v-divider vertical/>

        <v-col cols="6">
          <v-list>
           <v-card-title class="d-flex justify-center font-weight-bold">
              <h2>資格</h2>
            </v-card-title>

            <v-list-item
              v-for="(certification, index) in user.certifications"
              :key="`certification-${index}`"
            >
              <v-list-item-content>
                <v-list-item-title>
                  <h3>{{ certification.name }}</h3>
                </v-list-item-title>
                <v-list-item-subtitle>
                  {{ certification.acquisition }}
                </v-list-item-subtitle>
              </v-list-item-content>
            </v-list-item>
          </v-list>
        </v-col>
      </v-row>
    </v-card>
  </main>
</template>

<script>
export default {
  name: 'UserPage',
  async asyncData({ params, $axios, error }) {
    const user = await $axios.$get(`api/data/user/${params.id}`);

    if (!user) error({ statusCode: 404 });

    return {
      user,
    }
  },
}
</script>

<style lang="scss" scoped>
.organization::after {
  content: '，';
}

.organization:last-child:after {
  content: none;
}
</style>
