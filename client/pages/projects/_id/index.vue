<template>
  <main>
    <v-card class="my-8 pa-8">
      <v-list>
        <v-subheader class="black--text">
          <h1>{{ project.projectTitle }}</h1>
        </v-subheader>

        <v-list-item>

        </v-list-item>
      </v-list>
    </v-card>
  </main>
</template>

<script>
export default {
  name: 'ProjectPage',
  async asyncData({ params, $axios, error }) {
    const project = await $axios.$get(`api/data/user/${params.id}`);

    if (!project) return error({ statusCode: 404 });

    return {
      project
    };
  },
}
</script>
