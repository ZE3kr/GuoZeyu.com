export async function onRequest(request) {
  try {
    const url = new URL(request.url)
    url.protocol = 'https:'
    url.hostname = 'matomo.tloxygen.com'
    url.pathname = '/matomo.php'
    return fetch(url.toString(), request, { cf: { resolveOverride: url.hostname } })
  } catch (err) {
    return new Response(`${err.message}\n${err.stack}`, { status: 500 })
  }
}
