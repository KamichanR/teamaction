export default function ({ store, redirect, params }) {
  if (!store.state.auth.user.id === params.id) redirect('./');
}
