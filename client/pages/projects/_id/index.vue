<template>
  <main>
    <v-card>
      <v-img
        :src="project.projectImageUri"
        height="300px"
        dark
      />

      <v-list class="my-8 px-8 pb-8">
        <v-list-item
          v-if="$auth.loggedIn && $auth.user.id === project.userId"
          class="d-flex justify-end"
        >
          <a :href="`/projects/${project.id}/edit`">
            <v-icon large>mdi-pencil</v-icon>
          </a>
          <a @click="deleteConfirm = !deleteConfirm">
            <v-icon large>mdi-delete</v-icon>
          </a>
        </v-list-item>

        <v-subheader class="black--text">
          <h1>{{ project.projectTitle }}</h1>
        </v-subheader>

        <v-list-item>
          <v-avatar>
            <v-img
              :src="project.usericonImageUri"
              cover
            />
          </v-avatar>
          <span class="font-weight-medium pl-4 text-h6">
            {{ project.username }}
          </span>
        </v-list-item>

        <v-list-item>
          <v-icon>
            mdi-map-marker
          </v-icon>
          <span>
            {{ project.area.name }}
          </span>
        </v-list-item>

        <v-list-item>
          <v-sheet
            class="px-2 light-green darken-1 rounded-lg"
            elevation="6"
          >
            <v-icon>
              mdi-pound
            </v-icon>
            <span>
              {{ project.projectCategory.name }}
            </span>
          </v-sheet>
        </v-list-item>

        <v-list-item>
          <v-sheet
            v-for="(skill, index) in project.skills"
            :key="`skill${index}`"
            class="mr-2 px-2 light-green accent-4 rounded-lg"
            elevation="6"
          >
            <v-icon>
              mdi-format-list-bulleted
            </v-icon>
            <span>
              {{ project.projectCategory.name }}
            </span>
          </v-sheet>
        </v-list-item>

        <v-subheader class="black--text">
          <h2>プロジェクトの目的</h2>
        </v-subheader>

        <v-list-item>
          <p style="white-space: pre-wrap;" v-text="project.purpose" />
        </v-list-item>

        <v-subheader class="black--text">
          <h2>プロジェクトの説明</h2>
        </v-subheader>

        <v-list-item>
          <p style="white-space: pre-wrap;" v-text="project.description" />
        </v-list-item>

        <v-subheader v-if="project.idealCandidate" class="black--text">
          <h2>求める人材</h2>
        </v-subheader>

        <v-list-item v-if="project.idealCandidate">
          <p style="white-space: pre-wrap;" v-text="project.idealCandidate" />
        </v-list-item>

        <v-subheader v-if="project.merit" class="black--text">
          <h2>参加するメリット</h2>
        </v-subheader>

        <v-list-item v-if="project.merit">
          <p style="white-space: pre-wrap;" v-text="project.merit" />
        </v-list-item>

        <v-list-item>
          <v-row>
            <v-col
              v-for="(role, index) in project.projectRoles"
              :key="`role${index}`"
              cols="4"
            >
              <v-card>
                <v-list>
                  <v-subheader class="black--text">
                    <h3>
                      {{ role.role_title }}
                    </h3>
                  </v-subheader>

                  <v-list-item>
                    <p style="white-space: pre-wrap;" v-text="role.description" />
                  </v-list-item>

                  <v-subheader>
                    <span>募集人数</span>
                  </v-subheader>

                  <v-list-item>
                    <v-icon>
                      mdi-account
                    </v-icon>
                    <span>
                      {{ role.number_of_applicants }}人
                    </span>
                  </v-list-item>

                  <v-subheader>
                    <span>募集締切</span>
                  </v-subheader>

                  <v-list-item>
                    <v-icon>
                      mdi-calendar
                    </v-icon>
                    <span>
                      {{ role.close }}
                    </span>
                  </v-list-item>

                  <v-list-item v-if="$auth.loggedIn">
                    <v-btn
                      class="font-weight-bold amber darken-3"
                      @click="apply"
                    >
                      応募する
                    </v-btn>
                  </v-list-item>
                </v-list>
              </v-card>
            </v-col>
          </v-row>
        </v-list-item>
      </v-list>

      <v-dialog
        v-model="deleteConfirm"
        class="pa-4"
        width="600"
      >
        <v-card>
          <v-card-title>
            プロジェクトを削除しますか？
          </v-card-title>
          <v-card-actions>
            <v-btn @click="deleteConfirm = !deleteConfirm">
              キャンセル
            </v-btn>
            <v-btn class="red accent-4 white--text" @click="deleteProject">
              削除
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
    </v-card>

    <v-btn
      v-if="$auth.loggedIn && $auth.user.id !== project.userId"
      class="favorite-button"
      fab
      icon
      large
      outlined
    >

      <v-icon
        v-if="favorite"
        color="success"
        large
        @click="favorite = !favorite"
      >
        mdi-star
      </v-icon>

      <v-icon
        v-else
        large
        @click="favorite = !favorite"
      >
        mdi-star-outline
      </v-icon>
    </v-btn>
  </main>
</template>

<script>
export default {
  name: 'ProjectPage',
  async beforeRouteLeave(to, from, next) {
    if (this.$auth.loggedIn) await this.$axios.$post(`/api/update/favorite/${this.project.id}`, {favorite: this.favorite});
    next();
  },
  async asyncData({ params, $axios }) {
    const data = await $axios.$get(`api/data/project/${params.id}`);

    return {
      project: data.project,
      favorite: data.favorite ?? false,
      deleteConfirm: false,
    };
  },
  methods: {
    async deleteProject() {
      if (this.$auth.user.id === this.project.userId) await this.$axios.$delete(`api/delete/project/${this.project.id}`);

      this.$router.push('/projects');
    },
    apply() {
      this.$axios.$post(`api/project-apply/${this.$auth.user.id}/${this.project.id}`, {
        userId: this.$auth.user.id,
        projectId: this.project.id,
      });
    },
  }
}
</script>

<style lang="scss" scoped>
.favorite-button {
  bottom: 5%;
  position: absolute;
  right: 5%;
}
</style>
