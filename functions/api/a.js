export async function onRequest(request) {
  const url = new URL(request.url)
  url.protocol = 'https:'
  url.hostname = 'matomo.tloxygen.com'
  url.pathname = '/matomo.php'
  fetch(url.toString(), request, { cf: { resolveOverride: url.hostname } })
  return new Response(null, { status: 204 })
}
